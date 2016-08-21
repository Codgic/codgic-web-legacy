<?php
session_start();
require 'inc/database.php';
require 'inc/preferences.php';
if(!isset($_SESSION['user']))
	exit();
$user=$_SESSION['user'];
$pref=unserialize($_SESSION['pref']);

function processOption($name)
{
    require 'inc/database.php';
	global $pref,$user;
	if(isset($_POST[$name])){
		$tmp=mysqli_real_escape_string($con,$_POST[$name]);
	}else{
		$tmp='off';
	}
	$pref->$name=$tmp;
	mysqli_query($con,"insert into preferences(user_id,property,value) values ('$user','$name','$tmp') ON DUPLICATE KEY UPDATE value='$tmp'");
}

processOption('night');
processOption('edrmode');
processOption('sharecode');
processOption('i18n');

$_SESSION['pref']=serialize($pref);
//Refresh language.
setcookie('i18n','',time()-3600);