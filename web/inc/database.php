<?php
$con = mysqli_connect('localhost','root','YOURPASSWORD');
if(!$con){
	echo 'Can not connect to mysql!';
	throw new Exception('Can not connect to mysql!');
}
mysqli_select_db($con,'bsoj');
mysqli_set_charset($con,'utf8');