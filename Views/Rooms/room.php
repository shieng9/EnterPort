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

// チャット部屋への入室可能判定 -----------------------------------
require_once(ROOT_PATH .'Controllers/RoomController.php');
$room = new RoomController();
$results = $room->roomPageEnter();
$room = null;
// var_dump($results['error_msg']);

if (empty($results['confi']) || empty($results['room_data'])) {
  $results = null;
  header('Location: ./roomsIndex.php');
  exit;
}

if (!empty($results['error_msg'])) {
  $results = null;
  header('Location: ./roomsIndex.php');
  exit;
}
// ----------------------------------------------------------------
$room_data_json = json_encode($results['room_data']); //JSONエンコード

if (!empty($_POST['comment'])) {
  require_once(ROOT_PATH .'Controllers/UsersRoomController.php');
  $addComment = new UsersRoomController();
  $error_msg = $addComment->roomAddComment();
  $_POST = null;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>チャット部屋一覧</title>
  <link rel="stylesheet" type="text/css" href="/css/reset.css?<?php echo date('Ymd-His'); ?>">
  <link rel="stylesheet" type="text/css" href="/css/room/room.css?<?php echo date('Ymd-His'); ?>">
</head>
<body>
  <header>
    <div>
      <p>ユーザーID： <?= isset($_SESSION['id']) ? h($_SESSION['id']) : ''; ?></p>
      <p><a href='../Users/edit.php'>ユーザー設定変更</a></p>
      <a href="../Users/logout.php">ログアウト</a>
      <div id="btn_area">
        <button class="button_design" onclick="location.href='./roomsIndex.php'">＜＜ チャット一覧へ戻る</button>
      </div>
    </div>
  </header>
  <div id="container">
    <h1>チャット部屋</h1>
    <div id="container2">
      <div id="room_content">
        <h1>題名: <?= isset($results['room_data']['title']) ? h($results['room_data']['title']) : ''; ?></h1>
        <p>内容:</p>
        <p><?= isset($results['room_data']['content']) ? nl2br(h($results['room_data']['content'])) : ''; ?></p>
      </div>
      <div id="search_area">
        <input type="text" id="words_search" placeholder="検索" />
        <button id="words_search_btn" ><label for="words_search">🔎</label></button>
      </div>
    </div>
  
    <section id="comments_area"></section>
  
    <?php if ($results['room_data']['authority'] == 0) { ?>
      <div id="comment_send">
        <form method="post" action="./room.php?room_id=<?= isset($results['room_data']['id']) ? h($results['room_data']['id']) : ''; ?>">
          <input type="hidden" name="room_id" value="<?= isset($results['room_data']['id']) ? h($results['room_data']['id']) : ''; ?>">
          <input type="hidden" name="room_password" value="<?= isset($results['room_data']['room_password']) ? h($results['room_data']['room_password']) : ''; ?>">
          <!-- <p><label for="comment">コメント</label></p> -->
          <p class="red"><?= isset($error_msg) ? h($error_msg) : ''; ?></p>
          <textarea name="comment" id="comment" cols="60" rows="5" placeholder="コメントを入力（255文字以内）"></textarea>
          <br>
          <button type="submit" class="button_design">コメント送信</button>
        </form>
      </div>
    <?php } else if ($results['room_data']['authority'] == 1 && $results['room_data']['user_id'] == $_SESSION['id']) { ?>
      <div id="comment_send">
        <form method="post" action="./room.php?room_id=<?= isset($results['room_data']['id']) ? h($results['room_data']['id']) : ''; ?>">
          <input type="hidden" name="room_id" value="<?= isset($results['room_data']['id']) ? h($results['room_data']['id']) : ''; ?>">
          <input type="hidden" name="room_password" value="<?= isset($results['room_data']['room_password']) ? h($results['room_data']['room_password']) : ''; ?>">
          <!-- <p><label for="comment">コメント</label></p> -->
          <p class="red"><?= isset($error_msg) ? h($error_msg) : ''; ?></p>
          <textarea name="comment" id="comment" cols="60" rows="5" placeholder="コメントを入力（255文字以内）"></textarea>
          <br>
          <button type="submit" class="button_design">コメント送信</button>
        </form>
      </div>
    <?php } else { ?>
      <div id="comment_send">
        <p>この部屋ではコメント出来ません</p>
      </div>
    <?php } ?>
  </div>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>
    const params = JSON.parse('<?php echo $room_data_json; ?>');  //JSONデコード
    const sessionId = <?php echo $_SESSION['id']; ?>;
    $(function(){
      commentAjax();
      // setInterval(commentAjax, 10000);
      function commentAjax () {
        $.ajax({
          url: "/Server/UserRoomConnect.php", //送信先
          type: "POST", //送信方法
          datatype: "json", //受け取りデータの種類
          data:{
            id: params['id'],
            room_password: params['room_password']
          }
        })
        // Ajax通信が成功した時
        .then( function(data) {
          console.log(data);
          console.log(data.length);
          console.log(sessionId);

          const comments_area = document.getElementById("comments_area");
          while(comments_area.firstChild){
            comments_area.removeChild(comments_area.firstChild);
          }
          for (let commentNum = 0; commentNum <= data.length-1; commentNum++) {
            let comment_area = $('<div>', { "class" : "comment_area"});
            let comment_area_div = $('<div>', { "class" : "comment_area_div"});
            let $comme_num = $('<span />', {"class": 'comment_num'}).append((commentNum + 1) + ".  ");
            comment_area_div.append($comme_num);
            // comment_area.append($('<span>', { "html" : (commentNum + 1) + ".  " }));
            let $comme_date = $('<span />', {"class": 'comment_date'}).append("日時: " + (data[commentNum]['created_at'] ? data[commentNum]['created_at'] : ""));
            comment_area_div.append($comme_date);
            // comment_area.append($('<span>', { "html" : "日時: " + data[commentNum]['created_at'] ? data[commentNum]['created_at'] : ""}));
            let $comment_user_id = $('<span />', {"class": 'comment_user_id'}).append("ユーザーID: " + (data[commentNum]['user_id'] ? data[commentNum]['user_id'] : ""));
            comment_area_div.append($comment_user_id);
            // comment_area.append($('<span>', { "html" : "ユーザーID: " + data[commentNum]['user_id'] ? data[commentNum]['user_id'] : "" }));
            comment_area.append(comment_area_div);
            let $comme = $('<p />', {"class": 'comment_text'}).append(data[commentNum]['comment'] ? data[commentNum]['comment'] : "");
            comment_area.append($comme);
            // comment_area.append($('<p>', { "class" : "comment_text"}, { "html" : data[commentNum]['comment'] ? data[commentNum]['comment'] : ""}));
            if(data[commentNum]['user_id'] == sessionId) {
              let delete_comment = $('<p>', { "class" : "delete_comment"});
              // delete_comment.append($('<a>', { "href" : "./commentDelete.php?room_id="+(data[commentNum]['room_id'] ? data[commentNum]['room_id'] : "")+"&created_at="+(data[commentNum]['created_at'] ? data[commentNum]['created_at'] : "")}, { "onClick" : "return conf();" }, { "html" : "コメント削除" }));
              let $comme_dele = $('<a />', {"href": "./commentDelete.php?room_id="+(data[commentNum]['room_id'] ? data[commentNum]['room_id'] : "")+"&created_at="+(data[commentNum]['created_at'] ? data[commentNum]['created_at'] : "")}).append("コメント削除");
              $comme_dele = $comme_dele.attr("onClick", '"return conf();"');
              delete_comment.append($comme_dele);
              comment_area.append(delete_comment);
            }
            $('#comments_area').append(comment_area);
          }
          console.log('通信成功');
        },
        // Ajax通信が失敗した時
        function(data) {
          console.log('通信失敗');
          console.log(data);
        });
      };
    }); //END
  </script>
  <script>
    function conf(){
      if(!window.confirm('本当に削除しますか？')){
        window.alert('キャンセルされました'); 
        return false;
      }
      return true;
    }
  </script>
  <script src="/js/search2.js"></script>
</body>
</html>