<?php
//共通変数・関数ファイルを読込み
require('function.php');

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('「日記内容ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
//日記IDのGETパラメータを取得
$d_id = (!empty($_GET['d_id'])) ? $_GET['d_id'] : '';
// DBから日記データを取得
$viewData = getDiaryOne($d_id);

// DBからログインしているユーザーの日記かどうかの判別
// ログインしているユーザーかどうかの切り分け
$u_id = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';
// 実在するユーザーを格納
$savedUser = getUser($u_id);
// 実在するユーザーに対してdiaryデータがあるかどうかの切り分け
$savedDiary = (!empty($_SESSION['user_id'])) ? getDiary($_SESSION['user_id'], $d_id) : '';
// ログインユーザーかつアクセスしたユーザーのdiaryか、未ログインユーザーで編集ボタンを表示するかの切り分け
$appearButton = ($savedUser != $u_id && $savedUser != $savedDiary) ? getDiary($_SESSION['user_id'], $d_id) : '';

// パラメータに不正な値が入っているかチェック
if (empty($viewData)) {
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}
debug('取得したDBデータ：' . print_r($viewData, true));
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '日記内容';
require('head.php');
?>

<!-- ヘッダー -->
<?php
require('header.php');
?>

<!-- メインコンテンツ -->

<!-- Main -->
<section class="sec-container">
  <div class="sec-main">
    <div class="sec-wrap">
      <div class="sec-tit">
        <h1>
          <?php echo $viewData['title']; ?>
        </h1>
      </div>
      <div class="sec-detail">
        <p><?php echo $viewData['content']; ?></p>
      </div>
      <div class="sec-btn">
        <?php if ($appearButton) : ?>
          <button type="button" class="sec-btn_btn">
            <a href="createEdit.php<?php echo appendGetParam(array($viewData)); ?>">編集する</a>
          </button>
        <?php else : ?>
          編集できません
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

</div>

<!-- footer -->
<?php
require('footer.php');
?>