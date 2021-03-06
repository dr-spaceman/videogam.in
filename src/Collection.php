<?php

namespace Vgsite;

use Generator;

/**
 * A collection of objects (rows of data/arrays OR instantiated objects) made iterable through a generator
 */
abstract class Collection
{
    /** @var Mapper object */
    protected $mapper;

    /** @var integer Total number of items in collection */
    public $count = 0;
    
    /** @var array Rows of raw data, non-yet-instantiated objects */
    protected $rows = [];

    /**
     *  Instantiated objects added through Collection::add(DomainObject)
     *  If a client requests a particular element, the getRow() method 
     *  looks first in $objects to see if it has one already instantiated
     * @var array
     */
    private $objects = array();

    /**
     * Construct a Collection
     * 
     * @param array       $rows    Raw data not yet instantiated
     * @param Mapper|null $mapper A Mapper object
     */
    public function __construct(array $rows=[], Mapper $mapper=null)
    {
        $this->rows = $rows;
        $this->count = count($rows);

        if (count($rows) && is_null($mapper)) {
            throw new \InvalidArgumentException("Collection needs Mapper to generate objects");
        }

        $this->mapper = $mapper;
    }

    public function add(DomainObject $object)
    {
        $target_class = $this->targetClass();
        if (false === ($object instanceof $target_class)) {
            throw new \InvalidArgumentException("This is a {$target_class} collection");
        }

        $this->objects[$this->count] = $object;
        $this->count++;
    }

    public function getGenerator(): Generator
    {
        for ($x = 0; $x < $this->count; $x++) {
            yield $this->getRow($x);
        }
    }

    abstract protected function targetClass();

    public function getRow($num): DomainObject
    {        
        if ($num >= $this->count || $num < 0) {
            return null;
        }

        if (isset($this->objects[$num])) {
            return $this->objects[$num];
        }

        if (isset($this->rows[$num])) {
            $this->objects[$num] = $this->mapper->createObject($this->rows[$num]);
            return $this->objects[$num];
        }
    }

    // Below props and methods for Iterator
    
    // private $pointer = 0;

   //  public function rewind() {
   //     $this->pointer = 0;
   //  }

   // public function current() {
   //     return $this->getRow( $this->pointer );
   // }

   // public function key() {
   //     return $this->pointer;
   // }

   // public function next() {
   //      $row = $this->getRow( $this->pointer );
   //      if ( $row ) { $this->pointer++; }
   //      return $row;
   // }

   // public function valid() {
   //     return ( ! is_null( $this->current() ) );
   // }
}

// $genvencoll = new GenVenueCollection();
// $genvencoll->add(new Venue(-1, "Loud and Thumping"));
// $genvencoll->add(new Venue(-1, "Eeezy"));
// $genvencoll->add(new Venue(-1, "Duck and Badger"));
// $gen = $genvencoll->getGenerator();
// foreach ($gen as $wrapper) {
// print_r($wrapper);
// }

// /// dummy classes to test code

// class WrapperCollection extends Collection {
//     function __construct( $array, MockMapper $mapper ) {
//         parent::__construct( $array, $mapper );
//     }

//     function targetClass() {
//         return Wrapper::class;
//     }
// }

// abstract class Mapper {
//     abstract function createObject( array $fields );
// }

// class MockMapper extends Mapper {
//     function createObject( array $fields ) {
//         return new Wrapper( $fields );
//     }
// }

// class Wrapper {
//     private $fields;
//     function __construct( array $fields ) {
//         $this->fields = $fields;
//     }
//     function getFields() {
//         return $this->fields;
//     }
// }

// $blah = array( 
//             array( "name" => "bob" ),
//             array( "name" => "mary" ),
//             array( "name" => "sue" )
//         );
// $collection = new WrapperCollection( $blah, new MockMapper() );
// foreach ( $collection as $wrapper ) {
//     print_r( $wrapper );
// }
