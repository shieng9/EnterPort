<?php
session_start();

// ログイン済みか確認(ログインしてない場合ホームへ)
if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ../Users/index.php');
  exit;
}

function h($str){
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

require_once(ROOT_PATH .'Controllers/RoomController.php');
$rooms_index = new RoomController();
$roomsData = $rooms_index->roomsIndex();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>チャット部屋一覧</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/roomsIndex.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <header>
    <p>ユーザーID： <?= isset($_SESSION['id']) ? h($_SESSION['id']) : ''; ?></p>
    <p><a href='../Users/edit.php'>ユーザー設定</a></p>
    <a href="../Users/logout.php">ログアウト</a>
  </header>
  <div id="container">
    <h1>チャット部屋一覧</h1>
    <div id="container2">
      <div>
        <p><a href='./roomMake.php'>チャット部屋作成はこちら</a></p>
      </div>
      <div id="search_area">
        <input type="text" id="words_search" placeholder="検索" />
        <button id="words_search_btn" ><label for="words_search">🔎</label></button>
      </div>
    </div>
    <section id="rooms_area">
      <?php foreach($roomsData['rooms']['all'] as $room): ?>
        <div class="room_area">
          <div>
            <p>ルームID: <?= isset($room['id']) ? h($room['id']) : ''; ?></p>
            <p>作成日: <?= isset($room['created_at']) ? h($room['created_at']) : ''; ?></p>
            <p>題名: <?= isset($room['title']) ? h($room['title']) : ''; ?></p>
            <p>内容（先頭の一部のみ）: </p>
            <p><?= isset($room['content']) ? substr(nl2br(h($room['content'])), 0, 30) : ''; ?></p>
            <?php if ($room['user_id'] == $_SESSION['id']) { ?>
              <form method="post" action="./room.php?room_id=<?= isset($room['id']) ? h($room['id']) : ''; ?>">
                <input type="hidden" name="room_id" value="<?= isset($room['id']) ? h($room['id']) : ''; ?>">
                <input type="hidden" id="room_password" name="room_password" value="<?= isset($roomsData['rooms']['mypass']) ? h($roomsData['rooms']['mypass']) : ''; ?>">
                <button type="submit" class="button_design">自分のチャット部屋に入る</button>
              </form>
              <p id="room_delete_p"><a href="./roomDelete.php?id=<?= isset($room['id']) ? h($room['id']) : ''; ?>" onClick="return conf();">この部屋を削除</a></p>
            <?php } else { ?>
              <form method="post" action="./room.php?room_id=<?= isset($room['id']) ? h($room['id']) : ''; ?>">
                <input type="hidden" name="room_id" value="<?= isset($room['id']) ? h($room['id']) : ''; ?>">
                <input type="password" class="room_password" name="room_password" placeholder="この部屋のパスワードを入力">
                <button type="submit" class="button_design">チャット部屋に入る</button>
              </form>
            <?php } ?>
          </div>
        </div>
      <?php endforeach; ?>
      <div id="paging">
        <?php
        for($i = 0; $i<=$roomsData['pages']; $i++) {
          if(isset($_GET['page']) && $_GET['page'] == $i) {
            echo "<button>".($i+1)."</button>";
          }
          else {
            echo "<button onclick=".'"location.href='."'"."./roomsIndex.php?page=".$i."'".'">'.($i+1)."</button>";
          }
        }
        ?>
      </div>
    </section>
  </div>
  <script>
    function conf(){
      if(!window.confirm('本当に削除しますか？')){
        window.alert('キャンセルされました'); 
        return false;
      }
      return true;
    }
  </script>
  <script src="/js/search.js"></script>
</body>
</html>