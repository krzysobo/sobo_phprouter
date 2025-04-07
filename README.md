# Sobo_PhpRouter
## Secure router for PHP application with XSS and CSRF based on PhpRouter

This is a fork and a major overhaul of the original PHPRouter project. I've kept the original code
as router_orig.php out of respect for the work of the creator of PhpRouter. It's a brilliant project, but I needed more functionality, so I've decided to fork.

URL of the [Sobo_PhpRouter repository](https://github.com/krzysobo/sobo_phprouter) is: https://github.com/krzysobo/sobo_phprouter

URL of the [original PhpRouter repository](https://github.com/phprouter/main) is: https://github.com/phprouter/main


### Installation and configuration of the router 

* The heart of the Sobo_PhpRouter project is a singleton class named Sobo_PhpRouter\Router. It is located in src/sobo_phprouter/Router.php. 
* You have to put this class in some location within your project. Selecting the proper path is on your side, since it depends on your project structure and on such questions as whether you're using an autoloader (if you do, please adjust its settings and/or your paths) or you're just including the files with require, include, require_once, include_once... 
* Then, in the place in your code where you want to use the Sobo_PhpRouter\Router class, please do the following:

```
/* some include/require/autoloading honkey-ponkey */

/* fetch the class from its namespace */
use Sobo_PhpRouter\Router;

/* initialize an object of the class. Remember it's a singleton! */
$router = Router::getInstance();  // or: $router = Router::instance();
```

As same as the original PhpRouter, Sobo_PhpRouter allows to route both to file-based views and callback-based views. 
- The file-based views are simply some php files loaded when the route is matched, and the script exits after their loading. 
- The Callback-based views are based on callbacks, either functions or class/object methods (yes, it handles both static and instance methods). Remember, that neither Sobo_PhpRouter nor the original PhpRouter autoload the required functions: it's YOUR responsibility as a programmer (or your Autoloader's).

In Sobo_PhpRouter, you may decide to use either file-based or callback-based views, or both of those types. To select that, call the method `setAllowedRouteEnds` of your Router instance.
It accepts an array containing the following options:
    * `ROUTE_END_FILE_BASED_VIEW` for file-based views
    * `ROUTE_END_CALLBACK` for callback-based views
You can select one or both of them; by default, both of them are accepted.

```
$router-setAllowedRouteEnds([ROUTE_END_FILE_BASED_VIEW, ROUTE_END_CALLBACK]);
```

If you're using the file-based views, please inform the Router instance **EITHER** about your project root path **OR** your the file-based views root path:

```
$router->setFileBasedViewsRootPath("... the file-based views root path...");
```

OR 

```
$router->setProjectRootPath("... your project root path");
```


### Setting the routes
**This section needs expanding. TODO**

To see how to set the routes, please take a look at the test application located in the "samples"  path, refer the code and the README.md available there.