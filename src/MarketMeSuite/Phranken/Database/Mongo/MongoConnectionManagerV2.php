<?php
/**
 * phranken-php
 * MongoConnectionManagerV2.php
 *
 * @author   Bill Nunney <bill@marketmesuite.com>
 * @date     28/03/2014 08:48
 * @license  http://marketmesuite.com/license.txt MMS License
 */
namespace MarketMeSuite\Phranken\Database\Mongo;


use MarketMeSuite\Phranken\Database\Mongo\Exception\MongoConnectionManagerException;
use MongoClient;
use MongoConnectionException;

/**
 * Class MongoConnectionManagerV2
 * @package MarketMeSuite\Phranken\Database\Mongo
 */
class MongoConnectionManagerV2 extends MongoConnectionManager
{

    /**
     * @var MongoClient
     */
    protected $con;

    const TIMEOUT = 1000;

    public function __construct($config)
    {
        $this->configure($config);
    }

    /**
     * Returns the requested database
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        // fix rare case where __get can be called before a connection is created
        // or the connection is closed while this object still exists
        if ($this->con && count($this->con->getConnections()) === 0) {
            $this->con = $this->tryConnect($this->config);
        }

        return $this->con->{$name};
    }

    /**
     * Recursive function to try to connect to backup mongo connections
     *
     * @param                                             $options
     * @param int|MongoClient                             $fallBack
     *
     * @return \Mongo
     * @throws Exception\MongoConnectionManagerException
     */
    protected function tryConnect($options, $fallBack = 0)
    {
        // when no more fall backs are found and no connections can be made, then throw an error
        if (!isset($options[$fallBack])) {
            throw new MongoConnectionManagerException("could not connect to mongo server after '$fallBack' fallbacks");
        }

        try {

            // build a mongo connection string
            $connectionString = "mongodb://{$options[$fallBack]['user']}:";
            $connectionString .= "{$options[$fallBack]['pass']}@";
            $connectionString .= "{$options[$fallBack]['host']}";

            // Connect to Mongo Server
            @$con = new MongoClient($connectionString, array('connectTimeoutMS' => self::TIMEOUT));

        } catch (MongoConnectionException $e) {
            $con = self::TryConnect($options, ++$fallBack);
        }

        return $con;
    }

    /**
     * @return MongoClient
     */
    public function getClient()
    {
        return $this->con;
    }
}
