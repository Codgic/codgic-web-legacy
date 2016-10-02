<?php
require __DIR__.'/../conf/ojsettings.php';
require __DIR__.'/../inc/init.php';

if(!isset($_POST['uid']) || !isset($_POST['pwd'])){
	echo _('Wrong Username/Password...');
    exit();
}

require __DIR__.'/../conf/database.php';
require __DIR__.'/../func/userlogin.php';

$user=trim($_POST['uid']);
if((preg_match('/\W/',$user) && !preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$user)) || strlen($user)==0){
    echo _('Wrong Username/Password...');
    exit();
}

$ret=login($user, FALSE, $_POST['pwd']);

if($ret !== TRUE) die($ret);

if(isset($_POST['remember'])) $remember=1;
else $remember=0;

require __DIR__.'/../func/cookie.php';

write_cookie($remember);

if(isset($_SESSION['login_redirect'])) 
    unset($_SESSION['login_redirect']);

echo 'success';