<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
$inTitle='ERROR 404';
$Title=$inTitle .' - '. $oj_name;
$img_id=rand(1,2);
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php') ?>  
          
    <div class="container-fluid about-page">
      <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
          <div class="page-header">
            <h2>ERROR 404: 你要访问的页面不存在</h2>
          </div>
		  <div>
		  <?php echo"<p><a href=\"index.php\"><img src=\"/images/404_{$img_id}.jpg\"></a></p>";?>
        </div>
      </div>
	  <div class="row-fluid">
        <div class="offset2 span8" style="font-size:16px">
        </div>
      </div>
	  <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>
    </div>
	 <div class="btn-group pull-right">

<?php if(isset($_SESSION['user'])){?>
        <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" style="white-space:nowrap" href="#">
          <i class="icon-user"></i>
          <?php
          echo $_SESSION['user'],'<strong class="notifier"></strong>';
          ?>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" style="text-align:left;">
          <li><a href="mail.php" id="nav_mail"><i class="icon-envelope"></i> 私信<?php echo '<strong class="notifier"></strong>';
 ?>
</a></li>
          <li><a href="marked.php"><i class="icon-star"></i> 收藏</a></li>
          <li><a href="profile.php"><i class="icon-github"></i> 资料</a></li>
          <li><a href="control.php"><i class="icon-cogs"></i> 设置</a></li>
<?php   if(isset($_SESSION['administrator']))
          echo '<li class="divider"></li><li><a href="admin.php"><i class="icon-bolt"></i> 管理</a></li>'; 
?>
          <li class="divider"></li>
          <li><a id='logoff_btn' href="#"><i class="icon-signout"></i> 注销</a></li>
        </ul>
<?php }else{?>
        <a id="login_btn" title="Alt+L" data-toggle="modal" href="#LoginModal" class="btn shortcut-hint">登录</a>
        <a href="reg.php" class="btn">注册</a>
<?php }?>
      </div>
	</div>
	<script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>
  </body>
</html>
