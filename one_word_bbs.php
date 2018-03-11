<?php
// 必要なデータを取得して各変数に代入
$name = $_POST['namae']; // 名前
$comment = $_POST['comment']; // コメント
$pass = $_POST['pass']; //パスワード
$dNum = $_POST['delete_number']; // 削除対象番号
$eNum = $_POST['edit_number']; // 編集対象番号
$eNum2 = $_POST['eNum2']; // 編集対象番号(引き継ぎ)
$mode = $_POST['mode']; // モード [0]投稿 [1]編集
$date = date("Y/m/d H:i:s");  // 投稿日時
$filename = 'kadai2-6.txt'; // ファイル名


// ▼追記プログラム
if(!empty($name) && !empty($comment) && !empty($pass) && $mode == 0) {
  //以下編集モードでない時の処理

  touch($filename); // ファイルが存在しない場合新規作成
  $lNum = count( file( $filename ) );
  $lNum++;

  $fp = fopen($filename, 'a');
  fwrite($fp, $lNum . '<>' . $name . '<>' . $comment . '<>' . $date . '<>' . $pass . "\n");
  fclose($fp);
}


// ▼削除プログラム
if(!empty($dNum) && !empty($pass)){
  // 以下削除番号が送られてきた場合の処理
  $array = @file($filename, FILE_IGNORE_NEW_LINES);

  $fp = fopen($filename, 'r');
  foreach ($array as $res) {
    $result = explode("<>", $res);
    $lNum = $result[0];

    if($dNum == $lNum){
    $pw = $result[4];
    }
  }fclose($fp); // 初めに削除対象行の投稿番号とパスワードを取得

  if($pw != $pass){
      echo 'パスワードが違います';
      echo '$pw :' . $pw . '$pass : ' . $pass;
    } else {
      //ファイル操作
      $fp = fopen($filename, 'w');

      foreach ($array as $res) { //$arrayの要素を１つずつ$resに代入

        // 読み込んだ行の投稿番号を取得
        $result = explode("<>", $res);
        $lNum = $result[0];

        if($lNum != $dNum){ //以下削除対象でない場合の処理

          if($lNum < $dNum){
            fwrite($fp, $res . "\n");
          } else {
            $lNum--;
            fwrite($fp, $lNum . "<>" . $result[1] . "<>" . $result[2] . "<>" . $result[3] . '<>' .$result[4] . "\n");
          }
        }
      }
      fclose($fp);
    }
  }



// ▼編集用プログラム
if(!empty($eNum) && !empty($pass)){
  // 編集対象番号が送られてきていたらその行の名前とコメントを取得
  $array = @file($filename, FILE_IGNORE_NEW_LINES);

  $fp = fopen($filename, 'r');
  foreach ($array as $res) {
    $result = explode("<>", $res);
    $lNum = $result[0];

    if($eNum == $lNum){
    $pw = $result[4];
    }
  }fclose($fp); // 初めに編集対象行の投稿番号とパスワードを取得

  if($pw != $pass){
      echo 'パスワードが違います';
    } else {
      // ▼編集用プログラム
      if(!empty($eNum)){

        $array = @file($filename, FILE_IGNORE_NEW_LINES);

        //ファイル操作ここから
        $fp = fopen($filename, 'r');

        foreach ($array as $res) {
          if($lNum == $eNum){
            $result = explode("<>", $res);
            $get_name = $result[1];
            $get_comment = $result[2];
            $get_eNum = $result[0];
          }
        }
        fclose($fp);
        }
      }

    }

// 編集モードでコメントを送った時の処理
if($mode == 1) {
  $array = @file($filename, FILE_IGNORE_NEW_LINES);

  $fp = fopen($filename, 'w');

  foreach ($array as $res) {
    $result = explode("<>", $res);
    $lNum = $result[0];

    if($lNum == $eNum2){
       $res = $lNum . '<>' . $name . '<>' . $comment . '<>' . $date . '<>' . $pass . "\n";
       fwrite($fp, $res);
    } else {
      fwrite($fp, $res. "\n");
    }
  }
  fclose($fp);
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>情報入力画面</title>
  </head>
  <body>

    <h1>情報入力フォーム</h1>
    <form method="POST">
      <?php if(!empty($eNum)){ echo '<p>※' . $eNum . '番のコメントを編集します。</p>';} ?>
      <label for="namae">名前 : </label>
      <input type="text" name="namae" value="<?php if(!empty($get_name)){ echo $get_name; } ?>"><br>
      <label for="comment">コメント : </label>
      <input type="text" name="comment" value="<?php if(!empty($get_comment)){ echo $get_comment; } ?>"><br>
      <label for="pass">パスワード : </label>
      <input type="text" name="pass">
      <input type="hidden" name="eNum2" value="<?php if(!empty($eNum)){ echo $eNum ;} ?>">
      <input type="hidden" name="mode" value="<?php if(!empty($eNum)){ echo 1;} else {echo 0;}?>">
      <input type="submit" value="送信">
    </form><br>

    <form method="POST">
      <label for="del_num">編集対象番号 : </label>
      <input type="text" name="edit_number" value="<?php if(!empty($eNum) && empty($pass)){ echo $eNum; } ?>">
      <?php
      if(!empty($eNum) && empty($pass)){
        echo '<p style="color:red; font-size:10px">パスワードを入力してください</p>';
        echo '<input type="text" name="pass">';
      }
      ?>
      <input type="submit" value="編集">
    </form><br>

    <form method="POST">
      <label for="delete_number">削除対象番号 : </label>
      <input type="text" name="delete_number" value="<?php if(!empty($dNum) && empty($pass)){ echo $dNum; } ?>">
      <?php
      if(!empty($dNum) && empty($pass)){
        echo '<p style="color:red; font-size:10px">パスワードを入力してください</p>';
        echo '<input type="text" name="pass">';
      }
      ?>
      <input type="submit" value="削除">
    </form><br>



<?php
    echo '<hr>';


    //以下ファイルの内容を表示するプログラム
    $array = @file($filename, FILE_IGNORE_NEW_LINES); //

    if(!empty($array)){

      foreach($array as $res){
        $result = explode("<>", $res);
        echo $result[0] . ' ';
        echo $result[1] . ' ';
        echo $result[2] . ' ';
        echo $result[3] . '<br>';
      }
    }

 ?>

  </body>
</html>
