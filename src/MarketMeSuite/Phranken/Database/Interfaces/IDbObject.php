<?php
namespace MarketMeSuite\Phranken\Database\Interfaces;

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
}
