<?php
namespace App\Controllers\Users;

use App\Controllers\Controller;

class MeController extends Controller
{
    public function me($request, $response, $args)
    {
        return $this->view->render($response, 'users/me.twig', [
            'page' => 'me',
            'title' => 'Moi',
        ]);
    }
}
