<!DOCTYPE html>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title></title>
<style>


/* CSSコード */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  background: #fff;
}

.logo {
  font-size: 24px;
}

/* ここから下がハンバーガーメニューに関するCSS */
  
/* チェックボックスを非表示にする */
.drawer_hidden {
  display: none;
}

/* ハンバーガーアイコンの設置スペース */
.drawer_open {
  display: flex;
  height: 60px;
  width: 60px;
  justify-content: center;
  align-items: center;
  position: relative;
  z-index: 100;/* 重なり順を一番上にする */
  cursor: pointer;
}

/* ハンバーガーメニューのアイコン */
.drawer_open span,
.drawer_open span:before,
.drawer_open span:after {
  content: '';
  display: block;
  height: 3px;
  width: 25px;
  border-radius: 3px;
  background: #333;
  transition: 0.5s;
  position: absolute;
}

/* 三本線の一番上の棒の位置調整 */
.drawer_open span:before {
  bottom: 8px;
}

/* 三本線の一番下の棒の位置調整 */
.drawer_open span:after {
  top: 8px;
}

/* アイコンがクリックされたら真ん中の線を透明にする */
#drawer_input:checked ~ .drawer_open span {
  background: rgba(255, 255, 255, 0);
}

/* アイコンがクリックされたらアイコンが×印になように上下の線を回転 */
#drawer_input:checked ~ .drawer_open span::before {
  bottom: 0;
  transform: rotate(45deg);
}

#drawer_input:checked ~ .drawer_open span::after {
  top: 0;
  transform: rotate(-45deg);
}
  
/* メニューのデザイン*/
.nav_content {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 100%; /* メニューを画面の外に飛ばす */
  z-index: 99;
  background: rgb(110, 110, 110);
  transition: .5s;
  padding-top: 20px;
  opacity: 0.7;
}

/* メニュー黒ポチを消す */
.nav_list {
  list-style: none;
}


@media screen and (max-width: 767px) {
/* ここに横幅が767px以下の時に発動するスタイルを記述 */
/* アイコンがクリックされたらメニューを表示 */
#drawer_input:checked ~ .nav_content {
  left: 40%;/* メニューを画面に入れる */
}
}

@media screen and (min-width: 767px) {
/* ここに横幅が767pxより大きい時に発動するスタイルを記述 */
/* アイコンがクリックされたらメニューを表示 */
#drawer_input:checked ~ .nav_content {
  left: 80%;/* メニューを画面に入れる */
}
}

ul {
  font-size: 25px;
}
</style>
</head>
<body>
    <header class="header">
      <!-- ヘッダーロゴ -->
      <div class="logo"></div>
    
      <!-- ハンバーガーメニュー部分 -->
      <div class="nav">
    
        <!-- ハンバーガーメニューの表示・非表示を切り替えるチェックボックス -->
        <input id="drawer_input" class="drawer_hidden" type="checkbox">
    
        <!-- ハンバーガーアイコン -->
        <label for="drawer_input" class="drawer_open"><span></span></label>
    
        <!-- メニュー -->
        <nav class="nav_content">
		<ul class="nav_list">
        <li class="nav_item"><a href="http://localhost/PasswordReset.php">パスワード変更</a></li>
        <li class="nav_item"><a href="http://localhost/MemberEdit.php">顧客登録(登録)</a></li>
        <li class="nav_item"><a href="http://localhost/MemberList.php">顧客登録(検索)</a></li>
        <li class="nav_item"><a href="http://localhost/ReserveList.php">予約一覧</a></li>
        <li class="nav_item"><a href="http://localhost/ReserveEdit.php">予約作成</a></li>
        <li class="nav_item"><a href="http://localhost/Logout.php">ログアウト</a></li>
        <!-- <li><a href="#">ツアー検索</a></li> -->
      </ul>
        </nav>
   
      </div>
    </header>
  </body>
</html>
