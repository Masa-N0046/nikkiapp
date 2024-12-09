<?php
require('function.php');

debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debug('ユーザー登録ページ');
debug('<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
debugLogStart();

if (!empty($_POST)) {
  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $repass = $_POST['repass'];

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  validRequired($repass, 'repass');

  if (empty($err_msg)) {

    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    //email重複チェック
    validEmailDup($email);

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');

    //パスワード（再入力）の最大文字数チェック
    validMaxLen($repass, 'repass');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($repass, 'repass');

    if (empty($err_msg)) {

      //パスワードとパスワード再入力が合っているかチェック
      validMatch($pass, $repass, 'repass');

      if (empty($err_msg)) {

        //例外処理
        try {
          // DBへ接続
          $dbh = dbConnect();
          // SQL文作成
          $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
          $data = array(
            ':email' => $email,
            ':pass' => password_hash($pass, PASSWORD_DEFAULT),
            ':login_time' => date('Y-m-d H:i:s'),
            ':create_date' => date('Y-m-d H:i:s')
          );
          // クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          if ($stmt) {
            // ログイン有効期限（デフォルトを1時間とする）
            $sesLimit = 60 * 60;
            // 最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            // ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：' . print_r($_SESSION, true));

            header("Location:mypage.php"); //home.phpへ
          }
        } catch (Exception $e) {
          error_log('エラー発生:' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
?>



<?php
$siteTitle = 'サインアップ';
require('head.php');
?>

<body>
  <!-- header -->
  <?php
  require('header.php');
  ?>
  <!-- main -->
  <section class="sec-container">

    <div class="sec-main">

      <form action="" method="post" class="sec-form">
        <h2 class="sec-tit">サインアップ</h2>
        <div class="sec-area-msg">
          <?php
          if (!empty($err_msg['common'])) echo $err_msg['common'];
          ?>
        </div>
        <label class="<?php if (!empty($err_msg['email'])) echo 'err'; ?>">
          E-mail
          <input type="text" name="email" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
        </label>
        <div class="sec-area-msg">
          <?php
          if (!empty($err_msg['email'])) echo $err_msg['email'];
          ?>
        </div>
        <label class="<?php if (!empty($err_msg['pass'])) echo 'err'; ?>">
          password(英数字6文字以上!)
          <input type="password" name="pass" value="<?php if (!empty($_POST['pass'])) echo $_POST['pass']; ?>">
        </label>
        <div class="sec-area-msg">
          <?php
          if (!empty($err_msg['pass'])) echo $err_msg['pass'];
          ?>
        </div>
        <label class="<?php if (!empty($err_msg['repass'])) echo 'err'; ?>">
          password(確認用)
          <input type="password" name="repass" value="<?php if (!empty($_POST['repass'])) echo $_POST['repass']; ?>">
        </label>
        <div class="sec-area-msg">
          <?php
          if (!empty($err_msg['repass'])) echo $err_msg['repass'];
          ?>
        </div>
        <div class="sec-btn">
          <input type="submit" class="sec-btn_btn" value="signup">
        </div>
      </form>

    </div>

  </section>
  <!-- footer -->
  <?php
  require('footer.php');
  ?>