<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';

if(isset($_GET['level'])){
  die('Not ready...');
  require 'inc/problem_flags.php';
  $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
  if(isset($_GET['page_id']))
	$page_id=intval($_GET['page_id']);
  else
	$page_id=1;
  $level=intval($_GET['level']);
  if($level<=0 || $level>$level_max){
	header("Location: contest.php");
    exit();
  }
  $cond=" (has_tex&".PROB_LEVEL_MASK.")=".($level<<PROB_LEVEL_SHIFT);
  if(!check_priv(PRIV_PROBLEM))
	$cond.=" and defunct='N' ";
  $range="limit ".(($page_id-1)*100).",100";
  if(isset($_SESSION['user'])){
	$user_id=$_SESSION['user'];
    $result=mysqli_query($con,"SELECT contest_id,title,start_time,end_time,defunct,num,source,has_tex,res from contest LEFT JOIN (select contest_id as cid,1 as res from contest_status where user_id='$user_id') as fuckzk on (cid=contest_id) where $addt_cond contest_id $range order by contest_id");
  }else{
    $result=mysqli_query($con,"select contest_id,title,start_time,end_time,defunct,num,source,has_tex from contest where $addt_cond contest_id $range order by contest_id");
}
}else{
if(isset($_GET['page_id']))
  $page_id=intval($_GET['page_id']);
else if(isset($_SESSION['view']))
  $page_id=intval($_SESSION['view']/100);
else
  $page_id=10;
$row=mysqli_fetch_row(mysqli_query($con,'select max(contest_id) from contest'));
$maxpage=intval($row[0]/100);
if($page_id<10){
  header("Location: contest.php");
  exit();
}
else if($page_id>$maxpage){
  header("Location: contest.php?page_id=$maxpage");
  exit();
}

if(isset($_SESSION['administrator']))
  $addt_cond='';
else
  $addt_cond=" defunct='N' and ";
$range="between $page_id"."00 and $page_id".'99';
if(isset($_SESSION['user'])){
  $user_id=$_SESSION['user'];
  $result=mysqli_query($con,"SELECT contest_id,title,start_time,end_time,defunct,num,source,has_tex,res from contest LEFT JOIN (select contest_id as cid,1 as res from contest_status where user_id='$user_id') as fuckzk on (cid=contest_id) where $addt_cond contest_id $range order by contest_id");
}else{
  $result=mysqli_query($con,"select contest_id,title,start_time,end_time,defunct,num,source,has_tex from contest where $addt_cond contest_id $range order by contest_id");
}
}
$inTitle='比赛';
$Title=$inTitle .' - '. $oj_name;
//$Title="contest $page_id";
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php';?>

  <body>
    <?php require 'page_header.php';?>
    <div class="container">
      <div class="row">
		<div class="col-xs-12 text-center">
		<?php if(!isset($level)){?>
		  <ul class="pagination">
		  <?php
			if($maxpage>10){
			  for($i=10;$i<=$maxpage;++$i)
				if($i!=$page_id)
				  echo '<li><a href="contest.php?page_id=',$i,'">',$i,'</a></li>';
				else
				  echo '<li class="active"><a href="contest.php?page_id=',$i,'">',$i,'</a></li>';
			}?>
			<li><a href="contest.php?level=1"><i class="fa fa-fw fa-list-ul"></i> 按等级分类 &raquo;</a></li>
		  </ul>
		  <?php }else{?>  
		  <ul class="pagination">
			<li><a href="contest.php">&laquo; <i class="fa fa-fw fa-th-list"></i> 所有等级</a></li>
            <?php
              for($i=1;$i<=$level_max;++$i){
                if($i!=$level)
                  echo '<li>';
                else
                  echo '<li class="active">';
                echo '<a href="contest.php?level=',$i,'">',$i,'</a></li>';
              }
            ?>
		  </ul>
		  <?php }?>
		</div>
      </div>
      <div class="row">
        <div class="col-xs-12">
		  <div class="table-responsive">
		    <table class="table table-striped table-bordered" id="contest_table">
			<thead>
              <tr>
				<th style="width:6%">ID</th>
				<?php 
				if(isset($_SESSION['user']))
				  echo '<th colspan="2">标题</th>';
				else
				  echo '<th>标题</th>';?>
				<th style="width:15%">开始时间</th>  
				<th style="width:5%">状态</th>  
				<th style="width:5%">题量</th>
				<th style="width:25%">比赛标签</th>
              </tr>
            </thead>
		    <tbody>
			<?php 
			while($row=mysqli_fetch_row($result)){
			  if(time()>strtotime($row[3])) $cont_status='<span class="label label-ac">已经结束</span>';
			  else if(time()<strtotime($row[2])) $cont_status='<span class="label label-wa">尚未开始</span>';
			  else $cont_status='<span class="label label-re">正在进行</span>';
			  echo '<tr>';
			  echo '<td>',$row[0],'</td>';
			  if(isset($_SESSION['user'])){
				echo '<td class="width-for-2x-icon"><i class=', is_null($row[8]) ? '"fa fa-remove fa-2x" style="visibility:hidden"' : '"fa fa-2x fa-check" style="color:green"', '></i>', '</td>';
				echo '<td style="text-align:left;border-left:0;">';
			  }else{
				echo '<td style="text-align:left">';
			  }
			  echo '<a href="contestpage.php?contest_id=',$row[0],'">',$row[1];
			  if($row[4]=='Y')echo '&nbsp;&nbsp;<span class="label label-danger">已删除</span>';
			  echo '</a></td><td>',$row[2],'</a></td>';
			  echo '<td>',$cont_status,'</td>';
			  echo '<td>',$row[5],'</td>';
			  echo '<td>',$row[6],'</td></tr>';
			}?>
			</tbody>
		  </table>
		</div>
	  </div>
      </div>
      <div class="row">
        <ul class="pager">
          <li>
            <?php if(!isset($_GET['level'])){?>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php 
              if($page_id>10) echo 'href="contest.php?page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
            <?php }else{?>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php
              if($page_id>1) echo 'href="contest.php?level='.$level.'&page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
            <?php }?>
          </li>
          <li>
             <?php if(!isset($_GET['level'])){?>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php 
              if($page_id<$maxpage) echo 'href="contest.php?page_id='.($page_id+1).'"';
            ?>>下一页<i class="fa fa-fw fa-angle-right"></i></a>
            <?php }else{?>
            <a class="pager-pre-link shortcut-hint" title="Alt+D" <?php
              if(mysqli_num_rows($result)==100) echo 'href="contest.php?level='.$level.'&page_id='.($page_id+1).'"';
            ?>>下一页<i class="fa fa-fw fa-angle-right"></i></a>
            <?php }?>
          </li>
        </ul>
      </div>
      <hr>
      <footer>
      <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        change_type(2);
        var cur_page=<?php echo $page_id ?>;
        $('#nav_cont').parent().addClass('active');
		$('#nav_cont_text').removeClass("hidden-sm");
      });
    </script>
  </body>
</html>
