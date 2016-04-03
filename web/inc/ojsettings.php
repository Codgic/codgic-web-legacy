<?php
//CWOJ Configuration File
//=======================

//0. Notes
//Please configure database in inc/database.php.
//Please configure email in inc/mailsettings.php.

//1. Environment Variables
//1.1 Temporary Location
//----------------
//"os_type" defines the operating system that CWOJ is currently deployed on.
//You'll need to define a temporary directory for CWOJ to store a temporary file named "cwoj_postmessage.lock"
//This ensures the functionality of Messaging functions.
$temp_dir="/tmp/cwoj_postmessage.lock"; 

//1.2 OS Name
//"os_name" is shown in about.php which tells the users what platform CWOJ is currently deployed on.
$os_name="Ubuntu 14.04 LTS";

//2. OJ Variables
//-----------------
//2.1 Basic Settings
//"oj_name" defines the very yours name of your OJ.
//"oj_copy" defines the copyright text on the footer of each page.
//"web_ver" defines the version number of the web part, which is shown in preference.php.
//"daemon_ver" defines the version number of the judging service, which is shown in preference.php.
$oj_name = 'CWOJ'; 
$oj_copy = 'CWOJ Team'; 
$web_ver = '0.89.160403-2156';
$daemon_ver = '1.01.160227-1736';

//2.2 User policy settings
//"require_auth" determines whether log in is needed to access CWOJ.
//If "require_auth" is set to 0, then guests can access CWOJ without the need to log in.
//If "require_auth" is set to 1, then guests must login to access CWOJ.
$require_auth=1;

//"require_confirm" determines whether registers must be confirmed by administrators.
//If "require_confirm" is set to 0, then new users can log in instantly after registering.
//If "require_confirm" is set to 1, then new users must wait until their account is confirmed by administrators.
$require_confirm=0;

//2.3 Night Mode Setings
//The first statment defines the timezone that the server uses.
date_default_timezone_set("PRC"); //Time zone settings

//"day_start" defines the start hour of day mode (24 hour format)
$daystart = 7;

//"night_start" defines the start hour of night mode (24 hour format)
$nightstart = 21; 

//2.4 404 Page Settings
//"errorimg_num" defines the number of uploaded pictures to be shown in 404 page.
$num_404 = 2;
$num_403 = 1;  

//2.5 News Settings
//"news_num" defines the maxium number of news shown in index.php
$news_num=6; 

//2.6 Testcase upload filesize
//Don't forget to change the settings in php.ini at the same time!
$tc_size='50mb';

//2.7 Update Settings
//$updbranch defines the current branch that you are in.
//$updloc defines the address of update source (as a remote directory).
$updbranch='master';
$updloc='https://raw.githubusercontent.com/CDFLS/CWOJ/'.$updbranch.'/update/';