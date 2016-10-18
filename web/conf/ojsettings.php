<?php
/**
* CWOJ Configuration File
* =======================
* This is the configuration file of CWOJ, which stores a series of variables for you to customize your OJ experience.
*
* 1. Environment Variables
* 1.1 Temporary Location
* ----------------
* You'll need to define a temporary directory for CWOJ to store a temporary file named "cwoj_postmessage.lock"
* This acts as the mutex lock, which is necessary for message board function.
* Please ensure that php has the permission to RW in the directory you've defined.
*/
static $temp_dir="/tmp/cwoj_postmessage.lock"; 

/**
* 2. OJ Variables
* -----------------
* 2.1 Basic Settings
* "i18n" defines the display language of CWOJ.
* "oj_name" defines the very yours name of your OJ.
* "oj_copy" defines the copyright text on the footer of each page.
* "web_ver" defines the version number of the web part, which is shown in preference.php.
* "daemon_ver" defines the version number of the judging service, which is shown in preference.php.
*/
static $i18n = 'zh_CN';
static $oj_name = 'CWOJ'; 
static $oj_copy = 'CWOJ Team'; 
static $web_ver = '1.00.laverne-milestone-5';
static $daemon_ver = '1.03.160801-2101';

//2.2 User policy settings
//"require_auth" determines whether log in is needed to access CWOJ.
//If "require_auth" is set to 0, then guests can access CWOJ without the need to log in.
//If "require_auth" is set to 1, then guests must login to access CWOJ.
static $require_auth=0;

//"require_confirm" determines whether registers must be confirmed by administrators.
//If "require_confirm" is set to 0, then new users can log in instantly after registering.
//If "require_confirm" is set to 1, then new users must wait until their account is confirmed by administrators.
static $require_confirm=0;

//2.3 Night Mode Setings
//The first statment defines the timezone that the server uses.
date_default_timezone_set("PRC"); //Time zone settings

//"day_start" defines the start hour of day mode (24 hour format)
static $daystart = 6;

//"night_start" defines the start hour of night mode (24 hour format)
static $nightstart = 21; 

//2.4 News Settings
//"news_num" defines the maxium number of news shown in index.php
static $news_num=7; 

//2.5 Contact email
//Contact email that is shown in login.php.
static $contact_email = 'info@cwoj.tk';