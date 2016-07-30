<?php
session_start();
require 'inc/privilege.php';
if(!check_priv(PRIV_SYSTEM))
	die('你没有权限...');
if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa'])
	die('No TFA');
if(!isset($_POST['op']))
	die('参数无效...');
$op=$_POST['op'];
require 'inc/database.php';
require 'inc/problem_flags.php';
require 'inc/functions.php';
$level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
if($op=="list_usr"){
    if(isset($_POST['page_id'])){ 
        $page_id=(intval($_POST['page_id'])-1)*20;
        if($page_id<0) $page_id=0;
    }else $page_id=0;
    if(isset($_POST['q'])){
        $keyword=mysqli_real_escape_string($con,trim($_POST['q']));
    }else $keyword='';
	$res=mysqli_query($con,"select user_id,accesstime,solved,submit,accesstime,defunct,email,privilege,nick from users where (user_id like '%$keyword%' or nick like '%$keyword%') order by defunct desc,privilege desc,user_id limit $page_id,20");
	if(mysqli_num_rows($res)==0)
		die ('<div class="text-center none-text none-center"><p><i class="fa fa-meh-o fa-4x"></i></p><p><b>Whoops</b><br>看起来我们并没有找到你想要的东西</p></div>');
?>
	<table class="table table-condensed table-striped">
		<caption></caption>
		<thead>
			<tr>
				<th colspan="2">用户</th>
                <th style="width:30%">权限</th>
				<th style="width:15%">最近登录</th>
				<th style="width:7%">AC量/提交</th>
                <th style="width:20%">操作</th>
			</tr>
		</thead>
		<tbody>
			<?php
				while($row=mysqli_fetch_row($res)){
					echo '<tr><td><img class="img-circle" src="'.get_gravatar($row[6],30).'"></td>';
                    echo '<td style="text-align:left"><strong><a href="#linkU">'.$row[0].'</a></strong>';
					if($row[5]=='Y'){
                      if(is_null($row[1])) echo '<span style="color:red"> 待审核</span>';
                      else echo '<span style="color:red"> 已禁用</span>';
                    }
					echo '</td>';
                    echo '<td>'.list_priv($row[7]).'(<span>'.$row[7].'</span>)</td>';
					echo '<td>',$row[1],'</td>';
					echo '<td>',$row[3],' / ',$row[2],'</td>';
                    echo '<td><div class="btn-group"><a href="#email" class="btn btn-default">邮件</a><a href="#priv" class="btn btn-default">权限</a><a href="#del" class="btn btn-default';
                    if($row[7]&PRIV_SYSTEM) echo ' disabled';
                    if($row[5]=='Y') echo '">启用'; else echo '">禁用';
                    echo '</a></div></td></tr>';
				}
			?>
		</tbody>
	</table>
<?php
}else if($op=="list_news"){
	$res=mysqli_query($con,"select news_id,time,title,importance from news where news_id>0 order by importance desc, news_id desc");
	if(mysqli_num_rows($res)==0)
		die ('<div class="alert alert-info">目前还没有发布过新闻...</div>');
?>
	<table class="table table-condensed table-striped">
		<caption></caption>
		<thead>
			<tr>
				<th style="width:6%">ID</th>
				<th style="width:20%">日期</th>
				<th style="width:68%">标题</th>
				<th style="width:6%">编辑</th>
			</tr>
		</thead>
		<tbody>
			<?php
				while($row=mysqli_fetch_row($res)){
					$addt1='';
					$addt2='';
					if($row[3]=='1'){
						$row[2]='[顶置] '.$row[2];
						$addt1='<b>';
						$addt2='</b>';
						}
					echo '<tr><td>',$row[0],'</td><td>',$row[1],'</td><td style="text-align:left">',$addt1.htmlspecialchars($row[2]).$addt2,'</td><td><a href="#"><i class="fa fa-pencil"></i></a></td></tr>';
				}
			?>
		</tbody>
	</table>
<?php
}else if($op=='list_experience_title'){
	$res=mysqli_query($con,"select title,experience from experience_titles order by experience");
?>
<table class="table table-striped">
      <caption>头衔</caption>
      <thead>
        <tr>
          <th>经验&nbsp;&ge;</th>
          <th>头衔</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
		<?php
			while($row=mysqli_fetch_row($res)){
				$t=htmlspecialchars($row[0]);
				echo <<<EOF
        <tr>
          <td>$row[1]</td>
          <td>$t</td>
          <td><a href="#"><i data-id="$row[1]" class="fa fa-remove"></i></a></td>
        </tr>
EOF;
			}
		?>
      </tbody>
    </table>
<?php
}else if($op=='list_level_experience'){
	$res=mysqli_query($con,"select level,experience from level_experience order by level");
	$le = array_fill(0, $level_max+1, 0);
	while($row=mysqli_fetch_row($res)){
		$le[$row[0]]=$row[1];
	}
?>

	<table class="table table-striped">
	<caption>题目经验</caption>
	<thead>
	  <th>等级</th>
	  <th>经验</th>
	</thead>
	<tbody>
	<?php
		foreach ($le as $key => $value) {
			echo "<tr><td>$key</td><td><input type=\"number\" name=\"experience[]\" class=\"form-control input-sm center-block\" style=\"width:100px\" value=\"$value\"></td></tr>";
		}
	?>
	</tbody>
	</table>
<?php
}else if($op=='del_title'){
	if(!isset($_POST['id']))
		die('');
	$experience=intval($_POST['id']);
	mysqli_query($con,"DELETE FROM experience_titles where experience=$experience");
}else if($op=='add_experience_title'){
	if(!isset($_POST['experience'], $_POST['title']))
		die('');
	$e=intval($_POST['experience']);
	$t=mysqli_real_escape_string($con,$_POST['title']);
	mysqli_query($con,"INSERT INTO experience_titles VALUES ($e,'$t')");
}else if($op=='update_level_experience'){
	if(!isset($_POST['experience']))
		die('');
	$arr=$_POST['experience'];
	if(count($arr)!=$level_max+1)
		die('Wrong array length');
	foreach ($arr as $key => $value) {
		$key=intval($key);
		$value=intval($value);
		mysqli_query($con,"INSERT INTO level_experience VALUES ($key,$value) ON DUPLICATE KEY UPDATE experience=$value");
	}
}else if($op=='add_news'){
if(!isset($_POST['title'])||!isset($_POST['content']))
		die('error');
	if(!isset($_POST['importance'])) $importance=0;
	else $importance=1;
	$title=mysqli_real_escape_string($con,trim($_POST['title']));
	$content=isset($_POST['content']) ? mysqli_real_escape_string($con,str_replace("\n", "<br>", $_POST['content'])) : '';
	$row=mysqli_fetch_row(mysqli_query($con,"select max(news_id) from news"));
	$id=1;
	if($row[0])
		$id=$row[0]+1;
	if(mysqli_query($con,"insert into news(news_id,time,title,content,importance) values ($id,NOW(),'$title','$content','$importance')"))
		echo 'success';
	else
		echo 'error';
}else if($op=='get_news'){
	if(!isset($_POST['news_id']))
		die('error');
	$newsid=intval($_POST['news_id']);
	$res=mysqli_query($con,"select title,content,time,importance,privilege from news where news_id=$newsid");
    $row=mysqli_fetch_row($res);
    $row[1]=($res && ($row)) ? str_replace('<br>', "\n", $row[1]) : '';
    $arr=array('title'=>$row[0],'content'=>$row[1],'time'=>$row[2],'importance'=>$row[3],'priv'=>$row[4]);
    echo json_encode($arr);
}else if($op=='edit_news'){
    if(!isset($_POST['news_id'])||!isset($_POST['title']))
		die('error');
        
	if(!isset($_POST['importance'])) $importance=0;
	else $importance=1;
    
    $new_priv=0;
    for($i=0;$i<3;$i++)
        if(isset($_POST["$i"])) $new_priv+=pow(2,$i);
    if($new_priv!=0) $new_priv+=PRIV_SYSTEM; //System always has the privilege.
    
	$news_id=intval($_POST['news_id']);
	$title=mysqli_real_escape_string($con,trim($_POST['title']));
	$content=isset($_POST['content']) ? mysqli_real_escape_string($con,str_replace("\n", "<br>", $_POST['content'])) : '';
	if(mysqli_query($con,"update news set title='$title',content='$content',importance='$importance',privilege='$new_priv' where news_id=$news_id"))
		echo 'success';
	else
		echo 'error';
}else if($op=='update_priv'){
	isset($_POST['user_id']) ? $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');
	if(empty($uid)) die('');
    
    $new_priv=0;
    for($i=0;$i<4;$i++)
        if(isset($_POST["$i"])) $new_priv+=pow(2,$i);
      
    if(mysqli_query($con,"update users set privilege='$new_priv' where user_id='$uid'"))
        echo 'success';
    else
        echo '系统错误...';
}else if($op=='del_usr'){
	isset($_POST['user_id']) ? $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');
   if(!strcasecmp($uid,$_SESSION['user'])) die('');
	mysqli_query($con,"delete from users where user_id='$uid' and (accesstime IS NULL)");
}else if($op=='del_news'){
	isset($_POST['news_id']) ? $news_id=intval($_POST['news_id']) : die('');
	if(mysqli_query($con,"delete from news where $news_id>0 and news_id=$news_id")){
		$row=mysqli_fetch_row(mysqli_query($con,"select max(news_id) from news"));
		$news_id++;
		for($news_id;$news_id<=$row[0];$news_id++){
			$res=mysqli_query($con,"update news set news_id='".($news_id-1)."'where news_id='".$news_id."'");
			if(!$res) die($news_id);
		}
		echo 'success';
    }
	else
		echo 'error';
}else if($op=='toggle_usr'){
	isset($_POST['user_id']) ?  $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');
    $row=mysqli_fetch_row(mysqli_query($con,"select defunct,privilege from users where user_id='$uid'"));
    if($row[1]&PRIV_SYSTEM) die('');
	if($row[0]=='Y') mysqli_query($con,"update users set defunct='N' where user_id='$uid'");
    else if($row[0]=='N') mysqli_query($con,"update users set defunct='Y' where user_id='$uid'");
}else if($op=='update_index'){
	$index_text=isset($_POST['text']) ? mysqli_real_escape_string($con,str_replace("\n", "<br>", $_POST['text'])) : '';
	if(mysqli_query($con,"insert into news (news_id,content,time) VALUES (0,'$index_text',NOW()) ON DUPLICATE KEY UPDATE content='$index_text', time=NOW()"))
		echo 'success';
	else
		echo 'fail';
}else if($op=='update_category'){
	$category=isset($_POST['content']) ? mysqli_real_escape_string($con,trim($_POST['content'])) : '';
	if(mysqli_query($con,"insert into user_notes (id,problem_id,tags,user_id,content,edit_time) VALUES (0,0,'','root','$category',NOW()) ON DUPLICATE KEY UPDATE content='$category', edit_time=NOW()")) 
		echo 'success';
	else
		echo 'fail';
}else if($op=='sendemail'){
    if(isset($_POST['to_user'])) $uid=mysqli_real_escape_string($con,trim($_POST['to_user']));
	else die('收件人不能为空...');
    if(isset($_POST['title'])) $title=mysqli_real_escape_string($con,trim($_POST['title']));
    else die('标题/内容不能为空...');
    if(isset($_POST['content'])) $content=mysqli_real_escape_string($con,trim($_POST['content']));
    else die('内容不能为空...');
    $row=mysqli_fetch_row(mysqli_query($con,"select email from users where user_id='$uid'"));
    echo postmail($row[0],$title,$content);
}
?>
