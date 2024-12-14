<?php
require('function.php');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('日記投稿:編集ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//ログイン認証
require('auth.php');

// ================================
// 画面処理
// ================================

// 画面表示用データ取得
// ================================
// GETデータを格納
$d_id = (!empty($_GET['d_id'])) ? $_GET['d_id'] : '';
// DBから日記データを取得
$dbFormData = (!empty($d_id)) ? getDiary($_SESSION['user_id'], $d_id) : '';
// 新規投稿画面か編集画面か判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// DBからカテゴリデータを取得
debug('日記ID：' . $d_id);
debug('フォーム用DBデータ：' . print_r($dbFormData, true));

// パラメータ改ざんチェック
//================================
// GETパラメータはあるが、改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if (!empty($d_id) && empty($dbFormData)) {
  debug('GETパラメータの日記IDが違います。マイページへ遷移します。');
  header("Location:home.php"); //マイページへ
}

// POST送信時処理
//================================
if (!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：' . print_r($_POST, true));


  //変数にユーザー情報を代入
  $title = $_POST['title'];
  $content = $_POST['content'];

  // 更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if (empty($dbFormData)) {
    //未入力チェック
    validRequired($title, 'title');
    //最大文字数チェック
    validMaxLen($title, 'title');
    //未入力チェック
    validRequired($content, 'content');
    //最大文字数チェック
    validMaxLen($content, 'content', 500);
  } else {
    if ($dbFormData['title'] !== $title) {
      //未入力チェック
      validRequired($title, 'title');
      //最大文字数チェック
      validMaxLen($title, 'title');
    }
    if ($dbFormData['content'] !== $content) {
      //最大文字数チェック
      validMaxLen($content, 'content', 500);
    }
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を生成
      if ($edit_flg) {
        debug('DB更新です。');
        $sql = 'UPDATE diary SET title = :title, content = :content WHERE user_id = :u_id AND id = :d_id';
        $data = array(':title' => $title, ':content' => $content, ':u_id' => $_SESSION['user_id'], ':d_id' => $d_id);
      } else {
        debug('DB新規登録です。');
        $sql = 'INSERT INTO diary (title, content, user_id, create_date) values (:title, :content, :u_id, :date)';
        $data = array(':title' => $title, ':content' => $content, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：' . $sql);
      debug('流し込みデータ：' . print_r($data, true));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ
      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = (!$edit_flg) ? '日記appのCREATE' : '日記appのEDIT';
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
      <form action="" method="post" class="sec-form sec-form-create">
        <h2 class="sec-tit"><?php echo (!$edit_flg) ? '日記を作成する' : '日記を編集する'; ?></h2>
        <div class="sec-area-msg">
          <?php if (!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <label class="<?php if (!empty($err_msg['title'])) echo 'err'; ?>">
          タイトル
          <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
        </label>
        <div class="sec-area-msg">
          <?php if (!empty($err_msg['title'])) echo $err_msg['title']; ?>
        </div>
        <label class="<?php if (!empty($err_msg['content'])) echo 'err'; ?>">
          内容
          <textarea id="js-count" cols="30" rows="10" style="height: 150px;" name="content"><?php echo getFormData('content'); ?></textarea>
        </label>
        <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
        <div class="sec-area-msg">
          <?php if (!empty($err_msg['content'])) echo $err_msg['content'];
          ?>
        </div>
        <div class="sec-btn">
          <input type="submit" class="sec-btn_btn" value="<?php echo (!$edit_flg) ? '投稿する' : '更新する'; ?>">
        </div>
      </form>
      <a class="back" href="mypage.php">&lt;マイページに戻る</a>
    </div>
  </section>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>