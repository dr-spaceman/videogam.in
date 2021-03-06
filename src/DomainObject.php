<?php

namespace Vgsite;

/**
 * Common attributes and methods for objects
 */
abstract class DomainObject
{
    protected $id = -1;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Get the registered mapper object for this class.
     * Useful for quick chaining finds, eg. User::getMapper()->findByUsername('Rahul');
     *
     * @return Mapper
     */
    public static function getMapper(): Mapper
    {
        return Registry::getMapper(static::class);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): DomainObject
    {
        if ($this->id > 0 && $this->id != $id) {
            throw new \Exception("ID parameter cannot be changed after it is set.");
        }

        $this->id = $id;

        return $this;
    }

    public function __clone()
    {
        $this->id = -1;
    }
}
