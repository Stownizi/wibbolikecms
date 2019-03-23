<?php
namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\Bans;
use App\Models\UserStats;
use App\Controllers\Controller;

class RegisterController extends Controller{

  public function getRegister($request, $response){
    return $this->view->render($response, 'register.twig',[
      'title' => 'Inscription']);
  }

  public function postRegister($request, $response){
    $pathFor = $response->withRedirect($this->router->pathFor('auth.register'));

      if (false === $request->getAttribute('csrf_status')) {
      $this->flash->addMessage('error', 'Erreur...');  
      return $pathFor;
    } 

    $filter = preg_replace("/[^a-z\d\-=\?!@:\.]/i", "", $request->getParam('username'));

    if(empty($request->getParam('username'))){
      $this->flash->addMessage('error', 'Indiquez un pseudonyme');
      return $pathFor;
    }

    if(empty($request->getParam('password'))){
      $this->flash->addMessage('error', 'Indiquez un mot de passe');
      return $pathFor;
    }

    if(empty($request->getParam('repassword'))){
      $this->flash->addMessage('error', 'Répètez votre mot de passe');
      return $pathFor;
    }

    $ipcountry = (isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? $_SERVER["HTTP_CF_IPCOUNTRY"] : 'UNDEF');

    if($filter !== $request->getParam('username')){
      $this->flash->addMessage('error','Votre pseudonyme contient des caractères interdit');
      return $pathFor;
    }

    if(strlen($request->getParam('username')) > 24){
      $this->flash->addMessage('error', 'Votre pseudonyme est trop long');
      return $pathFor;     
    }    

    if(strlen($request->getParam('username')) < 3){
      $this->flash->addMessage('error', 'Votre pseudonyme est trop court');
      return $pathFor;     
    }

    if($request->getParam('password') != $request->getParam('repassword')){
      $this->flash->addMessage('error', 'Les mots de passe ne correspondent pas');
      return $pathFor;     
    }
	
	if(strlen($request->getParam('password')) < 6){
      $this->flash->addMessage('error', 'Votre mot de passe est trop court');
      return $pathFor;     
    }
	
	if($request->getParam('condition') != "true")
	{
		$this->flash->addMessage('error', 'Merci de lire et accepter nos conditions d\'utilisation avant de vous inscrire');
      return $pathFor;  
	}

    $ban = Bans::where('bantype', 'ip')->where('value', ip())->first();
    if($ban){
      $this->flash->addMessage('error', 'Votre IP ('.ip().') est banni de Wibbo');
      return $pathFor;
    }

    $user = User::where('username', $request->getParam('username'))->first();
    if($user){
      $this->flash->addMessage('error', 'Le pseudonyme est déjà utilisé par un autre Wibbo');
      return $pathFor;
    }

    $limiteip = User::where('ip_last', ip())->count();
    if($limiteip > 100){
      $this->flash->addMessage('error', 'La limite de création de 100 comptes par ip a été atteinte');
      return $pathFor;
    }

    $timecreated = time() - (60*60*1);

    $limiteip = User::where('ip_last', ip())->where('account_created', '>=', $timecreated)->count();
    if($limiteip > 1){
      $this->flash->addMessage('error', 'Vous avez atteint la limite de création de comptes par heures');
      return $pathFor;
    }
		
      $id = User::insertGetId([
        'username' => $request->getParam('username'),
        'password' => hashMdp($request->getParam('password')),
        'rank' => 1,
        'gender' => 'M',
        'motto' => '',
        'credits' => 1000000,
        'activity_points' => 100,
        'last_offline' => time(),
        'account_created' => time(),
        'last_online' => time(),
        'ip_last' => ip(),
        'ipcountry' => $ipcountry
      ]);
      UserStats::insert(['id' => $id]);

      $_SESSION['id'] = md5(ip()) .'.'. $id;
      setcookie("CheckWibbo", $auth->id . '.' . md5($auth->username . $auth->password . ip()), time() + (32140800));
      return $response->withRedirect("http://".$_SERVER['HTTP_HOST'].$this->router->pathFor('me'));
  }
}