<?php
session_start();

// 既にログイン済みか確認
if (!empty($_SESSION['mail']) && !empty($_SESSION['id'])) {
  header('Location: ./logind.php');
  exit;
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
  <title>ホーム</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/index.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>EnterPortへようこそ</h1>
    <p>当サービスは、主に動画配信者向けのサービスです。</p>
    <p>配信中または事前に当サービスでチャット等ができる部屋を作る。</p>
    <p>⬇</p>
    <p>配信中にIDとパスワードを公開。</p>
    <p>⬇</p>
    <p>視聴者と一緒に配信をもっと楽しみましょう。</p>
  </div>
  <div class="page_move">
    <div>
      <p><a href='./login.php'>ログインはこちら</a></p>
    </div>
    <div>
      <p><a href='./signup.php'>新規登録はこちら</a></p>
    </div>
  </div>
  <div class="page_move2">
    <p><a href='./passForgot.php'>パスワードを忘れた場合はこちら</a></p>
  </div>
</body>
</html>