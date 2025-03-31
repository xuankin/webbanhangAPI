<?php
session_start();
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once 'app/controllers/ProductApiController.php';
require_once 'app/controllers/CategoryApiController.php';
require_once 'app/controllers/AdminController.php';

// Hàm gửi phản hồi JSON
function sendJsonResponse($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Phân tích URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Xác định controller và action mặc định
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Định tuyến các yêu cầu API
if ($controllerName === 'ApiController' && isset($url[1])) {
    $apiControllerName = ($url[1] === 'product' || $url[1] === 'products') ? 'ProductApiController' : ucfirst($url[1]) . 'ApiController';

    if (file_exists('app/controllers/' . $apiControllerName . '.php')) {
        require_once 'app/controllers/' . $apiControllerName . '.php';
        $controller = new $apiControllerName();
        $method = $_SERVER['REQUEST_METHOD'];
        $id = $url[2] ?? null;

        switch ($method) {
            case 'GET':
                if ($id) {
                    $action = 'show';
                } else {
                    $action = 'index';
                }
                break;
            case 'POST':
                if (isset($_POST['action']) && $_POST['action'] === 'update' && $id) {
                    $action = 'update';
                } else {
                    $action = 'store';
                }
                break;
            case 'PUT':
                if ($id) {
                    $action = 'update';
                }
                break;
            case 'DELETE':
                if ($id) {
                    $action = 'destroy';
                }
                break;
            default:
                sendJsonResponse(['message' => 'Method Not Allowed'], 405);
        }

        if (method_exists($controller, $action)) {
            if ($id) {
                call_user_func_array([$controller, $action], [$id]);
            } else {
                call_user_func_array([$controller, $action], []);
            }
        } else {
            sendJsonResponse(['message' => 'Action not found'], 404);
        }
        exit;
    } else {
        sendJsonResponse(['message' => 'Controller not found'], 404);
        exit;
    }
}

// Định tuyến cho các yêu cầu không phải API
if ($controllerName === 'AdminController') {
    require_once 'app/controllers/AdminController.php';
    $controller = new AdminController();
    $action = $url[1] ?? 'index'; // Mặc định action là index cho admin
} elseif (file_exists('app/controllers/' . $controllerName . '.php')) {
    require_once 'app/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
} else {
    die('Controller not found: ' . $controllerName);
}

// Kiểm tra và gọi action
if (method_exists($controller, $action)) {
    call_user_func_array([$controller, $action], array_slice($url, 2));
} else {
    die('Action not found: ' . $action);
}