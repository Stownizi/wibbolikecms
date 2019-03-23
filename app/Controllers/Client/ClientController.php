<?php
namespace App\Controllers\Client;

use App\Controllers\Controller;

class ClientController extends Controller
{

    public function getClient($request, $response, $args)
    {
        $roomId = 0;

        if (isset($args['roomId']) && is_numeric($args['roomId'])) {
            $roomId = $args['roomId'];
        }

        return $this->view->render($response, 'client/client.twig', [
            'roomId' => $roomId,
        ]);
    }
}
