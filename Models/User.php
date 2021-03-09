<?php
require_once(ROOT_PATH .'/Models/Db.php');

class User extends Db {
  // private $table = 'users';
  public function __construct($dbh = null) {
    parent::__construct($dbh);
  }

  // 新規登録
  public function signUpUser($post) {
    $error_msg = array();
    $mail = null; //mail
    $password = null; //password

    if(!empty($post)) {
      $mail = $post['mail']; //mail
      $password = $post['password']; //password
    }
    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $error_msg['mail'] = '*入力された値が不正です。';
      return $error_msg;
    }
    // 既に登録済のmailか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM users WHERE mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    if (!empty($sth->fetch(PDO::FETCH_ASSOC))) {
      $sql = null;
      $sth = null;  
      $error_msg['mail'] = '*このメールアドレスは既に登録済みです。';
      return $error_msg;
    }
    $sql = null;
    $sth = null;

    //パスワードの正規表現
    if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $password)) {
      $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
      $error_msg['password'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
      return $error_msg;
    }

    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'INSERT INTO users (mail, password) VALUES (:mail, :password)';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->bindParam(':password', $password, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット

    $sql = null;
    $sth = null;
    $mail = null; //mail
    $password = null; //password
    $error_msg = null;
    return $error_msg;
  }

  // ログイン
  public function loginUser($post) {
    $error_msg = array();
    $mail = null; //mail
    $password = null; //password  

    if(!empty($post)) {
      $mail = $post['mail']; //mail
      $password = $post['password']; //password  
    }

    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $mail = null; //mail
      $password = null; //password    
      $error_msg['mail'] = '*入力された値が不正です。';
      return $error_msg;
    }

    //パスワードの正規表現
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $password)) {
      $mail = null; //mail
      $password = null; //password    
      $error_msg['password'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上を入力してください。';
      return $error_msg;
    }

    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM users WHERE mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;

    if (empty($row['password'])) {
      $row = null;
      $mail = null; //mail
      $password = null; //password    
      $error_msg['mail'] = '*メールアドレスが間違っています。';
      return $error_msg;
    }

    // パスワードが正しいか確認
    if (password_verify($password, $row['password'])) {
      session_regenerate_id(true); //session_idを新しく生成し、置き換える
      $_SESSION['id'] = $row['id'];
      $_SESSION['mail'] = $row['mail'];
      $_SESSION['user_authority'] = $row['user_authority'];

      $row = null;
      $mail = null; //mail
      $password = null; //password  
      $error_msg = null;
      return $error_msg;
    }
    else{
      $row = null;
      $mail = null; //mail
      $password = null; //password  
      $error_msg['password'] = '*パスワードが間違っています。';
      return $error_msg;
    }
  }
  
  // ユーザー情報変更
  public function editUser($post) {
    $error_msg = array();
    $id = null; //id
    $mail0 = null; //現在のmail
    $password0 = null; //現在のpassword
    $mail = null; //変更後のmail
    $password = null; //変更後のpassword

    if(!empty($post)) {
      $id = (int)$post['id']; //id
      $mail0 = $post['mail0']; //mail0
      $password0 = $post['password0']; //password0
      $mail = $post['mail']; //mail
      $password = $post['password']; //password
    }

    //mail0の正規表現
    if (!filter_var($mail0, FILTER_VALIDATE_EMAIL)) {
      $error_msg['mail0'] = '*入力された値が不正です。';
      return $error_msg;
    }
    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $error_msg['mail'] = '*入力された値が不正です。';
      return $error_msg;
    }
    //パスワード0の正規表現
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $password0)) {
      $error_msg['password0'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上を入力してください。';
      return $error_msg;
    }
    //パスワードの正規表現
    if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $password)) {
      $password = password_hash($password, PASSWORD_DEFAULT);
    } else {
      $error_msg['password'] = '*パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
      return $error_msg;
    }

    // 既存のデータがあるか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM users WHERE mail = :mail0';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail0', $mail0, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row['password'])) {
      $error_msg['mail0'] = '*メールアドレスが間違っています。';
      return $error_msg;
    }
    if (!password_verify($password0, $row['password'])) {
      $error_msg['password0'] = '*パスワードが間違っています。';
      return $error_msg;
    }
    $row = null;

    // 変更後のmailが既に登録済のmailか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM users WHERE mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    if (!empty($sth->fetch(PDO::FETCH_ASSOC))) {
      // 変更前と同じならok
      if ($mail !== $mail0) {
        $sql = null;
        $sth = null;
        $error_msg['mail'] = '*このメールアドレスは既に登録済みです。';
        return $error_msg;  
      }
    }
    $sql = null;
    $sth = null;

    // 変更実行（usersテーブル）
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'UPDATE users SET mail = :mail1, password = :password WHERE id = :id AND mail = :mail0';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->bindParam(':mail0', $mail0, PDO::PARAM_STR);
    $sth->bindParam(':mail1', $mail, PDO::PARAM_STR);
    $sth->bindParam(':password', $password, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    // 変更実行（roomsテーブル）
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'UPDATE rooms SET user_mail = :user_mail1 WHERE user_id = :user_id AND user_mail = :user_mail0';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail0', $mail0, PDO::PARAM_STR);
    $sth->bindParam(':user_mail1', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $id = null; //id
    $mail0 = null; //mail0
    $password0 = null; //password0
    $mail = null; //mail
    $password = null; //password
    $error_msg = null;
    return $error_msg;  
  }

  // ユーザー情報削除
  public function deleteUser($session, $get, $post) {
    $error_msg = null;
    $id = null; //id
    $mail = null; //mail
    $get_id = null; //getのid
    $password = null; //postのpassword

    if(!empty($session)) {
      $id = (int)$session['id']; //id
      $mail = $session['mail']; //mail

      if(!empty($get)) {
        $get_id = (int)$get; //getのid
      }
      if(!empty($post)) {
        $password = $post; //postのpassword
      }
    }

    //sessionとgetが合ってるか
    if ($get_id !== $id) {
      $id = null; //id
      $mail = null; //mail
      $get_id = null; //getのid
      $password = null; //postのpassword  
      $error_msg = '値が不正です。';
      return $error_msg;
    }

    //パスワードの正規表現
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $password)) {
      $id = null; //id
      $mail = null; //mail
      $get_id = null; //getのid
      $password = null; //postのpassword  
      $error_msg = 'パスワードに入力された値が不正です。';
      return $error_msg;
    }

    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM users WHERE id = :id AND mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $id = null; //id
      $mail = null; //mail
      $get_id = null; //getのid
      $password = null; //postのpassword  
      $error_msg = '値が不正です。';
      return $error_msg;
    }
    // パスワードが正しいか確認
    if (!password_verify($password, $row['password'])) {
      $row = null;
      $id = null; //id
      $mail = null; //mail
      $get_id = null; //getのid
      $password = null; //postのpassword  
      $error_msg = 'パスワードが正しくありません。';
      return $error_msg;
    }
    $row = null;

    // ユーザーが作成した部屋削除実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'DELETE FROM rooms WHERE user_id = :user_id AND user_mail = :user_mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':user_id', $id, PDO::PARAM_INT);
    $sth->bindParam(':user_mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;
    
    // ユーザー情報削除実行
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'DELETE FROM users WHERE id = :id AND mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':id', $id, PDO::PARAM_INT);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $id = null; //id
    $mail = null; //mail
    $get_id = null; //getのid
    $password = null; //postのpassword  
    $error_msg = null;
    return $error_msg;
  }

  // パスワードリセット準備（メール送信のため確認）
  public function forgotPass($post) {
    $result = array();
    $result['error_msg'] = null;
    $result['ok'] = null;
    $mail = null; //postのmail

    if(!empty($post)) {
      $mail = $post; //mail
    }

    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $mail = null; //postのmail
      $result['error_msg'] = '入力された値が不正です。';
      return $result;
    }

    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT id, created_at FROM users WHERE mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $mail = null; //mail
      $result['error_msg'] = 'メールを送信しました。';
      return $result;
    }

    $row = null;
    $mail = null; //mail
    $result['error_msg'] = 'メールを送信しました。';
    $result['ok'] = '送信ok';
    return $result;
  }

  // パスワードリセット準備（メール送信）
  public function sendMail($post) {
    $error_msg = null;
    $mail = null; //postのmail

    if(!empty($post)) {
      $mail = $post; //mail
    }

    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $mail = null; //postのmail
      $error_msg = '入力された値が不正です。';
      return $error_msg;
    }

    // ランダムな文字列生成
    $passResetToken = md5(uniqid(rand(),true));

    // 文字列と送信先メールを登録
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'INSERT INTO pass_reset (mail, reset_token) VALUES (:mail, :reset_token)';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->bindParam(':reset_token', $passResetToken, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    // メール送信実行
    mb_language("Japanese");
    mb_internal_encoding("UTF-8");
    $to = $mail;
    $from_mail = ""; //←送信元のメールアドレスを入力
    $title = '【EnterPort】ユーザーパスワード再設定のご案内';
    $message = 'パスワード再設定ページURL: ';
    $message .= 'http://localhost/Users/passReset.php?reset_token='.$passResetToken;
    $headers = "From: $from_mail";
    $from = "$from_mail";
    $pfrom = "-f $from";
    // mb_send_mail($to, $title, $message, $headers);
    if (mb_send_mail($to, $title, $message, $headers, $pfrom)) {
      $mail = null; //postのmail
      $error_msg = "送信成功";
      return $error_msg;
    }
    else {
      $mail = null; //postのmail
      $error_msg = 'メール送信失敗です。';
      return $error_msg;
    }
  }

  // パスワードリセット（メール送信後）
  public function resetPass($get, $post) {
    $error_msg = null;
    $reset_token = null; //getのreset_token
    $mail = null; //postのmail
    $re_password = null; //postのre_password

    if(!empty($get)) {
      $reset_token = $get; //getのreset_token

      if(!empty($post)) {
        $mail = $post['mail']; //postのmail
        $re_password = $post['re_password']; //postのre_password
      }
    }

    //mailの正規表現
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      $reset_token = null; //getのreset_token
      $mail = null; //postのmail
      $re_password = null; //postのre_password
      $error_msg['mail'] = 'メールアドレスに入力された値が不正です。';
      return $error_msg;
    }

    //パスワードの正規表現
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,16}+\z/i', $re_password)) {
      $reset_token = null; //getのreset_token
      $mail = null; //postのmail
      $re_password = null; //postのre_password
      $error_msg = 'パスワードに入力された値が不正です。';
      return $error_msg;
    }

    // pass_resetに保存してあるか確認
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'SELECT * FROM pass_reset WHERE reset_token = :reset_token';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':reset_token', $reset_token, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $sql = null;
    $sth = null;
    if (empty($row)) {
      $row = null;
      $reset_token = null; //getのreset_token
      $mail = null; //postのmail
      $re_password = null; //postのre_password
      $error_msg['data'] = '値が不正です。';
      return $error_msg;
    }
    // postのmailと同じか
    if ($row['mail'] !== $mail) {
      $row = null;
      $reset_token = null; //getのreset_token
      $mail = null; //postのmail
      $re_password = null; //postのre_password
      $error_msg['data'] = '値が不正です。';
      return $error_msg;
    }
    // メール送信から一分たっているか確認
    $limitTime = date("Y-m-d H:i:s", strtotime("-1 minute"));
    if (strtotime($row['created_at']) < strtotime($limitTime)) {
      $row = null;
      $reset_token = null; //getのreset_token
      $mail = null; //postのmail
      $re_password = null; //postのre_password
      $error_msg['data'] = '値が不正です。';
      return $error_msg;
    }
    $row = null;

    // パスワードをハッシュ化
    $re_password = password_hash($re_password, PASSWORD_DEFAULT);

    // パスワード変更実行（usersテーブル）
    $this->dbh->beginTransaction(); //オートコミットをオフ
    $sql = 'UPDATE users SET password = :password WHERE mail = :mail';
    $sth = $this->dbh->prepare($sql);
    $sth->bindParam(':mail', $mail, PDO::PARAM_STR);
    $sth->bindParam(':password', $re_password, PDO::PARAM_STR);
    $sth->execute();
    $this->dbh->commit(); //変更をコミット
    $sql = null;
    $sth = null;

    $reset_token = null; //getのreset_token
    $mail = null; //postのmail
    $re_password = null; //postのre_password
    $error_msg = null;
    return $error_msg;
  }
}
