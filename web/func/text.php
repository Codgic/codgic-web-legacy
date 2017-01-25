<?php
function encode_space($string) {
    $find = array('/\r?\n/', '/ /');
    $replace = array('<br>', '&nbsp;');

    $result = preg_replace($find, $replace, $string);
    
    return $result;
}