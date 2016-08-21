<?php
require 'inc/global.php';
function show_alert($str)
{
	echo '<html><body><script>alert("';
	echo htmlspecialchars($str);
	echo '");</script></body></html>';
}

if(!isset($_SESSION['user'])){
    $info=_('Please login first...');
	show_alert($info);
	exit;
}
require 'inc/database.php';
require 'inc/preferences.php';
require 'inc/lang_conf.php';
require 'inc/tgz.lib.php';

$pref=unserialize($_SESSION['pref']);
$now=time();
if($now - $pref->backuptime < 604800){
    $info=_('You can only backup your code not more than once a month...');
	show_alert($info);
	exit;
}
$archive="backup-$now";
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$archive.'.tar.gz"');

$fout = fopen('php://output','wb');
if($fout === FALSE)
	exit;

$tar = new TGZfile($fout);

$pref->backuptime=$now;
$_SESSION['pref']=serialize($pref);

$user=mysql_real_escape_string($_SESSION['user']);
mysql_query("insert into preferences(user_id,property,value) values ('$user','backuptime','$now') ON DUPLICATE KEY UPDATE value='$now'");

$res=mysql_query("SELECT problem_id,language,source FROM source_code , (SELECT max_sol,problem_id,language FROM solution,  (SELECT max(solution_id) AS max_sol FROM solution WHERE user_id='$user' AND result=0 GROUP BY problem_id) last WHERE solution_id=max_sol) tmp  WHERE source_code.solution_id=max_sol");

while($row=mysql_fetch_row($res)){
	$filename = $archive.'/'.$row[0].'.'.$LANG_EXT[$row[1]];
	$tar->AddFile($filename, $row[2]);
}

$tar->Close();