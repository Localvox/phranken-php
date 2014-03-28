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
    private $config = array();

    const TIMEOUT = 1000;

    public function __construct($config, $database = 'all', $slave = false)
    {

        $this->configure($config, $database, $slave);
    }

    public function addDb($databaseName, $slave = false, $config = null)
    {

        if ($config === null) {
            $config = $this->config;
        }

        $this->configure($config, $databaseName, $slave);
    }

    /**
     * Configures and connects a single or multiple mongo databases
     *
     * @param string|array $config An array containing information for connection to a
     *                             database, or a json string that represents the same
     *                             structure
     * @param string       $databaseName
     * @param bool         $slave
     *
     * @throws Exception\MongoConnectionManagerException
     */
    public function configure($config, $databaseName = 'all', $slave = false)
    {
        if (is_string($config)) {
            $config = json_decode($config, true);
            if ($config === null) {
                throw new MongoConnectionManagerException('invalid config, string given was not valid json');
            }
        } elseif (!is_array($config)) {
            throw new MongoConnectionManagerException('config given was neither a json string or an array');
        }

        // validate config array
        // @todo make a better validator, probably using some sort of recursive function
        if (!isset($config[0]['user'])) {
            throw new MongoConnectionManagerException('invalid config: \'user\' was not set');
        }

        if (!isset($config[0]['pass'])) {
            throw new MongoConnectionManagerException('invalid config: \'pass\' was not set');
        }

        if (!isset($config[0]['host'])) {
            throw new MongoConnectionManagerException('invalid config: \'host\' was not set');
        }

        // store the validated config array
        $this->config = $config;

        // try connect to the mongo servers
        $this->con = $this->tryConnect($config);

        // load all databases
        if ($databaseName === 'all') {

            // get a list of all databases
            $dbs = $this->con->listDbs();

            foreach ($dbs['databases'] as $theDb) {
                $this->loadDb($theDb['name']);
            }
        } elseif (is_array($databaseName)) {

            foreach ($databaseName as $theDb) {

                if (is_string($theDb)) {
                    $this->loadDb($theDb);
                }
            }

        } else {

            $this->loadDb($databaseName);
        }
    }

    /**
     * Connects to the specified connection
     *
     * @param string $name The name of the db connection that was specified in the config array
     */
    public function loadDb($name)
    {
        $this->{$name} = $this->con->{$name};
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
}
