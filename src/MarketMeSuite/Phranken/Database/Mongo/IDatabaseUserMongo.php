<?php
namespace MarketMeSuite\Phranken\Database\Mongo;

use MarketMeSuite\Phranken\Database\Interfaces\IDatabaseUser;

/**
 * Defines how an object should manage database connection information
 */
interface IDatabaseUserMongo extends IDatabaseUser
{
    public function getCollectionName();

    /**
     * Gets the set collection
     *
     * @param string $name
     *
     * @return string A collection name
     */
    public function setCollectionName($name);

    /**
     * Uses set information to resolve the actual collection instance
     * @return \MongoCollection The configured MongoCollection
     */
    public function getCollection();
}
