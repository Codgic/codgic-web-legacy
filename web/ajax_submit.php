<?php
require 'inc/lang_conf.php';
require 'inc/checklogin.php';

function posttodaemon($data){
   if(!isset($_SESSION['user'])) return ("您并没有登录...");
	$encoded="";
	while(list($k,$v) = each($data)){
		$encoded.=($encoded ? "&" : "");
		$encoded.=rawurlencode($k)."=".rawurlencode($v);
	}
	if(!($fp=@fsockopen('127.0.0.1', 8881)))
		die ("错误: 无法连接至评测服务...");

	fputs($fp, "POST /submit_prob HTTP/1.0\r\n");
	fputs($fp, "Host: 127.0.0.1\r\n");
	fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
	fputs($fp, "Content-length: " . strlen($encoded) . "\r\n");
	fputs($fp, "Connection: close\r\n\r\n");

	fputs($fp, "$encoded\r\n");

	$line = fgets($fp,128);
	if(!strstr($line,"HTTP/1.0 200"))
		return ("错误: 无法提交，服务器内部错误...\n");

	$results="";
	while(!feof($fp))
		$results.=fgets($fp,128);
	/*$inheader=true;
	while(!feof($fp)) {
		$line=fgets($fp,128);
		if($inheader && $line=="\r\n")
			$inheader=false;
		else if(!$inheader)
			$results.=$line;
	}*/
	fclose($fp);

	return $results;
}

if(!isset($_SESSION['user']) || strlen($_SESSION['user'])==0)
	die('您尚未登录...');
if(!isset($_POST['op'],$_POST['problem']))
	die('Wrong argument');
$prob=intval($_POST['problem']);

require('inc/database.php');
$res=mysqli_query($con,"select case_time_limit,memory_limit,case_score,compare_way,defunct,has_tex from problem where problem_id=$prob");
if(!($row=mysqli_fetch_row($res)))
	die('题目不存在...');

if($_POST['op']=='judge'){
	if(!isset($_POST['language']))
	  die('Wrong argument');
	$lang=intval($_POST['language']);
	if(!array_key_exists($lang,$LANG_NAME))
	  die('不支持的语言...');
	if(!isset($_POST['source']))
	  die('代码太短...');
	$code=$_POST['source'];
	if(strlen($code)>29990)
	  die('代码太长...');
	
	require 'inc/problem_flags.php';
    require 'inc/privilege.php';
	$forbidden=false;
	if($row[4]=='Y' && !check_priv(PRIV_PROBLEM))
	  $forbidden=true;
	else if($row[5]&PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
	  $forbidden=true;
	if($forbidden)
	  die('你没有权限访问此题目...');
	
	$_SESSION['lang']=$lang;
	mysqli_query($con,"update users set language=$lang where user_id='".$_SESSION['user']."'");
	mysqli_query($con,"update problem set in_date=NOW() where problem_id=$prob");
	
	$key=md5('key'.time().rand());
	$share_code=(isset($_POST['public']) ? 1 : 0);
	
	$data=array(
	'a'=>$prob,
	'b'=>$lang,
	'c'=>$row[0],
	'd'=>$row[1],
	'e'=>$row[2],
	'f'=>$code,
	'g'=>$_SESSION['user'],
	'h'=>$key,
	'i'=>$share_code,
	'j'=>$row[3]
	);
	ignore_user_abort(TRUE);
	$result = posttodaemon($data);
	if(strstr($result,"OK"))
	  echo 'success'.$key;
	else
	  die($result);
}else if($_POST['op']=='rejudge'){
	$data=array(
	'a'=>$prob,
	'c'=>$row[0],
	'd'=>$row[1],
	'e'=>$row[2],
	'h'=>"rejudge".$prob,
	'j'=>$row[3],
	'k'=>1 //TYPE_rejudge
	);
	ignore_user_abort(TRUE);
	$result = posttodaemon($data);
	
	if(strstr($result,"OK"))
	  echo 'success';
	else if(strstr($result,"another"))
	  echo "目前正在执行另一评测任务，请稍后再试...";
	else
	  echo $result;
}
?>
