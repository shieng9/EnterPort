<?php
require_once(ROOT_PATH .'/Models/UsersRoom.php');

class UsersRoomController {
  private $request; // リクエストパラメータ(GET, POST)
  private $UsersRoom; // Roomモデル

  public function __construct() {
    // リクエストパラメータの取得
    $this->request['get'] = $_GET;
    $this->request['post'] = $_POST;
    $this->request['session'] = $_SESSION;

    // モデルオブジェクトの生成
    $this->UsersRoom = new UsersRoom();
  }


  // コメント削除
  public function commentDelete() {
    if(empty($this->request['get']) || empty($this->request['session'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $delete_comment = $this->UsersRoom->deleteComment($this->request['session'], $this->request['get']);
    return $delete_comment;
  }  

  // コメント作成
  public function roomAddComment() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $add_comment = $this->UsersRoom->addComment($this->request['session'], $this->request['post']);
    return $add_comment;
  }  
}
?>