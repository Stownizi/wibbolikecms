<?php
namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\UserWebSocket;

class ApiController extends Controller
{

    public function getClientConfig($request, $response, $args)
    {

        $config = array();

        $config['ip'] = "127.0.0.1";
        $config['port'] = "30010";
        $config['UrlWibbo'] = "http://localhost";
        $config['Vars'] = "http://localhost/dcr/gamedata/vars.txt";
        $config['Texts'] = "http://localhost/dcr/gamedata/texts.txt";
        $config['Producdata'] = "http://localhost/dcr/gamedata/productdata.txt";
        $config['Furnidata'] = "http://localhost/dcr/gamedata/furnidata.txt";
        $config['MessageFun'] = "Nous faisons avancer la science/Chargement des messages amusants...Veuillez patienter./ça te dirait des frites avec ça ?/Suis le canard jaune./Le temps est juste une illusion./On est bientôt arrivés ?!/J'adore ton t-shirt./Regarde à gauche, regarde à droite, cligne des yeux deux fois et voilà !/Ce n'est pas toi, c'est moi./Chuut ! J'essaie de me concentrer là-haut./Chargement de l'univers pixélisé.";
        $config['Message'] = "Chargement...";
        $config['R_64'] = "http://localhost/dcr/gordon/R_64/";
        $config['swf'] = "Game.swf";
        $config['cache'] = time();

        return $response->getBody()->write(json_encode($config));
    }

    public function getClientData($request, $response, $args)
    {
        if (!$request->hasHeader('Authorization') || $request->getHeaderLine('Authorization') != "t5rZR4h7") {
            return $response->getBody()->write(json_encode(array('error' => "Non autoriser")));
        }

        if (!$this->auth->check()) {
            return $response->getBody()->write(json_encode(array('error' => "Hors ligne")));
        }

        if ($this->auth->checkban($this->auth->user()->username, $this->flash)) {
            unset($_SESSION['id']);
            setcookie("CheckWibbo", "", time() - 3600);

            return $response->getBody()->write(json_encode(array('error' => "Banni")));
        }

        $monTicket = TicketRefresh();

        $ipcountry = (isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? $_SERVER["HTTP_CF_IPCOUNTRY"] : '');

        User::where('id', $this->auth->user()->id)->update([
            'auth_ticket' => $monTicket,
            'last_offline' => time(),
            'ip_last' => ip(),
            'ipcountry' => $ipcountry,
        ]);

        $data = array(
            'id' => $this->auth->user()->id,
            'SSOTicket' => $monTicket,
            'WSUrl' => "ws://127.0.0.1:527",
            'RoomId' => "0",
        );

        return $response->getBody()->write(json_encode($data));
    }

    public function getSsoTicketWeb($request, $response, $args)
    {
        if (!$request->hasHeader('Authorization') || $request->getHeaderLine('Authorization') != "t5rZR4h7") {
            return $response->getBody()->write(json_encode(array('error' => "Non autoriser")));
        }

        if (!$this->auth->check()) {
            return $response->getBody()->write(json_encode(array('error' => "Hors ligne")));
        }

        if ($this->auth->checkban($this->auth->user()->username, $this->flash)) {
            unset($_SESSION['id']);
            setcookie("CheckWibbo", "", time() - 3600);

            return $response->getBody()->write(json_encode(array('error' => "Banni")));
        }

        $ticketWeb = TicketRefresh();

        $ipcountry = (isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? $_SERVER["HTTP_CF_IPCOUNTRY"] : '');

        UserWebSocket::updateOrCreate(
            ['user_id' => $this->auth->user()->id],
            ['user_id' => $this->auth->user()->id, 'auth_ticket' => $ticketWeb, 'is_staff' => $this->auth->user()->rank >= 6 ? '1' : '0']);

        $data = array(
            'id' => $this->auth->user()->id,
            'SSOTicketweb' => $ticketWeb,
        );

        return $response->getBody()->write(json_encode($data));
    }
}
