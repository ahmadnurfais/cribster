<?php
require_once './autoload.php';
require_once './api.php';

if ($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1') {
    $url = ($_SERVER['REQUEST_SCHEME'] == 'http') ? "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : null;
} else {
    $url = ($_SERVER['REQUEST_SCHEME'] == 'https') ? "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" : null;
}

$page = parse_url($url, PHP_URL_PATH);
$page = rtrim(ltrim($page, '/'), '/');
$page = str_replace('src', 'pages', $page);
// Note that in local, it would be localhost/cribster/api/ ...
// While in the hosting, it would be / ...
if (strstr($page, 'pages') === substr($page, -5)) { // Check if it is the index page of the pages dir, it is ended with 'pages'
    $page = ($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1') ? "../../../$page/index.php" : "../$page/index.php";
} elseif ($page == '' || $page == 'cribster/api') { // Check if it is the index page
    $page = BASE_PATH . "/pages/index.php";
} else {
    $page = ($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1') ? "../../../$page.php" : "../$page.php";
}
// echo $page;

if (file_exists($page)) {
    include ($page);
} else {
    http_response_code(404);
}
