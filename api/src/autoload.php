<?php
// Define the base path, both for local and hosting
$base_path = ($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1') ? ($_SERVER['DOCUMENT_ROOT'] . "/cribster/api") : $_SERVER['DOCUMENT_ROOT'];

define('BASE_PATH', $base_path);

// Set up the environment variables
require_once BASE_PATH . '/src/class.environment.php';
$__DotEnvironment = new DotEnvironment(BASE_PATH . "/.env");

spl_autoload_register(
    function ($raw_class) {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $raw_class);
        $class_part = explode(DIRECTORY_SEPARATOR, $class);

        $possible_paths[] = BASE_PATH . "/db/file.php";

        foreach ($possible_paths as $template_path) {
            $path = str_replace('file', end($class_part), $template_path);
            if (file_exists($path)) {
                require_once "$path";
                break;
            }
        }
    }
);
