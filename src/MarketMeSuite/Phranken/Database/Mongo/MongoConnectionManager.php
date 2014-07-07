<?php
namespace MarketMeSuite\Phranken\Database\Mongo;

use MarketMeSuite\Phranken\Database\Mongo\Exception\MongoConnectionManagerException;
use MongoConnectionException;
use \stdClass;
use \Mongo;

/**
 * Manages a connection to a mongo instance. Has the ability to connect to multiple
 * databases.
 */
class MongoConnectionManager extends stdClass
{
    /**
     * @var \Mongo
     */
    protected $con;
    private $config = array();

    const TIMEOUT = 1000;

    public function __construct($config, $db = 'all', $slave = false)
    {

        $this->configure($config, $db, $slave);
    }

    public function addDb($db, $slave = false, $config = null)
    {

        if ($config === null) {
            $config = $this->config;
        }

        $this->configure($config, $db, $slave);
    }

    /**
     * Configures and connects a single or multiple mongo databases
     *
     * @param mixed  $config An array containing information for connection to a
     *                       database, or a json string that represents the same
     *                       structure
     * @param string $db
     * @param bool   $slave
     *
     * @throws Exception\MongoConnectionManagerException
     */
    public function configure($config, $db = 'all', $slave = false)
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

        // set slave okay
        if ($slave === true) {
            @$this->con->setSlaveOkay(true);
        }

        // load all databases
        if ($db === 'all') {

            // get a list of all databases
            $dbs = $this->con->listDbs();

            foreach ($dbs['databases'] as $theDb) {
                $this->loadDb($theDb['name']);
            }
        } elseif (is_array($db)) {

            foreach ($db as $theDb) {

                if (is_string($theDb)) {
                    $this->loadDb($theDb);
                }
            }

        } else {

            $this->loadDb($db);
        }
    }
    
    /**
     * Connects to the specified connection
     * @param string $name The name of the db connection that was specified in the config array
     *
     * @deprecated use __get magic method access instead, its way easier
     */
    public function loadDb($name)
    {
        // magic method __get is used instead
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
        return $this->con->{$name};
    }

    /**
     * Recursive function to try to connect to backup mongo connections
     *
     * @param            $options
     * @param int        $fallBack
     *
     * @throws Exception\MongoConnectionManagerException
     * @return \Mongo
     */
    protected function tryConnect($options, $fallBack = 0)
    {
        // when no more fallbacks are found and no connections can be made, then throw an error
        if (!isset($options[$fallBack])) {
            throw new MongoConnectionManagerException("could not connect to mongo server after '$fallBack' fallbacks");
        }

        try {

            // build a mongo connection string
            $connectionString = "mongodb://{$options[$fallBack]['user']}:";
            $connectionString .= "{$options[$fallBack]['pass']}@";
            $connectionString .= "{$options[$fallBack]['host']}";

            // Connect to Mongo Server
            @$con = new Mongo($connectionString, array('timeout'=>self::TIMEOUT));

            //$con = $con->selectDB($options[$fallBack]['db']);

        } catch (MongoConnectionException $e) {
            $con = self::TryConnect($options, ++$fallBack);
        }

        return $con;
    }

    /**
     * @return Mongo
     */
    public function getClient()
    {
        return $this->con;
    }
}
