<?php
require 'inc/ojsettings.php';
require 'inc/privilege.php';
session_start();
if($require_auth==1&&!isset($_SESSION['user'])) die('你没有权限...');
if(!isset($_GET['user_id']))
	die('Wrong argument.');

require 'inc/database.php';
require 'inc/functions.php';
$user=mysqli_real_escape_string($con,$_GET['user_id']);

$query="select email,ip,accesstime,school,reg_time,submit,solved,motto,nick,privilege from users where user_id='$user'";
$row=mysqli_fetch_row(mysqli_query($con,$query));
$nowtime=date('Y-m-d h:i:s',time());
if(time()-strtotime($row[2])<=300){
    $status_text='在线';
    $status_col='success';
}else{
    $status_text='离线';
    $status_col='danger';
}

if(isset($_GET['type'])&&$_GET['type']=='json'){
	if(!$row)
		echo '{"nobody":0}';
	else{
		$failed=$solved="{";
		$res=mysqli_query($con,"select problem_id,min(result) from solution where user_id='$user' group by problem_id");
		while($row=mysqli_fetch_row($res)){
			$id=$row[0];
			if($row[1]==0)
				$solved.="\"$id\":0,";
			else
				$failed.="\"$id\":0,";
		}
		echo '{"solved":',rtrim($solved,','),'},"failed":',rtrim($failed,','),'}}';
	}
}else{
	if(!$row)
		die('用户不存在...');
	header('Content-Type: text/html; charset=utf-8');
?> 
<div class="media">
  <a class="pull-left">
    <img src="<?php echo get_gravatar($row[0],100)?>" class="media-object img-circle" width="100" height="100">
  </a>
  <div class="media-body">
    <h1 class="media-heading"><?php echo $user?></h1>
    <p class="motto-text"><?php echo $row[7]?></p>
    <label class="label label-<?php echo $status_col?>"><?php echo $status_text?></label>
  </div>
</div>

<table class="table table-condensed table-left-aligned" style="margin-top:15px">
	<colgroup>
		<col style="width:15%">
		<col style="width:5%">
		<col style="width:80%">
	</colgroup>
	<tbody>
    <tr><td colspan="2">昵称:</td><td><?php echo $row[8];?></td></tr>
	<tr><td colspan="2">最近访问:</td><td><?php echo $row[2];?></td></tr>
    <tr><td colspan="2">权限:</td><td><?php echo list_priv($row[9]);?></td></tr>
<?php if(check_priv(PRIV_SYSTEM)){?>
	<tr><td colspan="2">IP地址:</td><td><?php echo $row[1].' '.get_ipgeo($row[1]);?></td></tr>
<?php }?>
	<tr><td colspan="2">学校:</td><td><?php echo htmlspecialchars($row[3]);?></td></tr>
	<tr><td colspan="2">邮箱:</td><td><?php echo htmlspecialchars($row[0]);?></td></tr>
	<tr><td colspan="2">注册时间:</td><td><?php echo $row[4];?></td></tr>
	<tr><td colspan="2">AC/提交:</td><td><?php echo $row[6],'/',$row[5];?></td></tr>
	<?php
		$failed=mysqli_query($con,"select problem_id from solution where user_id='$user' group by problem_id having min(result)>0");
		$number=mysqli_num_rows($failed);
		echo '<tr><td colspan="2">未AC('.$number.'):</td><td><samp>';
		while($row=mysqli_fetch_row($failed)){
			echo '<span style="display:inline-block"><a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a></span>&nbsp;';
		}
		echo '</samp></td></tr>';
		$solved=mysqli_query($con,"select problem_id from solution where result=0 and user_id='$user' group by problem_id");
		$number=mysqli_num_rows($solved);
		echo '<tr><td colspan="2">已AC('.$number.'):</td><td><samp>';
		while($row=mysqli_fetch_row($solved)){
			echo '<span style="display:inline-block"><a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a></span>&nbsp;';
		}
	?>
	</samp></td></tr>
	</tbody>
</table>
<?php
}
?>
