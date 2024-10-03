<?php
include 'Common.php';
login_check();
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<title></title>
<style>
	
body {

	margin: 0;
	padding: 0;
}

/* 画面タイトル */
body > header {
	padding: 0 1em;
}
body > header > h1 {
	margin: 0;
	padding: 0.5em 0;
	font-size: 1.25em;
}
body > header > h1:empty {
	padding: 0;
}
body > form {
	position: relative;
	padding: 0 1em;
	box-shadow: 2px 2px 2px 0 rgba(0, 0, 0, .1);
}
td, th {
	padding: 0 1em;
	border-top: 1px solid #D0D0D0;
	border-left: 1px solid #D0D0D0;
	font-weight: normal;
}
input, select, textarea, button {
	margin: 0.5em 0;
}
.base{
	display: flex;
	justify-content: center;
}
.contents {
	background-color: white;
  margin:5px;
  padding:15px;
  width:600px;
  outline: solid 1px black;
}
dl {
  display: flex;
  flex-wrap: wrap;
  width: 550px;
  margin:0px;
}
dt {
 float: left ;
 clear: left ;
 width: 150px;
 text-decoration:underline;
}

dd {
 float: left ;
  min-width: 300px;
}

.child {
  width: 360px;
}
.required{
  text-decoration:underline #FF0010;
}

.child dt {
  width: 120px;
}
.child dd {
  min-width: 200px;
}
.warning{
	margin-top:0px;
	color:red;
	font-size: 15px;
}
.infoh1{
	margin:5px;
	font-size: 35px;
}
.infoh2{
	text-decoration:underline ;
	margin: 5px;
	font-size:25px ;
}
.infop{
	margin:5px;
	font-size: 15px;
	white-space: break-spaces;
}
.subsc{
	float:left;
	width: 150px;
	font-size: 12px;
	color: var(--background-color);
}
.submitbtn{
	margin: 3px;
	display: table-cell;
}
.inforeq{
	float:right;
	width: 100px;
	font-size: 12px;
	color:#FF0010;
}
input[type="date"]{
    width: 100px;
    position: relative;
}
input[type="date"]::-webkit-calendar-picker-indicator {
    position: absolute;
    width: 100%;
    height: 100%;
	opacity: 0;
}

</style>
</head>
<body>

<header>
	<h1></h1>
</header>
<?
	$cmd = filter_input(INPUT_POST, "cmd");

	$errorMsg = "";
	
	$tours = SearchDropDown();

	if (!is_null($cmd)) {
		// 登録処理
		insert_reserve();
			sleep(1);
			header("Location:".$_SERVER['PHP_SELF']);
		exit;
	}
	include 'Menubar.php';
?>
<form method="POST"  onReset="resetClick()">
	<div class="base">
		<div class="contents">
			<p class="warning" name="warning"></p>
			<p></p>
			<dl>
				<dt class="required">ツアーコード</dt>
				<dd>
					<td>
						<SELECT name="mst_tour" onchange="changeTour();">
							<option value="0">ツアーを選択してください</option>
							<?php
							foreach($tours as $tour){
							?>
							<option value="<?= $tour["TOUR_CODE"] ?>" data-day="<?= $tour["TOUR_DAYS"] ?>"><?= $tour["TOUR_CODE"] ?>:<?= $tour["TOUR_NAME"] ?></option>
							<?
							}
							?>
						</SELECT>
					</td>
				</dd>
				<dt class="required">出発日</dt>
				<dd>
					<input type="date" id="txtDepDate" name="txtDepDate" min="" max=""  onchange="changeTour();" />
				</dd>
				<dt class="under">選択可能出発日</dt>
				<dd name="formPrieod">ツアーコードを選択してください。</dd>
				<dt class="under">帰着日</dt>
				<dd name='goback'>ツアーを選択し、出発日を入力してください。</dd>
				<dt class="under">参加者</dt>
				<dd>
					<dl class="child">
						<dt class="required">氏名</dt>
						<dd><input type="text" id="txtName" name="txtName" /></dd>
						<dt>メールアドレス</dt>
						<dd><input type="text" id="txtMail" name="txtMail" /></dd>
						<dt>性別</dt>
						<dd><label><input type="radio"  name="rbGender" value="M" checked/>男性</label>　<label><input type="radio"  name="rbGender" value="F"/>女性</label></dd>
					</dl>
				</dd>
			</dl>
			<div>
				<input type="button" value="作成" class="submitbtn" onclick="submitCheck();">
				<input type="reset" value="リセット" class="submitbtn">
			</div>
			<div>
				<p class="subsc" name="subsc"></p>
				<p class="inforeq">※赤下線入力必須</p>
			</div>
			
		</div>
	</div>
	<input type="hidden" name="cmd" value="">
	
