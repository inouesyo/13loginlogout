<?php
///////////////////
// セッションを開始する
session_start();
session_regenerate_id();

// 必要なファイルを読み込む
require_once('./class/db/Base.php');
require_once('./class/db/TodoItems.php');

// ログインしていないときは、login.phpへリダイレクト
if (empty($_SESSION['user'])) {
    header('Location: ./login.php');
    exit;
}

// エラー時のメッセージ
$msg = 'アップロードに失敗しました。';

// $_FILESが存在しない、もしくは、アップロード時にエラーが発生したとき
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] > 0) {
    $_SESSION['err']['msg'] = $msg;
    header('Location: ./upload.php');
    exit;
}

// アップロードされたCSVファイルを開く
$fp = fopen($_FILES['csv_file']['tmp_name'], 'r');

try {
    // インスタンス生成
    $db = new TodoItems();

    // 開いたCSVファイルを1行ずつ読み込む
    while (($buf = fgetcsv($fp)) !== false) {

        // アップデート実行
        // $bufにはCSVから読み込んだ項目が配列として代入されている
        $db->update($buf[0], $buf[1], mb_convert_encoding($buf[2], 'UTF-8', 'SJIS-win'), $buf[3]);
    }

    // トップページへリダイレクトする
    header('Location: ./');
    exit;
} catch (Exception $e) {
    // var_dump($e);
    // echo $e->getMessage();
    $_SESSION['err']['msg'] = $msg;
    header('Location: ./upload.php');
    exit;
}