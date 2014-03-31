<?php
namespace MarketMeSuite\Phranken\Database\Interfaces;

/**
 * defines the basic methods that a database object should expose
 */
interface IDbObjectDynamic extends IDbObject
{
    /**
     * gets a parsed dynamic property by key
     * @param string $key The key of the value to get
     *
     * @return mixed|null When $key does not exist null is returned
     */
    public function getDynamicProp($key);

    /**
     * sets a parsed dynamic property
     * @param string $key   The key of the value to set
     * @param string $value The value to set
     */
    public function setDynamicProp($key, $value);

    /**
     * gets all dynamic properties
     * @return array An associative array of $key => $value pairs
     */
    public function getDynamicProps();

    /**
     * Stores all key => values in $data that are not included in the
     * configured $map
     * @param array $data An associative array of key => value pairs
     */
    public function parseDynamicProperties(array $data);
}
