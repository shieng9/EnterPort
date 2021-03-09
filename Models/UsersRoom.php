<?php
require_once(ROOT_PATH .'/Models/Db.php');

class UsersRoom extends Db {
  private $table = 'users_rooms';
  public function __construct($dbh = null) {
    parent::__construct($dbh);
  }


  // コメント作成
  public function addComment($session, $post) {
    $error_msg = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $room_id = null; //postのroom_id
    $room_password = null; //postのroom_password
    $comment = null; //postのcomment

    if (!empty($session)) {
      $id = (int)$session['id']; 
      $mail = $session['mail']; 

      if (!empty($post)) {
        $room_id = (int)$post['room_id'];
        $room_password = $post['room_password'];
        $comment = $post['comment'];
      }
    }

    // // パスワードの正規表現
    // if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $room_password)) {
    //   $id = null; //ログインユーザーのid
    //   $mail = null; //ログインユーザーのmail
    //   $room_id = null; //postのroom_id
    //   $room_password = null; //postのroom_password
    //   $comment = null; //postのcomment  
    //   $error_msg = '不正な値です。';
    //   return $error_msg;
    // }
    
    // ユーザーが存在しているか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, mail FROM users WHERE id = :id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row1 = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row1)) {
      $row1 = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //postのroom_id
      $room_password = null; //postのroom_password
      $comment = null; //postのcomment  
      $error_msg = '存在１不正な値です。';
      return $error_msg;
    }
    if ($row1['mail'] !== $mail) {
      $row1 = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //postのroom_id
      $room_password = null; //postのroom_password
      $comment = null; //postのcomment  
      $error_msg = 'メール不正な値です。';
      return $error_msg;
    }
    $row1 = null;

    // 部屋情報の整合性確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT user_id, room_password, authority FROM rooms WHERE id = :id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $room_id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row2 = $sth->fetch(PDO::FETCH_ASSOC); //roomのデータ
    $sql = null;
    $sth = null;
    if (empty($row2)) {
      $row2 = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //postのroom_id
      $room_password = null; //postのroom_password
      $comment = null; //postのcomment  
      $error_msg = '存在不正な値です。';
      return $error_msg;
    }
    // パスワードが正しいか確認
    if ($room_password !== $row2['room_password']) {
      $row2 = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //postのroom_id
      $room_password = null; //postのroom_password
      $comment = null; //postのcomment  
      $error_msg = 'pass不正な値です。';
      return $error_msg;
    }

    // コメント制限
    if ($row2['authority'] == 1) {
      if ($row2['user_id'] !== $id) {
        $row2 = null;
        $id = null; //ログインユーザーのid
        $mail = null; //ログインユーザーのmail
        $room_id = null; //postのroom_id
        $room_password = null; //postのroom_password
        $comment = null; //postのcomment  
        $error_msg = 'この部屋は作成者のみコメント可能です。';
        return $error_msg;
      }
    }
    else if ($row2['authority'] != 0) {
      $row2 = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //postのroom_id
      $room_password = null; //postのroom_password
      $comment = null; //postのcomment  
      $error_msg = 'この部屋の権限が不正な値です。';
      return $error_msg;
    }

    // コメント作成実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'INSERT INTO users_rooms (user_id, room_id, comment) VALUES (:user_id, :room_id, :comment)';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->bindParam(':room_id', $room_id, PDO::PARAM_INT);
    $sth->bindParam(':comment', $comment, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $error_msg = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $room_id = null; //postのroom_id
    $room_password = null; //postのroom_password
    $comment = null; //postのcomment  
    return $error_msg;
  }

  // コメント削除
  public function deleteComment($session, $get) {
    $error_msg = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $room_id = null; //getのroom_id
    $created_at = null; //getのcreated_at

    if (!empty($session)) {
      $id = (int)$session['id']; 
      $mail = $session['mail']; 

      if (!empty($get)) {
        $room_id = (int)$get['room_id'];
        $created_at = $get['created_at'];
      }
    }

    // ユーザーが存在しているか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, mail FROM users WHERE id = :id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //getのroom_id
      $created_at = null; //getのcreated_at  
      $error_msg = '不正な値です。';
      return $error_msg;
    }
    if ($row['mail'] !== $mail) {
      $row = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $room_id = null; //getのroom_id
      $created_at = null; //getのcreated_at  
      $error_msg = '不正な値です。';
      return $error_msg;
    }
    $row = null;

    // コメント削除実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'DELETE FROM users_rooms WHERE room_id = :room_id AND created_at = :created_at';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':room_id', $room_id, PDO::PARAM_INT);
    $sth->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $error_msg = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $room_id = null; //getのroom_id
    $created_at = null; //getのcreated_at
    return $error_msg;
  }
}
