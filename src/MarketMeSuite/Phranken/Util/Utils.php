<?php
namespace MarketMeSuite\Phranken\Util;

/**
 * A group of misc methods that do not fit into any other utility class 
 * or are otherwise not numerous enough to warrant a specific class
 * 
 * @author Bill Nunney    <bill@marketmesuite.com>
 * @author Alan Hamlyn    <alan@marketmesuite.com>
 * @author Matthew Harris <matt@marketmesuite.com>
 */
class Utils
{

    public static function xmlEncode($source, $spaces = 0, $nodeName = "")
    {
        $indent="";
        $str="";
        for ($i = 0; $i<$spaces; $i++) {
            $indent.= "";
        }

        foreach ($source as $key => $value) {

            if (is_numeric($key)) {
                $str .= $indent."<".$nodeName.">";

                if (is_array($value)) {
                    $str.= self::xmlEncode($value, $spaces, $key);
                } else {
                    $str.= $indent.$indent."<![CDATA[".$value."]]>";
                }

                $str .= $indent."</".$nodeName.">";
            } else {
                $str .= $indent."<".$key.">";

                if (is_array($value)) {
                    $str.= self::xmlEncode($value, $spaces, $key);
                } else {
                    $str.= $indent.$indent."<![CDATA[".$value."]]>";
                }

                $str .= $indent."</".$key.">";
            }
        }

        return $str;
    }

    public static function utf8EncodeAll($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        }
        if (!is_array($dat)) {
            return $dat;
        }

        $ret = array();
        foreach ($dat as $i => $d) {
            $ret[$i] = self::utf8_encode_all($d);
        }

        return $ret;
    }

    /**
     * @assert ('bigtallbill@gmail.com') === true
     * @assert ('blah') === false
     * @assert (null) === false
     * @assert (undefined) === false
     * @assert (0) === false
     * @assert (true) === false
     * @assert (false) === false
     */
    public static function checkEmailAddress($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }


    /**
     * Returns the var_dump string of an object without outputing
     * immediately
     * @param mixed $var
     * @return string
     * @see \var_dump()
     */
    public static function grabDump($var)
    {
        ob_start();
        var_dump($var);
        return ob_get_clean();
    }
    
    /**
     * checks that the target $url is a value URL
     * @param  string $url The url to check
     * @return mixed When a valid url is matched the matching url is returned, otherwise returns false.
     */
    public static function checkForUrl($url)
    {
        $re1='.*?'; # Non-greedy match on filler
        $re2='((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s"]*))'; # HTTP URL 1

        if (preg_match_all("/".$re1.$re2."/is", urldecode($url), $matches)) {
            return $matches[1][0];
        }

        return false;
    }

    /**
     * returns a pretty version of an epoch
     * @param int $today Unix epoch in seconds
     * @return string the string representation of the unix epoch following this pattern 'D M d H:i:s'
     * @see \date
     */
    public static function epochToPrettyDate($today)
    {
        return date('D M d H:i:s', $today);
    }

    /**
     * Determines if the $left value is below the $right value
     * @param  int $left  $left value
     * @param  int $right $right value
     * @return boolean        if $left was below $right then return true, otherwise
     * if $left was equal to or more than $right then return false
     */
    public static function below($left, $right)
    {
        return ($left < $right);
    }

    /**
     * determines whether $value is between $start and $end
     * @param  int $start start value
     * @param  int $end   end value
     * @param  int $value any valid integer
     * @return boolean        returns true if $value is between $start and $end.
     * this includes whether $value is equal to $start or $end, otherwise return false.
     */
    public static function between($start, $end, $value)
    {
        if ($start > $end) {
            return ($value <= $start && $value >= $end);
        } else if ($start < $end) {
            return ($value >= $start && $value <= $end);
        } else {
            return false;
        }
    }

    /**
     * Checks if an object or class has the specified properties
     * @param  mixed  $obj   Either a valid class name or an instance of an object
     * @param  array   $props The property names to search for
     * @return boolean        True if all props were found, false if any were not found
     */
    public static function hasProperties($obj, array $props)
    {
        foreach ($props as $prop) {
            if (!property_exists($obj, $prop)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Pretty prints as json string
     * @param  string $json A valid JSON string
     * @return string       A pretty version of $json or if $json is 
     *                      not valid the string 'null' is returned
     */
    public static function jsonPrettyPrint($json)
    {
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = null;
        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; $i++) {
            $char = $json[$i];
            $new_line_level = null;
            $post = "";
            if ($ends_line_level !== null) {
                $new_line_level = $ends_line_level;
                $ends_line_level = null;
            }
            if ($char === '"' && $prev_char != '\\') {
                $in_quotes = !$in_quotes;
            } else if (!$in_quotes) {
                switch($char) {
                    case '}':
                    case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level = $level;
                        break;

                    case '{':
                    case '[':
                        $level++;
                        // no break
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ":
                    case "\t":
                    case "\n":
                    case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = null;
                        break;
                }
            }

            if ($new_line_level !== null) {
                $result .= "\n" . str_repeat("\t", $new_line_level);
            }

            $result .= $char.$post;
            $prev_char = $char;
        }

        return $result;
    }
}
