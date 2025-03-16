<?php
//############################
//só entrou ao iniciao frpnt para receber requisiçoes de la
//header("Access-Control-Allow-Origin: http://localhost"); // Permite requisições do front
header("Access-Control-Allow-Origin: http://localhost:84");// Permite requisições do front
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
//####################################
// Definindo constantes do sistema
define('BASE_PATH', __DIR__);
define('APP_NAME', 'Sistema SSO');

// Load configuration
require_once __DIR__ . '/config/config.php';

// Configuração de erros para desenvolvimento
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload de classes -- essa linha após rodar composer require firebase/php-jwt
// Carrega o autoload do Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Autoload de classes customizadas
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    } else {
        error_log("Autoload falhou: Arquivo não encontrado - $file");
    }
});

// Por que isso resolve? acima

// O vendor/autoload.php carrega as dependências instaladas via Composer, como o firebase/php-jwt. Sem isso, o PHP não acha a classe Firebase\JWT\JWT.

// Initialize router and controller
use Router\Router;
use Controllers\ClientController;

$controller = new ClientController();
$router = new Router($controller);

// Define routes
$router->addRoute('GET', '/clients', 'index');
$router->addRoute('POST', '/clients', 'store');
$router->addRoute('PUT', '/clients/:id', 'update');
$router->addRoute('DELETE', '/clients/:id', 'delete');
$router->addRoute('POST', '/token/generate', 'generateToken');
$router->addRoute('POST', '/token/validate', 'validateToken');

//$router->addRoute('GET', '/clients', 'index');
$router->addRoute('GET', '/clients/:id', 'getById'); // Adiciona a rota para GET com ID

// Handle request
$router->handleRequest();
