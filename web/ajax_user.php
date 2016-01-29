<?php
if(!isset($_GET['user_id']))
	die('Wrong argument.');

require('inc/database.php');
$user=mysqli_real_escape_string($con,$_GET['user_id']);

$query="select email,ip,accesstime,school,reg_time,submit,solved from users where user_id='$user'";
$row=mysqli_fetch_row(mysqli_query($con,$query));

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
		die('用户不存在');
	session_start();
	header('Content-Type: text/html; charset=utf-8');
?>
<table class="table table-condensed table-first-left-aligned" style="margin-bottom:0px;">
	<colgroup>
		<col style="width:12%">
		<col style="width:5%">
		<col style="width:83%">
	</colgroup>
	<tbody>
	<tr><td colspan="2">用户:</td><td><?php echo $user;?></td></tr>
	<tr><td colspan="2">最近登录:</td><td><?php echo $row[2];?></td></tr>
<?php if(isset($_SESSION['administrator'])){?>
	<tr><td colspan="2">IP地址:</td><td><?php echo $row[1];?></td></tr>
<?php }?>
	<tr><td colspan="2">学校:</td><td><?php echo htmlspecialchars($row[3]);?></td></tr>
	<tr><td colspan="2">邮箱:</td><td><?php echo htmlspecialchars($row[0]);?></td></tr>
	<tr><td colspan="2">注册时间:</td><td><?php echo $row[4];?></td></tr>
	<tr><td colspan="2">AC/提交量:</td><td><?php echo $row[6],'/',$row[5];?></td></tr>
	<?php
		$i=0;
		$failed=mysqli_query($con,"select problem_id from solution where user_id='$user' group by problem_id having min(result)>0");
		$number=mysqli_num_rows($failed);
		echo "<tr><td>做错的题目:<br>($number)</td><td colspan=\"2\"><samp>";
		while($row=mysqli_fetch_row($failed)){
			echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a>&nbsp;';
			if((++$i)==11){
				echo '<br>';
				$i=0;
			}
		}
		echo '</samp></td></tr>';
		$i=0;
		$solved=mysqli_query($con,"select problem_id from solution where result=0 and user_id='$user' group by problem_id");
		$number=mysqli_num_rows($solved);
		echo "<tr><td>解决的题目:<br>($number)</td><td colspan=\"2\"><samp>";
		while($row=mysqli_fetch_row($solved)){
			echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a>&nbsp;';
			if((++$i)==11){
				echo '<br>';
				$i=0;
			}
		}
	?>
	</samp></td></tr>
	</tbody>
</table>
<?php
}
?>
