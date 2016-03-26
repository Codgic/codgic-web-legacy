<?php
require 'inc/ojsettings.php';
header("Content-type:text/html;charset=utf-8"); 
if(!isset($_POST['uid']) || !isset($_POST['pwd']) || !isset($_POST['url']))
	die('Invalid argument.');
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
<html manifest="appcache.manifest"><head><script language="javascript">
	alert('<?php echo $E->getMessage();?>');
	history.go(-1);
</script></head></html>
<?php
}
?>