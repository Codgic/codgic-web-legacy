<?php
require 'inc/global.php';
require 'inc/ojsettings.php';

if(!isset($_POST['i18n'])){
    echo _('Invalid Argument...');
    exit();
}

setcookie('i18n','',time()-3600);
$pref->i18n=$_POST['i18n'];
$_SESSION['pref']=serialize($pref);
setcookie('i18n',$pref->i18n,time()+31536000);

echo 'success';