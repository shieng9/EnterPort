<?php
require_once(ROOT_PATH .'/Models/Db.php');

class Room extends Db {
  private $table = 'rooms';
  public function __construct($dbh = null) {
    parent::__construct($dbh);
  }

  // チャット一覧を取得
  public function findRooms($session, $page = 0):Array {
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail

    if (!empty($session)) {
      $id = (int)$session['id']; 
      $mail = $session['mail']; 
    }

    // 一覧取得
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, user_id, title, content, created_at FROM rooms';
    $sql .= ' LIMIT 20 OFFSET '.(20 * $page);
    $sth = $this->dbh->prepare($sql);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $result['all'] = $sth->fetchAll(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;

    // 自分の部屋のパス
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT room_password FROM rooms WHERE user_id = :user_id AND user_mail = :user_mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $result['mypass'] = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    
    return $result;
  }

  // チャット部屋の数を取得
  public function countAll():Int {
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT count(*) as count FROM rooms';
    $sth = $this->dbh->prepare($sql);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $count = $sth->fetchColumn();
    $sql = null;
    $sth = null;
    return $count;
  }

  // 部屋削除
  public function deleteRoom($session, $get_id) {
    $error_msg = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $room_id = null; //roomのid

    if (!empty($session)) {
      $id = (int)$session['id']; 
      $mail = $session['mail']; 

      if (!empty($get_id)) {
        $room_id = (int)$get_id; 
      }
    }

    // 部屋が存在しているか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT user_id, user_mail, title FROM rooms WHERE id = :id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $room_id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $error_msg = '*このチャット部屋は存在しません。';
      return $error_msg;
    }

    // 本人確認
    if ($row['user_id'] != $id || $row['user_mail'] !== $mail) {
      $row = null;
      $error_msg = '*本人確認ができませんでした。';
      return $error_msg;
    }

    // 部屋削除実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'DELETE FROM rooms WHERE id = :id AND user_mail = :user_mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $room_id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット

    $sql = null;
    $sth = null;
    $row = null;
    $id = null; //id
    $mail = null; //mail
    $error_msg = null;
    return $error_msg;
  }


  // チャット部屋作成
  public function createRoom($post, $session) {
    $error_msg = array();
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $title = null; //title
    $room_password = null; //room_password
    $content = null; //content
    $authority = null; //authority

    if (!empty($session)) {
      $id = (int)$session['id']; //ログインユーザーのid
      $mail = $session['mail']; //ログインユーザーのmail

      if (!empty($post)) {
        $title = $post['title']; //title
        $room_password = $post['room_password']; //room_password
        $content = $post['content']; //content
        $authority = (int)$post['authority']; //authority
      }
    }

    // authorityの値が不正か
    if ($authority !== 0 && $authority !== 1) {
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $title = null; //title
      $room_password = null; //room_password
      $content = null; //content
      $authority = null; //authority  
      $error_msg['roomData'] = '*コメント制限の値が不正です。';
      return $error_msg;
    }

    // 現在部屋を登録済のユーザーか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, title FROM rooms WHERE user_id = :user_id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    if (!empty($sth->fetch(PDO::FETCH_ASSOC))) {
      $sql = null;
      $sth = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $title = null; //title
      $room_password = null; //room_password
      $content = null; //content
      $authority = null; //authority  
      $error_msg['roomData'] = '*１ユーザーが作成者でいられる部屋は同時に一つまでです。';
      return $error_msg;
    }
    $sql = null;
    $sth = null;

