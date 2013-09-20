<?php
namespace MarketMeSuite\Phranken\Commandline;

/**
 * provides methods for prompting a user for input in the shell
 * 
 * @author Bill Nunney
 */
class CommandPrompt
{
    /**
     * The prefix to append on to prompts
     * @var string
     */
    protected $_promptPrefix = ': ';

    /**
     * Stores values consider to be "truthy"
     * @var array
     */
    protected $_truthyValues = null;

    public function __construct()
    {
        // define base "truthy" values
        $this->_truthyValues = array(
            'true',
            'yes',
            'y'
        );
    }

    /**
     * prompts a user to make a boolean choice
     * @param  string $msg The message to show
     * @return boolean     True if the user answered with a "truthy" value, false otherwise
     */
    public function prompt($msg)
    {
        return $this->validateBooleanResponse(readline($msg . $this->renderHint($this->_truthyValues) . $this->_promptPrefix));
    }

    /**
     * displays a message and waits for user input
     * @param  string $msg  A short message to display
     * @param  string $hint An array of strings to display as possible values
     * @return string       The user-provided response
     */
    public function read($msg, array $hint = null)
    {
        // if hint is null then set it to an empty array
        if (!is_array($hint)) {
            $hint = array();
        }

        return readline($msg . $this->renderHint($hint) . $this->_promptPrefix);
    }

    /**
     * checks that the given input exists within $enumerable
     * @param  string $input      A string value to check for
     * @param  array  $enumerable An array of string values which $input must exist in
     * @return string             true when $input exists within $enumerable.
     *                            otherwise an error string is returned which details
     *                            the valid values
     */
    public function isEnumerable($input, array $enumerable)
    {
        if (in_array($input, $enumerable) === false) {
            return '"' . $input . '" was not enumerable in: ' . $this->renderHint($enumerable);
        }

        return true;
    }

    /**
     * Renders the truthy values hint
     * @param  array  $values An array of string values
     * @return string         A comma separated string values
     *                        encapsulated in parenthesis
     */
    protected function renderHint(array $values)
    {
        if (count($values) === 0) {
            return '';
        }

        return '(' . implode(', ', $values) . ')';
    }

    /**
     * Validates whethere a response in considered thruthy
     * @param  string $value The response to consider
     * @return boolean       True if $value is considered truthy, false otherwise
     */
    protected function validateBooleanResponse($value)
    {

        $value = trim($value);

        switch ($value) {
            case 'true':
            case 'yes':
            case 'y':
                return true;
                break;
            
            default:
                return false;
                break;
        }
    }
}
