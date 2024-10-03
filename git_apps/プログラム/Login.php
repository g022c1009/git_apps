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

font {
	margin-left:50px;
}

dl {
  display: flex;
  flex-wrap: wrap;
  width: 300px;
  margin-left:50px;
}

dt {
  float: left;
  clear: left;
  width: 80px;
}

dd {
  float: left ;
  min-width: 150px;
}

.base{
	display: flex;
	justify-content: center;
}
.contents {
  margin:5px;
  padding-top:15px;
  padding-bottom:15px;
  width:400px;
  outline: solid 1px black;
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
	$search_FLG = "";
	
	if (! is_null($cmd)) {
		// 入力されたユーザーが存在しているかチェックする。
		$result = check_login_info();
		
		if(count($result) == 1){
			$search_FLG = $result[0]["RETIRE_FLG"];
			$name = $result[0]["EMPLOYEE_NAME"];
			$count = $result[0]["LOGIN_FAILED_COUNT"];
			$locktime = $result[0]["LOCK_UNTIL"];

			if($search_FLG == 0){	//RETIRE_FLGが0の時	

				$failedCheckFlg = false;
				if(!is_null($locktime)){
					$now = new DateTime();
					$format_locktime = new DateTime($locktime);
					$failedCheckFlg = true;
				}

				if($format_locktime > $now && $failedCheckFlg ==true){	//間違えた回数が3回以上
					$errorMsg = 
					"<script type='text/javascript'>
						alert('3分経過していません。時間を空けてから再度お試しください。');
					</script>";

				} else {	//ログイン成功
					session_start();
					//$_SESSION = $_POST;
					$_SESSION['txtID'] = $_POST['txtID'];
					$_SESSION['txtPW'] = $_POST['txtPW'];
					$_SESSION['username'] = $name;
					session_regenerate_id(TRUE);
					update_reset();	// $count = 0;
					header('Location: ReserveList.php');
					exit;
				}
		
			} else {	//RETIRE_FLGが1の時
				$errorMsg = 
				"<script type='text/javascript'>
					alert('退職済みのユーザーです');
				</script>";
			}
			
		} else {	//IDまたはPWが間違っている時
			$errorMsg =
			"<script type='text/javascript'>
					alert('IDまたはPWが間違っています');
			</script>";
			update_count();	//$count + 1;
			$result = get_failed_count();

			if($result[0]["LOGIN_FAILED_COUNT"] >= 3){
				update_lock_time();
			}
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
				<dt>ID</dt>
				<dd><input type="text" id="txtID"  name="txtID" /></dd>
				<dt>PW</dt>
				<dd><input type="password" id="txtPW"  name="txtPW" /></dd>
				<dt><input type="button" value="ログイン" onclick="LoginButtonClick();"></dt>
				<dd><a href="PasswordReset.php">パスワードの変更</a></dd>
			</dl>
		</div>
	</div>
	<input type="hidden" name="cmd" value="">
</form>

<script>
// ----------------------------------
// ログインボタンのクリック
// ----------------------------------
function LoginButtonClick() {
	// 入力チェックを行う
	if (document.forms[0].txtID.value == "") {
		$('[name="errorMsg"]').text("IDを入力してください。");
		return;
	}
	if (document.forms[0].txtPW.value == "") {
		$('[name="errorMsg"]').text("PWを入力してください。");
		return;
	}

	document.forms[0].cmd.value = "login";
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
// 間違えた回数の変更
// ----------------------------------
function update_count()
{
	try{
		$search_ID = filter_input(INPUT_POST, "txtID");
		
		// データベースの接続情報を取得する
		$link = get_database_link();

		// 検索用クエリ
		$sql  = "";
		$sql .= " UPDATE";
		$sql .= "   MST_EMPLOYEE";
		$sql .= " SET";
		$sql .= " LOGIN_FAILED_COUNT  =  LOGIN_FAILED_COUNT + 1 ";
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
// 回数のリセット
// ----------------------------------
function update_reset()
{
	try{
		$search_ID = filter_input(INPUT_POST, "txtID");
		
		// データベースの接続情報を取得する
		$link = get_database_link();

		// 更新用クエリ
		$sql  = "";
		$sql .= " UPDATE";
		$sql .= "   MST_EMPLOYEE";
		$sql .= " SET";
		$sql .= " LOGIN_FAILED_COUNT  =  0 ";
		$sql .= " ,LOCK_UNTIL  =  NULL ";
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
// 間違えた時の時刻入力
// ----------------------------------
function update_lock_time()
{
	try{
		$search_ID = filter_input(INPUT_POST, "txtID");
		
		// データベースの接続情報を取得する
		$link = get_database_link();
		$now = new DateTime();
		$mod_now_time = $now->modify('+3 minute');

		// 検索用クエリ
		$sql  = "";
		$sql .= " UPDATE";
		$sql .= "   MST_EMPLOYEE";
		$sql .= " SET";
		$sql .= "  LOCK_UNTIL = '" . $mod_now_time->format('Y-m-d H:i:s') . "'";
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
// 間違えた回数を取得
// ----------------------------------
function get_failed_count()
{
	$search_ID = filter_input(INPUT_POST, "txtID");

	// データベースの接続情報を取得する
	$link = get_database_link();

	// 検索用クエリ
	$sql  = "";
	$sql .= " SELECT";
	$sql .= "  LOGIN_FAILED_COUNT AS LOGIN_FAILED_COUNT";
	$sql .= " FROM";
	$sql .= "   MST_EMPLOYEE";
	$sql .= " WHERE";
	$sql .= "  EMPLOYEE_CODE = '" . $search_ID . "'";

	// クエリを実行する
	$result = mysqli_query($link, $sql);

	// 件数を返す
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
