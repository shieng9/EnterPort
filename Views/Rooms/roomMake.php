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

if(!empty($_POST)){
  $error_msg = array();

  // title
  if(empty($_POST['title'])){
    $error_msg['title'] = "*題名は必須項目です";
  }
  
  // room_password
  if(empty($_POST['room_password'])){
    $error_msg['room_password'] = "*パスワードは必須項目です";
  }
    
  // エラーがない場合、完了画面へ移る
  if(empty($error_msg)){
    require_once(ROOT_PATH .'Controllers/RoomController.php');
    $room_make = new RoomController();
    $error_msg = $room_make->roomMake();
    // var_dump($error_msg);

    if($error_msg === null){
      $_SESSION['user_id'] = $_SESSION['id'];
      $_SESSION['room_password'] = $_POST['room_password'];
      header('Location: ./makeComp.php');
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
  <title>チャット部屋作成</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/roomMake.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <header>
    <p>ユーザーID： <?= isset($_SESSION['id']) ? h($_SESSION['id']) : ''; ?></p>
    <p><a href='../Users/edit.php'>ユーザー設定</a></p>
    <a href="../Users/logout.php">ログアウト</a>
  </header>
  <div id="container">
    <h1>チャット部屋作成</h1>
    <p>必要項目を入力してください。</p>
    <p>パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。</p>
    <p><span class="red">*</span>は必須項目です。</p>
    <p class="red">
      <?php
      if(isset($error_msg['roomData'])){
        echo $error_msg['roomData'];
      }
      ?>
    </p>
    <form method="post" action="./roomMake.php">
      <table>
        <tr>
          <th></th>
          <td><label for="title"><span class="red">*</span>チャット部屋の題名（100文字以内）</label></td>
        </tr>
        <tr>
          <th></th>
          <td><input type="text" id="title" name="title" value="<?= isset($_POST['title']) ? h($_POST['title']) : ''; ?>" placeholder="チャット部屋の題名を入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['title'])){
              echo $error_msg['title'];
            }
            ?>
          </td>
        </tr>
        <tr class="distance2"></tr>
        <tr>
          <th></th>
          <td><label for="room_password"><span class="red">*</span>チャット部屋のパスワード</label></td>
        </tr>
        <tr>
          <th></th>
          <td><input type="password" id="room_password" name="room_password" placeholder="チャット部屋のパスワードを入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['room_password'])){
              echo $error_msg['room_password'];
            }
            ?>
          </td>
        </tr>
      </table>
      <div id="check_area">
        <div>
        <p>コメント制限</p>
        <table>
          <tr>
            <th><label for="can_all">制限なし</label></th>
            <td><input type="radio" id="can_all" name="authority" value="0" checked></td>
          </tr>
          <tr>
            <th><label for="can_i">自分のみ可能</label></th>
            <td><input type="radio" id="can_i" name="authority" value="1"></td>
          </tr>
        </table>
        <p class="red">
          <?php
          if(isset($error_msg['authority'])){
            echo $error_msg['authority'];
          }
          ?>
        </p>
        </div>
      </div>
      <table>
        <tr>
          <th></th>
          <td><label for="content">チャット部屋の内容について（255文字以内）</label></td>
        </tr>
        <tr>
          <th></th>
          <td><textarea name="content" id="content" value="<?= isset($_POST['content']) ? nl2br(h($_POST['content'])) : ''; ?>" cols="30" rows="10" placeholder="チャット部屋の内容について"></textarea></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['content'])){
              echo $error_msg['content'];
            }
            ?>
          </td>
        </tr>
      </table>
      <div id="btn_area">
        <button type="submit" class="button_design" onClick="return conf();">作成する</button>
      </div>
    </form>
    <button class="button_design" onclick="location.href='./roomsIndex.php'">チャット一覧へ戻る</button>
  </div>
  <script>
    function conf(){
      if(!window.confirm('チャット部屋を作成しますか？')){
        window.alert('キャンセルされました'); 
        return false;
      }
      return true;
    }
  </script>
</body>
</html>