<?php
// ログ取り
// ログ取りのオン・オフ
ini_set('log_errors', 'on');
// ログファイルの指定
ini_set('error_log', 'php.log');

// デバック
// デバックフラグ
$debug_flg = true;
// デバックログ関数
function debug($str)
{
  global $debug_flg;
  if (!empty($debug_flg)) {
    error_log('デバック：' . $str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60 * 60 * 24 * 30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart()
{
  debug('**********************画面表示処理開始');
  debug('セッションID：' . session_id());
  debug('セッション変数の中身：' . print_r($_SESSION, true));
  debug('現在日時タイムスタンプ：' . time());
  if (!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
    debug('ログイン期限日時タイムスタンプ：' . ($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}

// エラーメッセの定数
define('MSG01', '入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03', 'パスワード（再入力）が合っていません');
define('MSG04', '半角英数字のみご利用いただけます');
define('MSG05', '6文字以上で入力してください');
define('MSG06', '500文字以内で入力してください');
define('MSG07', 'エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', '削除しました');

// $err_msg配列を作る
$err_msg = array();
// dbアクセス結果
$dbRst = false;

// バリデーション
// バリ関数(未入力チェック)
function validRequired($str, $key)
// validRequired($email, 'email'); $email->$strに'email'->$keyに入る
{
  if (empty($str)) {
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
// バリ関数(email未入力チェック)
function validEmail($str, $key)
{
  if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
// バリ関数(重複チェック)
function validEmailDup($email)
{
  global $err_msg;
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email';
    $data = array(':email' => $email);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
    if (!empty(array_shift($result))) {
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
// バリ関数(同値チェック)
function validMatch($str1, $str2, $key)
{
  if ($str1 !== $str2) {
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
// バリ関数(最小文字数チェック)
function validMinLen($str, $key, $min = 6)
{
  if (mb_strwidth($str) < $min) {
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
// バリ関数(最大文字数チェック)
function validMaxLen($str, $key, $max = 255)
{
  if (mb_strwidth($str) > $max) {
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
// バリ関数(半角チェック)
function validHalf($str, $key)
{
  if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
// 半角数字チェック
function validNumber($str, $key)
{
  if (!preg_match("/^[0-9]+$/", $str)) {
    global $err_msg;
    $err_msg[$key] = MSG07;
  }
}

// データベース
// DB接続関数
function dbConnect()
{
  $dsn = 'mysql:dbname=nikkiapp;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $option = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $option);
  return $dbh;
}
// SQL実行関数
function queryPost($dbh, $sql, $data)
{
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if (!$stmt->execute($data)) {
    debug('クエリに失敗しました。');
    debug('失敗したSQL：' . print_r($stmt, true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}

// どこまで必要かわからないからこれから取捨選択をする
function getUser($u_id)
{
  debug('ユーザー情報を取得します。');
  // 例外処理
  try {
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
  // クエリ結果のデータ返却
  // return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getDiary($u_id, $d_id)
{
  debug('日記情報を取得します。');
  debug('ユーザーID:' . $u_id);
  debug('日記ID:' . $d_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM diary WHERE user_id = :u_id AND id = :d_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':d_id' => $d_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      // クエリ結果のデータを1レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}
function getDiaryOne($d_id)
{
  debug('日記情報を取得します。');
  debug('日記ID：' . $d_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT d.id, d.title, d.content, d.user_id, d.create_date, d.update_date FROM diary AS d LEFT JOIN users AS u ON u.id = d.user_id WHERE d.id = :d_id AND d.delete_flg = 0';
    $data = array(':d_id' => $d_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      // クエリ結果のデータを１レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}
function getMydiary($u_id)
{
  debug('自分の日記情報を取得します。');
  debug('ユーザーID：' . $u_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM diary WHERE user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      // クエリ結果のデータを全レコード返却
      return $stmt->fetchAll();
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}
// サニタイズ
function sanitize($str)
{
  return htmlspecialchars($str ?? '', ENT_QUOTES);
}

// フォーム入力保持
function getFormData($str, $flg = false)
{
  if ($flg) {
    $method = $_GET;
  } else {
    $method = $_POST;
  }
  global $dbFormData;
  // ユーザーデータがある場合
  if (!empty($dbFormData)) {
    // フォームのエラーがある場合
    if (!empty($err_msg[$str])) {
      // POSTにデータがある場合
      if (isset($method[$str])) { //金額や郵便番号などのフォームで数字の０が入っている場合もあるので、issetを使うこと
        return sanitize($method[$str]);
      } else {
        // ない場合（フォームにエラーがある＝POSTされているはずなので、まずあり得ない）はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    } else {
      // POSTにデータがあり、DBの情報と違う場合(このフォームも変更していてエラーはないが、他のフォームで引っ掛かっている状態)
      if (isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
        return sanitize($method[$str]);
      } else { //そもそも変更していない
        return sanitize($dbFormData[$str]);
      }
    }
  } else {
    if (isset($method[$str])) {
      return sanitize($method[$str]);
    }
  }
}
// GETパラメータ付与
// $del_key：付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array())
{
  if (!empty($_GET)) {
    $str = '?';
    foreach ($_GET as $key => $val) {
      if (!in_array($key, $arr_del_key, true)) { //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
        $str .= $key . '=' . $val . '&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}
// ページング
// currentPageNum : 現在のページ数
// totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination($currentPageNum, $totalPageNum, $link = '', $pageColNum = 5)
{
  //現在のページが総ページ数と同じかつ総ページ数が表示項目以上なら、左にリンク４個出す
  if ($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
    //現在のページが総ページ数の１ページ前なら左にリンク３個、右に１個出す
  } elseif ($currentPageNum == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
    //現ページが２の場合は左にリンク１個、右にリンク３個出す
  } elseif ($currentPageNum == 2 && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
    //現ページが１の場合は左に何も出さない、右にリンク５個出す
  } elseif ($currentPageNum == 1 && $totalPageNum >= $pageColNum) {
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
    //総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  } elseif ($totalPageNum < $pageColNum) {
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
    //それ以外は左に２個出す
  } else {
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
  echo '<ul class="pagination-list">';
  if ($currentPageNum != 1) {
    echo '<li class="list-item"><a href="?p=1' . $link . '">&lt;</a></li>';
  }
  for ($i = $minPageNum; $i <= $maxPageNum; $i++) {
    echo '<li class="list-item ';
    if ($currentPageNum == $i) {
      echo 'active';
    }
    echo '"><a href="?p=' . $i . $link . '">' . $i . '</a></li>';
  }
  if ($currentPageNum != $maxPageNum && $maxPageNum > 1) {
    echo '<li class="list-item"><a href="?p=' . $maxPageNum . $link . '">&gt;</a></li>';
  }
  echo '</ul>';
  echo '</div>';
}
function getDiaryList($currentMinNum = 1, $span = 5)
{
  debug('日記情報を取得します。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // 件数用のSQL文作成
    $sql =
      'SELECT d.id, d.title, d.user_id, d.create_date, d.update_date, u.username FROM diary AS d INNER JOIN users AS u ON d.user_id = u.id AND d.delete_flg = 0';

    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // Call to a member function rowCount() on intの回避
    $rst['total'] = $stmt->rowCount(); //総レコード数
    $rst['total_page'] = ceil($rst['total'] / $span); //総ページ数
    if (!$stmt) {
      // ここで投稿されたデータがなかった際のWarning: Trying to access array offset on false inが出るのか
      return false;
    }

    // ページング用のSQL文作成
    $sql =
      'SELECT d.id, d.title, d.user_id, d.create_date, d.update_date, u.username FROM diary AS d INNER JOIN users AS u ON d.user_id = u.id AND d.delete_flg = 0';

    $sql .= ' LIMIT ' . $span . ' OFFSET ' . $currentMinNum;
    $data = array();
    debug('SQL：' . $sql);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      // クエリ結果のデータを全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    } else {
      // ここで投稿されたデータがなかった際のWarning: Trying to access array offset on false inが出るのか
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
// sessionを1回だけ取得できる
function getSessionFlash($key)
{
  if (!empty($_SESSION[$key])) {
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
