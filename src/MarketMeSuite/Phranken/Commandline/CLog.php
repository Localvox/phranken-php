<?php
namespace MarketMeSuite\Phranken\Commandline;

/**
 * allows logging to standard output using a standard logging template
 *
 * @author Bill Nunney
 */
class CLog
{
    const C_INFO    = 'info';
    const C_WARNING = 'warning';
    const C_FATAL   = 'fatal';
    const C_DEBUG   = 'debug';
    
    private $name = '';

    /**
     * When true output is suppressed
     * @var boolean
     */
    private $_suppressOutput = false;

    /**
     * An array of possible colors that can be used for output
     * @var array
     */
    private $_colors = null;
    private $_colorMap = null;
    
    public function __construct($name = '')
    {
        $this->name = $name;

        $this->_colors = array(
            'LIGHT_RED'   => "[1;31m",
            'LIGHT_GREEN' => "[1;32m",
            'YELLOW'      => "[1;33m",
            'LIGHT_BLUE'  => "[1;34m",
            'MAGENTA'     => "[1;35m",
            'LIGHT_CYAN'  => "[1;36m",
            'WHITE'       => "[1;37m",
            'NORMAL'      => "[0m",
            'BLACK'       => "[0;30m",
            'RED'         => "[0;31m",
            'GREEN'       => "[0;32m",
            'BROWN'       => "[0;33m",
            'BLUE'        => "[0;34m",
            'CYAN'        => "[0;36m",
            'BOLD'        => "[1m",
            'UNDERSCORE'  => "[4m",
            'REVERSE'     => "[7m",
            'YELLOW'     => "[1;33m",
        );

        $this->_colorMap = array(
            self::C_INFO => 'CYAN',
            self::C_WARNING => 'YELLOW',
            self::C_FATAL => 'RED',
            self::C_DEBUG => 'BROWN'
        );
    }
    
    /**
     * Prints a message to the buffer with a trailing newline
     * @param string $message The message
     * @param string $type    The type of message.
     * @param mixed $detail  Optional extra data to include, var_dump's output is used to add this to the log
     */
    public function log($message, $type = self::C_INFO, $detail = null)
    {
        $today = date('D M d H:i:s');

        if ($detail !== null) {
            
            // get the var_dump result of the $datail var
            ob_start();
            var_dump($detail);
            $detail = PHP_EOL.ob_get_clean();
        }
        
        // if there is a process name then append it
        $name = '';
        strlen($this->name) and $name = " [". $this->applyColorMap($this->name, $this->_colorMap, $this->_colors) . "]";
        
        $type = $this->applyColorMap($type, $this->_colorMap, $this->_colors);

        $this->out("{$today}{$name} [$type] $message $detail".PHP_EOL);
    }
    
    /**
     * Prints the message to the buffer
     * @param  string $message
     */
    protected function out($message)
    {
        if ($this->_suppressOutput === true) {
            return;
        }

        print($message);
    }

    /**
     * Wraps the specified text with the terminal codes
     * that represent $color
     * @param  string $text   The text to colourize
     * @param  string $color  The key of a registered color see $_colors
     * @param  array  $colors An associative array where $key = color name and
     *                        $value = terminal code
     * @return string         $text wrapped in colour codes
     */
    public function wrapInColor($text, $color, array $colors)
    {
        if (!isset($colors[$color])) {
            return $text;
        }

        $out = $colors[$color];
        
        if ($out == "") {
            $out = "[0m";
        }

        return chr(27)."$out$text".chr(27)."[0m";
    }

    /**
     * applies a colour map to $text
     * @param  string $text   The text to apply the map too
     * @param  array  $map    A colour map see $_colorMap
     * @param  array  $colors A colour list see $_colors
     * @return string         The colorized text
     */
    public function applyColorMap($text, array $map, array $colors)
    {
        if (isset($map[$text])) {
            return $this->wrapInColor($text, $map[$text], $colors);
        }
        return $text;
    }


    /**
     * @return array An associative array where $key = color name and
     *               $value = terminal code
     */
    public function getColors()
    {
        return $this->_colors;
    }

    /**
     * @param array $colors An associative array where $key = color name and
     *                         $value = terminal code
     */
    public function setColors(array $colors)
    {
        $this->_colors = $colors;
    }

    /**
     * @return array An associative array where $key = warning level and
     *               $value = color name
     */
    public function getColorMap()
    {
        return $this->_colorMap;
    }

    /**
     * @param array $map An associative array where $key = warning level and
     *                   $value = color name
     */
    public function setColorMap(array $map)
    {
        $this->_colorMap = $map;
    }

    /**
     * supresses all calls to Log
     * @param  boolean $val Set to true to disable all output, set to false to allow output
     */
    public function suppressOutput($val = false)
    {
        $this->_suppressOutput = $val;
    }
}
