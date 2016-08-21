<?php
$language = 'zh_CN.UTF-8';
putenv("LANGUAGE=" . $language); 
setlocale(LC_ALL, $language);
bindtextdomain("main", "locale"); 
bind_textdomain_codeset("main", 'UTF-8');
textdomain("main");