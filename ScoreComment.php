<?php
	header("Content-Type:text/html; charset=utf-8");
	// Create connection
	$conn = new mysqli("127.0.0.1", "root", "", "kdrama");
	// Check connection
	if ($conn->connect_error) {
	echo "connect failed";
	die("connect failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,'utf8');

	//載入評論
	if(isset($_GET["d_id"])){
		$d_id = $_GET["d_id"];
	}

	//評分
	if(isset($_GET["score"])){
		$userScore = $_GET["score"];
		$sql = "SELECT score FROM drama WHERE d_id = $d_id";   //抓原本的分數
		$result = $conn->query($sql);
		$row=mysqli_fetch_assoc($result);
		$score =  $row["score"];

		$sql = "SELECT scorepeople_count FROM drama WHERE d_id = $d_id"; //抓原本評分人數
		$result = $conn->query($sql);
		$row=mysqli_fetch_assoc($result);
		$scorepeople_count =  $row["scorepeople_count"];
		
		$score = round(($score*$scorepeople_count+$userScore)/($scorepeople_count+1), 1); //加上此次評分進行運算


		//修改評分人數(要+1)
		$sql = "UPDATE drama SET scorepeople_count=($scorepeople_count+1) WHERE d_id = $d_id";
		$conn->query($sql);

		$sql = "UPDATE drama SET score=$score WHERE d_id = $d_id";//修改分數
		if ($conn->query($sql)) {
	    	echo $score;
		} 
		else {
			echo "資料庫異常，維修中" . $conn->error;
		}
	}

	//留言
	if(isset($_GET["text"])){
		$u_account = $_GET["u_account"];
		$text = $_GET["text"];
		$time = $_GET["time"];
		$sql = "INSERT INTO comment(u_account, d_id, date_time, word)
				VALUES('$u_account', $d_id, '$time', '$text')";
		if ($conn->query($sql)) {
	    	echo "已成功留言";
		}
	}

	


?>
