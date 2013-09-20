<?php
namespace MarketMeSuite\Phranken\Database\MySql\Util;

use MarketMeSuite\Phranken\Database\MySql\Exception\MysqlUtilsException;

/**
 * Contains methods for generating segments of complex queries
 */
class MysqlUtils
{
    /**
     * Creates an IN() string from the supplied mysql result data and the target field name
     *
     * @param $data the raw mysql result (not the fetch array)
     * @param $field the table field to use in the IN()
     */
    public static function constructInString($data, $field)
    {
        if (!isset($data)) {
            return false;
        }
        
        if (!isset($field)) {
            return false;
        }
        
        if (mysql_num_rows($data)<=0) {
            return "IN()";
        }
        
        $in = "IN(";
        while ($row = mysql_fetch_array($data)) {
            $in .= "'".$row[$field] . "',";
        }

        $in = substr($in, 0, -1);
        $in.=")";
        
        return $in;
    }
    
    /**
     * Builds a MySQL IN() string, from and array, for use in a mysql query string 
     * @param type $array 
     * @param type $field
     * @return string 
     */
    public static function constructInStringFromArray($array, $field = "")
    {
        if (!isset($array)) {
            return false;
        }
        
        if (!isset($field)) {
            return false;
        }
        
        if (count($array)<=0) {
            return "IN()";
        }
        
        $in = "IN(";
        foreach ($array as $row) {
            if ($field == "") {
                $in .= "'".$row. "',";
            } else {
                $in .= "'".$row[$field] . "',";
            }
        }

        $in = substr($in, 0, -1);
        $in.=")";
        
        return $in;
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
        if (!isset($array)) {
            return false;
        }
        
        if (!isset($field)) {
            return false;
        }
        
        foreach ($array as $row) {
            $arr[] = $row[$field];
        }
        
        return $arr;
    }
    
    /**
     * Gets the full array from a mysql result
     * @param type $resource The mysql resource returned from the mysql query
     * @param type $type Either 'A' for assoc, 'N' for a regular indexed array or, finally,
     * 'B' for Both
     * @return array 
     */
    public static function getFullArray($resource, $type = "A")
    {
        $arr = array();
        
        switch($type)
        {
            case "A":
            case "a":
                while ($row = mysql_fetch_assoc($resource)) {
                    array_push($arr, $row);
                }
                break;
            case "N":
            case "n":
                while ($row = mysql_fetch_row($resource)) {
                    array_push($arr, $row);
                }
                break;
            case "B":
            case "b":
                while ($row = mysql_fetch_array($resource)) {
                    array_push($arr, $row);
                }
                break;
            default:
                while ($row = mysql_fetch_array($resource)) {
                    array_push($arr, $row);
                }
                break;
        }
        
        return $arr;
    }

    /**
     * Creates a HTML table of the mysql result
     * @param type $resource
     * @return string The HTML code for a regular HTML table
     */
    public static function getHTMLTable($resource)
    {
        if (!is_array($resource)) {
            $array = self::GetFullArray($resource);
        } else {
            $array = $resource;
        }
        
        $html = '<table border="1">';
        
        
        if (count($array) > 0) {
            $html .= "<tr>";
            foreach ($array[0] as $key => $val) {
                $html .= "<td>$key</td>";
            }
            $html .= "</tr>";
        }
        
        foreach ($array as $row) {
            $html .= "<tr>";

            foreach ($row as $key => $val) {
                $html .= "<td>$val</td>";
            }

            $html .= "</tr>";
        }
        
        $html .= "</table>";
        return $html;
    }
    
    /**
     * Displays a mysql result in an easy to read output
     * @param type $resource The mysql resource to display. Must be from a query which gets results
     * otherwise nothing is shown
     * @return string The pretty representation of the mysql result
     */
    public static function displayMysqlResult($resource)
    {
        if (!is_array($resource)) {
            $arr = self::GetFullArray($resource);
        } else {
            $arr = $resource;
        }
        
        // get longest field/value
        $longestField;
        $longestVal;
        $rowLength = count($arr[0]);
        
        foreach ($arr as $key => $val) {
            $length = max(
                array_keys($val)
            );
            
            if ($longestField < $length) {
                $longestField = $length;
            }

            $length = max(array_values($val));

            if ($longestVal < $length) {
                $longestVal = $length;
            }
        }
        
        $longestVal= strlen($longestVal)+2;
        $longestField= strlen($longestField)+2;
        $longestData = max(array($longestVal,$longestField));
        
        $rowWidth = ($rowLength+1)*($longestData+2);
        
        //echo $longestData;
        
        //var_dump($arr);
        
        $resStr = str_pad('', $longestData, " ")."| ";
        foreach ($arr[0] as $key => $val) {
            $padding = str_pad($key, $longestData, " ");
            $resStr .= "$padding| ";
        }
        $resStr .= "\r\n";
        $resStr .= str_repeat("~", $rowWidth)."\r\n";
        
        
        foreach ($arr as $key => $row) {
            $padding = str_pad($key, $longestData, " ");
            $resStr .= "$padding| ";
            foreach ($row as $key => $val) {

                $padding = str_pad($val, $longestData, " ");
                $resStr .= "$padding| ";
            }
            $resStr .= "\r\n";
        }
        
        return $resStr;
    }
    
    /**
     * Turns an array of strings into a fields string to use in SELECT statements
     * @param array $fields an array of strings that represent fields
     * @return string If there were any errors * will be returned, otherwise
     * a fields list will be returned. e.g `field1`,`field2`
     */
    public static function buildFieldString($fields)
    {
        if (!is_array($fields)) {
            return "*";
        }
        
        $queryFields = "*";
        
        if (count($fields) == 1) {
            $queryFields = "`".$fields[0]."`";
        } else if (count($fields) > 1) {
            $queryFields = "`".implode("`,`", $fields)."`";
        }
        
        return  $queryFields;
    }
    
    /**
     * 
     * @param type $query
     * @param type $field
     * @return type 
     */
    public static function buildLikeQueryString($query, $field)
    {
        $arr = explode(',', $query);
        $likeArr = array();
        
        if (count($arr) == 0) {
            return '';
        }
        
        foreach ($arr as $key => $val) {
            $likeArr[] = "`".$field."` LIKE '%$val%'";
        }

        $likeStr = join(' OR ', $likeArr);
        if (count($arr) > 1) {
            $likeStr="($likeStr)";
        }

        return $likeStr;
    }
    
    /**
     * Implode every array within a 2D array
     * @param type $array
     * @param type $del
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
     * Querys mysql and auto converts results to an assiciative array
     * @param string $query The mysql query to run
     * @param resource $con   The mysql connection resource
     * @param boolean $singleRecord Whether a single record shouldbe the only array returned. If this is true only a single associative array will be returned
     * @return array An array of arrays representing each row as an associative array
     */
    public static function queryAssoc($query, $con, $singleRecord = false)
    {
        $mysqlQueryR = mysql_query($query, $con);
        if ($mysqlQueryR === false) {
            throw new MysqlUtilsException(mysql_error($con));
        }
        
        $mysqlQuery = self::GetFullArray($mysqlQueryR);
        
        if ($singleRecord === true && count($mysqlQuery) > 0) {
            $mysqlQuery=$mysqlQuery[0];
        }
        
        return $mysqlQuery;
    }
}
