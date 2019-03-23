<?php
use App\Middleware\AuthAdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

// API
$app->group('/api', function () {
    $this->get('/getclientconfig', '\App\Controllers\Api\ApiController:getClientConfig');
    $this->get('/getclientdata', '\App\Controllers\Api\ApiController:getClientData');
    $this->get('/getssoticketweb', '\App\Controllers\Api\ApiController:getSsoTicketWeb');
});

// Connexion
$app->group('', function () {
    $this->get('/', '\App\Controllers\Auth\AuthController:getSignIn')->setName('auth.signin');
    $this->post('/', '\App\Controllers\Auth\AuthController:postSignIn');
})->add(new GuestMiddleware($container));

// Inscription
$app->group('/register', function () {
    $this->get('', '\App\Controllers\Auth\RegisterController:getRegister')->setname('auth.register');
    $this->post('', '\App\Controllers\Auth\RegisterController:postRegister');
})->add(new GuestMiddleware($container));

// Me
$app->group('', function () {
    $this->get('/me', '\App\Controllers\Users\MeController:me')->setName('me');

    $this->get('/logout', function ($request, $response) {
        unset($_SESSION['id']);
        setcookie("CheckWibbo", "", time() - 3600);
        return $this->view->render($response, 'logout.twig');
    })->setName('logout');

})->add(new AuthMiddleware($container));

// Erreur 404
$app->get('/error', function ($request, $response) {
    return $this->view->render($response, 'error/404.twig')->withStatus(404);
});

// Erreur 500
$app->get('/error500', function ($request, $response) {
    return $this->view->render($response, 'error/500.twig')->withStatus(500);
});

// Disclaimer
$app->get('/disclaimer', function ($request, $response) {
    return $this->view->render($response, 'disclaimer.twig');
});

$app->get('/hotel[/{roomId}]', '\App\Controllers\Client\ClientController:getClient')->setName('client')->add(new AuthMiddleware($container));
$app->post('/hotel[/{roomId}]', '\App\Controllers\Client\ClientController:getClient')->add(new AuthMiddleware($container));

$app->get('/room/{roomId}', '\App\Controllers\Client\RoomController:getRoom')->add(new AuthMiddleware($container));