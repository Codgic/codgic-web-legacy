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
    if($i & PRIV_SYSTEM) $r.=_('System').' ';
    if($i & PRIV_PROBLEM) $r.=_('Problems').' ';
    if($i & PRIV_SOURCE) $r.=_('Source').' ';
    if($i & PRIV_INSIDER) $r.=_('Insider').' '; 
    if($r=='') $r=_('User');
    else $r=substr($r,0,-1);
    return $r;
}