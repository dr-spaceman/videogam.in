<?php

namespace Vgsite;

/**
 * Handle database queries and logging, object storage into IdentityMap
 */
abstract class Mapper
{
    /** @var string Database table name and primary key field name */
    protected $db_table;

    /** @var string The primary key in database */
    protected $db_id_field = 'id';
    
    /** @var string */
    protected $select_sql = "SELECT * FROM `%s` WHERE `%s`=? LIMIT 1";
    /** @var PDOStatement */
    protected $select_statement;
    /** @var string */
    protected $select_all_sql = "SELECT * FROM `%s`";
    /** @var PDOStatement */
    protected $select_all_statement;
    /** @var PDOStatement */
    protected $save_statement;
    /** @var PDOStatement */
    protected $insert_statement;
    /** @var PDOStatement */
    protected $delete_statement;

    /** @var PDO Registry object */
    protected $pdo;

    /** @var Monolog Registry object */
    protected $logger;

    /** @var IdentityMap */
    protected $identity_map;

    public function __construct()
    {
        $this->pdo = Registry::get('pdo');
        $this->logger = Registry::get('logger');
        $this->identity_map = new IdentityMap();

        $this->select_statement = $this->pdo->prepare(sprintf($this->select_sql, $this->db_table, $this->db_id_field));
        $this->select_all_statement = $this->pdo->prepare(sprintf($this->select_all_sql, $this->db_table));
    }

    public function findById(int $id, $get_identity_map=true): DomainObject
    {
        if ($get_identity_map && true === $this->identity_map->hasId($id)) {
            return $this->identity_map->getObject($id);
        }

        $this->select_statement->execute([$id]);
        $row = $this->select_statement->fetch();
        $this->select_statement->closeCursor();

        if (!is_array($row)) {
            throw new \OutOfBoundsException("User with id `{$id}` could not be found.");
        }

        return $this->createObject($row);
    }

    public function findAll(): Collection
    {
        $this->select_all_statement->execute([]);
        $rows = array();
        while ($row = $this->select_all_statement->fetch()) {
            $rows[] = $row;
        }

        return $this->getCollection($rows);
    }

    /**
     * Create a DomainObject from an array
     * @param  array  $row A multidimentional array, or one ordered as the class constructor requires
     * @return DomainObject
     */
    public function createObject(array $row): DomainObject
    {
        $obj = $this->doCreateObject($row);
        
        $this->identity_map->set($obj);

        return $obj;
    }

    /**
     * Insert object into database
     *
     * @param DomainObject $obj
     * 
     * @return DomainObject New object with ID
     */
    public function insert(DomainObject &$obj): DomainObject
    {
        $insert_result = $this->doInsert($obj);

        return $insert_result;
    }

    /**
     * Prepare a list of MySQL database fields for a MySQL query
     *
     * @param array $fields List of fields to filter
     * @param array $whitelist List of fields to allow
     * 
     * @return string Prepared safe string
     */
    public function prepareFields(array $fields=[], array $whitelist=[]): string
    {
        if (empty($fields)) {
            return '*';
        }

        $fields_pass = array_map(function ($value) {
            $value = trim($value);
            if (empty($value)) return null;
            // Nullify any fields with anything except alphanumerics, -, _
            if (preg_match('/[^a-z0-9\-_]/i', $value)) return null;
            return $value;
        }, $fields);
        $fields_pass = array_filter($fields_pass);

        if (! empty($whitelist)) {
            $fields_pass = array_intersect($fields, $whitelist);
        }

        if (empty($fields_pass)) {
            return '*';
        }

        return implode(', ', array_map(function ($value) {
            return '`'.$value.'`';
        }, $fields_pass));
    }

    public function __destruct()
    {
        unset($this->pdo, $this->logger, $this->identity_map);
    }
    
    abstract public function getCollection(array $rows): Collection;
    abstract protected function doCreateObject(array $row): DomainObject;
    abstract protected function doInsert(DomainObject &$object): DomainObject;
    abstract protected function targetClass(): string;
}