<?php
session_start();

// ログイン済みか確認(ログインしてない場合ホームへ)
if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ../Users/index.php');
  exit;
}

if (empty($_GET['room_id']) || empty($_GET['created_at'])) {
  header('Location: ./roomsIndex.php');
  exit;
}

require_once(ROOT_PATH .'Controllers/UsersRoomController.php');
$deleteComment = new UsersRoomController();
$error_msg = $deleteComment->commentDelete();

if ($error_msg === null) {
  $url = "./room.php?room_id=".$_GET['room_id'];
  header("Location:".$url);
  exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>コメント削除</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/commentDelete.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1><?= $error_msg ?></h1>
    <p><a href="./roomsIndex.php">チャット一覧へ戻る</a></p>
  </div>
</body>
</html>