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

// DBから日記データを取得
$diaryData = diaryList();
debug('取得した日記データ：' . print_r($diaryData, true));


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
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
        foreach ($diaryData as $key => $val) :
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
  </section>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>