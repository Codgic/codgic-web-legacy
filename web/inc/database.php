<?php
$con = mysqli_connect('localhost','YOURUSERNAME','YOURPASSWORD');
if(!$con){
	echo 'Can not connect to mysql!';
	throw new Exception('Can not connect to mysql!');
}
mysqli_select_db($con,'cwoj_zkfucker123');
mysqli_set_charset($con,'utf8');
