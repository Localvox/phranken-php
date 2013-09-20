<?php
namespace MarketMeSuite\Phranken\Util;

/**
 * Contains methods for testing file related components
 *
 * @author Bill Nunney
 */
class FileUtils
{
    /**
     * Finds whether the given path is relative
     * @param  string  $path A valid *nix path
     * @return boolean       true if the path is relative, false otherwise
     */
    public static function isRelative($path)
    {
        $match = preg_match("/^\//", $path);
        if ($match === 0 || $match === false) {
            return true;
        }

        return false;
    }

    /**
     * Finds whether the given path is absolute
     * @param  string  $path A valid *nix path
     * @return boolean       true if the path is absolute, false otherwise
     */
    public static function isAbsolute($path)
    {
        $match = preg_match("/^\//", $path);
        if ($match === 1) {
            return true;
        }

        return false;
    }

    /**
     * Loads file(s) which are json encoded into associative arrays
     * @param mixed $file A string representing a path to a file, or an array containing multiple paths
     * @return mixed false if the target file was really a directory, an array if a single file was supplied
     * and a multidimentional array, where each element is an array of loaded json
     */
    public static function loadFileToJson($file)
    {
        if (is_array($file)) {
            $results = array();
            foreach ($file as $afile) {
                $results[] = self::LoadFileToJSON($afile);
                
            }
            return $results;
        }
        
        // we cant load directorys so error nicely
        if (is_dir($file)) {
            return false;
        }
        
        return json_decode(file_get_contents($file), true);
    }
    
    /**
     * Saves an array as a json document to a file
     * @param string $fname The filename to save as
     * @param array  $array The array to save
     */
    public static function saveJsonToFile($fname, array $array)
    {
        return file_put_contents($fname, json_encode($array)) > 0;
    }
}
