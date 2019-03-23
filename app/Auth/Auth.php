<?php
namespace App\Auth;

use App\Models\Bans;
use App\Models\User;

class Auth
{

    public function user()
    {
        if (isset($_SESSION['id'])  && strpos($_SESSION['id'], '.') !== false) {
            $data = explode('.', $_SESSION['id']);
            if($data[0] == md5(ip()))
                return User::where('id', $data[1])->first();
            else
                return null;
        }
    }

    public function attempt($username, $password)
    {
        $user = User::where('username', $username)->where('password', '=', hashMdp($password))->select('id')->first();
        if (!$user) {
            return false;
        } else {
            $_SESSION['id'] = md5(ip()) .'.'. $user->id;
            return true;
        }

        return false;
    }

    public function checkban($username, $flash)
    {
        $IpBan = Bans::where('bantype', 'ip')->where('value', ip())->first();
        if ($IpBan) {
            if ($IpBan->expire > time()) {
                $expireip = date('d/m/Y H:i:s', $IpBan->expire);

                $flash->addMessage('error', "Ton IP a été banni pour la raison suivante: " . $IpBan->reason . ". Il expira le: " . $expireip . "");
                return true;
            }
            $IpBan->delete();
        }

        $AccountBan = Bans::where('bantype', 'user')->where('value', $username)->first();
        if ($AccountBan) {
            if ($AccountBan->expire > time()) {
                $expireaccount = date('d/m/Y H:i:s', $AccountBan->expire);

                $flash->addMessage('error', "Ton compte a été banni pour la raison suivante: " . $AccountBan->reason . ". Il expira le: " . $expireaccount . "");
                return true;
            }
            $AccountBan->delete();
        }

        return false;
    }

    public function check()
    {
        if (isset($_SESSION['id']) && strpos($_SESSION['id'], '.') !== false) {

            $data = explode('.', $_SESSION['id']);
            if($data[0] == md5(ip()))
                return true;
        }

        if (!isset($_COOKIE["CheckWibbo"])) {
            return false;
        }

        $cookie = $_COOKIE["CheckWibbo"];
        $userid = explode('.', $cookie)[0];

        $user = User::where('id', $userid)->select('id', 'username', 'password')->first();
        if (!$user) {
            setcookie("CheckWibbo", "", time() - 3600);
            return false;
        } else {
            $data = explode('.', $cookie)[1];
            $datacheck = md5($user->username . $user->password . ip());
            if ($data == $datacheck) {
                $_SESSION['id'] = md5(ip()) .'.'. $user->id;
                return true;
            } else {
                setcookie("CheckWibbo", "", time() - 3600);
                return false;
            }
        }
    }

}
