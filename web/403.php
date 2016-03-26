<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
$inTitle='ERROR 403';
$Title=$inTitle .' - '. $oj_name;
$img_id=mt_rand(1,intval($num_403));
?>
<!DOCTYPE html>
<html>
  <?php require('head.php');?>
  <body>
    <?php require('page_header.php') ?>  
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
          <div class="page-header">
            <h2>ERROR 403: 我拒绝!</h2>
          </div>
		  <div>
		  <center><p><a href="/index.php"><img src="/assets/res/403_<?php echo $img_id?>.jpg"></a></p></center>
        </div>
      </div>
      </div>
	  <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
      </div>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>
	<script type="text/javascript">
	window.setTimeout("window.location='/index.php'",2000); 
	</script>
  </body>
</html>
