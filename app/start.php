<?php
session_start();
// WibboCMS 0.1
// WolpeurDEV
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
// App
$app = new \Slim\App([
    'settings' => [
    'determineRouteBeforeAppMiddleware' => true,
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,
    ]
]);


$container = $app->getContainer();

$debug = true;
if(!$debug)
{
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $container['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->withRedirect('/error');
    };
};

$container['phpErrorHandler'] = function ($container) {
    return function ($request, $response, $error) use ($container) {
        return $container['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->withRedirect('/error500');
    };
};

$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        return $container['response']
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'text/html')
            ->withRedirect('/error');
    };
};

$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        return $container['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->withRedirect('/error');
    };
};
}

// Database
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection([
 'driver'     => 'mysql',
 'host'       => '127.0.0.1',
 'port'       => '3306',
 'database'   => 'wibbo',
 'username'   => 'root',
 'password'   => '',
 'charset'    => 'utf8',
 'collation'  => 'utf8_unicode_ci',
 'prefix'     => ''
], "default");

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($container) use ($capsule){
    return $capsule;
};

$container['auth'] = function($container){
    return new \App\Auth\Auth;
};

$container['config'] = function($container){
    return new \App\Config\Web;
};

$container['AuthController'] = function($container){
    return new \App\Controllers\Auth\AuthController($container);
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../templates', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    $view->getEnvironment()->addGlobal('flash', $container->flash);
    $view->getEnvironment()->addGlobal('auth', $container->auth);
    $view->getEnvironment()->addGlobal('uri', $container['request']->getUri()->getPath());
    $view->getEnvironment()->addGlobal('config', $container->config);
    
    $view->getEnvironment()->addFilter(new Twig_SimpleFilter('DecodeUtf8', 'DecodeUtf8'));
	
    return $view;
};

$container['flash'] = function($container){
    return new \Slim\Flash\Messages;
};

$container['csrf'] = function($container){
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        return $next($request, $response);
    });
    return $guard;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add($container->csrf);
$app->add(function($request, $response, $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        $uri = $uri->withPath(substr($path, 0, -1));
        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Routes
require 'routes.php';

// function
require 'function.php';

// Run App
$app->run();
