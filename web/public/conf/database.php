<?php
/**
* CWOJ Database Configuration File
* ================================
* Please configure your database connection info here.
*/

$con = mysqli_connect('localhost','cwoj_test','YOURPASSWORD');
if(!$con){
    echo 'Can not connect to mysql!';
    throw new Exception('Can not connect to mysql!');
}
mysqli_select_db($con,'cwoj_test');
mysqli_set_charset($con,'utf8mb4');
