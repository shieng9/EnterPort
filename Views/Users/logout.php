<?php
session_start();

$output = '';
if(!empty($_SESSION["mail"])){
  $output = '<h1>Logoutしました。</h1>';
}
else{
  $output = '<h1>SessionがTimeoutしました。</h1>';
}
//セッション変数のクリア
$_SESSION = array();
//セッションクッキーも削除
if(ini_get("session.use_cookies")){
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
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
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/logout.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <?= $output?>
    <p><a href="./index.php">ホームへ戻る</a></p>
  </div>
</body>
</html>