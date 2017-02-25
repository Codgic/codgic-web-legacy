<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config/config.php';

define('MARKDOWN_PRESERVE', [
    [
        'begin' => MATHJAX_INLINE_BEGIN,
        'end' => MATHJAX_INLINE_END
    ], [
        'begin' => MATHJAX_TEX_BEGIN,
        'end' => MATHJAX_TEX_END
    ]
]);

function parse_markdown($text) {
    $replaced_text = $text;
    $replace_list = [];
    foreach (MARKDOWN_PRESERVE as $preserve_obj) {
        $delimiter = '/';
        $pregex = $delimiter . preg_quote($preserve_obj['begin'], $delimiter) .
            '[\s\S]*?' . preg_quote($preserve_obj['end'], $delimiter) . $delimiter .'iu'; 
        $replaced_text = preg_replace_callback($pregex, function($matches) use (&$replace_list) {
            $token = uniqid('p_placeholder_');
            $replace_list[$token] = $matches[0];
            return $token;
        }, $replaced_text);
    }
    
    $parsedown = new ParsedownExtra();
    $replaced_text = $parsedown->text($replaced_text);

    foreach($replace_list as $token => $text) {
        $replaced_text = str_replace($token, $text, $replaced_text);
    }
    return $replaced_text;
}

