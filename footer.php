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

        // メッセージ表示
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        if (msg.replace(/^[\s　]+|[\s　]+$/g, "").length) {
          $jsShowMsg.slideToggle('slow');
          setTimeout(function() {
            $jsShowMsg.slideToggle('show');
          }, 5000);
        }

        $('#delete').click(function() {
          if (!confirm('本当に削除しますか？')) {
            /* キャンセルの時の処理 */
            return false;
          } else {
            /* OKの時の処理 */
            location.href = 'index.php';
          }
        });
      });
    </script>
    </body>

    </html>