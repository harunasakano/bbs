<?php

//id取得
$tmp = $_SERVER['REQUEST_URI'];
$few = strpos($tmp, "id=");

if ($few==true) {
	$few += 3;
	$id = substr($tmp, $few);
//URLのid部分がユーザによって消されたとき
}else{ ?>
	<div>戻ってやりなおしてください</div>
	<div><a href="/bbs/view/post.php">TOP</a></div>

<?php
	exit();
}

require_once('\xampp\htdocs\bbs\class\sqlAction.php');

//選択記事取得
$action = new GetAction();
$post_datas = $action->getDbPostDataCom($id);

if (empty($post_datas)){ ?> 
	<div>ツイートが存在しません、戻ってやりなおしてください</div>
	<div><a href="/bbs/view/post.php">TOP</a></div>
<?php
	exit();
}

//コメント一覧取得
$allComment = $action->getDbPostComment($id);

?>
<?php
//コメントの保存
if(isset($_POST['comment'])){

	$data['message'] = $_POST['comment'];
	$data['password'] = 'xxxxx';

	$result = $action->textChecker($data);
	echo $result;

	if($result==true){
			$comdata =array();
			$comdata['post_id'] = $id;
			$comdata['comment'] = $_POST['comment'];

			$comPost = $action->saveDbPostcomment($comdata);
			header("Location: http://localhost/bbs/view/comment.php?empid=${id}");
			exit();
		}
}
?>

<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href='../style.css'>
</head>
<body>
<form method="post" action="">
<title>COMMENT WRITE</title>
<h3>コメントを書こう</h3>
<div class="display">
		<div class="item">
			<div class="message">
				<div class="message"><?php echo $post_datas[0]['message'];?></div>
					<div class="created_at"><?php echo $post_datas[0]['created_at']; ?>
					</div>
				</div>
			</div>
<form method="post" action="">
	<div class="comment_box">
		<div>コメント:</div>
		<input type="text" class="comment_form" name="comment">
		<input type="hidden" name="cid" value='<?php echo $_POST['cid']; ?>'>
		<input type="hidden" name="ticket" value='<?php echo $ticket; ?>'>
		<input type="submit" class="comment_sub" name="submit">
	</div>
</div>
</form>
<?php //コメント表示
if(!empty($allComment)){ ?>
<?php
	for ($i=0; $i<=count($allComment)-1; $i++) { ?>
		<div class="commentAria">
		<div class="comNumber">コメント<?php print $i+1; ?></div>
		<div class="comMessage"><?php print $allComment[$i]['comment'];?></div>
		<div class="comPostDate">投稿日時:<?php print $allComment[$i]['created_at'];?></div></div><?php
	}
} ?>
</div>
<div><a href="/bbs/view/post.php">戻る</a></div>
</body>
</html>
