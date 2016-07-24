<?php
$idle_jiffies=0;
$total_jiffies=0;

$free_memory=0;
$total_memory=0;

$daemon_status=0;
function read_proc_stat()
{
  global $idle_jiffies, $total_jiffies;
  $file = fopen('/proc/stat','r');
  if(FALSE === $file)
    return -1;

  $line = fgets($file);
  $numbers = explode(' ', $line);
  fclose($file);

  $total_jiffies = 0;
  for($i=2;$i<=5;$i++)
    $total_jiffies += intval($numbers[$i]);

  $idle_jiffies = intval($numbers[5]);
}
function get_mem_usage()
{
  global $free_memory, $total_memory;
  $file = fopen('/proc/meminfo','r');
  if(FALSE === $file)
    return -1;

  $type='';
  $val=0;
  $unit='';
  while(!feof($file)) {
    $line = fgets($file);
    sscanf($line, "%s%d%s", $type, $val, $unit);

    if(stristr($type, 'MemTotal') !== false){
      $total_memory=$val;
    }else if(stristr($type, 'MemFree') !== false){
      $free_memory+=$val;
    }else if(stristr($type, 'Buffers') !== false){
      $free_memory+=$val;
    }else if(stristr($type, 'Cached') !== false){
      $free_memory+=$val;
    }
  }

  fclose($file);
}
function check_daemon(){
	global $daemon_status;
	$fp = @fsockopen('127.0.0.1', 8881, $errno, $errstr, 5);
	if (!$fp) 
	  $daemon_status=0;
	else {
	  $daemon_status=1;
	  fclose($fp);  
    }
}

header('content-type: text/plain');
session_start();
require 'inc/privilege.php';
if(!check_priv(PRIV_SYSTEM)&&!check_priv(PRIV_PROBLEM))
  exit;

if(-1==read_proc_stat())
  exit;
// echo $idle_jiffies,' ',$total_jiffies,"\n";
$old_idle_jiffies=$idle_jiffies;
$old_total_jiffies=$total_jiffies;

if(-1==get_mem_usage())
  exit;
// echo $free_memory,' ',$total_memory,"\n";
if(-1==check_daemon())
  exit;
usleep(100000);

if(-1==read_proc_stat())
  exit;
// echo $idle_jiffies,' ',$total_jiffies,"\n";
echo '{"cpu":',intval(100*(1-($idle_jiffies-$old_idle_jiffies)/($total_jiffies-$old_total_jiffies))),",";
echo '"mem":',intval(100*(1-$free_memory/$total_memory)),",";
echo '"daemon":',intval($daemon_status),"}";
?>