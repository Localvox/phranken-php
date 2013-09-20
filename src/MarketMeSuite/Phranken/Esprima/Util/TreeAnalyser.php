<?php
namespace MarketMeSuite\Phranken\Esprima\Util;

use \Exception;

/**
 * Provides methods to find objects within an esprima exported syntax tree
 */
class TreeAnalyser
{

    /**
     * the json decoded array of the syntax tree
     * @var array
     */
    public $tree = null;

    /**
     * the original source json of the syntax tree
     * @var string
     */
    public $_treeSource = null;

    public $_context = null;
    public $_finds = null;

    /**
     * finds a key => value pair in the syntax tree and returns the context (parent) of the found key > value
     * @param  string $sKey    The key to search for $value
     * @param  string $sValue  The value that $key shoudl be
     * @param  array $context  The current context. Use this to specify a starting point
     * @return array           The final context of the $key => $value
     */
    public function find($sKey, $sValue, $context = null)
    {
        if ($context === null) {
            $this->_finds = array();
            $context = $this->_context;
        }

        foreach ($context as $key => $value) {

            if ($key === $sKey && $value === $sValue) {
                $this->_finds[] = $context;
            }

            // recursively search deeper
            if (gettype($value) === 'array') {
                $this->find($sKey, $sValue, $value);
            }
        }

        return $this->_finds;
    }

    /**
     * Set the syntax tree
     * This will also decode the tree
     * @param string $tree JSON string exported from esprima
     */
    public function setTree($tree)
    {
        $this->tree = $this->decodeTree($tree);
        $this->_context = $this->tree;
    }

    /**
     * Decodes a json string exported from esprima
     * @param  string $treeSource The raw json string from esprima
     * @return array              The decoded json as an associative array
     */
    protected function decodeTree($treeSource)
    {
        $decodedTree = json_decode($treeSource, true);

        if ($decodedTree === null) {
            $this->raise('json could not be decoded');
        }

        $this->_treeSource = $treeSource;

        return $decodedTree;
    }

    /**
     * Helper method to raise exceptions
     * @param  string $msg  Message
     * @param  int    $code Code
     */
    public function raise($msg, $code = null)
    {
        throw new Exception($msg, $code);
    }
}
