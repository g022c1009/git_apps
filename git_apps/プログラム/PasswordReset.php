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

.base{
	display: flex;
	justify-content: center;
}

.contents {
  margin:5px;
  padding-top:15px;
  padding-bottom:15px;
  width:450px;
  outline: solid 1px black;
}

font {
	margin-left:40px;
}

dl {
  display: flex;
  flex-wrap: wrap;
  width: 400px;
  margin-left:40px;
}

dt {
  width: 150px;
}

dd {
  min-width: 150px;
}

</style>
</head>
<body>
<header>
</header>
<?
	$cmd = filter_input(INPUT_POST, "cmd");
	$errorMsg = "";
	$search_FLG = "";
	if (! is_null($cmd)) {
		$result = check_login_info();

	   	if(count($result) == 1){
		$search_FLG = $result[0]["RETIRE_FLG"];
			if($search_FLG == 0){	//RETIRE_FLGが0の時
				update_password();
				header('Location: ReserveList.php');
				exit;
			} else {	//RETIRE_FLGが1の時
				$errorMsg = 
				"<script type='text/javascript'>
					alert('退職済みのユーザーです');
				</script>";
			} 
		} else {
			$errorMsg =
			"<script type='text/javascript'>
					alert('IDまたは現在のPWのが間違っています');
			</script>";
		}
	}
?>
<form method="POST">
	<div class="base">
		<div class="contents">
			<font color="red">
				<span name="errorMsg"><?php echo($errorMsg) ?></span>
			</font>
			<dl>
				<dt>ID</dd>
				<dd><input type="text" id="txtID"  name="txtID" /></dd>
				<dt>現在のPW</dt>
				<dd><input type="text" id="txtPW"  name="txtPW" /></dd>
				<dt>新しいPW</dt>
				<dd><input type="text" id="txtPWNew1"  name="txtPWNew1" pattern="^\w{7,}$" placeholder="半角英数字7文字以上" /></dd>
				<dt>新しいPWの確認</dt>
				<dd><input type="text" id="txtPWNew2"  name="txtPWNew2" /></dd>
				<dt><input type="button" value="パスワードの変更" onclick="ResetButtonClick();"></dt>
				<dd></dd>
			</dl>
		</div>
	</div>
	<input type="hidden" name="cmd" value="">
</form>

<script>
// ----------------------------------
// ログインボタンのクリック
// ----------------------------------
function ResetButtonClick() {
	// 入力チェックを行う
	if (document.forms[0].txtID.value == "") {
		$('[name="errorMsg"]').text("IDを入力してください。");
		return;
	}
	if (document.forms[0].txtPW.value == "") {
		$('[name="errorMsg"]').text("現在のPWを入力してください。");
		return;
	}
	if (document.forms[0].txtPWNew1.value == "") {
		$('[name="errorMsg"]').text("新しいPWを入力してください。");
		return;
	}
	if (document.forms[0].txtPWNew2.value == "") {
		$('[name="errorMsg"]').text("新しいPWの確認を入力してください。");
		return;
	}
	if (document.forms[0].txtPWNew1.value != document.forms[0].txtPWNew2.value) {
		$('[name="errorMsg"]').text("新しいPWと新しいPWの確認の入力内容が一致しません");
		return;
	}
	//var newPW = document.forms[0].txtPWNew1.value.match(/\w{7,}/);
	if (!document.forms[0].txtPWNew1.value.match(/^\w{7,}$/)) {
		$('[name="errorMsg"]').text("アンダースコアを含む半角英数字7文字以上使用してください");
		return;
	}

	document.forms[0].cmd.value = "reset";
	document.forms[0].submit();
}
</script>
</body>
</html>
<?

// ----------------------------------
// ログインチェックを行う
// ----------------------------------
function check_login_info()
{

	// データベースの接続情報を取得する
	$link = get_database_link();

	// 検索用クエリ
	$sql  = "";
	$sql .= " SELECT";
	$sql .= "   EMPLOYEE_CODE AS EMPLOYEE_CODE";
	$sql .= "  ,EMPLOYEE_NAME AS EMPLOYEE_NAME";
	$sql .= "  ,LOGIN_PASSWORD AS LOGIN_PASSWORD";
	$sql .= "  ,RETIRE_FLG AS RETIRE_FLG";
	$sql .= "  ,LOGIN_FAILED_COUNT AS LOGIN_FAILED_COUNT";
	$sql .= "  ,LOCK_UNTIL AS LOCK_UNTIL";
	$sql .= " FROM";
	$sql .= "   MST_EMPLOYEE";
	$sql .= " WHERE";
	$sql .= "   1 = 1";

	$search_ID = filter_input(INPUT_POST, "txtID");
	$search_PW = filter_input(INPUT_POST, "txtPW");

	if ($search_ID != "") {
		$sql .= "  AND (EMPLOYEE_CODE = '" . $search_ID . "')";
	}
	if ($search_PW != "") {
		$sql .= "  AND (LOGIN_PASSWORD = '" . $search_PW . "')";
	}

	// クエリを実行する
	$result = mysqli_query($link, $sql);

	// 件数を返す
	return mysqli_fetch_all($result, MYSQLI_ASSOC);

}

// ----------------------------------
// パスワードの変更
// ----------------------------------
function update_password()
{
	try{

		$search_PWNew = filter_input(INPUT_POST, "txtPWNew1");
		$search_ID = filter_input(INPUT_POST, "txtID");
		$search_PW = filter_input(INPUT_POST, "txtPW");
		if($search_PWNew == "" || $search_ID == ""  || $search_PW == "" )
		{
			return;
		}
		
		
		// データベースの接続情報を取得する
		$link = get_database_link();


		// 検索用クエリ
		$sql  = "";
		$sql .= " UPDATE";
		$sql .= "   MST_EMPLOYEE";
		$sql .= " SET";
		$sql .= "  LOGIN_PASSWORD = '" . $search_PWNew . "'";
		$sql .= " WHERE";
		$sql .= "  EMPLOYEE_CODE = '" . $search_ID . "'";
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
