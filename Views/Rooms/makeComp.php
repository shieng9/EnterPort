<?php
session_start();

// ログイン済みか確認(ログインしてない場合ホームへ)
if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ../Users/index.php');
  exit;
}

if (empty($_SESSION['user_id']) || empty($_SESSION['room_password'])) {
  header('Location: ./roomMake.php');
  exit;
}

function h($str){
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

require_once(ROOT_PATH .'Controllers/RoomController.php');
$make_comp = new RoomController();
$roomData = $make_comp->makeComp();
$_SESSION['user_id'] = null;
$_SESSION['room_password'] = null;
$error_msg = null;
$row = null;
$error_msg = $roomData['error_msg'];
$row = $roomData['row'];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ユーザー情報変更完了</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/makeComp.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <div>
      <?php if ($error_msg !== null) { ?>
        <h1><?= $error_msg ?></h1>
      <?php } else { ?>
        <h1>チャット部屋の作成が完了しました。</h1>
        <div>
          <table>
            <tr>
              <th></th>
              <td>ルームID: <?= isset($row['id']) ? h($row['id']) : ''; ?></td>
            </tr>
            <tr class="distance2"></tr>
            <tr>
              <th></th>
              <td>チャット部屋のパスワード: <?= isset($row['room_password']) ? h($row['room_password']) : ''; ?></td>
            </tr>
            <tr class="distance2"></tr>
            <tr>
              <th></th>
              <td>題名: <?= isset($row['title']) ? h($row['title']) : ''; ?></td>
            </tr>
            <tr class="distance2"></tr>
            <tr>
              <th></th>
              <td>内容:</td>
            </tr>
            <tr>
              <th></th>
              <td><?= isset($row['content']) ? nl2br(h($row['content'])) : ''; ?></td>
            </tr>
            <tr class="distance2"></tr>
            <tr>
              <th></th>
              <td>コメント権限: 
                <?php if($row['authority'] === 0) { ?>
                  <span>なし</span>
                <?php } else if($row['authority'] === 1) { ?>
                  <span>自分のみ可能</span>
                <?php } else {echo '';} ?>
              </td>
            </tr>
            <tr class="distance2"></tr>
            <tr>
              <th></th>
              <td>作成日時: <?= isset($row['created_at']) ? h($row['created_at']) : ''; ?></td>
            </tr>
          </table>
          <div id="btn_area">
            <form method="post" action="./room.php?room_id=<?= isset($row['id']) ? h($row['id']) : ''; ?>">
              <input type="hidden" name="room_id" value="<?= isset($row['id']) ? h($row['id']) : ''; ?>">
              <input type="hidden" name="room_password" value="<?= isset($row['room_password']) ? h($row['room_password']) : ''; ?>">
              <button type="submit" class="button_design">作ったチャット部屋ページへ</button>
            </form>
          </div>
        </div>
      <?php } ?>
      <button class="button_design" onclick="location.href='./roomsIndex.php'">チャット一覧へ戻る</button>
    </div>
  </div>
</body>
</html>