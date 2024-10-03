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
	width:1100px;
	outline: solid 1px black;

}

dl {
  display: flex;
  flex-wrap: wrap;
  width: 350px;
  margin-left:40px;
}

dt {
  width: 150px;
}

dd {
  min-width: 180px;
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

.table-container {
	display: flex;
	justify-content: center;
	padding: 0.5em 1em;
	background-color: lightgray;
	text-align: center;
}

/* テーブルヘッダの行 */
.table-container thead > tr {
	color: white;
	background-color: gray;
}

.card-container {
	display: flex;
	flex-wrap: wrap;
	padding: 0 1em;
	background-color: lightgray;
}

</style>
</head>
<body>
<header>
	<h1></h1>
</header>
<?
	$cmd = filter_input(INPUT_POST, "cmd");
	include 'Menubar.php';
?>
<!--ここから-->
<div class="base">
	<div class="contents">
		<dl>
			<a href="MemberList.php">会員一覧</a>　<a href="ReserveEdit.php">予約の作成</a>

			<form method="POST">
			
				<div>
					<a>出発日</a>
					<input type="date" name="date_from" id="date_from" value=<?echo filter_input(INPUT_POST, "date_from");?>>
					<a>～</a>
					<input type="date" name="date_to" id="date_to" value=<?echo filter_input(INPUT_POST, "date_to");?>>
			
  					<?
					if (isset($cmd)) {

					}

					// 都道府県を取得する
					$rows = GetAddress();
					// 取得した都道府県を表示用関数に渡す
					show_Address($rows);
	
					// ----------------------------------
					// 取得した都道府県をドロップダウンで表示する
					// ----------------------------------
					function show_Address($rows)
					{
						?>
						<div>
							<select name="depature_place" id="place-select" value=<?echo filter_input(INPUT_POST, "depature_place");?>>
								<option value="">出発地</option>
						<?
								foreach ($rows as $row) {
						?>
								<option value = "<? echo htmlspecialchars ($row['ADDRESS_CD']); ?> "<?if(htmlspecialchars ($row['ADDRESS_CD']) == filter_input(INPUT_POST, "depature_place")){?>selected<?} ?>>
									<? echo htmlspecialchars ($row['ADDRESS_NAME']); ?> 
								</option>
						<?		
								}
						?>
							</select>
						</div>
					<?
					}
  					?>
				</div>

				<div>
					<a>ツアー名</a>
					<input type="text" name="tour_name" id="tour_name" value=<?echo filter_input(INPUT_POST, "tour_name");?>>
				</div>
					<input type="button" value="検索" onclick="showSearchResults();">
					<input type="button" value="クリア" onclick="inputClear();">
					<input type="hidden" name="cmd" value="">
			
			</form>
			
		</dl>
	</div>
</div>
<?
				if (isset($cmd)) {
					// 検索結果
					$rows = search();
					show_search_results($rows);
				}
?>
<!--ここまで-->

<script>
// ----------------------------------
// 表示ボタンのクリック
// ----------------------------------
function showSearchResults() {
	document.forms[0].cmd.value = "search";
	// var test = $("[name = date]").text();
	document.forms[0].submit();
	// $("[name = date]").text(test);
}

function inputClear(){
	var datefrom = document.getElementById("date_from");
	var dateto = document.getElementById("date_to");
	var tourname = document.getElementById("tour_name");
	var placeselect = document.getElementById("place-select");
  	datefrom.value = '';
 	dateto.value = '';
	tourname.value = '';
	placeselect.selectedIndex = 0;
}

function restoreInput(){

}
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
						<th>予約番号</th>
						<th>ツアーコード</th>
						<th>ツアー名</th>
						<th>出発日</th>
						<th>帰着日</th>
						<th>出発地</th>
						<th>参加人数</th>
						<th>ツアー料金</th>
					</tr>
				</thead>
				<tbody>
		<?
			$counter = 0;
			$color = "";
			foreach ($rows as $row) {
				if ( $counter % 2 == 0 ){
					$color = "#fff";
				}else{
					$color = "#e0ffff";
				}
		?>
					<tr style="background-color: <?= $color ?>">
						<td><?= $row["RSV_NO"] ?></td>
						<td><?= $row["TOUR_CODE"] ?></td>
						<td><?= $row["TOUR_NAME"] ?></td>
						<td><?= $row["DEPARTURE_DATE"] ?></td>
						<td><?= $row["RETURN_DATE"] ?></td>
						<td><?= $row["DEPARTURE_PLACE"] ?></td>
						<td><?= $row["TRAVELER_COUNT"] ?></td>
						<td><?= $row["TOUR_MONEY"] ?></td>
					</tr>
		<?
		$counter ++;
		}
		?>
				</tbody>
			</table>
		</div>
	</div>
<div>
<?
}
// ----------------------------------
// 検索結果を取得する
// ----------------------------------
function search()
{

	try{
		// 入力された日付を取得
		$date_from = filter_input(INPUT_POST, "date_from");
		$date_to = filter_input(INPUT_POST, "date_to");
		// 入力された都道府県情報を取得
		$depature_place = filter_input(INPUT_POST, "depature_place");

		$tour_name = filter_input(INPUT_POST, "tour_name");
		// データベースの接続情報を取得する
		$link = get_database_link();

		// 検索用クエリ
		$sql  = "";
		$sql .= " SELECT";
		$sql .= "   RESERVE.RSV_NO AS RSV_NO";
		$sql .= "  ,RESERVE.TOUR_CODE AS TOUR_CODE";
		$sql .= "  ,RESERVE.DEPARTURE_DATE AS DEPARTURE_DATE";
		$sql .= "  ,RESERVE.RETURN_DATE AS RETURN_DATE";
		$sql .= "  ,RESERVE.TOUR_CANCEL_FLG AS TOUR_CANCEL_FLG";
		$sql .= "  ,MA.ADDRESS_NAME AS DEPARTURE_PLACE";
		$sql .= "  ,COALESCE(TRAVELER.COUNT,0) AS TRAVELER_COUNT";
		$sql .= "  ,TOUR_NAME AS TOUR_NAME";
		$sql .= "  ,TOUR_FARE AS TOUR_FARE";
		$sql .= "  ,format((COALESCE(TRAVELER.COUNT,0) * TOUR_FARE), '#,##0') AS TOUR_MONEY ";
		$sql .= " FROM";
		$sql .= "   RESERVE_TOUR AS RESERVE";
		$sql .= " LEFT JOIN MST_TOUR AS MT ON MT.TOUR_CODE = RESERVE.TOUR_CODE";
		$sql .= " LEFT JOIN MST_ADDRESS AS MA ON MA.ADDRESS_CD = MT.DEP_ADDRESS_CD ";
		$sql .= " LEFT JOIN (SELECT RSV_NO, COUNT(RSV_NO) AS COUNT FROM RESERVE_TRAVELER GROUP BY RSV_NO) AS TRAVELER";
		$sql .= " ON TRAVELER.RSV_NO = RESERVE.RSV_NO ";
		$sql .= " WHERE";
		$sql .= "   1 = 1";

		if($date_from != "") {
			// 日付が入力されている場合
			// 日付の検索条件を追加
			$sql .= "  AND DEPARTURE_DATE >= '$date_from' ";
		} 

		if($date_to != "") {
			// 日付が入力されている場合
			// 日付の検索条件を追加
			$sql .= "  AND DEPARTURE_DATE <= '$date_to' ";
		} 


		// ToDo: 都道府県の検索条件を追加する

		if($depature_place != "") {
			// 都道府県が選択されている場合
			// 都道府県の検索条件を追加
			$sql .= "  AND MA.ADDRESS_CD = '$depature_place' ";
		} 

		if($tour_name  != "") {
			// 都道府県が選択されている場合
			// 都道府県の検索条件を追加
			$sql .= "  AND  TOUR_NAME LIKE '%$tour_name%' ";
		} 
		$sql .= " ORDER BY RSV_NO ";

		// クエリを実行する
		$result = mysqli_query($link, $sql);

		// すべての行を取得して、返却する
		return mysqli_fetch_all($result, MYSQLI_ASSOC);
	}
	catch (Exception $e){
		echo $e->getMessage();
	}
}

// ----------------------------------
// 都道府県を取得する
// ----------------------------------
function GetAddress(){

	try{
		// データベースの接続情報を取得する
		$link = get_database_link();

		// 検索用クエリ
		$sql  = "";
		$sql .= " SELECT ADDRESS_NAME , ADDRESS_CD FROM mst_address";

		// クエリを実行する
		$result = mysqli_query($link, $sql);

		// すべての行を取得して、返却する
		return mysqli_fetch_all($result, MYSQLI_ASSOC);
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
