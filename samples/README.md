# Sobo_PhpRouter
## Test application
This is a sample application for testing Sobo_PhpRouter.
it contains routes both for the original PhpRouter (routes_orig.php), 
and for Sobo_PhpRouter (routes_sobo.php): the version modified and adapted by Krzysztof Sobolewski.

In order to test both of them do the following:
- open three terminal windows
- change the directory in each of them to "samples"
- in the first window start a dev server for the original router:

    ```php -S localhost:4400 -t public routes_orig.php``` 

    --> it will use the original router renamed to router_orig.php, which I've kept out of respect for the work of its creator(s), since they did the brilliant work!

- in the second window start a dev server for Sobo_PhpRouter:

    ```php -S localhost:4500 -t public routes_sobo.php```

    --> it will use the Krzysztof Sobolewski's router from file router_sobo.php..

- in the third window call:

    ```php tester.php```

    it will do the integration tests for both routers.



