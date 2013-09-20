<?php
namespace MarketMeSuite\Phranken\Util;

class StringUtils
{
    
    public static function commaStringToArray($str)
    {
        // if it was null then return it. this is to facilitate
        // Checker classes that expect null as a marker for unset
        if ($str === null) {
            return $str;
        }
        
        if (strpos($str, ',') === false) {
            return array($str);
        } else {
            return explode(',', $str);
        }
        
        return array();
    }
}
