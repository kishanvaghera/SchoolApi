<?php
 header('Access-Control-Allow-Origin: *');
 header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
 header('Access-Control-Allow-Headers: token, Content-Type');
 header('Access-Control-Max-Age: 1728000');
 header('Content-Length: 0');
 header('Content-Type: text/plain');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

error_reporting(0);

include ("includes/class.php");

$input = file_get_contents('php://input');

$data = json_decode($input);

$_POST = json_decode(json_encode($data), true);