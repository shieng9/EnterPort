<?php
require_once(ROOT_PATH .'/Models/User.php');

class UserController {
  private $request; // リクエストパラメータ(GET, POST)
  private $User; // Userモデル

  public function __construct() {
    // リクエストパラメータの取得
    $this->request['get'] = $_GET;
    $this->request['post'] = $_POST;
    $this->request['session'] = $_SESSION;

    // モデルオブジェクトの生成
    $this->User = new User();
  }


  // 新規登録
  public function signUp() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $user = $this->User->signUpUser($this->request['post']);
    return $user;
  }

  // ログイン
  public function login() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $user = $this->User->loginUser($this->request['post']);
    return $user;
  }
  
  // ユーザー情報編集
  public function editComp() {
    if(empty($this->request['post']['id'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $user = $this->User->editUser($this->request['post']);
    return $user;
  }
  
  // ユーザー情報削除
  public function delete() {
    if(empty($this->request['get']['id'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $error_msg = $this->User->deleteUser($this->request['session'], $this->request['get']['id'], $this->request['post']['user_password']);
    return $error_msg;
  }

  // パスワードリセット準備（メール送信前）
  public function passForgot() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $pass_forgot = $this->User->forgotPass($this->request['post']['mail']);
    return $pass_forgot;
  }

  // パスワードリセット準備（メール送信）
  public function mailSend() {
    if(empty($this->request['post'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $error_msg = $this->User->sendMail($this->request['post']['mail']);
    return $error_msg;
  }
  
  
  // パスワードリセット実行（メール送信後）
  public function passReset() {
    if(empty($this->request['post']) || empty($this->request['get'])) {
      ini_set('display_errors', 1); //ブラウザにエラー表示
      echo '指定のパラメータが不正です。このページを表示できません。';
      exit;
    }
    $pass_reset = $this->User->resetPass($this->request['get']['reset_token'], $this->request['post']);
    return $pass_reset;
  }
}
?>