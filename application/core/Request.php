<?php

/**
 * This is under development. Expect changes!
 * Class Request
 * Abstracts the access to $_GET, $_POST and $_COOKIE, preventing direct access to these super-globals.
 * This makes PHP code quality analyzer tools very happy.
 * @see http://php.net/manual/en/reserved.variables.request.php
 */
class Request
{
    /** @var array parameters passed as part of the URL */
    private static $inUrl = array();

    /**
     * Gets/returns the value of a specific key of the POST super-global.
     * When using just Request::post('x') it will return the raw and untouched $_POST['x'], when using it like
     * Request::post('x', true) then it will return a trimmed and stripped $_POST['x'] !
     *
     * @param mixed $key key
     * @param bool $clean marker for optional cleaning of the var
     * @return mixed the key's value or nothing
     */
    public static function post($key, $clean = false)
    {
        if (isset($_POST[$key])) {
            // we use the Ternary Operator here which saves the if/else block
            // @see http://davidwalsh.name/php-shorthand-if-else-ternary-operators
            return ($clean) ? trim(strip_tags($_POST[$key])) : $_POST[$key];
        }
    }

    /**
     * Returns the state of a checkbox.
     *
     * @param mixed $key key
     * @return mixed state of the checkbox
     */
    public static function postCheckbox($key)
    {
        return isset($_POST[$key]) ? 1 : NULL;
    }

    /**
     * gets/returns the value of a specific key of the GET super-global
     * @param mixed $key key
     * @return mixed the key's value or nothing
     */
    public static function get($key)
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
    }

    /**
     * gets/returns the value of a specific key of the COOKIE super-global
     * @param mixed $key key
     * @return mixed the key's value or nothing
     */
    public static function cookie($key)
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
    }

    /**
     * creates in URL parameters from a part of a URL
     * @param string|array $urlParts slash-delimited inUrl parameters or an array of parameters
     */
    public static function generateInUrlParams($urlParts)
    {
        if (is_string($urlParts)) $urlParts = explode('/', $urlParts);
        foreach($urlParts as $part) self::$inUrl[] = $part;
    }

    /**
     * gets/returns an array of inUrl parameters
     * or a specific index if provided one
     * @param int|null $index the index to return, or null to return all
     * @return mixed
     */
    public static function inUrl($index = null)
    {
        if ($index === null) return self::$inUrl;
        else return ($index < count(self::$inUrl[$index]) ? self::$inUrl[$index] : false);
    }

    /**
     * gets/returns the method used for the current request
     * @return string
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
