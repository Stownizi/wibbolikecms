<?php
namespace App\Controllers\Client;

use App\Controllers\Controller;
use App\Models\Rooms;

class RoomController extends Controller{
  
  public function getRoom($request, $response, $args){

    $roomId = 0;

    if(isset($args['roomId']) && is_numeric($args['roomId']))
      $roomId = $args['roomId'];

    $room = Rooms::where('id', $roomId)->first();

    if(!$room)
      return $response->withRedirect($this->router->pathFor('me'));

    if($this->auth->user()->online == 0)
      return $response->withRedirect($this->router->pathFor('client').'/'.$roomId);

    
    sendMusCommand('senduser', $this->auth->user()->id.chr(1).$room->id);
    

    return $this->view->render($response, 'room.twig',[
      'room' => $room,
      'title' => 'Hôtel'
    ]);
  }
}