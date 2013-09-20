<?php
namespace MarketMeSuite\Phranken\Database\Object;

use MarketMeSuite\Phranken\Database\Interfaces\IDbObject;
use MarketMeSuite\Phranken\Database\Exception\DbObjectException;
use MarketMeSuite\Phranken\Crypt\UniqueIDGenerator;

/**
 * Provides a way to load database objects into a well defined class structure
 */
abstract class DbObject implements IDbObject
{
    /**
     * The default "id" field for records
     * In mongo this is usually '_id' and in mysql it is 'id'
     * Because DbObject is database agnostic this is able to be overriden
     * by subclasses that may be for other database engines.
     * @var string
     */
    public static $ID_FIELD = '_id';

    /**
     * loads a mongo document array into this object structure
     * @param  array  $data A document array from a mongo query
     */
    public function fromArray(array $data)
    {
        $map = $this->getMap();

        foreach ($map as $dbKey => $variableName) {

            if (! property_exists($this, $variableName)) {
                throw new DbObjectException('property "' . $variableName . '" was not found in this object');
            }

            // if the key exists then set the property
            if (isset($data[$dbKey])) {
                $this->{$variableName} = $data[$dbKey];
            }
        }
    }

    /**
     * Converts this object into a database-compatable associative array
     * @return array An associative array representing this object
     */
    public function toArray()
    {
        $map = $this->getMap();
        $out = array();

        foreach ($map as $dbKey => $variableName) {
            // @note that "$this->$variableName" is a variable variable
            // it is on purpose
            $out[$dbKey] = $this->{$variableName};
        }

        return $out;
    }

    /**
     * Converts this object into particular mongo queries
     * @param  string $type The type of query to export
     * @return array        The result of toArray() with particular fields ommitted/added
     *                      depending on the type
     */
    public function toQuery($type)
    {
        $arr = $this->toArray();
        switch($type) {
            case 'set':
                // the _id field can not exist for set queries
                unset( $arr[static::$ID_FIELD] );
                return $arr;
            break;
            case 'insert':
                // genreate a UUID for the insert query
                $arr[static::$ID_FIELD] = UniqueIDGenerator::GenerateUUID();
                return $arr;
            break;
            default:
                throw new DbObjectException('query type "' . $type . '" has no actions');
            break;
        }
    }

    /**
     * sets an object property
     * @param string $key   The name of the property
     * @param mixed  $value The value to set $key to
     */
    public function setProp($key, $value)
    {
        $map = $this->getMap();

        // does the key exist in the map?
        if (!in_array($key, $map)) {
            throw new DbObjectException('"' . $key . '" does not exist in map');
        }

        $this->{$key} = $value;
    }

    /**
     * Gets a property
     * @param  string $key The name of the property
     * @return mixed       The value of $key
     */
    public function getProp($key)
    {
        $map = $this->getMap();

        if (!in_array($key, $map)) {
            throw new DbObjectException('"' . $key . '" does not exist in map');
        }

        return $this->{$key};
    }

    /**
     * Runs multiple fromArray() for every item in $arr
     * @param array $arr An array of mongo documents, Can also be a mongo cursor
     * @param string $class A valid class name that implements IDbObject
     * @return array An array of objects
     * @throws DbObjectException When $class does not implement IDbObject
     */
    public static function multiFromArray(array $arr, $class = 'DbObject')
    {

        if (new $class instanceof IDbObject) {
            // nothing
        } else {
            throw new DbObjectException('$class must implement IDbObject');
        }

        $objects = array();

        foreach ($arr as $dbArr) {
            $newObject = new $class;
            $newObject->fromArray($dbArr);
            $objects[] = $newObject;
        }

        return $objects;
    }

    /**
     * Runs multiple toArray() for every IDbObject in $arr
     * @param array $arr An array of IDbObject instances
     * @return array An array of arrays where each sub array is the result of calling
     * toArray on each objecting in $arr
     * @throws DbObjectException When an object in $arr does not implement IDbObject
     */
    public static function multiToArray(array $arr)
    {
        $arrs = array();

        foreach ($arr as $dbobject) {

            if ($dbobject instanceof IDbObject) {
                // nothing
            } else {
                throw new DbObjectException('$dbobject must implement IDbObject');
            }

            $arrs[] = $dbobject->toArray();
        }

        return $arrs;
    }

    public function getMap()
    {
        return array();
    }
}
