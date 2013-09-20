<?php
namespace MarketMeSuite\Phranken\Database\Mongo\Util;

class MongoUtils
{
    /**
     * Converts a one dimentional array of strings to a valid mongodb fields array
     * @param array $array The source array of strings
     * @param boolean $exclude Set to true to turn this into an exclusions array
     * @return array 
     */
    public static function stringArrayToFieldsArray($array, $exclude = false)
    {
        if (!is_array($array)) {
            return false;
        }

        $fieldsArray = array();
        $exval = (($exclude)?0:1);

        foreach ($array as $key => $val) {
           
            if (!is_string($val)) {
                continue;
            }

            $fieldsArray[$val] = $exval;
        }
        
        return $fieldsArray;
    }
    
    /**
     * Creates an $in array for mongo
     * @param array $array
     * @param boolean $not
     * @param string $field
     * @return array 
     */
    public static function constructInArray($array, $not = false, $field = null)
    {
        if (!is_array($array)) {
            return false;
        }

        if ($field != null) {

            $tmpArr = array();
            foreach ($array as $item) {
                $tmpArr[] = $item[$field];
            }
            
            $array = $tmpArr;
        }
        
        return array((($not)?'$nin':'$in')=>$array);
    }

    public static function constructAndArray()
    {
        $and = array('$and'=>array());
        foreach (func_get_args() as $key => $val) {
            if (!is_array($val)) {
                continue;
            }
            
            $and['$and'][] = $val;
            
        }
        return $and;
    }

    /**
     * Adds an '$and' parameter to a mongo request array.
     * 
     * This is done by reference so this function returns nothing.
     * 
     * @param array $request
     * @param array $and 
     * @return void
     */
    public static function addAndParams(&$request, $and)
    {
        if (!isset($request['$and'])) {
            $request['$and'] = array();
        }
        
        $request['$and'][] = $and;
    }

    /**
     * Gets a full array of documents from a mongo cursor
     * @param MongoCursor  $cursor      The mongoCursor reference given from a mongo query such as "find"
     * @param boolean      $alwaysValid If set to true this function will always return an array even if the
     *                                  cursor is null (array will be empty).
     *                                  
     *                                  Use ONLY in cases where the result is expected to be empty or not
     */
    public static function getArrayFromCursor($cursor, $alwaysValid = false)
    {
        // return an empty array if the cursor is null and the return should always be valid
        if ($alwaysValid === true && $cursor === null) {
            return array();
        }
        
        $arr = array();
        foreach ($cursor as $doc) {
            $arr[]=$doc;
        }

        return $arr;
    }

    public static function arrayMembersToInt(&$array)
    {
        foreach ($array as $key => $val) {
            $array[$key] = (int) $val;
        }
    }
}
