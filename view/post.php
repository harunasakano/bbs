<?php
require_once('\xampp\htdocs\bbs\class\sqlAction.php');
$action = new GetAction();

//掲示板セレクターに表示する内容
$allBoard = $action->getDbBoarddata();

//ボードネームだけを取得
for ($k=0; $k<=count($allBoard)-1; $k++) {
		$boardname[] = $allBoard[$k]['board_name'];
}

//default_flag==1の掲示板を取得
for ($m=0; $m<=count($allBoard)-1; $m++) { 
		if ($allBoard[$m]['default_flag']==1) {
			$defBoard = $allBoard[$m];
		}
}

//空のときはdefault_flag==1の掲示板を表示
if (empty($_POST['board_id']) || $_POST['board_id']==-1) {
	$board_id = $defBoard['id'];
}else{
	$board_id = $_POST['board_id']+1;
}

if($board_id == "1"){
	$post_datas = $action->getDbPostData1();
}else if($board_id == "2"){
	$post_datas = $action->getDbPostData2();
}else{
	$post_datas = $action->getDbPostData3();
}

//掲示板書き込み後、パラメータによって表示する掲示板を変える
//掲示板id取得

$url = $_SERVER['REQUEST_URI'];
$hit = strpos($url, "id=");

//書き込んだ後であり、まだ掲示板を選択していない状態のときは書き込んだ直後の掲示板を表示したままにする

if ($hit==true && empty($_POST)) {
	$hit += 3;
	$board_num = substr($url, $hit);

		if ($board_num == 1) {
			$post_datas = $action->getDbPostData1();
		}else if ($board_num == 2) {
			$post_datas = $action->getDbPostData2();
		}else{
			$post_datas = $action->getDbPostData3();
		}
}

if (isset($_POST['message'],$_POST['password'])) {
	$result = $action->textChecker($_POST);
	echo $result;
}

//postされたら書き込む
if(isset($_POST['eventId']) && $result==true){
	$eventId = $_POST['eventId'];
	$action->saveDbPostData($_POST);
	$board_id -=1;
	header("Location: http://localhost/bbs/view/post.php?empid=${board_id}");
	exit();
}

if (isset($_POST['pid'])) {
	$id = $_POST['pid'];
}

//再入力されたパスワードをクッキーに保存する
if (!empty($_POST['repassword'])) {
	$repassword = $_POST['repassword'];
	$sendKey = setcookie("sendkey",$repassword);

//何も入力されなかった場合はひとまず'空白入力の場合のチェック用パスワード'をいれる
}else if(isset($_POST['pid'])){
	$repassword ='空白入力の場合のチェック用パスワード';
	$sendKey = setcookie("sendkey",'空白入力の場合のチェック用パスワード');
}

if (isset($_POST['pid'])) {
	$result = $action->passwordMatched($repassword,$id);
		if ($result==true) {
			header("Location: http://localhost/bbs/view/edit.php?empid=${id}");
			exit;
		}else{?>
		<p><?php echo "パスワードが違うようです";?></p><?php
		}
}

?>

<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href='../style.css'>
<meta charset="utf-8">
</head>
<body>
	<title>TWEET SQUARE</title>
	<h2>TWEET SQUARE</h2>
<div class="boadSelect">
<form action="" method="post">
<p>掲示板選択</p>

<select name="board_id" class="selecter" onchange="this.form.submit()"><?php
for ($b=-1; $b<count($boardname); $b++) { ?>
		<option value=<?php
		if(isset($_POST['board_id']) && $b==-1){
			echo $b; ?> selected>選択して下さい<?php
		}else if($b==-1){
			echo $b.">"; ?>選択して下さい<?php
		}else if(isset($_POST['board_id']) && $_POST['board_id']==$b){
			echo $b; ?> selected><?php echo $boardname[$b];
		}else{
			echo $b.">"; ?><?php echo $boardname[$b];
		} ?>
	</option>
<?php } ?>
</select>
</form>
</div>
<div class="tweetAria">
<form method="post" action="">
<p>つぶやき</p><input type="text" name="message" value="">
<p>パスワード</p>
	<input type="password" name="password" value="" >
<input type="hidden" name="eventId" value="save">
<input type="hidden" name="board_id" value="<?php echo $board_id; ?>">
<input style="height: 28px;" type="submit" name="submit" value="登録">
</div>
</form>
<?php if (!empty($post_datas)) { ?>
<div class="display">
	<?php foreach ($post_datas as $post) { ?>
	<div class="item">
		<div class="message"><?php echo ($post['message']); ?>
		<div class="created_at"><?php echo ($post['created_at']); ?></div>
		<form method="post" action="">
		<input type="password" name="repassword" value="">
			<input type="hidden" name="pid" value='<?php echo $post['id']; ?>'>
				<input type="submit" name="edit" value="編集"></a>
		</form>
		<div>
		<a href="/bbs/view/comment.php?empid=<?php echo $post['id'];?>">コメントする</a>
		</div>
	</div>
</div>
<?php } ?>
<?php } ?>
</div>
</body>
</html>
