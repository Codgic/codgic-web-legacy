<?php
session_start();
require('inc/database.php');
require('inc/preferences.php');
if(!isset($_SESSION['user']))
	exit();
$user=$_SESSION['user'];
$pref=unserialize($_SESSION['pref']);

function processOption($name)
{
	global $pref,$user;
	if(isset($_POST[$name])){
		$tmp='on';
	}else{
		$tmp='off';
	}
	$pref->$name=$tmp;
	mysqli_query($con,"insert into preferences(user_id,property,value) values ('$user','$name','$tmp') ON DUPLICATE KEY UPDATE value='$tmp'");

}

processOption('sharecode');
processOption('hidehotkey');
processOption('night');
processOption('autonight');

$_SESSION['pref']=serialize($pref);


