<?php

/**
* Minifier
*
* @uses     
*
* @category Category
* @package  Package
* @author   JUST BV
* @link     http://wearejust.com/
*/
class Minifier
{
    /**
     * Create a new image element from an email address.
     * @param  array  $files array of the files to minify and combine
     * @return string The source for combined url
     */
    public static function make($files)
    {
        require_once 'libraries/min/utils.php';

        // cast to array
        if(!is_array($files))
            $files = array($files);

        $uri = Minify_getUri($files);
        $uri = str_replace('/min/', '/min/?', $uri);

        return $uri;
    }
}
