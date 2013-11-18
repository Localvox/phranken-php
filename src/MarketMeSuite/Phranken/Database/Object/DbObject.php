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
     * Because DbObject is database agnostic this is able to be overridden
     * by subclasses that may be for other database engines.
     * @var string
     */
    public static $ID_FIELD = '_id';

    /**
     * The property version of ID_FIELD
     *
     * @var string
     */
    public static $ID_PROP = 'id';

    /**
     * @var array An array of key => value pairs
     */
    public $map;

    /**
     * To be recognised by the toArray method, this bool should
     * control whether null values in properties are allowed.
     *
     * @var bool When true, null propert values are permitted
     *           otherwise properties with null values are
     *           ommitted from the final array
     */
    protected static $TO_ARRAY_ALLOW_NULL = false;

    /**
     * loads a mongo document array into this object structure
     *
     * @param  array $data A document array from a mongo query
     *
     * @throws DbObjectException If a required mapped property does not exist in $data
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

            $value = $this->getProp($variableName);

            // If the property is an instance of
            // IDbObject then execute it's toArray
            // and use the result as the value
            if ($value instanceof IDbObject) {
                $value = $value->toArray();
            }

            // if a value is encountered that is null then
            // and the class is set to ignore null values
            // then do not add this key => value to the array
            if (static::$TO_ARRAY_ALLOW_NULL === false && $value === null) {
                continue;
            }

            $out[$dbKey] = $value;
        }

        return $out;
    }

    /**
     * Converts this object into particular mongo queries
     *
     * @param  string $type The type of query to export
     *
     * @return array        The result of toArray() with particular fields ommitted/added
     *                      depending on the type
     * @throws DbObjectException If $type is not a recognised type
     */
    public function toQuery($type)
    {
        $arr = $this->toArray();
        switch($type) {
            case 'set':
                // the _id field can not exist for set queries
                unset( $arr[$this->getIdFieldName()] );
                return $arr;
            break;
            case 'insert':
                // genreate a UUID for the insert query
                $arr[$this->getIdFieldName()] = UniqueIDGenerator::GenerateUUID();
                return $arr;
            break;
            default:
                throw new DbObjectException('query type "' . $type . '" has no actions');
            break;
        }
    }

    /**
     * sets an object property
     *
     * @param string $key   The name of the property
     * @param mixed  $value The value to set $key to
     *
     * @throws DbObjectException If $key does not exist in the configured map
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
     *
     * @throws DbObjectException When $key does not exist in map
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
     *
     * @param array  $arr   An array of mongo documents, Can also be a mongo cursor
     * @param string $class A valid class name that implements IDbObject
     *
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
     *
     * @param array $arr An array of IDbObject instances
     *
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

    /**
     * @see IDbObject::getMap
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @see IDbObject::setMap
     */
    public function setMap(array $map)
    {
        $this->map = $map;
    }

    /**
     * @see IDbObject::setIdFieldName
     */
    public function setIdFieldName($name)
    {
        static::$ID_FIELD = $name;
    }

    /**
     * @see IDbObject::getIdFieldName
     */
    public function getIdFieldName()
    {
        return static::$ID_FIELD;
    }

    /**
     * The local id field is the local name of the property
     *
     * @param string $name The name of the id field
     */
    public function setIdPropName($name)
    {
        static::$ID_PROP = $name;
    }

    /**
     * Gets the set id that was set with setIdPropName()
     * @see setIdPropName
     */
    public function getIdPropName()
    {
        return static::$ID_PROP;
    }
}
