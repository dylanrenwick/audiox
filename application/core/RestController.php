<?php

/**
 * This is the base controller for all REST endpoint controllers.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 * 3. if the user is not logged in and there is no cookie, we check the request for an oauth token
 */
class RestController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        // Initialize base class with OAuth support
        parent::__construct(true);
        // API endpoints will always return JSON
        header('Content-Type: application/json');
    }

    /**
     * Returns an array of implemented HTTP request methods.
     * Child classes should implement methods by defining a function with the name of the method
     * 
     * @return array
     */
    public function getSupportedMethods()
    {
        // Framework-supported HTTP methods
        $actions = array('HEAD', 'GET', 'POST', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'CONNECT', 'PATCH');
        $availableActions = array();
        foreach($actions as $action) {
            // Check if method for HTTP method exists
            if (method_exists($this, $action)) {
                $availableActions[] = $action;
            }
        }
        return $availableActions;
    }
}