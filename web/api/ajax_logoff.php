<?php
session_start();
session_unset();
session_destroy();

require __DIR__.'/../func/cookie.php';
clear_cookie('SID');