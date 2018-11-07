<?php
require_once('\xampp\htdocs\bbs\class\sqlAction.php');
$action = new GetAction();

//常にパスワード認証をかける
if (isset($_COOKIE['sendkey'])) {
	//id取得
	//URL抜き出し
	$tmp = $_SERVER['REQUEST_URI'];
	//idの開始位置を調べる
	$few = strpos($tmp, "id=");
	$few += 3;
	//id=以降の全てを抜き出すとidが取得できる
	$id = substr($tmp, $few);

	$repassword = $_COOKIE['sendkey'];
	$authAnswer = $action->passwordMatched($repassword,$id);

}else{
	echo "戻ってやり直してください";?>
	<div><a href="http://localhost/bbs/view/post.php">戻る</a></div><?php
	exit();
}

if($authAnswer==false){
	echo "戻ってやり直してください";?>
	<div><a href="http://localhost/bbs/view/post.php">戻る</a></div><?php
	exit();
}else{
 $boardId = $action->getBoardData($id);
  	if ($boardId[0]['board_id']==1) {
 		  $post_datas = $action->getDbPostData1();
 	}else if($boardId[0]['board_id']==2){
 	      $post_datas = $action->getDbPostData2();
 	}else if($boardId[0]['board_id']==3){
 		  $post_datas = $action->getDbPostData3();
 	}
}

?>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href='../style.css'>
</head>
<body>
<title>TWEET EDIT</title>
	<h3>ツイートを編集・削除する</h3>
<?php
//id取得
$tmp = $_SERVER['REQUEST_URI'];
$few = strpos($tmp, "id=");
$few += 3;
$id = substr($tmp, $few);

foreach ($post_datas as $post) {
		if ($post['id'] == $id) {
			$Message = $post['message'];
			$Pass = $post['password'];
		}
}

//もし新しいパスが入力されたら置き換える
//入力されなければもとのパスのまま
if ((isset($_POST['newpassword'])) && $_POST['newpassword']!==""){
		$newpassword = $_POST['newpassword'];
}elseif((isset($_POST['newpassword'])) && $_POST['newpassword']==""){
		$newpassword = $Pass;
}

//もし新しいテキストが入力されたら置き換える
//入力されなければもとのつぶやきのまま
if ((isset($_POST['newtext'])) && $_POST['newtext']!=="") {
		$newMessage = $_POST['newtext'];
}elseif((isset($_POST['newtext'])) && $_POST['newtext']==""){
		$newMessage = $Message;
}

//もし更新が押されたにもかかわらず、つぶやきもパスワードも変更されないままだったら、更新はせずに戻るを表示する
if ((isset($_POST['update'])) && ($newMessage==$Message) && ($newpassword == $Pass) ) { ?>
	<div><a href="/bbs/view/post.php">戻る</a></div>
<?php
	exit();
}

//もし新しいつぶやきがセットされたら表示するつぶやきを置き換える
if (isset($newMessage)) {
	$Message = $newMessage;
}

//編集ツイートのidを取得する
if (isset($_POST['postid'])) {
		$sendid = $_POST['postid'];
}

?>
<form action="" method="post">
	<div class="display">
		<div class="item">
			<div class="message">
				<input type="text" name="newtext" value="<?php echo $Message;?>">
				<div class="password">pass:
					<input type="password" name="newpassword" value="">
					<div class="created_at"><?php echo $post['created_at']; ?>
					</div>
				</div>
			</div>
		<input type="hidden" name="postid" value="<?php echo $id; ?>">
		<input type="submit" name="update" value="更新">
		<input type="submit" name="delete" value="削除" onclick="return confirm('本当に削除しますか？')">
		</div>
	</div>
<?php}
}
?>
</form>
</body>
</html>
<?php

if (isset($_POST['update'])) {

		$checkData['message'] = $newMessage;
		$checkData['password'] = $newpassword;
		$result = $action->textChecker($checkData);
		echo $result;

	if($result==true){
		$edit_datas = $action->updateDbPostData($sendid,$newMessage,$newpassword);
 		echo  $edit_datas;?>
 		<div><a href="/bbs/view/post.php">戻る</a></div>
<?php }
}

if (isset($_POST['delete'])) {
 		$delete_datas = $action->deleteDbPostData($sendid);
 		echo  $delete_datas; ?>
 		<div><a href="/bbs/view/post.php">戻る</a></div>
<?php }
?>
