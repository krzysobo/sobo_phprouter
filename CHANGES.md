# Sobo_PhpRouter
## Version 1.0.0

- creation of the "Sobo_PhpRouter" project, based on "PhpRouter". 
- The original "PhpRouter" was cool and useful, so I, Krzysztof Sobolewski <krzysztof.sobolewski@gmail.com>, have kept it as src/original_phprouter/router.php, albeit with the following improvements and fixes: 
    - I've added http_response_code(response_code: 404) to 404.php - it should return 404, not 200.
    - the original PhpRouter used the "delete" function, which is not allowed, since "delete" is a reserved PHP keyword. Therefore, I've changed it to "del".
    - the orignal PhpRouter used `__DIR__` to include file-based views in the route() function. This is a major flaw, since it precludes passing full paths and assumes that the router engine and routes are in the same directory. After my changes, it only uses `__DIR__` if the path does not start with DIRECTORY_SEPARATOR.
    - Test views locations have been changed. All test code has been moved to samples, and views have been placed in two hierarchies: samples/api and samples/views.

