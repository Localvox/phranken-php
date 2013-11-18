<?php
namespace MarketMeSuite\Phranken\Database\Interfaces;

use MarketMeSuite\Phranken\Database\Exception\DbObjectException;

/**
 * defines the basic methods that a database object should expose
 */
interface IDbObject
{

    /**
     * loads a mongo document array into this object structure
     * @param  array  $data A document array from a mongo query
     */
    public function fromArray(array $data);

    /**
     * Converts this object into a database-compatable associative array
     * @return array An associative array representing this object
     */
    public function toArray();

    /**
     * Converts this object into particular mongo queries
     * @param  string $type The type of query to export
     * @return array        The result of toArray() with particular fields ommitted/added
     *                      depending on the type
     */
    public function toQuery($type);

    /**
     * gets a property map.
     * @return array Associative array where:
     *
     * $key   = the target database key
     * $value = the target property name
     */
    public function getMap();

    /**
     * @param array $map An associative array where:
     *
     * $key   = the target database key
     * $value = the target property name
     */
    public function setMap(array $map);

    /**
     * Runs multiple fromArray() for every item in $arr
     * @param array $arr An array of mongo documents, Can also be a mongo cursor
     * @param string $class A valid class name that implements IDbObject
     * @return array An array of objects
     * @throws DbObjectException When $class does not implement IDbObject
     */
    public static function multiFromArray(array $arr, $class = 'DbObject');

    /**
     * Runs multiple toArray() for every IDbObject in $arr
     * @param array $arr An array of IDbObject instances
     * @return array An array of arrays where each sub array is the result of calling
     * toArray on each objecting in $arr
     * @throws DbObjectException When an object in $arr does not implement IDbObject
     */
    public static function multiToArray(array $arr);

    /**
     * The id field is the field that is used to build insert queries
     * @param string $name The name of the id field
     */
    public function setIdFieldName($name);

    /**
     * Gets the set id that was set with setIdFieldName()
     * @see setIdFieldName
     */
    public function getIdFieldName();

    /**
     * The local id field is the local name of the property
     * @param string $name The name of the id field
     */
    public function setIdPropName($name);

    /**
     * Gets the set id that was set with setIdPropName()
     * @see setIdPropName
     */
    public function getIdPropName();

    /**
     * sets an object property
     *
     * @param string $key   The name of the property
     * @param mixed  $value The value to set $key to
     *
     * @throws DbObjectException If $key does not exist in the configured map
     */
    public function setProp($key, $value);

    /**
     * Gets a property
     * @param  string $key The name of the property
     * @return mixed       The value of $key
     *
     * @throws DbObjectException When $key does not exist in map
     */
    public function getProp($key);
}
