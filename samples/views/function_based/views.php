<?php
namespace Sobo_PhpRouter\Views\FunctionBased;

function full_name($name, $last_name)
{
    echo "USER IN VIEWS: $name $last_name";
}

function index()
{
    echo 'INDEX';
}

function product($type, $color)
{
    echo "PRODUCT TYPE: $type IN VIEWS WITH COLOR: $color";
}

function user($id)
{
    echo "USER IN VIEWS WITH ID: $id";
}

function error404()
{
    echo 'PAGE NOT FOUND';
}
