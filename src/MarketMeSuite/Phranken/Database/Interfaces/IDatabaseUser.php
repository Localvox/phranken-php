<?php
namespace MarketMeSuite\Phranken\Database\Interfaces;

/**
 * Defines how an object should manage a single database connection
 */
interface IDatabaseUser
{
    /**
     * Gets the set database name
     * @return string The database name
     */
    public function getDatabaseName();

    /**
     * Sets the current database name
     * @param string $db The name of the database
     */
    public function setDatabaseName($name);

    /**
     * Gets the set connection object
     * @return mixed The actual database connection object
     */
    public function getConnection();

    /**
     * Sets the connection object
     * @param mixed $con The database connection object
     */
    public function setConnection($con);
}
