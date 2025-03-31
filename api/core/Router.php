<?php
require_once __DIR__ . '/../controllers/DepartmentController.php';

class Router {
    private array $routes = [];

    public function __construct() {
        $this->defineRoutes();
    }

    private function defineRoutes() {
        $this->routes = [
            'GET' => [
                '' => function() { echo json_encode(["message" => "API is running"]); }, // Default route
                'department' => [DepartmentController::class, 'GetAllDepartment'],
                'department/{id}' => [DepartmentController::class, 'GetDepartmentById'],
            ],
            'POST' => [
                'department' => [DepartmentController::class, 'AddDepartment'],
            ],
            'PUT' => [
                'department/{id}' => [DepartmentController::class, 'UpdateDepartment'],
            ],
            'DELETE' => [
                'department/{id}' => [DepartmentController::class, 'DeleteDepartment'],
            ],
        ];
    }

    public function handleRequest() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $this->getProcessedUri();

        if (!isset($this->routes[$requestMethod])) {
            $this->sendNotFound();
            return;
        }

        foreach ($this->routes[$requestMethod] as $route => $handler) {
            $pattern = $this->convertToRegex($route);
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);
                if ($requestMethod === 'POST' || $requestMethod === 'PUT') {
                    $requestData = $this->getRequestData();
                    $this->dispatch($handler, array_merge([$requestData], $matches));
                } else {
                    $this->dispatch($handler, $matches);
                }
                return;
            }
        }

        $this->sendNotFound();
    }

    private function getProcessedUri(): string {
        $requestUri = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");
        $scriptName = trim(dirname($_SERVER["SCRIPT_NAME"]), "/");

        if (!empty($scriptName) && strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }

        return trim($requestUri, "/");
    }

    private function convertToRegex(string $route): string {
        $pattern = preg_replace('/\{(\w+)\}/', '([\w-]+)', $route);
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }

    private function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : []; // Ensure it's an array
    }

    private function dispatch($handler, array $params) {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
        } else {
            [$controllerClass, $method] = $handler;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        }
    }

    private function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Route not found"]);
    }
}