</form>
<div class="base">
		<div class="contents" onchange="changeDay()">
			<h1 class="infoh1">ツアー内容詳細</h1>
			<h2 class="infoh2" name="changeName"></h2>
			<p class="infop" name="changeDetail"></p>
			<h2 class="infoh2" name="changePrieod"></h2>
			<h2 class="infoh2" name="changeFare"></h2>
			<p name="test5"></p>
			<p name="test6"></p>				
		</div>
		
	</div>
	<?php
		foreach($tours as $tour){
	?>
	<input type="hidden" name="t_name_<?= $tour["TOUR_CODE"] ?>" value="<?= $tour["TOUR_NAME"] ?>">
	<input type="hidden" name="t_detail_<?= $tour["TOUR_CODE"] ?>" value="<?= $tour["TOUR_DETAIL"] ?>">
	<input type="hidden" name="t_fare_<?= $tour["TOUR_CODE"] ?>" value="<?= $tour["TOUR_FARE"] ?>">
	<input type="hidden" name="t_from_<?= $tour["TOUR_CODE"] ?>" value="<?= $tour["TOUR_PRIEOD_FROM"] ?>">
	<input type="hidden" name="t_to_<?= $tour["TOUR_CODE"] ?>" value="<?= $tour["TOUR_PRIEOD_TO"] ?>">
	<?
		}
	?>	
<script>
//------------------------------------
//登録完了時のメッセージの色を変える処理
//------------------------------------
function randomColor(name){
  document.addEventListener('DOMContentLoaded', function(){
    let result = "#";
    for(let i = 0; i < 6; i++) {
      result += (16 * Math.random() | 0).toString(16);
    }
    document.documentElement.style.setProperty(name, result);
  });  
}
randomColor('--background-color');

// ----------------------------------
// yyyy/mm/ddへの変換処理
// ----------------------------------
function formatDate(date, sep="") {
	const yyyy = date.getFullYear();
	const mm = ('00' + (date.getMonth()+1)).slice(-2);
 	 const dd = ('00' + date.getDate()).slice(-2);

  	return `${yyyy}${sep}${mm}${sep}${dd}`;
}

// ----------------------------------
// 入力項目の過不足がないか確認
// ----------------------------------
function submitCheck(){
	var changeCode= $('[name=mst_tour]').val();
	var tourFrom = $('[name=t_from_' + changeCode +']').val();
	var tourTo = $('[name=t_to_' + changeCode +']').val();	
	if($('[name=mst_tour]').val()=="" || $('[name=mst_tour]').val()=="0"){
		$('[name="warning"]').text("ツアーコードを選択してください。");
		return false;
	}else if($("#txtDepDate").val()==""){
		$('[name="warning"]').text("出発日を入力してください。");
		return false;
	}else if($("#txtName").val()==""){
		$('[name="warning"]').text("参加者の氏名を入力してください。");
		return false;
	}else if(tourFrom>$('#txtDepDate').val()||tourTo<$('#txtDepDate').val()){
		$('[name="warning"]').text("予約可能な範囲内で出発日を入力してください。");
		return false;
	}
	var tourName = $('[name=t_name_' + changeCode +']').val();
	var checkDay=$('[name="txtDepDate"]').val();
	var checkName=$('[name="txtName"]').val();
	var checkMail=$('[name="txtMail"]').val();
	let elements = document.getElementsByName('rbGender');
	let len = elements.length;
	let checkGender = '';
	for (let i = 0; i < len; i++){
   	 	if (elements.item(i).checked){
			checkGender = elements.item(i).value;
    	}
	}
	if(checkGender=='M'){
		var genderJp="男性";
	}else if(checkGender=='F'){
		var genderJp="女性";
	}
	let result = confirm('登録内容の確認\nツアー内容:'+tourName+'\nツアー出発日:'+checkDay+'\nお名前:'+checkName+'\nメールアドレス:'+checkMail+"\n性別:"+genderJp);
	if (result) {
	} else {
		alert("送信しませんでした");
		return false;
	}
	$('[name="warning"]').text("");
	$('[name="txtMail"]').text("");
	$('[name="txtName"]').text("");
	$('[name="subsc"]').text("登録完了しました。");
	InsertReserve();
}

