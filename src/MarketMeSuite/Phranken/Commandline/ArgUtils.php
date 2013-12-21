<?php
namespace MarketMeSuite\Phranken\Commandline;

class ArgUtils
{
    /**
     * gets a target key => value from an arguments array, specifically, the $argv
     * variable created for CLI scripts.
     * @example
     * given the command:
     * <pre>php myscript.php -dir "foo/"</pre>
     * then the call to getArgPair:
     * <pre>getArgPair($argv, '-dir');</pre>
     * will return an array like this:
     * <pre>array(1) {
     *  ["-dir"]=>
     *  string(4) "foo/"
     * }</pre>
     * 
     * 
     * @param  array   $args              An arguments array, like that which php creates for CLI scripts
     * @param  string  $key               The key to find the value of
     * @param  mixed   $default           The default value to assign to an argument that does not exist
     * @param  boolean $defaultMissingArg set to true to allow the default value to be applied to the argument when
     *                                    the argument is missing
     * @return array                      A key => value associative array of the got pair
     */
    public static function getArgPair(array $args, $key = '', $default = null, $defaultMissingArg = false)
    {
        $argIndex = static::argIndex($args, $key);

        if ($argIndex !== -1) {

            if (isset($args[$argIndex+1]) && static::isOption($args[$argIndex+1]) === false) {
                $value = $args[$argIndex+1];
            } else {
                $value = $default;
            }

            return array($key => $value);

        } elseif (static::argExists($args, $key) === false) {

            // return a blank array when the $key does not exist, unless otherwise
            // overriden by $defaultMissingArg
            if ($defaultMissingArg === false) {
                return array();
            } else {
                $value = $default;
            }
        }

        // else return default value
        return array($key => $default);
    }

    /**
     * Checks that the given arguemnt is an "option",
     * that is; any arguemnt that begins with '-' (hyphen) is
     * considered to be an option
     * @param  string  $arg The arguemnt to check
     * @return boolean      True if the given arguemnt is an option, false otherwise
     */
    public static function isOption($arg)
    {
        if (strpos($arg, '-') === 0) {
            return true;
        }

        return false;
    }

    /**
     * Checks that the given argument exists in the given arguments array
     * @param  array  $args The argument array to search
     * @param  string $arg  The argument to search for
     * @return boolean      the return value of in_array
     * @see PHP::in_array
     */
    public static function argExists(array $args, $arg)
    {
        return in_array($arg, $args);
    }

    /**
     * Gets the index of the given argument in the given arguments array
     * @param  array  $args An array of arguments
     * @param  string $arg  The argument to fidn the index of
     * @return integer      A non-negative integer when $arg exists in $args,
     *                      otherwise if $arg is not found -1 is returned
     */
    public static function argIndex(array $args, $arg)
    {
        if (static::argExists($args, $arg) === false) {
            return -1;
        }

        return array_search($arg, $args);
    }
}
