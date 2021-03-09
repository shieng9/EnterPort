<?php
session_start();

if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ./index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログイン中</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/logind.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>現在、ログイン中です。</h1>
    <p><a href="../Rooms/roomsIndex.php">チャット一覧へ</a></p>
    <p><a href="./logout.php">ログアウトする</a></p>
  </div>
</body>
</html>