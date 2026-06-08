<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../config/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$controllerClass = ucfirst($controller) . 'Controller';
$controllerFile = __DIR__ . "/../controllers/{$controllerClass}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerObj = new $controllerClass();

    if ($id && method_exists($controllerObj, $action)) {
        $controllerObj->$action($id);
    } elseif (method_exists($controllerObj, $action)) {
        $controllerObj->$action();
    } else {
        die("Error 404: Acción no encontrada");
    }
} else {
    die("Error 404: Controlador no encontrado");
}
