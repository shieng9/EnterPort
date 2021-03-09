<?php
session_start();

if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ./index.php');
  exit;
}

require_once(ROOT_PATH .'Controllers/UserController.php');
$delete = new UserController();
$error_msg = $delete->delete();

//セッションクリア
session_destroy();

$output = '';
if ($error_msg === null) {
  $output = '<h1>ユーザー情報を削除しました。</h1>';
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
  <title>ユーザー情報削除</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/ok_edit.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <?= $output ?>
  <p><a href="./index.php">ホームへ戻る</a></p>
</body>
</html>