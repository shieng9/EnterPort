<?php
session_start();

// 既にログイン済みか確認
if (!empty($_SESSION['mail']) && !empty($_SESSION['id'])) {
  header('Location: ./logind.php');
  exit;
}

if(!empty($_POST)){
  $error_msg = array();

  // mail
  if(empty($_POST['mail'])){
    $error_msg['mail'] = "*名前は必須項目です";
  }
  
  // password
  if(empty($_POST['password'])){
    $error_msg['password'] = "*パスワードは必須項目です";
  }
    
  // エラーがない場合、確認画面へ移る
  if(empty($error_msg)){
    require_once(ROOT_PATH .'Controllers/UserController.php');
    $user_signup = new UserController();
    $error_msg = $user_signup->signUp();
    // var_dump($error_msg);

    if($error_msg === null){
      header('Location: ./ok_signup.php');
      exit;
    }
  }
}

//セッションクリア
session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>signup</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/signup.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>ユーザー登録（無料）</h1>
    <p>メールアドレスとパスワードを設定してください</p>
    <p>パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上16文字以下で設定してください。</p>
    <form method="post" action="./signup.php">
      <table>
        <tr>
          <th><label for="mail">メールアドレス</label></th>
          <td><input type="mail" id="mail" name="mail" maxlength='100' placeholder="メールアドレスを設定"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['mail'])){
              echo $error_msg['mail'];
            }
            ?>
          </td>
        </tr>
        <tr id="distance"></tr>
        <tr>
          <th><label for="password">パスワード</label></th>
          <td><input type="password" id="password" name="password" maxlength='16' placeholder="パスワードを設定"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['password'])){
              echo $error_msg['password'];
            }
            ?>
          </td>
        </tr>
      </table>
      <button type="submit" class="button_design">登録する</button>
    </form>
    <button class="button_design" onclick="location.href='./index.php'">ホームへ戻る</button>
  </div>
</body>
</html>