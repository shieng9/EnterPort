<?php
session_start();

// 既にログイン済みか確認
if (!empty($_SESSION['mail']) && !empty($_SESSION['id'])) {
  header('Location: ./logind.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー情報変更完了</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/ok_edit.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>ユーザー情報の変更を完了しました。</h1>
    <p><a href="./login.php">ログイン画面へ</a></p>
    <p><a href="./index.php">ホームへ戻る</a></p>
  </div>
</body>
</html>