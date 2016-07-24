<?php
require 'inc/ojsettings.php';
if(!isset($_POST['uid']) || !isset($_POST['pwd']))
	die('');
    
require 'inc/database.php';
require 'inc/userlogin.php';
require 'inc/cookie.php';

$user=trim($_POST['uid']);
if(preg_match('/\W/',$user) || strlen($user)==0)
    die ('用户名/密码错误...');

session_start();
$ret=login($user, FALSE, $_POST['pwd']);

if($ret !== TRUE) die($ret);
if(isset($_POST['remember'])) write_cookie();
if(isset($_SESSION['login_redirect'])) unset($_SESSION['login_redirect']);

echo 'success';

