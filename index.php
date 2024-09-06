<?php
require('function.php');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('トップページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//================================
// 画面処理
//================================
// 画面表示用データ取得
//================================

// GETパラメータを取得
// -------------------------------
// カレントページ
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは１ページめ

// パラメータに不正な値が入っているかチェック
if (!is_int((int)$currentPageNum)) {
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
// 表示件数
$listSpan = 5;
// 現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum - 1) * $listSpan); //1ページ目なら(1-1)*20 = 0 、 ２ページ目なら(2-1)*20 = 20
// DBから商品データを取得
$dbDiaryData = getDiaryList($currentMinNum);
// debug('現在のページ：'.$currentPageNum);
// debug('DBデータ：'.print_r($dbFormData,true));
//debug('カテゴリデータ：'.print_r($dbCategoryData,true));

// DBから日記データを取得
// $diaryData = diaryList();
// debug('取得した日記データ：' . print_r($diaryData, true));
// debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '日記appのINDEX';
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
      <div class="sec-arti-wrap">
        <?php
        foreach ($dbDiaryData['data'] as $key => $val) :
        ?>
          <a href="article.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&d_id=' . $val['id'] : '?d_id=' . $val['id']; ?>">
            <div class="sec-arti-body">
              <p class="sec-arti-tit"><?php echo sanitize($val['title']); ?> </p>
              <div class="sec-arti-info">
                <p class="sec-arti-info-sub"><?php echo sanitize($val['username']); ?></p>
                <p class="sec-arti-info-sub"><?php echo sanitize($val['create_date']); ?></p>
                <p class="sec-arti-info-sub"><?php echo sanitize($val['update_date']); ?></p>
              </div>
            </div>
          </a>
        <?php
        endforeach;
        ?>
      </div>
    </div>

    <?php pagination($currentPageNum, $dbDiaryData['total_page']); ?>

  </section>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>