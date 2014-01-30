<?php
namespace MarketMeSuite\Phranken\Database\Mongo;

/**
 * provides an interface for a class to implement usage of an instance of the
 * MongoConnectionManager class, as well as database connectivity methods
 *
 * @see    MongoConnectionManager
 * @author Bill Nunney
 */
interface IMcmUser
{
    /**
     * Gets the MongoConnectionManager instance
     * @return MongoConnectionManager The instance of MongoConnectionManager
     */
    public function getMcm();

    /**
     * Sets the MongoConnectionManager instance
     *
     * @param MongoConnectionManager $mcm An instance of MongoConnectionManager
     */
    public function setMcm($mcm);

    /**
     * Checks to see whether a set of databases are connected to
     *
     * @param  array $databases An array of strings representing database names
     *
     * @return boolean|array true if all are valid, if some or all databases aren't loaded then those
     * database names will be returned in an array
     */
    public function hasDatabases(array $databases);

    /**
     * Gets the required databases that the $mcm instance should be connected to
     * @return array An array of strings representing mongo database names
     */
    public function getRequiredDatabases();
}
