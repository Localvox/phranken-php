<?php
namespace MarketMeSuite\Phranken\Crypt;

/**
* Provides methods for creating various unique ids
* 
* @author Alan Hamlyn
* @author Bill Nunney
*/
class UniqueIDGenerator
{
    /**
     * creates an md5 hashed id based of of a random number from 1000 to 9999 and uniqid()
     * @return string The generated id
     * @see uniqid()
     * @see md5()
     * @see rand()
     */
    public function generateID()
    {
        // md5 encrypt a random number between 1000 & 9999 combined with a UUID
        return md5(rand(1000, 9999) . uniqid());
    }
    
    /**
     * Generates a Universally Unique ID
     * You can be assured that ids generated will not collide even when generating billions of ids
     * @return string the generated id
     * @link http://en.wikipedia.org/wiki/Universally_unique_identifier
     */
    public static function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
    
    /**
     * Generates an id using GenerateID() and trims it to a particular length
     * @param integer $length how long to trim the id down to
     * @return string the trimmed id
     * @see GenerateID()
     */
    public function generateShortID($length = 5)
    {
        $longurl = GenerateID();
        // return short URL
        return substr($longurl, 0, $length);
    }
}
