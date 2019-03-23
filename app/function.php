<?php

function DecodeUtf8($str)
{
    $decoded = utf8_decode($str);
    if (mb_detect_encoding($decoded , 'UTF-8', true) === false)
        return $str;
    return $decoded;
}

function sendMusCommand($command, $data=NULL)
		{
			 $port=30001;
			 $ip='127.0.0.1';

			$data = $command . chr( 1 ) . $data;
			$connection = socket_create( AF_INET, SOCK_STREAM, getprotobyname( 'tcp' ) );
			socket_connect( $connection, $ip, $port );
			if( !is_resource( $connection ) )
			{
				socket_close( $connection );
				return false;
			}
			else
			{
				socket_send( $connection, $data, strlen( $data ), MSG_DONTROUTE );
				socket_close( $connection );
				return true;
			}
		}

function html_secure($texte)
{
	return htmlspecialchars($texte,ENT_QUOTES,'ISO-8859-1');
}

function hashMdp($str){
  return md5("KEYPASSWORD123".$str);
}

function ip(){
  return $_SERVER['REMOTE_ADDR'];
}

function TicketRefresh(){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 10; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
		
	return "WibboME-".md5($randomString)."-WibboME";
}