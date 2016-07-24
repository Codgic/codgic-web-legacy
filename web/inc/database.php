<?php
$con = mysqli_connect('localhost','YOUR_USERNAME','YOUR_PASSWORD');
if(!$con){
	echo 'Can not connect to mysql!';
	throw new Exception('Can not connect to mysql!');
}
mysqli_select_db($con,'cwoj');
mysqli_set_charset($con,'utf8mb4');
