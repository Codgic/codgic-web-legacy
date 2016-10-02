<?php
/**
* CWOJ Global Initialization
*
* Hello World!
*
*/

//Obtain Absolute Path
define("OJDIR", substr(__DIR__,0,-3));

session_start();

if(!isset($i18n))
	require __DIR__.'/../conf/ojsettings.php';

//Check & Initialize preference if uninitialized.
if(!class_exists('preferences')) 
    require __DIR__.'/../func/preferences.php';
if(isset($_SESSION['pref']))
    $pref=unserialize($_SESSION['pref']);
else
    $pref=new preferences();

//Set language.
putenv("LANGUAGE=" . $i18n.'.UTF-8'); 
setlocale(LC_ALL, $i18n.'.UTF-8');
bindtextdomain("main", OJDIR.'/locale'); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");