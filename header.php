<header>
  <div class="header-wrap">
    <h1 class="header-title"><a href="index.php">日記app</a></h1>
    <nav class="header-wrap__nav">
      <ul class="header-list">
        <?php
        if (empty($_SESSION['user_id'])) {
        ?>
          <li class="header-list__item"><a href="signup.php" class="header-list__link">サインアップ</a></li>
          <li class="header-list__item"><a href="login.php" class="header-list__link">ログイン</a></li>
        <?php
        } else {
        ?>
          <li class="header-list__item"><a href="mypage.php" class="header-list__link">マイ日記</a></li>
          <li class="header-list__item"><a href="logout.php" class="header-list__link">ログアウト</a></li>
        <?php
        }
        ?>
      </ul>
    </nav>
  </div>
</header>