// ----------------------------------
// 作成ボタンのクリック
// ----------------------------------
function InsertReserve() {
	document.forms[0].cmd.value = "reserve";
	document.forms[0].submit();
}

// ----------------------------------
// 帰着日の登録
// ----------------------------------
function changeTour() {
	$('[name="subsc"]').text("");
	//ツアー詳細を表示
	var changeCode= $('[name=mst_tour]').val();
	var tourName = $('[name=t_name_' + changeCode +']').val();
	var tourDetail = $('[name=t_detail_' + changeCode +']').val();
	var tourFare = $('[name=t_fare_' + changeCode +']').val();
	var tourFrom = $('[name=t_from_' + changeCode +']').val();
	var tourTo = $('[name=t_to_' + changeCode +']').val();	

	if(changeCode==0){
		$('[name="changeName"]').text("");
		$('[name="changeDetail"]').text("");
		$('[name="changePrieod"]').text("");
		$('[name="formPrieod"]').text("ツアーを選択してください")
		$('[name="changeFare"]').text("");
	}else{
		$('[name="changeName"]').text(tourName);
		$('[name="changeDetail"]').text(tourDetail);
		$('[name="changePrieod"]').text("ツアー出発日:"+tourFrom+"～"+tourTo);
		$('[name="formPrieod"]').text(tourFrom+"～"+tourTo);
		$('[name="changeFare"]').text("ツアー料金："+tourFare+"円");
		$('[name="txtDepDate"]').attr("min",tourFrom);
		$('[name="txtDepDate"]').attr("max",tourTo);
	}
	//日付変更時に帰着日を更新、入っていなければ表示
	var selectDay = $('[name="mst_tour"] option:selected').data("day");
	var selectDay = parseInt(selectDay);
	var showDay = $('#txtDepDate').val();
	var showDay=showDay.replace(/-/g,'/');
	var showDay=new Date(showDay);
	var showDay=showDay.setDate(showDay.getDate()+selectDay-1);
	var showDay=new Date(showDay).toLocaleDateString("ja-JP", {year: "numeric",month: "2-digit",day: "2-digit"}).replaceAll('/', '-');
	if(showDay=="Invalid Date"){
		$('[name="goback"]').text("ツアーを選択し、出発日を入力してください。");
	}else{
		$('[name="goback"]').text(showDay);
	}
	
}

//----------------------------------
//リセット時に消えないところを消す処理
//----------------------------------
function resetClick(){
	$('[name="changeName"]').text("");
	$('[name="warning"]').text("");
	$('[name="changeDetail"]').text("");
	$('[name="changePrieod"]').text("");
	$('[name="formPrieod"]').text("ツアーコードを選択してください。")
	$('[name="changeFare"]').text("");
	$('[name="txtDepDate"]').attr("min","");
	$('[name="txtDepDate"]').attr("max","");
	$('[name="goback"]').text("ツアーを選択し、出発日を入力してください。");
}
</script>
</body>
</html>
<?

