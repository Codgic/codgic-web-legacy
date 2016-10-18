<?php
require __DIR__.'/../conf/ojsettings.php';
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';

if($require_auth==1&&!isset($_SESSION['user'])){
    echo _('Permission Denied...');
    exit();
}
if(!isset($_GET['user_id'])){
	echo _('Invalid Argument...');
    exit();
}
require __DIR__.'/../conf/database.php';
require __DIR__.'/../func/userinfo.php';

$user=mysqli_real_escape_string($con,$_GET['user_id']);
$query="SELECT user_id,email,ip,accesstime,school,reg_time,submit,solved,motto,nick,privilege,t1.experience,experience_titles.title FROM (SELECT user_id,email,ip,accesstime,school,reg_time,submit,solved,motto,nick,privilege,t.experience,MAX(experience_titles.experience) AS m FROM (SELECT user_id,email,ip,accesstime,school,reg_time,submit,solved,motto,nick,privilege,experience from users)t,experience_titles where t.experience>=experience_titles.experience GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience where user_id='$user'";
$row=mysqli_fetch_row(mysqli_query($con,$query));
if(time()-strtotime($row[3])<=300){
    $status_text=_('Online');
    $status_col='success';
}else{
    $status_text=_('Offline');
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
	if(!$row){
		echo _('There\'s no such user...');
        exit();
    }
	header('Content-Type: text/html; charset=utf-8');
?> 
<div class="media">
	<a class="pull-left">
		<img src="<?php echo get_gravatar($row[1],100)?>" class="media-object img-circle" width="100" height="100">
	</a>
	<div class="media-body">
		<h1 class="media-heading"><?php echo $user?></h1>
		<p class="motto-text"><?php echo $row[8]?></p>
		<label class="label label-<?php echo $status_col?>">
			<?php echo $status_text?>
		</label>
	</div>
</div>

<table class="table table-condensed table-left-aligned" style="margin-top:15px">
	<colgroup>
		<col style="width:15%">
		<col style="width:5%">
		<col style="width:80%">
	</colgroup>
	<tbody>
		<tr><td colspan="2"><?php echo _('Nickname')?></td><td><?php echo $row[9];?></td></tr>
		<tr><td colspan="2"><?php echo _('Level')?></td><td><?php echo $row[12],' (',$row[11],')';?></td></tr>
		<tr><td colspan="2"><?php echo _('Last Seen')?></td><td><?php echo $row[3];?></td></tr>
		<tr><td colspan="2"><?php echo _('Privilege')?></td><td><?php echo list_priv($row[10]);?></td></tr>
		<?php if(check_priv(PRIV_SYSTEM)){?>
			<tr><td colspan="2"><?php echo _('IP Address')?></td><td><?php echo $row[2].' '.get_ipgeo($row[2]);?></td></tr>
		<?php }?>
		<tr><td colspan="2"><?php echo _('School')?></td><td><?php echo htmlspecialchars($row[4]);?></td></tr>
		<tr><td colspan="2"><?php echo _('Email')?></td><td><?php echo htmlspecialchars($row[1]);?></td></tr>
		<tr><td colspan="2"><?php echo _('Reg Date')?></td><td><?php echo $row[5];?></td></tr>
		<tr><td colspan="2"><?php echo _('AC/Submit')?></td><td><?php echo $row[7],'/',$row[6];?></td></tr>
		<?php
			$failed=mysqli_query($con,"select problem_id from solution where user_id='$user' group by problem_id having min(result)>0");
			$number=mysqli_num_rows($failed);
			echo '<tr><td colspan="2">',_('Failed'),"($number)",'</td><td><samp>';
			while($row=mysqli_fetch_row($failed))
				echo '<span style="display:inline-block"><a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a></span>&nbsp;';
			echo '</samp></td></tr>';
			$solved=mysqli_query($con,"select problem_id from solution where result=0 and user_id='$user' group by problem_id");
			$number=mysqli_num_rows($solved);
			echo '<tr><td colspan="2">',_('Solved'),"($number)",'</td><td><samp>';
			while($row=mysqli_fetch_row($solved))
				echo '<span style="display:inline-block"><a href="problempage.php?problem_id=',$row[0],'">',$row[0],'</a></span>&nbsp;';
			echo '</samp></td></tr>';
		?>
	</tbody>
</table>
<?php }?>