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
table {
	margin: 0.5em 0;
	border-bottom: 1px solid #D0D0D0;
	border-right: 1px solid #D0D0D0;
	border-collapse: separate;
	border-spacing: 0;
	box-shadow: 2px 2px 2px 0 rgba(0, 0, 0, .1);
	background-color: white;
	white-space: nowrap;
}
/** */
.base{
	display: flex;
	justify-content: center;
}
.contents {
	margin:5px;
	padding-top:20px;
	padding-bottom:20px;
	width:450px;
	outline: solid 1px black;
}
.list {
	margin:5px;
	padding-top:20px;
	padding-bottom:20px;
	width:100%;
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
/** */
td, th {
	padding: 0 1em;
	border-top: 1px solid #D0D0D0;
	border-left: 1px solid #D0D0D0;
	font-weight: normal;
}
input, select, textarea, button {
	margin: 0.5em 0;
}

.table-container {
	display: flex;
	justify-content: center;
	padding: 0.5em 1em;
	background-color: lightgray;
}

/* テーブルヘッダの行 */
.table-container thead > tr {
	background-color: gray;
}
</style>
</head>
<body>
<header>
	<h1></h1>
</header>
<?
	include 'Menubar.php';
	
    // 検索結果を取得
	$cmd = filter_input(INPUT_POST, "cmd");
	// 都道府県情報を取得
	$prefectures = get_prefectures_info();
	//削除する顧客のNOを取得
    $deleteNo = filter_input(INPUT_POST, "delete_no");
	// 顧客情報削除(論理削除)
	if ($deleteNo != ""){
		delete_member($deleteNo);
		}
?>
<form method="POST">
<div class="base">
		<div class="contents">
		<br /><a href="MemberEdit.php">会員登録</a>
			<dl>
			<dt>氏名(漢字)</dd>
			<dd><input type="text" id="name" name="name" placeholder="田中 太郎"/></dd>
			<dt>氏名(かな)</dd>
			<dd><input type="text" id="nameKana" name="nameKana" placeholder="たなか たろう"/></dd>
			<dt>メールアドレス</dt>
			<dd><input type="text" id="email" name="email" placeholder="aaaabbbb@xxxx.yyy"/></dd>
			<dt>住所(都道府県)</dt>
				<dd>
					<SELECT id="prefectures" name="prefectures">
						<option></option>
					<?php
					foreach ($prefectures as $pref) {
					?>
					
					<option><?= $pref["ADDRESS_NAME"] ?></option>
					<?php
					}
					?>
					</SELECT>
				</dd>
			</dl>
			<input type="button" value="表示" onclick="showSearchResults();">
			<input type="button" value="リセット"name="reset_btn" class="reset_btn">
			
		</div>
	</div>
	<?
	        if (isset($cmd)) {
				// 検索結果
		        $rows = search();
		        // 検索結果を表示する
		        show_search_results($rows);
	        }
            ?>
	<input type="hidden" name="cmd" value="">
	<input type="hidden" name="page_number" value="1">
	<input type="hidden" name="per_page" value="">
	<input type="hidden" name="delete_no" value="">
	<input type="hidden" name="order_list" value="<?php echo filter_input(INPUT_POST, "order_list");?>">
</form>
<script>
// ----------------------------------
// 表示ボタンのクリック
// ----------------------------------
function showSearchResults() {
	document.forms[0].cmd.value = "search";
	document.forms[0].per_page.value = "10";
	document.forms[0].submit();
}
// ----------------------------------
// リセットボタンがクリックされたとき
// ----------------------------------
$('.reset_btn').on('click', function(){
	document.forms[0].order_list.value = $(this).attr("");
	document.forms[0].submit();
})
// ----------------------------------
// 昇順降順ボタンがクリックされたとき
// ----------------------------------
$('.order_button').on('click', function(){
	//document.forms[0].order_listのvalueに対して名前を格納したい
	//カンマ区切りで格納したいけど、一律カンマを先頭に入れると問題あります。
	//どうにかして先頭だけカンマをつけずに値を格納するようにしてください。
	if(document.forms[0].order_list.value == ""){
		document.forms[0].order_list.value += $(this).attr('name');
	} else {
	 	document.forms[0].order_list.value += ","
		document.forms[0].order_list.value += $(this).attr('name');
	}

	document.forms[0].submit();
})
// ----------------------------------
// 削除ボタンのクリック
// ----------------------------------
$('input[name="delete_btn"]').on('click', function(){
	var btnObject = $(this);
	if(window.confirm('この操作を実行しますか？')){
		var delete_no = $(btnObject).data('member-no');
		document.forms[0].delete_no.value = delete_no;
		document.forms[0].submit();
        } else {
			// 何もしない
			return;
		}
});
</script>
</body>
</html>
<?
// ----------------------------------
// 検索結果を表示する
// ----------------------------------
function show_search_results($rows)
{
?>
<div class="base">
<div class="list">
<div class="table-container">
	<table>
		<thead>
			<tr>
				<th>NO.<input type="button" value="昇順" name="ascending_no_btn" class="order_button"><input type="button" value="降順" name="descending_no_btn" class="order_button"></th>
				<th>名前<input type="button" value="昇順" name="ascending_name_btn"  class="order_button"><input type="button" value="降順" name="descending_name_btn"  class="order_button"></th>
				<th>性別<input type="button" value="昇順" name="ascending_gender_btn"  class="order_button"><input type="button" value="降順" name="descending_gender_btn"  class="order_button"></th>
				<th>メールアドレス<input type="button" value="昇順" name="ascending_email_btn"  class="order_button"><input type="button" value="降順" name="descending_email_btn"  class="order_button"></th>
				<th>誕生日<input type="button" value="昇順" name="ascending_birthday_btn"  class="order_button"><input type="button" value="降順" name="descending_birthday_btn"  class="order_button"></th>
				<th>住所<input type="button" value="昇順" name="ascending_prefectures_btn"  class="order_button"><input type="button" value="降順" name="descending_prefectures_btn"  class="order_button"></th>
				<th>電話番号1<input type="button" value="昇順" name="ascending_tel_btn"  class="order_button"><input type="button" value="降順" name="descending_tel_btn"  class="order_button"></th>
				<th>削除ボタン</th>
			</tr>
		</thead>
		<tbody>
<?
	foreach ($rows as $row) {
		$dateFormat = new Datetime($row["BIRTHDAY"]);
		?>
			<tr>
				<td><?= $row["NO"] ?> </td>
				<td><?= $row["NAME"] ?></td>
				<td><?= $row["GENDER_NAME"] ?></td>
				<td><?= $row["MAIL_ADDRESS"] ?></td>
				<td><?= $dateFormat->format("Y/m/d"); ?></td>
				<td><?= $row["ADDRESS_NAME"] ?></td>
				<td><?= $row["TEL1"] ?></td>
				<td><input type="button" name="delete_btn" value="削除" data-member-no="<?= $row["NO"] ?>"></td>
				<input type="hidden" name="delete_pocket" value="<?= $row["NO"] ?>">
			</tr>
        <?
	}
?>
		</tbody>
	</table>
</div>
</div>
</div>
<?
}
?>
<?
// ----------------------------------
// 検索結果を取得する
// ----------------------------------
function search()
{
	try{
		// 入力した名前の取得
		$name = filter_input(INPUT_POST, "name");
		// 入力した名前の取得
		$nameKana = filter_input(INPUT_POST, "nameKana");
		// 入力したメールアドレスを取得
		$email = filter_input(INPUT_POST, "email");
		//選択した都道府県を取得
		$prefectures = filter_input(INPUT_POST, "prefectures");
		//生年月日の習得
		$birthday = filter_input(INPUT_POST, "birthday");
		//電話番号を取得
		$tel = filter_input(INPUT_POST, "tel");
		$test = filter_input(INPUT_POST, "order_list");
		// データベースの接続情報を取得する
		$link = get_database_link();
		// 検索用クエリ
		$sql  = "";
		$sql .= " SELECT";
		$sql .= "   MEMBER.NO AS NO";
		$sql .= "  ,MEMBER.NAME AS NAME";
		$sql .= "  ,MEMBER.NAME_KANA AS NAME_KANA";
		$sql .= "  ,GENDER.GENDER_NAME AS GENDER_NAME";
		$sql .= "  ,MEMBER.MAIL_ADDRESS AS MAIL_ADDRESS";
		$sql .= "  ,MEMBER.BIRTHDAY AS BIRTHDAY";
		$sql .= "  ,ADDRESS.ADDRESS_NAME AS ADDRESS_NAME";
		$sql .= "  ,MEMBER.TEL1 AS TEL1";
		$sql .= " FROM";
		$sql .= "   MST_MEMBER AS MEMBER";
		$sql .= " LEFT JOIN MST_GENDER AS GENDER";
		$sql .= "    ON GENDER.GENDER_CD = MEMBER.GENDER_CD";
		$sql .= " LEFT JOIN MST_ADDRESS AS ADDRESS";
		$sql .= "    ON ADDRESS.ADDRESS_CD = MEMBER.ADDRESS_CD";
		$sql .= " WHERE";
		$sql .= "   1 = 1";
		$sql .= "   AND DELETE_FLG = 0";
		//入力された漢字の文字が入っている名前を表示
		if($name != "" ) {
			$sql .= " AND `NAME` LIKE '%$name%'";
		}
		//ひらがな
		else if($nameKana != "" ) {
			$sql .= " AND `NAME_KANA` LIKE '%$nameKana%'";
		}
		//入力された文字が入っているメールアドレスを表示
		if($email != ""){
			$sql .= " AND `MAIL_ADDRESS`LIKE '%$email%'";
		}
		//選択された都道府県をすべて出す
		if($prefectures != ""){
		$sql .= " AND `ADDRESS_NAME` = '$prefectures'";
		}
		if($test != ""){
			//$stack = array($test);
			$stack = explode(",", $test);
			$sql .= " ORDER BY 1=1";
			foreach($stack as $name){
				//昇順ボタンが押されたら(NO)
				if($name == 'ascending_no_btn'){
					$sql .= " , NO ";
				}
				//降順ボタンが押されたら(NO)
				if($name == 'descending_no_btn'){
					$sql .= " , NO DESC";
				}
			    //昇順ボタンが押されたら(名前)
    			if($name == 'ascending_name_btn'){
	    			$sql .= " , NAME_KANA ";
		    	}
			    //降順ボタンが押されたら(名前)
    			if($name == 'descending_name_btn'){
	    			$sql .= " , NAME_KANA DESC";
		    	}
			    //昇順ボタンが押されたら(性別)
			    if($name == 'ascending_gender_btn'){
				    $sql .= " , GENDER_NAME ASC";
			    }
    			//降順ボタンが押されたら(性別)
	    		if($name == 'descending_gender_btn'){
		    		$sql .= " , GENDER_NAME DESC";
			    }
			    //昇順ボタンが押されたら(メールアドレス)
    			if($name == 'ascending_email_btn'){
	    			$sql .= " , MAIL_ADDRESS";
		    	}
			    //降順ボタンが押されたら(メールアドレス)
    			if($name == 'descending_email_btn'){
	    			$sql .= " , MAIL_ADDRESS DESC";
		    	}
			    //昇順ボタンが押されたら(誕生日)
    			if($name == 'ascending_birthday_btn'){
	    			$sql .= " , BIRTHDAY";
		    	}
			    //降順ボタンが押されたら(誕生日)
    			if($name == 'descending_birthday_btn'){
	    			$sql .= " , BIRTHDAY DESC";
		    	}
			    //昇順ボタンが押されたら(住所)
    			if($name == 'ascending_prefectures_btn'){
	    			$sql .= " , ADDRESS_NAME";
		    	}
			    //降順ボタンが押されたら(住所)
		    	if($name == 'descending_prefectures_btn'){
			    	$sql .= " , ADDRESS_NAME DESC";
    			}
	     		//昇順ボタンが押されたら(電話番号)
		    	if($name == 'ascending_tel_btn'){
			    	$sql .= " , TEL1,NAME_KANA ASC";
    			}
	    		//降順ボタンが押されたら(電話番号)
		    	if($name == 'descending_tel_btn'){
			    	$sql .= " , TEL1 DESC,NAME_KANA ASC";
    			}
	    	}
		}
		
		// クエリを実行する
		$result = mysqli_query($link, $sql);
	}
	catch (Exception $e){
		echo $e->getMessage();
	}
	// すべての行を取得して、返却する
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
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
	// 結果を返す
	return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// ---------------------
// 顧客情報を削除する（論理削除）
// ---------------------
function delete_member($deleteNo){
	try{
		// データベースの接続情報を取得する
		$link = get_database_link();
		// 削除用クエリ (論理削除)
		$sql  = "";
		$sql .= " UPDATE";
		$sql .= "   MST_MEMBER";
		$sql .= " SET";
		$sql .= "   DELETE_FLG = 1";
		$sql .= " WHERE";
		$sql .= "   NO = $deleteNo";
		// クエリを実行する
	    $result = mysqli_query($link, $sql);
	}
	catch (Exception $e){
		echo $e->getMessage();
	}
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
?>
