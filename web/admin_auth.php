<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');

if(!isset($_SESSION['user'],$_SESSION['administrator']))
  die('<div class="center">You are not administrator.</div>');

require('inc/database.php');
if(isset($_POST['paswd'])){

  require_once('inc/checkpwd.php');
  if(password_right($_SESSION['user'], $_POST['paswd'])){
    $_SESSION['admin_tfa']=1;
    if(isset($_SESSION['admin_retpage']))
      $ret = $_SESSION['admin_retpage'];
    else
      $ret = "index.php";
    header("Location: $ret");
    exit(0);
  }
}

$inTitle='管理员验证';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php'); ?>  
          
    <div class="container-fluid admin-page">
      <div class="row-fluid">
      
        <div class="span5 offset5">
          <form action="admin_auth.php" class="form-inline" method="post">
            <div><label for="input_adminpass"><p>请输入密码以验证管理员身份</p></label></div>
            <input type="password" autofoucs id="input_adminpass" name="paswd" class="input-medium">
            <input type="submit" class="btn" value="确定">
          </form>
        </div>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>

    </div>

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        $('#ret_url').val("admin_auth.php");
        $('#input_adminpass').focus();
      });

    </script>
  </body>
</html>
