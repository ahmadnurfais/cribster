<?php
// Set headers to allow cross-origin resource sharing (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// The client side will send the HTTP request to the API endpoint
// URL: api/src/type/ [?param1=... $param2= ...]
// Each request will contains its request method. It can be GET, POST PUT, or DELETE
// There is also a possibilty where a request has the query strings that consist of one or several parameters
if (count($_GET) != 0) {
    $url_params = $_GET; // Declare the query string parameters if it is exist
} else {
    $url_params = [];
}

$request_method = $_SERVER['REQUEST_METHOD']; // Declare the HTTP request method
$request_data = json_decode(file_get_contents('php://input'), true); // Get and convert the JSON request data to array
if ($request_data === null) {
    $request_data = array(); // Set it to empty array if there is no JSON request data
}
