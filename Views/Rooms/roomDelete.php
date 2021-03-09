<?php
session_start();

if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ../Users/index.php');
  exit;
}

require_once(ROOT_PATH .'Controllers/RoomController.php');
$delete = new RoomController();
$error_msg = $delete->roomDelete();

$output = '';
if ($error_msg === null) {
  $output = '<h1>チャット部屋を削除しました。</h1>';
}
else {
  $output = '<h1>'.$error_msg.'</h1>';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>部屋削除</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/roomDelete.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <?= $output ?>
    <p><a href="./roomsIndex.php">チャット一覧へ戻る</a></p>
  </div>
</body>
</html>