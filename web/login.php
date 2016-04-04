<?php
require 'inc/ojsettings.php';
header("Content-type:text/html;charset=utf-8"); 
$Title='正在登录...';
if(!isset($_POST['uid']) || !isset($_POST['pwd']) || !isset($_POST['url'])){
	header("Location: index.php");
	exit();
}
try{
	require 'inc/database.php';
	require 'inc/userlogin.php';
	require 'inc/cookie.php';
	$user=trim($_POST['uid']);
	if(preg_match('/\W/',$user) || strlen($user)==0)
		throw new Exception('无效的用户名');

	session_start();
	$ret=login($user, FALSE, $_POST['pwd']);
	if($ret !== TRUE)
		throw new Exception($ret);

	if(isset($_POST['remember'])){
		write_cookie();
	}
	//echo("Login succeeded.");
	header("location: ".$_POST['url']);
}catch(Exception $E){?>
<html>
  <head>
  <?php require('head.php');?>
	<body>
	  <div class="center" style="margin-top:50px">
		<h2>错误: <?php echo $E->getMessage();?></h2>
		<hr>
		<p><font size=3>页面即将跳转...</font></p>
	  </div>
	<script language="javascript">
	window.setTimeout("history.go(-1);",2000);
	</script>
  </head>
</html>
<?php
}
?>