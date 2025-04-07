<?php
// require_once __DIR__.'/router_orig.php';
ini_set('display_errors', '1');
error_reporting(E_ALL);

function join_url()
{
    $parts     = func_get_args();
    $parts_out = [];

    foreach ($parts as $i => $part) {
        $part_out    = ($i == 0) ? ltrim($part, '/') : trim($part, '/');
        $parts_out[] = $part_out;
    }

    return implode("/", $parts_out);
}

/**
 * makes a curl request and returns results
 * @param mixed $url
 * @param mixed $http_method
 * @param mixed $return_transfer
 * @return array<string, string, string>
 */
function do_curl_request($url, $http_method, $post_fields = [], $return_transfer = true): array
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // routes_orig
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, false);

    curl_setopt($ch, CURLOPT_POST, ($http_method == 'POST') ? true : false);

    // PUT, PATCH, DELETE etc
    if (! in_array($http_method, ['GET', 'POST'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
    }

    // POST, PUT and PATCH are using CURLOPT_POSTFIELDS
    if (in_array($http_method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return_transfer);

    $res = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_error($ch)) {
        $curl_error = curl_error($ch);
        echo "==== !! CURL_ERROR: $curl_error";
        $success = false;
    } else {
        $curl_error = "";
    }

    curl_close($ch);

    return [$res, $http_code, $curl_error];

}

function do_test_cases($test_cases, $host)
{
    $no_total  = count($test_cases);
    $no_passed = 0;
    $no_failed = 0;

    foreach ($test_cases as $i => $test_case) {
        $success = true;

        [$http_method, $test_case_url, $expected_res, $expected_http_code, $post_fields] = $test_case;

        $http_method = (isset($http_method) ?? $http_method != '') ? strtoupper($http_method) : 'GET';
        $full_url    = join_url($host, $test_case_url);

        echo "\n\n---------\nTEST CASE $i / $no_total -- URL: $full_url METHOD: $http_method";

        [$res, $http_code, $curl_error] = do_curl_request(
            $full_url, $http_method, $post_fields, true);

        if (isset($curl_error) && ($curl_error != '')) {
            echo "==== !! CURL_ERROR: $curl_error";
            $success = false;
        }

        if ($http_code == $expected_http_code) {
            echo "\n== HTTP CODE: $http_code - expected: $expected_http_code - [OK!]";
        } else {
            $success = false;
            echo "\n== HTTP CODE: $http_code - expected: $expected_http_code - [FAILED!]";
        }

        if ($res == $expected_res) {
            echo "\n== CONTENT FOUND: '$res'\n==== EXPECTED: '$expected_res' - [OK!]";
        } else {
            echo "\n== CONTENT FOUND: '$res'\n==== EXPECTED: '$expected_res' - [FAILED!]";
            $success = false;
        }

        if ($success) {
            $no_passed++;
        } else {
            $no_failed++;
        }
    }

    echo "\n\n[[[[[ SUMMARY: ]]]]]" .
        "\n==> Total tests: $no_total" .
        "\n==> Total passed: $no_passed" .
        "\n==> Total failed: $no_failed";
    if ($no_failed == 0) {
        echo "\n====> ALL TESTS WERE SUCCESSFUL!\n\n";
    }

    return [
        'no_total'   => $no_total,
        'no_passed'  => $no_passed,
        'no_failed'  => $no_failed,
        'all_passed' => ($no_failed == 0) ? true : false,
    ];

}

$host_orig = "localhost:4400"; // php -S localhost:4400 routes_orig.php
$host_sobo = "localhost:4500"; // php -S localhost:4400 routes_sobo.php

$test_cases_orig = [
    ['GET', '/', 'INDEX', 200, []],
    ['GET', '/user/123', 'USER IN VIEWS WITH ID: 123', 200, []],
    ['GET', '/user/Thomas/Jefferson', 'USER IN VIEWS: Thomas Jefferson', 200, []],
    ['GET', '/product/car/color/red', 'PRODUCT TYPE: car IN VIEWS WITH COLOR: red', 200, []],
    ['GET', '/callback', 'Callback executed', 200, []],
    ['GET', '/callback/admin', 'Callback executed. The name is admin', 200, []],
    ['GET', '/callback/Thomas/Jefferson', 'Callback executed. The full name is Thomas Jefferson', 200, []],
    ['POST', '/user', 'Thomas Jeffersonuser saved', 200, ['user_name' => 'Thomas Jefferson']],
    ['PUT', '/user', 'PUT_USER - TODO', 200, ['user_name' => 'Thomas Jefferson', 'user_points' => 1776, 'user_rank' => 1]],
    ['PATCH', '/user', 'PATCH_USER - TODO', 200, ['user_name' => 'Thomas Jefferson', 'user_points' => 1776]],
    ['DELETE', '/user/1', 'user with ID 1 has been successfully deleted', 200, []],

    ['GET', '/non-existing-page', 'PAGE NOT FOUND', 404, []],
];

$test_cases_sobo = [
    ['GET', '/', 'INDEX', 200, []],
    ['GET', '/user/123', 'USER IN VIEWS WITH ID: 123', 200, []],
    ['GET', '/user/Thomas/Jefferson', 'USER IN VIEWS: Thomas Jefferson', 200, []],
    ['GET', '/product/car/color/red', 'PRODUCT TYPE: car IN VIEWS WITH COLOR: red', 200, []],
    ['GET', '/callback', 'Callback executed', 200, []],
    ['GET', '/callback/admin', 'Callback executed. The name is admin', 200, []],
    ['GET', '/callback/Thomas/Jefferson', 'Callback executed. The full name is Thomas Jefferson', 200, []],
    ['POST', '/user', 'Thomas Jeffersonuser saved', 200, ['user_name' => 'Thomas Jefferson']],
    ['PUT', '/user', 'PUT_USER - TODO', 200, ['user_name' => 'Thomas Jefferson', 'user_points' => 1776, 'user_rank' => 1]],
    ['PATCH', '/user', 'PATCH_USER - TODO', 200, ['user_name' => 'Thomas Jefferson', 'user_points' => 1776]],
    ['DELETE', '/user/1', 'user with ID 1 has been successfully deleted', 200, []],

    ['GET', '/non-existing-page', 'PAGE NOT FOUND', 404, []],
];

$res_orig = do_test_cases($test_cases_orig, $host_orig);
$res_sobo = do_test_cases($test_cases_sobo, $host_sobo);

if ($res_orig['all_passed']) {
  echo "\n======> For orig, ALL tests ({$res_orig['no_passed']}/{$res_orig['no_total']}) passed.\n";
} else {
  echo "\n======> For orig, ({$res_orig['no_passed']}/{$res_orig['no_total']}) passed OK, but {$res_orig['no_failed']} failed.\n";
}

if ($res_sobo['all_passed']) {
    echo "\n======> For sobo, ALL tests ({$res_sobo['no_passed']}/{$res_sobo['no_total']}) passed.\n";
} else {
    echo "\n======> For sobo, ({$res_sobo['no_passed']}/{$res_sobo['no_total']}) passed OK, but {$res_sobo['no_failed']} failed.\n";
}

