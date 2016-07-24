<?php
define("PRIV_INSIDER",1);
define("PRIV_SOURCE",2);
define("PRIV_PROBLEM",4);
define("PRIV_SYSTEM",8);

function check_priv($priv){
    if(!isset($_SESSION)) session_start();
    if(!isset($_SESSION['priv'])) return false;
    if($_SESSION['priv'] & $priv) return true;
    else return false;
}

function list_priv($i){
    $r='';
    if($i & PRIV_SYSTEM) $r.='系统 ';
    if($i & PRIV_PROBLEM) $r.='题库 ';
    if($i & PRIV_SOURCE) $r.='源码 ';
    if($i & PRIV_INSIDER) $r.='校内 '; 
    if($r=='') $r='用户';
    return $r;
}