<?php
require 'inc/global.php';
require 'inc/ojsettings.php';

if(!isset($_POST['uid']) || !isset($_POST['pwd'])){
	echo _('Wrong Username/Password...');
    exit();
}

require 'inc/database.php';
require 'inc/userlogin.php';
require 'inc/cookie.php';

$user=trim($_POST['uid']);
if((preg_match('/\W/',$user) && !preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$user)) || strlen($user)==0){
    echo _('Wrong Username/Password...');
    exit();
}

session_start();
$ret=login($user, FALSE, $_POST['pwd']);

if($ret !== TRUE) die($ret);

if(isset($_POST['remember'])) $remember=1;
else $remember=0;

write_cookie($remember);

if(isset($_SESSION['login_redirect'])) unset($_SESSION['login_redirect']);

echo 'success';
