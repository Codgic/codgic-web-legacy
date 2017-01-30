<?php
define("PRIV_USER",1);
define("PRIV_INSIDER",2);
define("PRIV_SOURCE",4);
define("PRIV_PROBLEM",8);
define("PRIV_SYSTEM",16);

//Check if user has certain privilege.
function check_priv($priv){
    if(!isset($_SESSION)) 
        session_start();
    if(!isset($_SESSION['priv']))
        return false;
    if($_SESSION['priv'] & $priv)
        return true;
    else 
        return false;
}

//List user's privilege.
function list_priv($i){
    $r='';
    if($i & PRIV_SYSTEM)
        $r.=_('System').' ';
    if($i & PRIV_PROBLEM)
        $r.=_('Problems').' ';
    if($i & PRIV_SOURCE)
        $r.=_('Source').' ';
    if($i & PRIV_INSIDER)
        $r.=_('Insider').' '; 
    if($i & PRIV_USER)
        $r.=_('User').' '; 
    if($r=='')
        $r=_('Everyone');
    else 
        $r=substr($r,0,-1);
    return $r;
}
