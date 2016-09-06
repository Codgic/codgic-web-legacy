<?php
session_start();
session_unset();
session_destroy();

require 'inc/cookie.php';
clear_cookie('SID');