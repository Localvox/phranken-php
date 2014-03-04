<?php
namespace MarketMeSuite\Phranken\Util;

/**
 * Description: ArrayUtils class
 *  
 */
class ArrayUtils
{
    /**
    * Implode every array within a 2D array
    * @param array  $array
    * @param string $del
    * @return array The imploded array
    */
    public static function buildJoinedArray(array $array, $del)
    {
        $newArr = array();
        foreach ($array as $item) {
                $newArr[] = implode($del, $item);
        }
        return $newArr;
    }

    /**
     * Constructs a flat (indexed) array with values copied from the source array
     * @param array $array
     * @param string $field
     * @return array 
     */
    public static function constructFieldArray($array, $field)
    {
        $arr = array();
        if (empty($array)) {
            return false;
        }
        
        if (empty($field)) {
            return false;
        }

        foreach ($array as $row) {
                $arr[] = $row[$field];
        }
        
        return $arr;
    }

    /**
     * Extract all properties associated with the key '$key'
     * within each element in $array and create a new array from the result
     *
     * @param array  $array An array of associative arrays
     * @param string $key
     *
     * @return array
     */
    public static function flattenArray(array $array, $key)
    {
        if (count($array) == 0) {
            return $array;
        }
        
        foreach ($array as $index => $value) {
            if (!isset($array[$index])) {
                break;
            }
            $array[$index] = $value[$key];
        }
        
        return $array;
    }

    /**
     * walks through an array to find whether or not the target "directory" exists.
     * Much like checking to see if a directory exists in a file system, this 
     * function will look through the path provided and error when a path
     * segment is not found.
     * @param  array $array     The array to walk through
     * @param  string $path      The path to search though. By default segments are 
     * separated by a dot.
     * @param  string $separator Optional, change the separator which the path is split by
     * @return boolean true if the path exists, false on failure
     * @see array_path_create()
     * @see array_path_put()
     * @see array_path_get()
     */
    public static function arrayPathExists(&$array, $path, $separator = '.')
    {
        $a =& $array;
        $paths = explode($separator, $path);
        $i = 0;
        foreach ($paths as $p) {
            
            if (isset($a[$p])) {
                
                if ($i == count($paths) - 1) {
                    
                    return true;
                } elseif (is_array($a[$p])) {
                    
                    $a =& $a[$p];
                } else {
                    
                    return false;
                }
            } else {
                return false;
            }
            $i++;
        }

        return false;
    }
    
    /**
     * Creates the target path in an associative array with the specified value.
     * @param  array $array     The target array
     * @param  string $path     The path to search though. By default segments are 
     * separated by a dot.
     * @param  mixed $value     The value to inject at the end of the path, defaults to null
     * @param  string $separator Optional, change the separator which the path is split by
     * @return void
     * @see array_path_exists()
     * @see array_path_put()
     * @see array_path_get()
     */
    public static function arrayPathCreate(&$array, $path, $value = null, $separator = '.')
    {
        $segments = explode($separator, $path);
        $len = count($segments);
        $a = &$array;
        $i = 0;
        
        foreach ($segments as $seg) {
            
            if ($len-1 == $i) {
                
                $a[$seg] = $value;
            }
            
            if (!isset($a[$seg])) {
                $a[$seg] = array();
                $a = &$a[$seg];
            } else {
                $a = &$a[$seg];
            }
            
            ++$i;
        }
    }
    
    /**
     * Puts the specified value into the value of the target path.
     * If the path does not exist then this function will fail
     * @param  array $array     The target array
     * @param  string $path      The path to search though. By default segments are 
     * separated by a dot.
     * @param  mixed $value     The value to put at the end of the path, defaults to null
     * @param  string $separator Optional, change the separator which the path is split by
     * @return boolean true if the value was put successfully, false if the path was not found
     * in the target array.
     * @see array_path_exists()
     * @see array_path_create()
     * @see array_path_get()
     */
    public function arrayPathPut(&$array, $path, $value = null, $separator = '.')
    {
        
        $segments = explode($separator, $path);
        $len = count($segments);
        $a = &$array;
        $i = 0;
        
        foreach ($segments as $seg) {
            
            if (!isset($a[$seg])) {
                
                return false;
            }
            
            if ($len-1 == $i) {
                
                $a[$seg] = $value;
                return true;
            }
            
            $a = &$a[$seg];
            
            ++$i;
        }
        
        return true;
    }
    
    /**
     * Gets the value of the target path in an array.
     * @param  array $array     [description]
     * @param  string $path      [description]
     * @param  string $separator [description]
     * @return mixed On success returns the value found at the end of the path, otherwise returns false
     * @see array_path_exists()
     * @see array_path_create()
     * @see array_path_put()
     */
    public static function arrayPathGet(&$array, $path, $separator = '.')
    {
        $segments = explode($separator, $path);
        $len = count($segments);
        $a = &$array;
        $i = 0;
        
        foreach ($segments as $seg) {
            
            if (!isset($a[$seg])) {
                
                return false;
            }
            
            if ($len-1 == $i) {
                
                return $a[$seg];
            }
            
            $a = &$a[$seg];
            
            ++$i;
        }
        
        return true;
    }

    /**
     * Checks that the given array implements the given schema
     *
     * @param array $array
     * @param array $schema
     *
     * @return bool
     */
    public static function structureExists(array $array, array $schema)
    {
        foreach ($schema as $key => $value) {

            if (!array_key_exists($key, $array)) {
                return false;
            }

            if (is_array($array[$key]) && is_array($schema[$key])) {
                return static::structureExists($array[$key], $schema[$key]);
            }
        }

        return true;
    }

    /**
     * This is modeled off the PHP array_rand() function, this one randomly selects array elements from the array it is passed.
     * @param  array $array The source array to select random elements from
     * @param  int $count The number of random elements to return
     * @return array An array of randomly selected elements from the source array. The returned array can contain duplicates.
     * @see \array_rand()
     */
    public static function betterArrayRand(array $array, $count)
    {
        // Define the newArray that gets all randomly selected elements.
        $newArray = array();

        // Takes the number of results being demanded ($count) and selects that many random elements from the array.
        for ($i=0; $i < $count; ++$i) {

            // Defines a random key in the range of the $array elements
            $randKey = rand(0, count($array)-1);

            // Then pushes an elements from the $array with that key to the $newarray
            array_push($newArray, $array[$randKey]);
        }
        // Returns the $newArray with all the randomly selected elements in it.
        return $newArray;
    }

    /**
     * Creates an xml representation of the provided associative array
     * @param  array  $arr An associative array
     * @return string The XML representation of the input array. array keys are used for element identifiers
     * and values are used as element contents. Where the value is an array it is also converted as XML, recursively.
     */
    public static function arrayToXmlAssoc(array $arr)
    {
        $xml = '';

        foreach ($arr as $key => $value) {

            if (is_array($value)) {
                $value = self::ArrayToXmlAssoc($value);
            } else {
                $value = "<![CDATA[$value]]>";
            }

            $xml .= "<$key>$value</$key>";
        }

        return $xml;
    }
}
