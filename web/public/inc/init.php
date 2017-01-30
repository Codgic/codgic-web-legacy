<?php
/**
* CWOJ Global Initialization
*
* Hello World!
*
*/

//Obtain Absolute Path
require __DIR__.'/../conf/ojsettings.php';
define("OJDIR", substr(__DIR__,0,-3));

session_start();

//Check & Initialize preference if uninitialized.
if(!class_exists('preferences')) 
    require __DIR__.'/../func/preferences.php';
if(isset($_SESSION['pref']))
    $pref=unserialize($_SESSION['pref']);
else{
    $pref=new preferences();
    $_SESSION['pref']=serialize($pref);
}

//Set language.
putenv("LANGUAGE=" . $i18n.'.UTF-8'); 
setlocale(LC_ALL, $i18n.'.UTF-8');
bindtextdomain("main", OJDIR.'/locale'); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");
