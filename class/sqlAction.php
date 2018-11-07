<?php
class GetAction{

public $pdo;

	function __construct(){

		try {
		 	$this->pdo = new PDO('mysql:host=localhost;dbname=bbs;charset=utf8','root','');

		} catch (PDOException $e){
		 	echo('データベースの接続に失敗しました'.$e->getMessage());
		 	die();
		}
	}

//掲示板の取得
	function getDbBoarddata(){
		$smt = $this->pdo->prepare('select * from boards');
	 			$smt->execute();
	 			//配列に入れる
	 			$boardsData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			return $boardsData;
	 }

	function textChecker($data){
		$countMess = mb_strlen($data['message']);
		$countPass = mb_strlen($data['password']);

			if($countMess>140){
				echo "文字数オーバーです!";
			 	return false; 
			}else if($countMess<1){
				echo  "入力されていません!";
				return false;
			}else if($countPass>12){
				echo "パスワードは12文字までです!";
				return false;
			}else{
				return true;
			}
	}

//記事の投稿保存
	function saveDbPostdata($data){

		 	$smt = $this->pdo->prepare('INSERT INTO posts SET board_id=?, message=?, password=?,
		 															created_at=now(), updated_at=now();');
		 	$smt->bindParam(1,$data['board_id']);
		 	$smt->bindParam(2,$data['message']);
		 	$smt->bindParam(3,$data['password']);

			$smt->execute();

  		}

//掲示板１の記事データを取り出す
	 function getDbPostData1(){
	 			$smt = $this->pdo->prepare('select * from posts where
	 			 board_id=1 order by created_at DESC limit 10;');
	 			$smt->execute();
	 			//配列に入れる
	 			$allData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			return $allData;
	 }

//掲示板２の記事データを取り出す
	 function getDbPostData2(){
	 			$smt = $this->pdo->prepare('select * from posts where board_id=2 order by created_at DESC limit 10;');
	 			$smt->execute();
	 			//配列に入れる
	 			$allData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			return $allData;
	 }

//掲示板３の記事データを取り出す
	 function getDbPostData3(){
	 			$smt = $this->pdo->prepare('select * from posts where board_id=3 order by created_at DESC limit 10;');
	 			$smt->execute();
	 			//配列に入れる
	 			$allData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			return $allData;
	 }

//パスワードの判定
	 function passwordMatched($password,$id){
	 			$smt = $this->pdo->prepare("select * from posts where id=".$id);
	 			if(is_numeric($id)){
	 			$smt->execute();
	 			$matchData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			}
	 			if (isset($matchData[0])){
	 				if ($matchData[0]['password']==$password) {
	 					return true;
	 				}else if($matchData[0]['password']=="" && $password=='空白入力の場合のチェック用パスワード'){
	 					return true;
	 				}else{
	 				return false;
	 				}
				}
		}

//編集時ボードデータの検索
	 function getBoardData($id){
	 		$smt = $this->pdo->prepare("select board_id from posts where id=".$id);
	 		$smt->execute();
	 		$board_id = $smt->fetchAll(PDO::FETCH_ASSOC);
	 		return $board_id;
	}

//記事の更新
	 function updateDbPostData($id,$newmessage,$newpassword){
				$smt = $this->pdo->prepare("UPDATE posts SET message=?, password=?, updated_at=now()
									WHERE id=".$id);
				$smt->bindParam(1,$newmessage);
				$smt->bindParam(2,$newpassword);
				$smt->execute();

				return "更新しました";
	}

//記事の削除
	function deleteDbPostData($id){
				$smt = $this->pdo->prepare("DELETE FROM posts WHERE id=".$id);
				$smt->execute();
				echo "削除しました"."<br>";
				$delete = true;

					if($delete==true){
						$smt = $this->pdo->prepare("DELETE FROM comments WHERE post_id=".$id);
						$smt->execute();
						echo "コメントも削除されました";						
				}
	}

//コメント欄に表示する記事取得
	function getDbPostDataCom($id){
	 			$smt = $this->pdo->prepare("select * from posts where
	 			id=".$id);
	 			if(is_numeric($id)){
	 				$smt->execute();
	 				//配列に入れる
	 				$comData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 				return $comData;
	 			}
	 }

//コメントの投稿保存
	function saveDbPostcomment($data){
			 	$smt = $this->pdo->prepare('INSERT INTO comments SET post_id=?, comment=?,
			 								created_at=now();');
		 		$smt->bindParam(1,$data['post_id']);
		 		$smt->bindParam(2,$data['comment']);

				$smt->execute();
	}

//選択した投稿のコメント表示
	 function getDbPostComment($id){
	 			$smt = $this->pdo->prepare("select * from comments where
	 			post_id=".$id." order by created_at;");
	 			$smt->execute();

	 			//配列に入れる
	 			$allData = $smt->fetchAll(PDO::FETCH_ASSOC);
	 			if(!empty($allData)){
	 			return $allData;
	 			}
	 }
}
