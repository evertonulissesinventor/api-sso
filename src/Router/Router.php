<?php

namespace Router;

class Router {
    private $routes = [];
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace('/api-sso/', '/', $path);

        // Set CORS headers
        header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json');

        // Handle preflight requests
        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        // Get request data
    $data = [];

    if ($method === 'POST' || $method === 'PUT') {
        $rawInput = file_get_contents('php://input');
        error_log("Raw input: " . $rawInput); // Debug no log
        $data = json_decode($rawInput, true);
        error_log("Decoded data: " . print_r($data, true)); // Debug no log
        // Debug: Verifique se os dados estão chegando
        if ($data === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()]);
                exit;
        }
    }

    
     // Find matching route
     foreach ($this->routes as $route) {
        if ($route['method'] === $method && $this->matchPath($route['path'], $path, $params)) {
            $handler = $route['handler'];
            // Passa apenas $data para rotas sem parâmetros como /clients
            if (empty($params)) {
                $result = call_user_func([$this->controller, $handler], $data);
            } else {
                $result = call_user_func([$this->controller, $handler], $params['id'], $data);
                // Não muda a lógica, mas o erro estava no ClientController, não aqui
            }
            echo json_encode($result);
            return;
        }
    }

        // No route found
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Route not found']);
    }

    private function matchPath($routePath, $requestPath, &$params) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if (strpos($routeParts[$i], ':') === 0) {
                $paramName = substr($routeParts[$i], 1);
                $params[$paramName] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        return true;
    }
}