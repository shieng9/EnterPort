# EnterPort(エンターポート)

PHP自作
名前の由来はentertainerとsupport合わせました。
 
# 概要
動画配信者とその視聴者が集まって楽しめる、
掲示板サイトを作りました。
 
# 使い方
はじめに、ユーザにはユーザー登録を行ってもらいます。
ユーザー登録では、メールアドレスとパスワードを設定してもらいます。

* 動画配信者

部屋を作成しておき、
live配信前や配信中に部屋のIDとパスワードを公開。
部屋の作成時、
部屋でのコメントが自分のみ可能にするか、
入室したユーザー全員可能にするか選択できます。

* 視聴者

公開されたIDから部屋を探し、
パスワードを入力して部屋に入る。

部屋の中のコメントはページ更新せずとも、
誰かが新しいコメントを入力した場合、
10秒ごとに自動で新しいコメントが表示されます。

# 環境
MAMP/MySQL/PHP
 
# データベース
データベース名：enterport
 
* テーブル

お使いのphpMyAdminに上のデータベースを作り、入っているPDFファイルの「テーブル定義書」に記載されているテーブルを作成してください。
