<?php
@session_start();
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Controller\MyCustomErrorRenderer;
use DI\Container;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ .'/../vendor/owasp/csrf-protector-php/libs/csrf/csrfprotector.php';



$views_path = __DIR__ . '/../views';
define('RAND',md5(rand(123456,9876543)));
define('DOMAIN','http://localhost:7000');
setcookie('samesite', 'strict',0);
define('ENV', parse_ini_file('../.env'));




$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$configContainer = require_once __DIR__ . '/../configContainer.php';
$configContainer($container);


$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$twig = Twig::create($views_path, ['cache' => false, 'session'=>$_SESSION]);
$app->add(TwigMiddleware::create($app, $twig));


$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', MyCustomErrorRenderer::class);

$router = $configContainer = require_once __DIR__ . '/../router.php';
$router($app);

$app->run();
