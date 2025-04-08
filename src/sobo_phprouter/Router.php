<?php
namespace Sobo_PhpRouter;

/**
 * SOBO PHPRouter - based on PHPRouter (https://phprouter.com)
 * @license MIT
 */
class Router
{
    public const ROUTE_END_FILE_BASED_VIEW = 1;
    public const ROUTE_END_CALLBACK        = 2;

    protected static $instance = null;

    protected $allowed_route_ends = [
        self::ROUTE_END_FILE_BASED_VIEW,
        self::ROUTE_END_CALLBACK];

    protected $file_based_views_root_path     = null;
    protected $project_root_path              = null;
    protected $after_callback_callable        = null;
    protected $after_callback_callable_params = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function instance()
    {
        return self::getInstance();
    }

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    public function __wakeup()
    {
        throw new \Exception("One does not simply clone a singleton!");
    }

    /**
     * method for setting the root path for file-based views. It's used at include in the "route" method
     * @param mixed $path
     * @return void
     */
    public function setFileBasedViewsRootPath($path)
    {
        $this->file_based_views_root_path = $path;
    }

    /**
     * method for setting the root path for the project using Sobo_PhpRouter. It's used at include in the "route" method
     * @param mixed $path
     * @return void
     */
    public function setProjectRootPath($path)
    {
        $this->project_root_path = $path;
    }

    /**
     * sets which route ends are allowed; it will be enforced at routing in route()
     * @param mixed $allowed_route_ends
     * @return void
     */
    public function setAllowedRouteEnds($allowed_route_ends)
    {
        $this->allowed_route_ends = $allowed_route_ends;
    }

    public function setAfterCallbackCallable($callable, $params = [])
    {
        $this->after_callback_callable        = $callable;
        $this->after_callback_callable_params = $params;
    }

