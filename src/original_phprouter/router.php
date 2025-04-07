<?php

function get($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        route($route, $path_to_include);
    }
}
function post($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        route($route, $path_to_include);
    }
}
function put($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        route($route, $path_to_include);
    }
}

/**
 * route for the PATCH method
 * @param mixed $route
 * @param mixed $path_to_include
 * @return void
 */
function patch($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        route($route, $path_to_include);
    }
}

/**
 * route for the DELETE method
 * @param mixed $route
 * @param mixed $path_to_include
 * @return void
 */
function del($route, $path_to_include)
{
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        route($route, $path_to_include);
    }
}

/**
 * http-method-independent route
 * @param mixed $route
 * @param mixed $path_to_include
 * @return void
 */
function any($route, $path_to_include)
{
    route($route, $path_to_include);
}

/**
 * do the routing
 * @param mixed $route
 * @param mixed $path_to_include
 * @return void
 */
function route($route, $path_to_include)
{
    $callback = $path_to_include;
	
    // it's a file-based view/api point, so define the proper path_to_include:
    if (! is_callable($callback)) {
        if (! strpos($path_to_include, '.php')) {
            $path_to_include .= '.php';
        }

        if ($path_to_include[0] != DIRECTORY_SEPARATOR) {
            $path_to_include = rtrim(__DIR__, '/') . "/$path_to_include";
        }
    }

    // 404 - include the template and quit
    if ($route == "/404") {
        include_once $path_to_include;
        exit();
    }

    $request_url       = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
    $request_url       = rtrim($request_url, '/');
    $request_url       = strtok($request_url, '?');
    $route_parts       = explode('/', $route);
    $request_url_parts = explode('/', $request_url);
    array_shift($route_parts);
    array_shift($request_url_parts);
    if ($route_parts[0] == '' && count($request_url_parts) == 0) {
        // Callback function
        if (is_callable($callback)) {
            call_user_func_array($callback, []);
            exit();
        }
        include_once $path_to_include;
        exit();
    }

    if (count($route_parts) != count($request_url_parts)) {
        return;
    }

    $parameters = [];
    for ($__i__ = 0; $__i__ < count($route_parts); $__i__++) {
        $route_part = $route_parts[$__i__];
        if (preg_match("/^[$]/", $route_part)) {
            $route_part = ltrim($route_part, '$');
            array_push($parameters, $request_url_parts[$__i__]);
            $$route_part = $request_url_parts[$__i__];
        } else if ($route_parts[$__i__] != $request_url_parts[$__i__]) {
            return;
        }
    }

    // Callback function
    if (is_callable($callback)) {
        call_user_func_array($callback, $parameters);
        exit();
    }

    include_once $path_to_include;
    exit();
}
function out($text)
{
    echo htmlspecialchars($text);
}

function set_csrf()
{
    session_start();
    if (! isset($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(50));
    }
    echo '<input type="hidden" name="csrf" value="' . $_SESSION["csrf"] . '">';
}

function is_csrf_valid()
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
