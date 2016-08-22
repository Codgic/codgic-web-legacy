<?php
require 'inc/global.php';
require 'inc/ojsettings.php';

if(!isset($_POST['i18n'])){
    echo _('Invalid Argument...');
    exit();
}

$_SESSION['i18n']=$_POST['i18n'];

if(!function_exists('write_i18n_cookie')) 
    require 'inc/cookie.php';
setcookie('i18n','',time()-3600);
write_i18n_cookie($_SESSION['i18n']);

echo 'success';