<?php
session_start();

// 既にログイン済みか確認
// if (!empty($_SESSION['mail']) && !empty($_SESSION['id'])) {
//   header('Location: ./logind.php');
//   exit;
// }

// if (empty($_GET['reset_token'])) {
//   header('Location: ./passForgot.php');
//   exit;
// }


if(!empty($_POST)){
  $error_msg = null;

  // mail
  if(empty($_POST['mail'])){
    $error_msg['mail'] = "*メール入力は必須です。";
  }

  // password
  if(empty($_POST['re_password'])){
    $error_msg['re_password'] = "*パスワード入力は必須です。";
  }
    
  // エラーがない場合、確認画面へ移る
  if(empty($error_msg)){
    require_once(ROOT_PATH .'Controllers/UserController.php');
    $pass_reset = new UserController();
    $error_msg = $pass_reset->passReset();

    if($error_msg === null){
      header('Location: ./ok_edit.php');
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>パスワードリセット</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/login.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>パスワードの再設定</h1>
    <p>パスワードの再設定ページです。</p>
    <p>パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。</p>
    <p>
      <?php
      if(isset($error_msg['data'])){
        echo $error_msg['data'];
      }
      ?>
    </p>
    <form method="post" action="./passReset.php?reset_token=<?= $_GET['reset_token'] ?>">
      <table>
        <tr>
          <th><label for="mail">登録しているメールアドレス</label></th>
          <td><input type="mail" id="mail" name="mail" placeholder="登録しているメールアドレスを入力"></td>
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
          <th><label for="re_password">新しいパスワード</label></th>
          <td><input type="password" id="re_password" name="re_password" placeholder="新しいパスワードを入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['re_password'])){
              echo $error_msg['re_password'];
            }
            ?>
          </td>
        </tr>
      </table>
      <button type="submit" class="button_design">変更する</button>
    </form>
    <button class="button_design" onclick="location.href='./index.php'">ホームへ戻る</button>
  </div>
</body>
</html>