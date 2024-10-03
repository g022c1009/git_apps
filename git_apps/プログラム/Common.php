
<?
function login_check(){

	//session
	session_start();

	//IDがない場合ログイン画面に戻る
	if (!isset($_SESSION["txtID"])) {
		header("Location: Login.php");
		exit;
	  }

}
?>

