    <footer id="footer">
      Copyright <a href="index.php">日記app</a>. All Rights Reserved.
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
      $(function() {
        // フッターを最下部に固定
        var $ftr = $('#footer');
        if (window.innerHeight > $ftr.offset().top + $ftr.outerHeight()) {
          $ftr.attr({
            'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
          });
        }

        // テキストエリアカウント
        var $countUp = $('#js-count'),
          $countView = $('#js-count-view');
        $countUp.on('keyup', function(e) {
          $countView.html($(this).val().length);
        });
      });
    </script>
    </body>

    </html>