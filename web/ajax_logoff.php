<?php
session_start();
session_unset();
session_destroy();

setcookie('SID', '', 31415926);