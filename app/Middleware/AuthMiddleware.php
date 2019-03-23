<?php
namespace App\Middleware;

class AuthMiddleware extends Middleware
{

    public function __invoke($request, $response, $next)
    {

        if (!$this->container->auth->check()) {
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        if ($this->container->auth->checkban($this->container->auth->user()->username, $this->container->flash)) {
            unset($_SESSION['id']);
            setcookie("CheckWibbo", "", time() - 3600);
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        $response = $next($request, $response);
        return $response;
    }

}