    //パスワードの正規表現
    if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $room_password)) {
      $room_password = password_hash($room_password, PASSWORD_DEFAULT);
    } else {
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $title = null; //title
      $room_password = null; //room_password
      $content = null; //content
      $authority = null; //authority  
      $error_msg['room_password'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
      return $error_msg;
    }

    // 部屋作成実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'INSERT INTO rooms (user_id, user_mail, room_password, title, content, authority) VALUES (:user_id, :user_mail, :room_password, :title, :content, :authority)';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail', $mail, PDO::PARAM_STR);
    $sth->bindParam(':room_password', $room_password, PDO::PARAM_STR);
    $sth->bindParam(':title', $title, PDO::PARAM_STR);
    $sth->bindParam(':content', $content, PDO::PARAM_STR);
    $sth->bindParam(':authority', $authority, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $title = null; //title
    $room_password = null; //room_password
    $content = null; //content
    $authority = null; //authority
    $error_msg = null;
    return $error_msg;
  }
  
  // 部屋作成完了確認
  public function createComp($session) {
    $results = array();
    $results['error_msg'] = null;
    $results['row'] = null;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $user_id = null; //sessionに入れたユーザーid
    $room_password = null; //sessionに入れたroom_password

    if (!empty($session)) {
      $id = (int)$session['id']; //ログインユーザーのid
      $mail = $session['mail']; //ロユーザーのmail
      $user_id = (int)$session['user_id']; //sessionに入れたユーザーid
      $room_password = $session['room_password']; //sessionに入れたroom_password
    }

    if ($id !== $user_id) {
      $results['error_msg'] = '本人確認ができませんでした。';
      return $results;
    }

    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, room_password, title, content, authority, created_at FROM rooms WHERE user_id = :user_id AND user_mail = :user_mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    
    $results['row'] = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;

    // パスワードが正しいか確認
    if (password_verify($room_password, $results['row']['room_password'])) {
      $results['row']['room_password'] = $room_password;
    }
    else {
      $results['row'] = null;
      $results['error_msg'] = '本人確認ができませんでした。';
      return $results;
    }
    
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $user_id = null; //sessionに入れたユーザーid
    $room_password = null; //sessionに入れたroom_password
    return $results;
  }

  // チャット部屋入室
  public function enterRoom($get, $post, $session) {
    $results = array();
    $results['confi'] = null;
    $results['error_msg'] = null;
    $results['room_data'] = null;

    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $get_room_id = null; //room_id(get)
    $room_id = null; //room_id(post)
    $room_password = null; //room_password(post)

    if (!empty($post)) {
      $room_id = (int)$post['room_id']; //room_id
      $room_password = $post['room_password']; //room_password

      if (!empty($get)) {
        $get_room_id = (int)$get['room_id']; //room_id
      }

      if (!empty($session)) {
        $id = (int)$session['id']; //ログインユーザーのid
        $mail = $session['mail']; //ログインユーザーのmail  
      }
    }

    // 作成者が自分か確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM rooms WHERE id = :id';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $get_room_id, PDO::PARAM_INT);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC); //roomのデータ
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $get_room_id = null; //room_id(get)
      $room_id = null; //room_id(post)
      $room_password = null; //room_password(post)  
      $results['error_msg'] = '部屋が存在しません。';
      return $results;
    }

    // 作成者ならok
    if ($row['user_id'] == $id && $row['user_mail'] == $mail) {
      $results['confi'] = '本人確認ok';
      $row['user_mail'] = null;
      $results['room_data'] = $row;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $get_room_id = null; //room_id(get)
      $room_id = null; //room_id(post)
      $room_password = null; //room_password(post)
      return $results;
    }

    // postで送ったidが正しいか確認
    if ($row['id'] != $room_id) {
      $row = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $get_room_id = null; //room_id(get)
      $room_id = null; //room_id(post)
      $room_password = null; //room_password(post)  
      $results['error_msg'] = '部屋が存在しません。';
      return $results;
    }

    // //パスワードの正規表現
    // if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $room_password)) {
    //   $row = null;
    //   $id = null; //ログインユーザーのid
    //   $mail = null; //ログインユーザーのmail
    //   $get_room_id = null; //room_id(get)
    //   $room_id = null; //room_id(post)
    //   $room_password = null; //room_password(post)  
    //   $results['error_msg'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
    //   return $results;
    // }

    // パスワードが正しいか確認
    if ($room_password !== $row['room_password'] && !password_verify($room_password, $row['room_password'])) {
      $row = null;
      $id = null; //ログインユーザーのid
      $mail = null; //ログインユーザーのmail
      $get_room_id = null; //room_id(get)
      $room_id = null; //room_id(post)
      $room_password = null; //room_password(post)  
      $results['error_msg'] = 'ルームIDまたはパスワードが間違っています。';
      return $results;
    }
    
    $results['confi'] = '情報確認ok';
    $row['user_mail'] = null;
    $results['room_data'] = $row;
    $id = null; //ログインユーザーのid
    $mail = null; //ログインユーザーのmail
    $get_room_id = null; //room_id(get)
    $room_id = null; //room_id(post)
    $room_password = null; //room_password(post)
    return $results;
  }  
}
