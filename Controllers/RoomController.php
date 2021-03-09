<?php
require_once(ROOT_PATH .'/Models/Room.php');

class RoomController {
  private $request; // リクエストパラメータ(GET, POST)
  private $Room; // Roomモデル

  public function __construct() {
    // リクエストパラメータの取得
    $this->request['get'] = $_GET;
    $this->request['post'] = $_POST;
    $this->request['session'] = $_SESSION;

    // モデルオブジェクトの生成
    $this->Room = new Room();
  }


  // チャット一覧
  public function roomsIndex() {
    $page = 0;
    if(isset($this->request['get']['page'])) {
      $page = $this->request['get']['page'];
    }

    $rooms = $this->Room->findRooms($this->request['session'], $page);
    $rooms_count = $this->Room->countAll();
    $params = [
      'rooms' => $rooms,
      'pages' => $rooms_count / 20
    ];
    return $params;
  }

  // チャット部屋削除
  public function roomDelete() {
    if(empty($this->request['get']['id'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $room = $this->Room->deleteRoom($this->request['session'], $this->request['get']['id']);
    return $room;
  }  

  // チャット部屋作成
  public function roomMake() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $room = $this->Room->createRoom($this->request['post'], $this->request['session']);
    return $room;
  }

  // チャット部屋作成完了確認
  public function makeComp() {
    if(empty($this->request['session'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $room = $this->Room->createComp($this->request['session']);
    return $room;
  }
  
  // チャット部屋入室
  public function roomPageEnter() {
    if(empty($this->request['session'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $room = $this->Room->enterRoom($this->request['get']['room_id'], $this->request['post'], $this->request['session']);
    return $room;
  }
  
}
?>