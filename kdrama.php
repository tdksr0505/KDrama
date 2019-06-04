<?php
	function dramaDataQuery($conn, $sql) {
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

	function dramaCountQuery($conn, $sql) {
		$result = $conn->query($sql);
		$row=mysqli_fetch_array($result);
		return $row[0];
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

	$page = 1;
	$search="";

	//分為搜尋頁面或主頁面
	if(isset($_GET["search"])){ //搜尋頁面(中間)
		$search = $_GET["search"];

		
		if(isset($_GET["page"])){
			$page = $_GET["page"];
		}

		$start = 6*($page-1);

		$sql = "SELECT COUNT(*)
				FROM drama
				WHERE title LIKE '%$search%'
				ORDER BY year DESC 
				LIMIT $start,6";
		$totalDramaCount = dramaCountQuery($conn, $sql);
		$totalPage = ceil($totalDramaCount/6); //計算總頁數(搜尋結果)

		$sql = "SELECT * 
				FROM drama 
				WHERE title LIKE '%$search%'
				ORDER BY year DESC 
				LIMIT $start,6";
				$drama_data = dramaDataQuery($conn, $sql);				
	}
	else{// 主頁面內容(中間)

		if(isset($_GET["page"])){
			$page = $_GET["page"];
		}
		$start = 6*($page-1);
		$sql = "SELECT COUNT(*) FROM drama";
		$totalDramaCount = dramaCountQuery($conn, $sql);
		$totalPage = ceil($totalDramaCount/6); //計算總頁數(主頁面)

		$sql = "SELECT * FROM drama ORDER BY year DESC LIMIT $start,6"; //從第0筆資料開始，抓6筆出來
		$drama_data = dramaDataQuery($conn, $sql);
		
	}
	

	//ranking 抓排行榜資料
	$sql = "SELECT * FROM drama ORDER BY score DESC LIMIT 5"; //從第0筆資料開始，抓6筆出來
	$drama_data_ranking = dramaDataQuery($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
	<title>KDrama韓劇</title>
	<link rel="stylesheet"  href="kdrama_css.css" charset=utf-8>	
</head>

<body>
<div class="main">
	<div id = "account_block">
		<a href="member.php" target="_blank" style = "color: white">登入</a>&emsp;
		<a href="member.php" target="_blank" style = "color: white">註冊</a>
<!-- 		<span>您好: 111</span>
		<a href="member.php" style ="color:white;float:right">登出</a> -->
	</div>
	<div id="index_logo" style="padding:5px;">
		<a href="kdrama.php"><img src="src/logo.png" alt="連結失效" style="height: 150px;width: 332;float:left;"></a>
	</div>
	<div style="background-color:#A42D00;width:100%;height:5px">
	</div>
	<div>
		<form action="kdrama.php?search=" id = "search_form">
			<input type="text" name="search" id = "search_text">
			<input type="submit" value="搜尋" id = "search_submit">
		</form>
	</div>

	<div id = "drama_list_div">
		<ul id = "drama_list_ul"><!-- 韓劇列表 -->
		</ul>
	</div>
	
	<div id="ad">
		<img style="widt h: 100%;height: 100%;" src="src/ad/MemoriesoftheAlhambra.jpg"><!-- 廣告圖片 -->
	</div>

	<div id = "ranking_div">  <!-- 排行榜 -->
		<p style="font-size:35px;text-align:center;">排行榜</p> 
		<ol id = "ranking" class = "drama_hyperlink">
		</ol>
	</div>

	<div id = "page_div"> <!-- 頁數 -->
	</div>

</div>
</body>
<script type="text/javascript">

	    function sequentialImg(){ //自動更換圖片
	    	
		    var ad_img = new Array("src/ad/WhileYouWereSleeping.jpg","src/ad/GangnamBeauty.jpg","src/ad/PrisonPlaybook.jpg","src/ad/MemoriesoftheAlhambra.jpg");

	    	var ad_img_len = ad_img.length;

	        document.getElementById("ad").innerHTML="<img style=\"width: 100%;height: 100%;\" src="+ad_img[pic_index]+">";
	        pic_index++;
	        if(pic_index>=ad_img_len)  pic_index=0;
	    }

	    function addLi(obj, drama_data){ //新增韓劇列表
			var ul = document.getElementById(obj);
			for(i=0; i<drama_data.length; i++){
				var li = document.createElement("li");
				li.setAttribute("id", "drama_list_li");
				var img = document.createElement("img");
				img.setAttribute("src", drama_data[i]["poster_path"]);
				img.setAttribute("alt", "連結失敗");
				img.setAttribute("border", "1");
				img.setAttribute("id", "drama_list_poster");
				li.appendChild(img);

				var a = document.createElement("a");
				a.innerHTML = drama_data[i]["title"];
				a.setAttribute("href", "drama_intro.php?id="+drama_data[i]["d_id"]);
				a.setAttribute("target", "_blank");
				a.className = "drama_hyperlink";
				li.appendChild(a);

				var hr = document.createElement("hr");
				hr.setAttribute("color", "#A42D00");
				hr.setAttribute("size", "3");
				hr.setAttribute("align", "left");
				hr.setAttribute("width", "50%");
				li.appendChild(hr);

				var p = document.createElement("p");
				p.innerHTML = "年份:" + drama_data[i]["year"];
				li.appendChild(p);

				p = document.createElement("p");
				p.innerHTML = "集數:" + drama_data[i]["episode"];
				li.appendChild(p);

				p = document.createElement("p");
				p.innerHTML = "評分:" + drama_data[i]["score"];
				li.appendChild(p);

				ul.appendChild(li);
			}
	    }

	    function addRanking(obj, drama_data_ranking){ //新增排行榜
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

	    function addPage(obj, currentPage, totalPage, searchString){ //新增頁數

	    	var i = 1;
	    	var div = document.getElementById(obj);

	    	var span = document.createElement("span");
	    	var a = document.createElement("a");
	    	if(currentPage == 1){
	    		font = document.createElement("font");
		    	font.innerHTML = "   上一頁";
		    	span.appendChild(font);
	    	}
	    	else{
	    		a = document.createElement("a");
		    	a.innerHTML = "   上一頁";

		    	//有搜索字串的話，超連結要放參數
		    	if(searchString=="")
		    		a.setAttribute("href", "kdrama.php?page="+(currentPage-1));
		    	else
		    		a.setAttribute("href", "kdrama.php?search="+searchString+"&page="+(currentPage-1));
		    	span.appendChild(a);
	    	}
	    	div.appendChild(span);

	    	var pageStart = 0; //頁數欄位-起始頁數
	    	var pageEnd = 0;   //頁數欄位-末頁數
	    	if(currentPage - 3 <= 0) 
	    		pageStart = 1;
	    	else 
	    		pageStart = currentPage - 3;

	    	if(currentPage + 3 > totalPage)
	    		pageEnd = totalPage;
	    	else 
	    		pageEnd = currentPage + 3;

	    	// span = document.createElement("span");
	    	for(i = pageStart ;i<=pageEnd;i++ ){
	    		if (i == currentPage){
	    			span = document.createElement("span");
	    			span.innerHTML = i;
		    		span.className = "page_num";
		    		div.innerHTML += "&nbsp&nbsp";
		    		div.appendChild(span);
	    		}
	    		else{
	    			a = document.createElement("a");
		    		a.innerHTML = i;
		    		a.className = "page_num";

		    		//有搜索字串的話，超連結要放參數
		    		if(searchString=="") 
			    		a.setAttribute("href", "kdrama.php?page="+i);
			    	else
			    		a.setAttribute("href", "kdrama.php?search="+searchString+"&page="+i);
			    	div.innerHTML += "&nbsp&nbsp";
		    		div.appendChild(a);
	    		}
	    		
	    	}

	    	span = document.createElement("span");
	    	if(currentPage == totalPage){
	    		font = document.createElement("font");
		    	font.innerHTML = "   下一頁";
		    	span.appendChild(font);
	    	}
	    	else{
	    		a = document.createElement("a");
		    	a.innerHTML = "   下一頁";

		    	//有搜索字串的話，超連結要放參數
		    	if(searchString=="")
		    		a.setAttribute("href", "kdrama.php?page="+(currentPage+1));
		    	else
		    		a.setAttribute("href", "kdrama.php?search="+searchString+"&page="+(currentPage+1));
		    	span.appendChild(a);
	    	}
	    	div.appendChild(span);

	    	p = document.createElement("p");
	    	p.innerHTML = "目前在第"+currentPage+"頁 共"+totalPage+"頁";
	    	p.setAttribute("align", "center");
	    	div.appendChild(p);
	    }

	    function addSearchString(obj, searchString){ //搜索字串
	    	var div = document.getElementById(obj);
	    	div.innerHTML = "以下為\""+searchString+"\"的搜尋結果";
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


	    var drama_data = <?php echo json_encode($drama_data); ?>;
	    var drama_data_ranking = <?php echo json_encode($drama_data_ranking); ?>;
	    var page = "<?php echo $page ?>";
	    var totalPage = "<?php echo $totalPage ?>";
	    var searchString = "<?php echo $search ?>";
	    
	    addLi("drama_list_ul", drama_data);
	    addRanking("ranking", drama_data_ranking);

	    if(searchString!=""){    //判斷是否為搜索頁面
	    	addSearchString("searchString", searchString);
	    }

		
	    addPage("page_div", parseInt(page), parseInt(totalPage), searchString);

	    var session = "<?php echo $session ?>";
	    if(session!=""){
	    	addAccountInfo("account_block", session);
	    }
	</script>
</html>
