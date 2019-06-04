<?php
	
	function addMember($conn, $account, $password){
		$sql = "SELECT count( * ) 
				FROM member
				WHERE u_account = '$account'";
		$result = $conn->query($sql);
		$row=mysqli_fetch_array($result);
		if($row[0]>0)
			return "此帳號已註冊";


		$sql = "INSERT INTO member(u_account, u_password)
				VALUES ('$account', '$password');";
		if ($conn->query($sql)) {
		    return "註冊成功";
		} else{
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}

	function signInCheck($conn, $account, $password){
		$sql = "SELECT * 
				FROM member 
				WHERE u_account = '$account'";

				$result = $conn->query($sql);
				$row = mysqli_fetch_assoc($result);
			    if($password == $row["u_password"]){
			    	$_SESSION['user_account'] = $account;
			    	return "登入成功";
			    } 
				else {
					return "帳號密碼錯誤!!";
				}
	}

	session_start();
	header("Content-Type:text/html; charset=utf-8");
	// Create connection
	$conn = new mysqli("127.0.0.1", "root", "", "kdrama");
	// Check connection
	if ($conn->connect_error) {
		echo "資料庫異常，維修中" . $conn->error;
		die("connect failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,'utf8');

	$status = "0";
	//註冊會員
	if(isset($_POST["signup_account"]) && isset($_POST["signup_password"])){
		$account = $_POST["signup_account"];
		$password = $_POST["signup_password"];
		$status = addMember($conn, $account, $password);
	}

	//登入會員
	if(isset($_POST["signin_account"]) && isset($_POST["signin_password"])){
		$account = $_POST["signin_account"];
		$password = $_POST["signin_password"];
		$status = signInCheck($conn, $account, $password);
	}

?>

<!DOCTYPE html>
<html>
<head>


	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>KDrama韓劇-註冊會員</title>
	<style>
	#sinUpBlock {
	    height: 600px;
	    width: 600px;
	 	border-color: #A42D00;
	 	border-width:3px;
	 	border-style:solid;
  		position: absolute;
  		top: 150px;
  		left: 100px;
	    text-align: center;
	    font-weight:bold;
	    margin:15px;

	}

	#sinInBlock {
	    height: 500px;
	    width: 600px;
	 	border-color: #A42D00;
	 	border-width:3px;
	 	border-style:solid;
	 	position: absolute;
	    top: 150px;
	    left: 800px;   
	    text-align: center;
	    font-weight:bold;
	    margin:15px;
	}

	.inputFont {
		font-size: 30px;
		border-style:solid;
	}
	.button{
		background-color: #A42D00;
		color: white;
		padding: 20px 30px;
  		text-align: center;
  		font-size: 20px;
  		cursor: pointer;
  		border-radius: 12px;
  		border: none;
	}
	#logo{
		height: 100%;
	}
	</style>
</head>
<body>
	<div align="center" style = "height: 100px">
		<a href="kdrama.php"><img src="src/logo.png" alt="連結失效" id = "logo"></a>
	</div>


	<div id = "sinUpBlock">
	<h1>註冊會員</h1>
	<form action="member.php" method="post"  onsubmit = "return isFill('signup')">
		<p style = "font-size: 30px;">帳號:</p>
		<input type="text" name="signup_account" class = "inputFont">
		<br><br>
		<p style = "font-size: 30px;">密碼:</p>
		<input type="password" name="signup_password" class = "inputFont">
		<br><br>
		<p style = "font-size: 30px;">確認密碼:</p>
		<input type="password" name="signup_password_confirm" class = "inputFont">
		<br><br>
		<input type="submit" value="註冊" id = "signin_button" class = "button">
	</form> 
	</div>


	<div id = "sinInBlock">
	<h1>登入</h1>
	<form action="member.php" method="post"  onsubmit = "return isFill('signin')">
		<p style = "font-size: 30px;">帳號:</p>
		<input type="text" name="signin_account" class = "inputFont">
		<br><br>
		<p style = "font-size: 30px;">密碼:</p>
		<input type="password" name="signin_password" class = "inputFont">
		<br><br>
		<input type="submit" value="登入" id = "signup_button" class = "button">
	</form> 
	</div>


</body>
<script type="text/javascript">

	function isFill(str){

		if(str == "signup"){
			var account = document.getElementsByName("signup_account")[0].value;
			var password = document.getElementsByName("signup_password")[0].value;
			var password_confirm = document.getElementsByName("signup_password_confirm")[0].value;
			if(password!=password_confirm){
				alert("請在確認密碼欄輸入相同密碼");
				return false;
			}
		}
		else{
			var account = document.getElementsByName("signin_account")[0].value;
			var password = document.getElementsByName("signin_password")[0].value;
		}
		
		
		
		if(account =="" || password ==""){
			alert("帳號密碼都必須填寫!!");
			return false;
		}
		if(account != account.replace(/\s/g, "") || password != password.replace(/\s/g, "")){
			alert("帳號密碼不能有空格!!");
			return false;
		}

		return true;
	}
	function signUpSucess(obj){
		div = document.getElementById(obj);
		div.innerHTML = "";
		var p = document.createElement("p");
		p.innerHTML = "註冊成功";
		p.setAttribute("style", "font-size:40px;color: red;");
		div.appendChild(p);

		var a = document.createElement("a");
		a.innerHTML = "回首頁";
		a.setAttribute("href", "kdrama.php");
		a.setAttribute("style", "font-size:30px;text-decoration:none;");
		div.appendChild(a);
	}

	var status = "<?php echo $status ?>"
	// alert(status);
	if(status=="註冊成功"){
		signUpSucess("sinUpBlock");
	}
	else if(status=="登入成功"){
		alert("登入成功");
		window.location.href="kdrama.php";
	}
	else{
		if(status!="0")
			alert(status);
	}
</script>
</html>
