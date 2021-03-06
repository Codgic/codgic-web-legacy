<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../../src/database.php';

if(!isset($_SESSION['user']))
    exit();
$user=$_SESSION['user'];
$pref=unserialize($_SESSION['pref']);

function processOption($name)
{
    require __DIR__.'/../../src/database.php';

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

$_SESSION['pref']=serialize($pref);
