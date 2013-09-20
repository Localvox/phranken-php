<?php
namespace MarketMeSuite\Phranken\Commandline;

/**
 * A very simple logging class that deals with pure logging.
 *
 * @author Bill Nunney <bill@marketmesuite.com>
 */
class SimpleLog
{
    /**
     * Accepts multiple arguments, each argument is printed with a newline
     * at the end. Arrays and objects are pretty printed and any unkown 
     * type is coerced to a string and then printed.
     *
     * Booleans are converted to either 'true' or 'false' strings.
     */
    public function log()
    {
        // get supplied arguments
        $args = func_get_args();

        foreach ($args as $arg) {
            if (is_bool($arg)) {

                // print string NULL because coercing null to string results
                // in a blank string, which is not true to what is being logged
                print (($arg) ? 'true' : 'false') . PHP_EOL;
            } else if (is_null($arg)) {

                // print string NULL because coercing null to string results
                // in a blank string, which is not true to what is being logged
                print 'NULL' . PHP_EOL;
            } else if (is_string($arg)) {

                // print with a regular newline
                print $arg . PHP_EOL;
            } else if (is_array($arg)) {

                // print with nice array formatting
                print_r($arg);
            } else if (is_object($arg)) {

                // print with nice array formatting
                print_r($arg);
            } else {

                // coerce to string and print with newline
                print (string) $arg . PHP_EOL;
            }
        }
    }
}