    /**
     * HTTP method "GET"
     * @param string $route
     * @param mixed $path_to_include
     * @return void
     */
    public function get(string $route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::route($route, $path_to_include);
        }
    }

    /**
     * HTTP method "POST"
     * @param string $route
     * @param mixed $path_to_include
     * @return void
     */
    public function post(string $route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            self::route($route, $path_to_include);
        }
    }

    /**
     * HTTP method "PUT"
     * @param string $route
     * @param mixed $path_to_include
     * @return void
     */
    public function put(string $route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            self::route($route, $path_to_include);
        }
    }

    /**
     * HTTP method "PATCH"
     * @param string $route
     * @param mixed $path_to_include
     * @return void
     */
    public function patch(string $route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            self::route($route, $path_to_include);
        }
    }

    /**
     * HTTP method "DELETE"
     * @param string $route
     * @param mixed $path_to_include
     * @return void
     */
    public function delete(string $route, $path_to_include)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            self::route($route, $path_to_include);
        }
    }

    /**
     * ANY HTTP METHOD. Used for actions like redirecting to a 404 page.
     * @param mixed $route
     * @param mixed $path_to_include
     * @return void
     */
    public function any($route, $path_to_include)
    {
        self::route($route, $path_to_include);
    }

    /**
     * Routing method itself.
     * @param mixed $route
     * @param mixed $path_to_include
     * @return void
     */
    public function route($route, $path_to_include)
    {
        $callback = $path_to_include;

        // it's a file-based view/api point, so define the proper path_to_include:

        if (is_callable($callback)) { // a callable - function or class/object method
            if (! $this->is_route_end_allowed(self::ROUTE_END_CALLBACK)) {
                error_log(
                    "Route end 'CALLBACK' is not allowed in your implementation. " .
                    "Check the Router configuration.");
                return;
            }
        } else { // just a path to include a file-based view
            if (! $this->is_route_end_allowed(self::ROUTE_END_FILE_BASED_VIEW)) {
                error_log(
                    "Route end 'FILE_BASED_VIEW' is not allowed in your " .
                    "implementation. Check the Router configuration.");
                return;
            }

            if (! strpos($path_to_include, '.php')) {
                $path_to_include .= '.php';
            }

            if ($path_to_include[0] != DIRECTORY_SEPARATOR) { // we are using a relative path
                if (isset($this->file_based_views_root_path) && ($this->file_based_views_root_path != '')) {
                    $path_to_include = rtrim($this->file_based_views_root_path, '/') . "/$path_to_include";
                } elseif (isset($this->project_root_path) && ($this->project_root_path != '')) {
                    $path_to_include = rtrim($this->file_based_views_root_path, '/') . "/$path_to_include";
                } else {
                    $path_to_include = rtrim(__DIR__, '/') . "/$path_to_include";
                }
            }
        }

        $request_url       = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $request_url       = rtrim($request_url, '/');
        $request_url       = strtok($request_url, '?');
        $route_parts       = explode('/', $route);
        $request_url_parts = explode('/', $request_url);

        array_shift($route_parts);
        array_shift($request_url_parts);

        // ** empty route and empty URL --> "main page" -> route is matching -> LOAD AND FINISH
        if ($route_parts[0] == '' && count($request_url_parts) == 0) {
            // Callback function
            if (is_callable($callback)) {
                call_user_func_array($callback, []);
                $this->callAfterCallbackIfDefined();
                return;
            }
            include_once $path_to_include;
            exit();
        }

        // ** route doesn't match -> QUIT
        if (count($route_parts) != count($request_url_parts)) {
            return;
        }

        // ** route checking/match finding mechanism
        $parameters = [];
        for ($__i__ = 0; $__i__ < count($route_parts); $__i__++) {
            $route_part = $route_parts[$__i__];
            if (preg_match("/^[$]/", $route_part)) {
                $route_part = ltrim($route_part, '$');
                array_push($parameters, $request_url_parts[$__i__]);
                $$route_part = $request_url_parts[$__i__];
            } else if ($route_parts[$__i__] != $request_url_parts[$__i__]) {
                return; // route doesn't match -> QUIT
            }
        }

        // ** route is matching -> LOAD AND FINISH
        // Callback function
        if (is_callable($callback)) {
            call_user_func_array($callback, $parameters);
            $this->callAfterCallbackIfDefined();
        }

        include_once $path_to_include;
        exit();
    }

    /**
     * output sanitization
     * @param mixed $text
     * @return void
     */
    public static function out($text)
    {
        echo htmlspecialchars($text);
    }

    /**
     * sets a CSRF field
     * @return void
     */
    public static function set_csrf()
    {
        session_start();
        if (! isset($_SESSION["csrf"])) {
            $_SESSION["csrf"] = bin2hex(random_bytes(50));
        }
        echo '<input type="hidden" name="csrf" value="' . $_SESSION["csrf"] . '">';
    }

    /**
     * validates a CSRF
     * @return bool
     */
    public static function is_csrf_valid()
    {
        session_start();
        if (! isset($_SESSION['csrf']) || ! isset($_POST['csrf'])) {
            return false;
        }
        if ($_SESSION['csrf'] != $_POST['csrf']) {
            return false;
        }
        return true;
    }

    /**
     *  checks whether a route end is allowed in your particular implementation.
     * @param mixed $route_end
     * @return bool
     */
    protected function is_route_end_allowed($route_end)
    {
        return (in_array($route_end, $this->allowed_route_ends));
    }

    protected function callAfterCallbackIfDefined()
    {
        if (isset($this->after_callback_callable) &&
            is_callable($this->after_callback_callable)) {
            if (isset($this->after_callback_callable_params) &&
                is_array($this->after_callback_callable_params) &&
                ! empty($this->after_callback_callable_params)) {
                call_user_func_array($this->after_callback_callable,
                    $this->after_callback_callable_params);
            } else {
                call_user_func_array($this->after_callback_callable, []);
            }
        }
    }
}
