<?php
if(!isset($_GET['q']))
	die('Wrong argument.');
require('inc/database.php');
$q=mysqli_real_escape_string($con,$_GET['q']);
$result=mysqli_query($con,"select title from problem where title like '%$q%' limit 10");
$arr=array();
while($row=mysqli_fetch_row($result))
	array_push($arr,$row[0]);
echo '{"arr":',json_encode($arr),'}';
