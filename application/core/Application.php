<?php

/**
 * Class Application
 * The heart of the application
 */
class Application
{
    /** @var mixed Instance of the controller */
    private $controller;

    /** @var array URL parameters, will be passed to used controller-method */
    private $parameters = array();

    /** @var string Just the name of the controller, useful for checks inside the view ("where am I ?") */
    private $controller_name;

    /** @var string Just the name of the controller's method, useful for checks inside the view ("where am I ?") */
    private $action_name;

    /** @var bool Whether the request was to an API controller */
    private $is_api;

    /**
     * Start the application, analyze URL elements, call according controller/method or relocate to fallback location
     */
    public function __construct()
    {
        // create array with URL parts in $url
        $this->splitUrl();

        // creates controller and action names (from URL input)
        $this->createControllerAndActionNames();

        $controller_path = Config::get($this->is_api ? 'PATH_API' : 'PATH_CONTROLLER') . $this->controller_name . '.php';
        // does such a controller exist ?
        if (file_exists($controller_path)) {
            // load this file and create this controller
            // example: if controller would be "car", then this line would translate into: $this->car = new car();
            require $controller_path;
            $this->controller = new $this->controller_name();

            // check for method: does such a method exist in the controller ?
            if (method_exists($this->controller, $this->action_name)) {
                if (!empty($this->parameters)) {
                    // call the method and pass arguments to it
                    call_user_func_array(array($this->controller, $this->action_name), $this->parameters);
                } else {
                    // if no parameters are given, just call the method without parameters, like $this->index->index();
                    $this->controller->{$this->action_name}();
                }
            } else {
                // load 404 error page
                require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
                $this->controller = new ErrorController;
                $this->controller->error404();
            }
        } else {
            // load 404 error page
            require Config::get('PATH_CONTROLLER') . 'ErrorController.php';
            $this->controller = new ErrorController;
            $this->controller->error404();
        }
    }

    /**
     * Get and split the URL
     */
    private function splitUrl()
    {
        if (Request::get('url')) {

            // split URL
            $url = trim(Request::get('url'), '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // put URL parts into according properties
            $this->controller_name = isset($url[0]) ? array_shift($url) : null;
            $this->action_name = isset($url[0]) ? array_shift($url) : null;

            // rebase array keys and store the URL parameters
            $this->parameters = array_values($url);
        }
    }

    /**
     * Checks if controller and action names are given. If not, default values are put into the properties.
     * Also renames controller to usable name.
     */
    private function createControllerAndActionNames()
    {
        // check for API request
        if ($this->controller_name === Config::get('API_ROOT')) {
            $this->is_api = true;
            $this->controller_name = $this->action_name;
            $this->action_name = Request::method();
            Request::generateInUrlParams($this->parameters);
        }
        // check for controller: no controller given ? then make controller = default controller (from config)
        if (!$this->controller_name) {
            $this->controller_name = Config::get('DEFAULT_CONTROLLER');
        }

        // check for action: no action given ? then make action = default action (from config)
        if (!$this->action_name || (is_array($this->action_name) && count($this->action_name) == 0)) {
            $this->action_name = Config::get('DEFAULT_ACTION');
        }

        // rename controller name to real controller class/file name ("index" to "IndexController")
        $this->controller_name = ucwords($this->controller_name) . ($this->is_api ? 'Api' : '') . 'Controller';
    }
}
