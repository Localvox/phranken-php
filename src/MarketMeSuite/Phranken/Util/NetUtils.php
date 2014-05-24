<?php
namespace MarketMeSuite\Phranken\Util;
    
/**
 * Provides helper methods for working with network data, such as curl.
 *
 * @author Bill Nunney
 */
class NetUtils
{
    const CURL_POST = 'post';
    const CURL_GET = 'get';

    /**
     * Gets the response from a URL using POST or GET, accepting an array of url variables
     * if no URL variables are supplied then a 'get' will be implied (even if you set 'post')
     * @param string $url The URL to request
     * @param array $vars The variables to use (leave as blank array if none are needed)
     * @param string $method Default 'post' any other value will cause a 'get'
     * @param boolean $disableEncoding Disables URL encoding on parameter values
     * @return string The result of the curl operation
     * @see curl_exec()
     */
    public static function curlGetContents($url, array $vars, $method = 'post', $disableEncoding = false)
    {
        $c = curl_init();
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            curl_setopt($c, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
        }
        $strParams = "";
        if (count($vars) > 0) {
            array_walk(
                $vars,
                function (&$val, $key, $disableEncoding) {
                    $val = $key.'='.(($disableEncoding)?$val:urlencode($val));
                },
                $disableEncoding
            );

            $strParams = implode('&', $vars);
        }

        if ($method == 'post' && count($vars) > 0) {
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, $strParams);
        } else if (count($vars)>0) {
            $url .= '?' . $strParams;
        }

        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($c);
        curl_close($c);

        return $result;
    }

    /**
     * @param string $url
     *
     * @return bool True if url returns a status less than 400
     *              False otherwise or http code is zero
     */
    public static function urlIsReachable($url)
    {
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_NOBODY, true);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handler);
        $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
        $valid = $httpCode < 400 && $httpCode !== 0;
        curl_close($handler);

        return $valid;
    }
}
