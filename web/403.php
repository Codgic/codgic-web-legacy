<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
$inTitle='ERROR 403';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php');?>
  <body>
    <?php require('page_header.php') ?>  
    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <div class="text-center none-text none-center">
            <p><i class="fa fa-meh-o fa-4x"></i></p>
            <p><b>ERROR 403</b><br>
            看起来您无权访问此页面</p>
          </div>
        </div>
      </div>
	  <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
      </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
  </body>
</html>
