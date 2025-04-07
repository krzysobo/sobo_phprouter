<?php
require_once __DIR__ . '/../src/original_phprouter/router.php';
ini_set('display_errors', '1');
error_reporting(E_ALL);

// ##################################################
// ##################################################
// ##################################################

$views_file_based_path = __DIR__ . '/views/file_based';
$api_file_based_path   = __DIR__ . '/api/file_based';

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/', "$views_file_based_path/index.php");

// Dynamic GET. Example with 1 variable
// The $id will be available in user.php
get('/user/$id', "$views_file_based_path/user");

// Dynamic GET. Example with 2 variables
// The $name will be available in full_name.php
// The $last_name will be available in full_name.php
// In the browser point to: localhost/user/X/Y
get('/user/$name/$last_name', "$views_file_based_path/full_name.php");

// Dynamic GET. Example with 2 variables with static
// In the URL -> http://localhost/product/shoes/color/blue
// The $type will be available in product.php
// The $color will be available in product.php
get('/product/$type/color/$color', "$views_file_based_path/product.php");

// A route with a callback
get('/callback', function () {
    echo 'Callback executed';
});

// A route with a callback passing a variable
// To run this route, in the browser type:
// http://localhost/user/A
get('/callback/$name', function ($name) {
    echo "Callback executed. The name is $name";
});

// Route where the query string happends right after a forward slash
get('/product', '');

// A route with a callback passing 2 variables
// To run this route, in the browser type:
// http://localhost/callback/A/B
get('/callback/$name/$last_name', function ($name, $last_name) {
    echo "Callback executed. The full name is $name $last_name";
});

// ##################################################
// ##################################################
// ##################################################
// Route that will use POST data
post('/user', "$api_file_based_path/create_user");

// ##################################################
// ##################################################
// ##################################################
// Route that will use PUT data
put('/user', "$api_file_based_path/put_user");

// ##################################################
// ##################################################
// ##################################################
// Route that will use PATCH data
patch('/user', "$api_file_based_path/patch_user");

// ##################################################
// ##################################################
// ##################################################
// Route that will perform the DELETE request
del('/user/$user_id', "$api_file_based_path/delete_user");

// ##################################################
// ##################################################
// ##################################################
// any can be used for GETs or POSTs

// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404', "$views_file_based_path/404.php");
