<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">         
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn">         
<head>         
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />         
<meta http-equiv="Content-Language" content="UTF-8" />         
</head>         
<form method="post">         
<input name="url" size="20" />         
<input name="submit" type="submit" />         
<!-- <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />-->         
</form>         
<?php         
set_time_limit (24 * 60 * 60);         
if (!isset($_POST['submit'])) die();         
$destination_folder = './client/';   // 文件夹保存下载文件。必须以斜杠结尾         
$url = $_POST['url'];         
$newfname = $destination_folder . basename($url);         
$file = fopen ($url, "rb");         
if ($file) {         
$newf = fopen ($newfname, "wb");         
if ($newf)         
while(!feof($file)) {         
fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );         
}         
}         
if ($file) {         
fclose($file);         
}         
if ($newf) {         
fclose($newf);         
}         
?>    
