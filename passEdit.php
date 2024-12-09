<?php
require('function.php');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('パスワード変更ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//ログイン認証
require('auth.php');


debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = '日記appのパスワード変更';
require('head.php');
?>

<body>
  <!-- header -->
  <?php
  require('header.php');
  ?>
  <!-- contents -->
  <!-- main -->
  <section class="sec-container">
    <div class="sec-main">
      COMING SOON!
    </div>
    <a href="mypage.php">&lt; マイページに戻る</a>

  </section>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>