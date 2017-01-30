<?php
/**
* Codgic Database Configuration File
* ================================
* Please configure your database connection info here.
*/

$con = mysqli_connect('localhost','codgic','YOURPASSWORD');
if(!$con){
    echo 'Can not connect to mysql!';
    throw new Exception('Can not connect to mysql!');
}
mysqli_select_db($con,'codgic');
mysqli_set_charset($con,'utf8mb4');