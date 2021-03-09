<?php
ini_set('display_errors',1);

$dbh = null;
try {
  $dbh = new PDO(
    'mysql:dbname='.'enterport'.
    ';host='.'localhost', 'root', 'root'
  );// 接続成功
} catch(PDOException $e) {
  echo "接続失敗: " . $e->getMessage() . "\n";
  exit;
}

$room_id = null;
$room_password = null;

if (!empty($_POST['id']) && !empty($_POST['room_password'])) {
  $room_id = (int)$_POST['id']; //部屋のid
  $room_password = $_POST['room_password']; //部屋のパス
}

$row = null;
// postデータと部屋が正しいか確認
$dbh->beginTransaction(); //オートコミットをオフ
$sql = 'SELECT title, room_password FROM rooms WHERE id = :id';
$sth = $dbh->prepare($sql);
$sth->bindParam(':id', $room_id, PDO::PARAM_INT);
$sth->execute();
$dbh->commit(); //変更をコミット
$row = $sth->fetch(PDO::FETCH_ASSOC); //roomのデータ
$sql = null;
$sth = null;

// パスワードが正しいか確認
if ($room_password != $row['room_password']) {
  $row = null;
  $room_id = null;
  $room_password = null;  
  echo "接続失敗";
  exit;
}

$row = null; //roomデータ削除
$rows = null;
// users_roomsテーブルから、部屋のコメントを取得
$dbh->beginTransaction(); //オートコミットをオフ
$sql = 'SELECT user_id, room_id, comment, created_at FROM users_rooms WHERE room_id = :room_id';
$sth = $dbh->prepare($sql);
$sth->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$sth->execute();
$dbh->commit(); //変更をコミット
$rows = $sth->fetchAll(PDO::FETCH_ASSOC); //部屋のコメントデータ
$sql = null;
$sth = null;

// 攻撃対策
function h($str){
  return htmlspecialchars($str, ENT_QUOTES, "UTF-8");
}

for ($i=0; $i < count($rows); $i++) { 
  $rows[$i]['user_id'] = h($rows[$i]['user_id']);
  $rows[$i]['room_id'] = h($rows[$i]['room_id']);
  $rows[$i]['comment'] = nl2br(h($rows[$i]['comment']));
  $rows[$i]['created_at'] = h($rows[$i]['created_at']);
}
// $rows['id'] = h($rows['id']);
// $rows['user_id'] = h($rows['user_id']);
// $rows['room_id'] = h($rows['room_id']);
// $rows['comment'] = nl2br(h($rows['comment']));
// $rows['created_at'] = h($rows['created_at']);

// $room_id = null;
// $room_password = null;

// //jsonとして出力
// header('Content-type: application/json');
// echo json_encode($rows, JSON_UNESCAPED_UNICODE);

//あらかじめ配列を生成しておき、while文で回します。
// $return_array = [];
// while($rows = $sth->fetch(PDO::FETCH_ASSOC)){
//  $return_array = array(
//   'user_id'=>$rows['user_id'],
//   'comment'=>$rows['comment'],
//   'created_at'=>$rows['created_at']
//  );
// }
// $sql = null;
// $sth = null;

//jsonとして出力
header('Content-type: application/json');
// echo json_encode(['data'=>$memberList],JSON_UNESCAPED_UNICODE);
echo json_encode($rows,JSON_UNESCAPED_UNICODE);
?>