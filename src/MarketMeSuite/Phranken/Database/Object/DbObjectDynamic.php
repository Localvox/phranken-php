<?php
namespace MarketMeSuite\Phranken\Database\Object;

use MarketMeSuite\Phranken\Database\Interfaces\IDbObjectDynamic;

/**
 * Extends on DbObject by allowing storage of all aky => value pairs that
 * are not defined in the property map
 */
abstract class DbObjectDynamic extends DbObject implements IDbObjectDynamic
{
    /**
     * @var array An associative array of key => value pairs
     */
    protected $dynamicProps;

    /**
     * @var string The database id
     */
    public $id;

    /**
     * loads a mongo document array into this object structure
     * @param  array  $data A document array from a mongo query
     */
    public function fromArray(array $data)
    {
        parent::fromArray($data);
        $this->parseDynamicProperties($data);
    }

    /**
     * @return array
     * @see DbObject::toArray
     */
    public function toArray()
    {
        // do regular parse
        $arr = parent::toArray();

        // get all dynamic properties
        $dynamicProps = $this->getDynamicprops();

        // if no dynamic properties are set then
        // just return the parent result
        if (!is_array($dynamicProps)) {
            return $arr;
        }

        // merge and return full data
        return array_merge($arr, $dynamicProps);
    }


    /**
     * @see IDbObjectDynamic::parseDynamicProperties
     */
    public function parseDynamicProperties(array $data)
    {
        $map = $this->getMap();

        // for each key => value pair in provided data
        foreach ($data as $key => $value) {

            // check if the property is mapped
            foreach ($map as $databaseKey => $variableName) {
                if ($key === $databaseKey) {
                    continue 2;
                }
            }

            // if the property is not mapped then save it for later
            $this->setDynamicProp($key, $value);
        }
    }

    /**
     * @see IDbObjectDynamic::getDynamicProp
     */
    public function getDynamicProp($key)
    {
        if (!array_key_exists($key, $this->dynamicProps)) {
            return null;
        }

        return $this->dynamicProps[$key];
    }

    /**
     * @see IDbObjectDynamic::setDynamicProp
     */
    public function setDynamicProp($key, $value)
    {
        $this->dynamicProps[$key] = $value;
    }

    /**
     * @see IDbObjectDynamic::getDynamicprops
     */
    public function getDynamicprops()
    {
        return $this->dynamicProps;
    }
}
