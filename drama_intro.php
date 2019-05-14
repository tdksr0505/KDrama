
<?php
	function dramaSqlQuery($conn, $sql) {
		$i = 0;
		$drama_data = array();
		$result = $conn->query($sql);
		if (mysqli_num_rows($result) > 0) {
		    while($row = mysqli_fetch_assoc($result)) {
		    	$drama_data[$i]['d_id'] = $row['d_id'];
		        $drama_data[$i]['title'] = $row['title'];
		        $drama_data[$i]['year'] = $row['year'];
		        $drama_data[$i]['episode'] = $row['episode'];
		        $drama_data[$i]['score'] = $row['score'];
		        $drama_data[$i]['scorepeople_count'] = $row['scorepeople_count'];
		        $drama_data[$i]['poster_path'] = $row['poster_path'];
		        $i++; 
		    }
		} 
		else {
			echo mysqli_error();
		    echo "0 结果";
		    echo $sql;
		}
		return $drama_data;
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

	//檢查session
	if(isset($_GET['r'])){
		unset($_SESSION['user_account']);
	}

	if(isset($_SESSION['user_account'])){
		$session = $_SESSION['user_account'];
	}
	else{
		$session = "";
	}

	//drama information
	if(isset($_GET["id"])){
		$d_id = $_GET["id"];
		$sql =  "SELECT * FROM drama WHERE d_id=".$d_id;
		$dramaInfo = dramaSqlQuery($conn, $sql);
	}


	//ranking
	$sql = "SELECT * FROM drama ORDER BY score DESC LIMIT 5"; //從第0筆資料開始，抓6筆出來
	$drama_data_ranking = dramaSqlQuery($conn, $sql);

?>
<!DOCTYPE html>

<html>

<head>
	<title id ="webtitle">KDrama韓劇</title>
	<link rel=stylesheet type="text/css" href="kdrama_css.css">
	<link rel="stylesheet" href="src/jsRapStar.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="src/jsRapStar.js"></script>
</head>

<body>
<div class="main">
	<div id = "account_block">
		<a href="member.php" target="_blank" style = "color: white">登入</a>&emsp;
		<a href="member.php" target="_blank" style = "color: white">註冊</a>
	</div>
	<div id="index_logo">
		<a href="kdrama.php"><img src="src/logo.png" alt="連結失效" style="height: 150px;width: 332;float:left;"></a>
	</div> 
	<div style="background-color:#A42D00;width:100%;height:5px">
	</div>
	<div>
		<form action="kdrama.php?search="  id = "search_form">
			<input type="text" name="search" id = "search_text">
			<input type="submit" value="搜尋" id = "search_submit">
		</form>
	</div>
	
	<!-- 韓劇資訊 -->
	<div style="width:65%;height:800px;float:left;">
		<div id = "poster_path">
			<!-- <img  id = "poster" style="width: 400px;height: 600px;float:left;" src="src/poster/skycastle.jpg"> -->
		</div>
		<span>
			<ul id ="dramaIntro_block">
<!-- 				<li style="font-size: 50px "><span id = "title">title</span></li><br>
				<li style="font-size: 40px ">年份:<span id = "year">0</span></li><br>
				<li style="font-size: 40px ">集數:<span id = "episode">0</span></li><br>
				<li style="font-size: 40px ">評分:<span id = "score">0</span><span id = "scorepeople_count">(0人評分)</span></li> -->
			</ul>
		</span>
		<div style="width: 500px;height: 200px; float: left;">
			<br><br><div style="font-size: 40px;">您的評分</div>
			<div id = "star" start="0"></div>
		</div>
	</div>

	<!-- 廣告圖片 -->
	<div id="ad">
		<img style="width: 100%;height: 100%;" src="src/ad/MemoriesoftheAlhambra.jpg"><!-- 廣告圖片 -->
	</div>

	<!-- 排行榜 -->
	<div id = "ranking_div">
		<p style="font-size:35px;text-align:center;">排行榜</p>
		<ol id = "ranking" style="font-size: 25px;">
<!-- 			<li>title</li>
			<hr color = #A42D00 size="2" align="left" width="70%" >
			<li>title</li>
			<hr color = #A42D00 size="2" align="left" width="70%" >
			<li>title</li>
			<hr color = #A42D00 size="2" align="left" width="70%" >
			<li>title</li>
			<hr color = #A42D00 size="2" align="left" width="70%" >
			<li>title</li> -->
		</ol>

	</div>
</div>
</body>
<script type="text/javascript">
	function sequentialImg(){
	    	
	    var ad_img = new Array("src/ad/WhileYouWereSleeping.jpg","src/ad/GangnamBeauty.jpg","src/ad/PrisonPlaybook.jpg","src/ad/MemoriesoftheAlhambra.jpg");

    	var ad_img_len = ad_img.length;

        document.getElementById("ad").innerHTML="<img style=\"width: 100%;height: 100%;\" src="+ad_img[pic_index]+">";
        pic_index++;
        if(pic_index>=ad_img_len)  pic_index=0;
    }

    function addInfo(obj, obj_path, dramaInfo){

    	//處理資訊欄
    	var ul = document.getElementById(obj);
    	var li = document.createElement("li");
    	li.setAttribute("style", "font-size:50px");
    	li.innerHTML = "<span>"+dramaInfo[0]["title"]+"</span>";
    	ul.appendChild(li);

    	li = document.createElement("li");
    	li.innerHTML = "年份:<span>"+dramaInfo[0]["year"]+"</span>";
    	ul.appendChild(document.createElement("br"));
    	ul.appendChild(li);

    	li = document.createElement("li");
    	li.innerHTML = "集數:<span>"+dramaInfo[0]["episode"]+"</span>";
    	ul.appendChild(document.createElement("br"));
    	ul.appendChild(li);

    	li = document.createElement("li");
    	li.innerHTML = "評分:<span id = \"score\">"+dramaInfo[0]["score"]+"</span><span id = \"scorepeople_count\">分("+dramaInfo[0]["scorepeople_count"]+"人評分)</span>";
    	ul.appendChild(document.createElement("br"));
    	ul.appendChild(li);

    	//左方海報
    	var div = document.getElementById(obj_path);
    	var img = document.createElement("img");
    	img.setAttribute("style", "width:400px;height:600px;float:left");
    	img.setAttribute("src", dramaInfo[0]["poster_path"]);
    	div.appendChild(img);
    }

    function addRanking(obj, drama_data_ranking){
    	var ol = document.getElementById(obj);
    	var ranking = 1
    	for(i=0; i<drama_data_ranking.length; i++, ranking++){

    		var a = document.createElement("a");
			a.innerHTML = ranking+". "+drama_data_ranking[i]["title"];
			a.setAttribute("href", "drama_intro.php?id="+drama_data_ranking[i]["d_id"]);
			a.setAttribute("target", "_blank");
			a.className = "drama_hyperlink";
			ol.appendChild(a);

    		var hr = document.createElement("hr");
			hr.setAttribute("color", "#A42D00");
			hr.setAttribute("size", "2");
			hr.setAttribute("align", "left");
			hr.setAttribute("width", "70%");
			ol.appendChild(hr);
    	}
    }

    function addAccountInfo(obj, sessionStr){
			var div = document.getElementById(obj);
			var span = document.createElement("span");
	    	var a = document.createElement("a");

	    	div.innerHTML="";
	    	span.innerHTML="您好: "+sessionStr;
	    	div.appendChild(span);

	    	a.innerHTML="登出";
	    	a.setAttribute("href", "kdrama.php");
	    	a.setAttribute("onclick", "javascript:logout()");
	    	a.setAttribute("style", "color:white;float:right");
	    	div.appendChild(a);
		}

		function logout(){
			var httpRequest;
			if (window.XMLHttpRequest) {
				httpRequest = new XMLHttpRequest();
			}
			else if (window.ActiveXObject) {
				httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
			}

			httpRequest.onreadystatechange = function(){
				if (httpRequest.readyState ==4 && httpRequest.status==200){
					alert("已成功登出");
				}
			}
			httpRequest.open("GET", "kdrama.php?r=logout");
			httpRequest.send();
		}

    var i=0;	  
    var pic_index = 0;  
    setInterval("sequentialImg()",2000);

    var dramaInfo = <?php echo json_encode($dramaInfo); ?>;
    var drama_data_ranking = <?php echo json_encode($drama_data_ranking); ?>;
    addInfo("dramaIntro_block", "poster_path", dramaInfo);
    addRanking("ranking", drama_data_ranking);

    //評分
    $('#star').jsRapStar({length:10,starHeight:64,colorBack:'gray',
		onClick:function(score){
			if(session!=""){
				var httpRequest;
				if (window.XMLHttpRequest) {
					httpRequest = new XMLHttpRequest();
				}
				else if (window.ActiveXObject) {
					httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
				}

				httpRequest.onreadystatechange = function(){
					if (httpRequest.readyState ==4 && httpRequest.status==200){
					document.getElementById("score").innerHTML=httpRequest.responseText;
					dramaInfo[0]["scorepeople_count"]++;
					document.getElementById("scorepeople_count").innerHTML="分("+dramaInfo[0]["scorepeople_count"]+"人評分)";
					alert("已成功評分!");
					}
				}
				httpRequest.open("GET", "score.php?score="+score+"&d_id="+dramaInfo[0]['d_id']);
				httpRequest.send();
			}
			else{
				alert("請先登入再評分!");
			}
		}
	});

    var session = "<?php echo $session ?>";
    if(session!=""){
    	addAccountInfo("account_block", session);
    }
</script>
</html>
