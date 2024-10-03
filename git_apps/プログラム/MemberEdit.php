<?php
include 'Common.php';
login_check();
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
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
.warning{
	margin-top:0px;
	color:red;
	font-size: 15px;
}

.base{
	display: flex;
	justify-content: center;
}
.contents {
  margin:5px;
  padding-top:15px;
  padding-bottom:15px;
  width:460px;
  outline: solid 1px black;
}

dl {
  display: flex;
  flex-wrap: wrap;
  width: 350px;
  margin-left:40px;
}

dt {
  width: 120px;
}

dd {
  min-width: 150px;
}

.base{
	display: flex;
	justify-content: center;
}

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<header>
	<h1></h1>
</header>
<?
	$prefectures = get_prefectures_info();
	
	
	$cmd = filter_input(INPUT_POST, "cmd");
	
	if (! is_null($cmd)) {
		// 登録処理を行う
		if(insert_menber() == true){
			header('Location: MemberList.php');
			exit;
		}
	}
	include 'Menubar.php';
?>



<form method="POST">
	<div class="base">
		<div class="contents">
		<p class="warning" name="warning"></p>
			<dl>
				<dt>氏名(漢字)</dd>
				<dd><input type="text" id="name" name="name" placeholder="田中 太郎" /></dd>
				<dt>氏名(かな)</dd>
				<dd><input type="text" id="nameKana" name="nameKana" placeholder="たなか たろう"/></dd>
				<dt>性別</dt>
				<dd><input type="radio"  name="rbGender" value="M" checked/>男性</label> <label><input type="radio"  name="rbGender" value="F"/>女性</label></dd>
				<dt>メールアドレス</dt>
				<dd><input type="text" id="email" name="email" placeholder="aaaabbbb@xxxx.yyy"/></dd>
				<dt>生年月日</dt>
				<dd><input type="date" id="birthday" name="birthday" placeholder="yyyy-mm-dd"/></dd>
				<dt>住所(都道府県)</dt>
				<dd>
					<SELECT id="prefectures" name="prefectures">
					?>
					<?php
					foreach ($prefectures as $pref) {
					?>
					<option value=<?php echo htmlspecialchars($pref["ADDRESS_CD"]) ?>><?= $pref["ADDRESS_NAME"] ?></option>
					<?php
					}
					?>
					</SELECT>
				</dd>
				<dt>電話番号1</dt>
				<dd><input type="text" id="tel" name="tel" placeholder="012-3456-789"/></dd>
				<dt><input type="button" value="登録" onclick="InsertButtonClick();"></dt>
				<dd></dd>
			</dl>
		</div>
	</div>
	<input type="hidden" name="cmd" value="">
</form>

<script>
// ----------------------------------
// 登録のクリック
// ----------------------------------
function InsertButtonClick() {
	var telnum = $('[name=tel]').val();

	var regex = /^[-]?([1-9]\d*|0)$/;


	if($('[name=name]').val()==""){
		$('[name="warning"]').text("氏名(漢字)を入力してください");
		return false;
	}else if($('[name=nameKana]').val()==""){
		$('[name="warning"]').text("氏名(かな)を入力してください。");
		return false;
	}else if($('[name=email]').val()==""){
		$('[name="warning"]').text("メールアドレスを入力してください。");
		return false;
	}else if($("#birthday").val()==""){
		$('[name="warning"]').text("生年月日を選択してください。");
		return false;
	}else if($('[name=tel]').val()==""){
		$('[name="warning"]').text("電話番号を入力してください。");
		return false;
	}/*else if(!regex.test(telnum)){
		$('[name="warning"]').text("電話番号に数値で入力してくださいしてください。");
	}*/


	$('[name="warning"]').text("");
	$('[name="txtMail"]').text("");
	$('[name="txtName"]').text("");

	document.forms[0].cmd.value = "insert";
	document.forms[0].submit();
}
</script>
</body>
</html>
<?
// ---------------------
// 都道府県を取得する
// ---------------------
function get_prefectures_info()
{

	// データベースの接続情報を取得する
	$link = get_database_link();

	// 検索用クエリ
	$sql  = "";
	$sql .= " SELECT";
	$sql .= "   ADDRESS_CD AS ADDRESS_CD";
	$sql .= "  ,ADDRESS_NAME AS ADDRESS_NAME";
	$sql .= " FROM";
	$sql .= "   MST_ADDRESS";
	$sql .= " WHERE";
	$sql .= "   1 = 1";

	// クエリを実行する
	$result = mysqli_query($link, $sql);

	// 件数を返す
	//return $result;

	return mysqli_fetch_all($result, MYSQLI_ASSOC);


}
// ----------------------------------
// 会員情報の登録
// ----------------------------------
function insert_menber()
{
	try{
		$name = filter_input(INPUT_POST, "name");
		$nameKana = filter_input(INPUT_POST, "nameKana");
		$gender = filter_input(INPUT_POST, "rbGender");
		$email = filter_input(INPUT_POST, "email");
		$birthday = filter_input(INPUT_POST, "birthday");
		$prefectures = filter_input(INPUT_POST, "prefectures");
		$tel = filter_input(INPUT_POST, "tel");
		if($name == "" || $gender == "" || $email == "" || $birthday == "" || $prefectures == "" || $tel == "")
		{
			return;
		}
		// データベースの接続情報を取得する
		$link = get_database_link();
		
		// 登録用クエリ
		$sql  = "";
		$sql .= " INSERT INTO";
		$sql .= "   MST_MEMBER";
		$sql .= "( NO";
		$sql .= " ,NAME";
		$sql .= " ,NAME_KANA";
		$sql .= " ,GENDER_CD";
		$sql .= " ,MAIL_ADDRESS";
		$sql .= " ,BIRTHDAY";
		$sql .= " ,ADDRESS_CD";
		$sql .= " ,TEL1";
		$sql .= " ,DELETE_FLG";
		$sql .= " )";
		$sql .= " VALUES (";
		$sql .= "  (select max(NO) +1  from mst_member as No )";
		$sql .= " ,'" . $name . "'";
		$sql .= " ,'" . $nameKana . "'";
		$sql .= " ,'" . $gender . "'";
		$sql .= " ,'" . $email . "'";
		$sql .= " ,'" . $birthday . "'";
		$sql .= " ,'" . $prefectures . "'";
		$sql .= " ,'" . $tel . "'";
		$sql .= " ,0"; 
		$sql .= " )";
		
		// クエリを実行する
		$result = mysqli_query($link, $sql);
	}
	catch (Exception $e){
		$result  = false;
		echo $e->getMessage();
	}
	return $result;
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
