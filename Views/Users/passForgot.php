<?php
session_start();

// 既にログイン済みか確認
if (!empty($_SESSION['mail']) && !empty($_SESSION['id'])) {
  header('Location: ./logind.php');
  exit;
}

if(!empty($_POST)){
  $result = null;

  // mail
  if(empty($_POST['mail'])){
    $result['error_msg'] = "メール入力は必須です。";
  }

  $error_msg = "なし";
  // エラーがない場合、確認画面へ移る
  if(empty($result)){
    require_once(ROOT_PATH .'Controllers/UserController.php');
    $pass_forgot = new UserController();
    $result = $pass_forgot->passForgot();

    if($result['ok'] !== null){
      $result['ok'] = null;
      require_once(ROOT_PATH .'Controllers/UserController.php');
      $mail_send = new UserController();
      $error_msg = $mail_send->mailSend();
      // var_dump($error_msg);
    }
    $result['ok'] = null;
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>パスワードの再設定</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/signup.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>パスワードの再設定</h1>
    <p>パスワードの再設定が必要になります。</p>
    <p>再設定を行うユーザー情報に、登録されているメールアドレスへ、<br>
    パスワードの再設定についての案内メールをお送りします。</p>
    <form method="post" action="./passForgot.php">
      <table>
        <tr>
          <th><label for="mail">登録しているメールアドレス</label></th>
          <td><input type="mail" id="mail" name="mail" placeholder="登録しているメールアドレスを入力"></td>
        </tr>
      </table>
      <p class="red">
        <?php
        if(isset($result['error_msg'])){
          echo $result['error_msg'];
        }
        ?>
      </p>
      <button type="submit" class="button_design">送信する</button>
    </form>
    <button class="button_design" onclick="location.href='./index.php'">ホームへ戻る</button>
  </div>
</body>
</html>