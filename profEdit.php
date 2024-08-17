<?php

//共通変数・関数ファイルを読込み
require('function.php');

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('プロフィール編集ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：' . print_r($dbFormData, true));

// post送信されていた場合
if (!empty($_POST)) {
  debug('POST送信があります。');
  debug('POST情報：' . print_r($_POST, true));

  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $age = $_POST['age'];
  $email = $_POST['email'];

  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if ($dbFormData['username'] !== $username) {
    //名前の最大文字数チェック
    validMaxLen($username, 'username');
  }
  if ($dbFormData['age'] !== $age) {
    //年齢の最大文字数チェック
    validMaxLen($age, 'age');
    //年齢の半角数字チェック
    validNumber($age, 'age');
  }
  if ($dbFormData['email'] !== $email) {
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    if (empty($err_msg['email'])) {
      //emailの重複チェック
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
  }

  if (empty($err_msg)) {
    debug('バリデーションOKです。');

    //例外処理
    try {
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users  SET username = :u_name, age = :age, email = :email WHERE id = :u_id';
      $data = array(':u_name' => $username, ':age' => $age, ':email' => $email, ':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if ($stmt) {
        $_SESSION['msg_success'] = SUC02;
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ

      }
    } catch (Exception $e) {
      error_log('エラー発生:' . $e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<body class="page-profEdit page-2colum page-logined">

  <!-- メニュー -->
  <?php
  require('header.php');
  ?>

  <!-- メインコンテンツ -->
  <div>
    <!-- Main -->
    <section class="sec-container">
      <div class="sec-main">
        <h1 class="page-title">プロフィール編集</h1>
        <form action="" method="post" class="sec-form" enctype="multipart/form-data">
          <div class="area-msg">
            <?php
            if (!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['username'])) echo 'err'; ?>">
            名前
            <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['username'])) echo $err_msg['username'];
            ?>
          </div>
          <label style="text-align:left;" class="<?php if (!empty($err_msg['age'])) echo 'err'; ?>">
            年齢
            <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['age'])) echo $err_msg['age'];
            ?>
          </div>
          <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
            Email
            <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php
            if (!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="変更する">
          </div>
        </form>
      </div>
    </section>
  </div>

  <!-- footer -->
  <?php
  require('footer.php');
  ?>