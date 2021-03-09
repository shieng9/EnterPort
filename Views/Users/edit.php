<?php
session_start();

// ログイン済みか確認(ログインしてない場合ホームへ)
if (empty($_SESSION['mail']) || empty($_SESSION['id'])) {
  header('Location: ./index.php');
  exit;
}

if(!empty($_POST)){
  $error_msg = array();

  // id
  if(!is_int((int)$_POST['id'])){
    $error_msg['id'] = "idが不正です";
  }
  
  // mail0
  if(empty($_POST['mail0'])){
    $error_msg['mail0'] = "*現在のメールアドレスは必須項目です";
  }
  
  // mail
  if(empty($_POST['mail'])){
    $error_msg['mail'] = "*変更後のメールアドレスは必須項目です";
  }
  
  // password0
  if(empty($_POST['password0'])){
    $error_msg['password0'] = "*現在のパスワードは必須項目です";
  }
  
  // password
  if(empty($_POST['password'])){
    $error_msg['password'] = "*変更後のパスワードは必須項目です";
  }
    
  // エラーがない場合、確認画面へ移る
  if(empty($error_msg)){
    require_once(ROOT_PATH .'Controllers/UserController.php');
    $user_edit = new UserController();
    $error_msg = $user_edit->editComp();
    var_dump($error_msg);

    if($error_msg === null){
      //セッションクリア
      session_destroy();
      header('Location: ./ok_edit.php');
      exit;
    }
  }
}

function h($str){
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>edit</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/user/edit.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <div id="container">
    <h1>ユーザー情報変更</h1>
    <p>変更後のメールアドレスとパスワードを設定してください</p>
    <p>パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。</p>
    <p class="red">
      <?php
      if(isset($error_msg['id'])){
        echo $error_msg['id'];
      }
      ?>
    </p>
    <form method="post" action="./edit.php">
      <input type="hidden" name="id" value="<?= isset($_SESSION['id']) ? h($_SESSION['id']) : ''; ?>">
      <table>
        <tr>
          <th><label for="mail0">現在のメールアドレス</label></th>
          <td><input type="mail" id="mail0" name="mail0" value="<?= isset($_SESSION['mail']) ? h($_SESSION['mail']) : ''; ?>" placeholder="現在登録しているメールアドレス"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['mail0'])){
              echo $error_msg['mail0'];
            }
            ?>
          </td>
        </tr>
        <tr class="distance2"></tr>
        <tr>
          <th><label for="mail">変更後のメールアドレス</label></th>
          <td><input type="mail" id="mail" name="mail" placeholder="変更後のメールアドレスを入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['mail'])){
              echo $error_msg['mail'];
            }
            ?>
          </td>
        </tr>
        <tr id="distance"></tr>
        <tr>
          <th><label for="password0">現在のパスワード</label></th>
          <td><input type="password" id="password0" name="password0" placeholder="現在登録しているパスワードを入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['password0'])){
              echo $error_msg['password0'];
            }
            ?>
          </td>
        </tr>
        <tr class="distance2"></tr>
        <tr>
          <th><label for="password">変更後のパスワード</label></th>
          <td><input type="password" id="password" name="password" placeholder="変更後のパスワードを入力"></td>
        </tr>
        <tr>
          <th></th>
          <td class="red">
            <?php
            if(isset($error_msg['password'])){
              echo $error_msg['password'];
            }
            ?>
          </td>
        </tr>
      </table>
      <button type="submit" class="button_design" onClick="return conf2();">変更する</button>
    </form>
    <button class="button_design" onclick="location.href='../Rooms/roomsIndex.php'">チャット一覧へ戻る</button>
    <div id="delete_area">
      <h1>⬇︎ユーザー情報の削除はこちら⬇︎</h1>
      <form method="post" action="./delete.php?id=<?= h($_SESSION['id']) ?>">
        <input type="password" name="user_password" placeholder="削除したいデータのパスワードを入力">
        <br>
        <button type="submit" class="button_design" onClick="return conf1();">ユーザー登録を削除</button>
      </form>
    </div>
  </div>
  <script>
    function conf1(){
      if(!window.confirm('本当に削除しますか？')){
        window.alert('キャンセルされました'); 
        return false;
      }
      return true;
    }
    function conf2(){
      if(!window.confirm('本当にユーザー情報を変更しますか？')){
        window.alert('キャンセルされました'); 
        return false;
      }
      return true;
    }
  </script>
</body>
</html>