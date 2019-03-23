<?php
namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\StaffIp;
use App\Models\User;

class AuthController extends Controller
{

    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'login.twig', [
            'page' => 'login',
            'title' => 'Crée ton avatar, décore ton appart, chatte et fais-toi plein d amis. Habbo en gratuit']);
    }

    public function postSignIn($request, $response)
    {
        $pathFor = $response->withRedirect($this->router->pathFor('auth.signin'));

        if (empty($request->getParam('username'))) {
            $this->flash->addMessage('error', 'Indiquez votre pseudonyme');
            return $pathFor;
        }

        if (empty($request->getParam('password'))) {
            $this->flash->addMessage('error', 'Indiquez votre mot de passe');
            return $pathFor;
        }

        if (false === $request->getAttribute('csrf_status')) {
        $this->flash->addMessage('error', 'Erreur système...');
        return $pathFor;
        }

        $auth = User::where('username', $request->getParam('username'))->where('password', '=', hashMdp($request->getParam('password')))->select('id', 'username', 'password')->first();

        if (!$auth) {
            $this->flash->addMessage('error', 'Vos identifiants sont incorrrects');
            return $pathFor;
        }

        $ipcountry = (isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? $_SERVER["HTTP_CF_IPCOUNTRY"] : 'UNDEF');

        if ($this->auth->checkban($request->getParam('username'), $this->flash)) {
            return $pathFor;
        }

        $_SESSION['id'] = md5(ip()) .'.'. $auth->id;
        setcookie("CheckWibbo", $auth->id . '.' . md5($auth->username . $auth->password . ip()), time() + (32140800));

        User::where('id', $auth->id)->update([
            'last_offline' => time(),
            'ip_last' => ip(),
            'ipcountry' => $ipcountry,
        ]);

        return $response->withRedirect($this->router->pathFor('me'));
    }

}