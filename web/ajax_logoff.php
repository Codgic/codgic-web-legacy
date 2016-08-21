<?php
session_start();
session_unset();
session_destroy();

setcookie('SID', '', time()-3600,'/','.cwoj.org');

?>