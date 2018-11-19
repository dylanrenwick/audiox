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

    /**
     * Outputs a JSON encoded error with the given errorCode and message
     * @param int $errorCode The code of the error
     * @param string $message The description of the error, left blank for default message
     * @param bool $append Whether to append the given message to the default message
     */
    public function error($errorCode, $message = '', $append = true)
    {
        // Default error messages for each error code
        $defaultMessages = array(
            0 => 'An unknown error has occurred',
            1 => 'You are not authorized to perform this action.',
            2 => 'You are attempting to access the API using a banned authentication token.',
            3 => 'You are attempting to access the API from a banned IP address.',
            4 => 'Missing parameters!',
            5 => 'Content not found!'
        );

        if (empty($message)) {
            if (isset($defaultMessages[$errorCode])) {
                $message = $defaultMessages[$errorCode];
            } else {
                $message = $defaultMessages[0];
            }
        } else if ($append) {
            if (isset($defaultMessages[$errorCode])) {
                $message = $defaultMessages[$errorCode] . ' ' . $message;
            }
        }

        $error = array(
            'errorCode' => $errorCode,
            'message' => $message
        );
        echo json_encode($error);
    }
}