// ----------------------------------
// データを登録する
// ----------------------------------
function insert_reserve()
{
	$tourCode = filter_input(INPUT_POST, "mst_tour");
	$depDate = filter_input(INPUT_POST, "txtDepDate");
	$member = filter_input(INPUT_POST, "slReserveMenber");
	
	if($tourCode == "" || $depDate == "")
	{
		return;
	}
	try{
		// データベースの接続情報を取得する
		$link = get_database_link();
		
		// 予約番号を取得
		$sql  = "";
		$sql .= " SELECT";
		$sql .= "   MAX(RSV_NO) + 1 AS RSV_NO";
		$sql .= " FROM";
		$sql .= "  RESERVE_TOUR";

		// クエリを実行する
		$result = mysqli_query($link, $sql);
	    $data = mysqli_fetch_assoc($result);
	    $reserveNo = $data["RSV_NO"];
	    
	    if($reserveNo == "")
		{
			$reserveNo = 1;
		}
		
		// 予約情報の登録
		$sql  = "";
		$sql .= " INSERT INTO";
		$sql .= "   RESERVE_TOUR";
		$sql .= "( RSV_NO";
		$sql .= " ,TOUR_CODE";
		$sql .= " ,DEPARTURE_DATE";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .= "  " . $reserveNo;
		$sql .= " ,'" . $tourCode . "'";
		$sql .= " ,'" . $depDate . "'";
		$sql .= " )";
		
		$seq = 1;
		// クエリを実行する
		$result = mysqli_query($link, $sql);
	    
		$name = filter_input(INPUT_POST, "txtName");
		$gender = filter_input(INPUT_POST, "rbGender");
		$mail = filter_input(INPUT_POST, "txtMail");
		// 入力された内容で登録
		// 旅行者の登録2
		$sql  = "";
		$sql .= " INSERT INTO";
		$sql .= "   RESERVE_TRAVELER";
		$sql .= "( RSV_NO";
		$sql .= " , SEQ";
		$sql .= " ,NAME";
		$sql .= " ,MAIL_ADDRESS";
		$sql .= " ,GENDER_CD";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .= "  " . $reserveNo;
		$sql .= " ,1";
		$sql .= " ,'" . $name ."'";
		$sql .= " ,'" . $mail ."'";
		$sql .= " ,'" . $gender ."'";
		$sql .= " )";
		echo($sql);
		// クエリを実行する
		$result = mysqli_query($link, $sql);	
	}
	catch (Exception $e){
		$result  = false;
		echo $e->getMessage();
	}
}

// ----------------------------------
// DB[mst_tour]へ接続
// ----------------------------------
function SearchDropDown(){
	try{
		// データベースの接続情報を取得する
		$link = get_database_link();

		// 検索用クエリ
		$sql  = "";
		$sql .= " SELECT";
		$sql .= " TOUR_CODE";
		$sql .= ",TOUR_NAME";
		$sql .= ",TOUR_PRIEOD_FROM";
		$sql .= ",TOUR_PRIEOD_TO";
		$sql .= ",TOUR_DAYS";
		$sql .= ",TOUR_DETAIL";
		$sql .= ",TOUR_FARE";
		$sql .= ",CANCEL_FARE";
		$sql .= " FROM ";	
		$sql .= "mst_tour";
		// クエリを実行する
		$result = mysqli_query($link, $sql);
		
	}
	catch (Exception $e){
		echo $e->getMessage();
	}
	// すべての行を取得して、返却する
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ----------------------------------
// データベースの接続情報を取得する (変更不可)
// ----------------------------------
function get_database_link()
{
	static $info = [
		"url" => "localhost:3306",
		"username" => "internuser",
		"password" => "internpass",
		"database" => "interndb",
		"link" => null
	];

	if (! is_null($info["link"])) {
		return $info["link"];
	}

	if (file_exists(dirname(__FILE__) . '/is_intern1')) {
		$info["database"] = "interndb1";
	} else if (file_exists(dirname(__FILE__) . '/is_intern2')) {
		$info["database"] = "interndb2";
	} else if (file_exists(dirname(__FILE__) . '/is_intern3')) {
		$info["database"] = "interndb3";
	} else if (file_exists(dirname(__FILE__) . '/is_intern4')) {
		$info["database"] = "interndb4";
	} else if (file_exists(dirname(__FILE__) . '/is_intern5')) {
		$info["database"] = "interndb5";
	}

	$info["link"] = mysqli_connect($info["url"], $info["username"], $info["password"]);

	mysqli_select_db($info["link"], $info["database"]);

	mysqli_query($info["link"], "SET NAMES utf8");

	return $info["link"];
}