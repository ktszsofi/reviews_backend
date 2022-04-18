<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

include_once '../config/Database.php';
include_once '../models/Review.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

/*if($uri[1] !== 'reviews'){
    header("HTTP/1.1 404 Not Found");
    exit();
}*/

$reviewId = null;

$requestMethod = $_SERVER["REQUEST_METHOD"];
if($requestMethod == "OPTIONS"){
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
$database = new Database();
$db = $database->connect();

$controller = new Review($db, $requestMethod);
$controller->processRequest();