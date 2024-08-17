    <footer class="footer">
      <div class="footer-copy">
        Copyright <a href="index.php">日記app</a>. All Rights Reserved.
      </div>
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
      });
    </script>

    </body>

    </html>