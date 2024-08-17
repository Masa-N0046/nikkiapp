<?php
require('function.php');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('日記投稿ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];
// DBから日記データを取得
$diaryData = getMydiary($u_id);

// DBからきちんとデータがすべて取れているかのチェックは行わず、取れなければ何も表示しないこととする

debug('取得した日記データ：' . print_r($diaryData, true));

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>


<?php
$siteTitle = '日記appのMYPAGE';
require('head.php');
?>

<body>
  <!-- header -->
  <?php
  require('header.php');
  ?>
  <!-- contents -->
  <!-- main -->
  <div class="sec-width">
    <section class="sec-container sec-cont-float">
      <div class="sec-main sec-main-float">
        <h1 class="sec-tit">マイ日記一覧</h1>
        <div class="sec-arti-wrap">
          <?php
          if (!empty($diaryData)) {
            foreach ($diaryData as $key => $val) {
          ?>
              <a href="article.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&d_id=' . $val['id'] : '?d_id=' . $val['id']; ?>" class="panel">
                <div class="sec-arti-body">
                  <p class="sec-arti-tit"><?php echo sanitize($val['title']); ?> </p>
                </div>
              </a>
            <?php
            }
          } else {
            ?>
            まだ投稿はありません
          <?php
          }
          ?>
        </div>
    </section>
    <?php
    require('sidebar_mypage.php');
    ?>
  </div>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>