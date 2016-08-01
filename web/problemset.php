<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';

if(isset($_GET['level'])){
  require 'inc/problem_flags.php';
  $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
  if(isset($_GET['page_id']))
	$page_id=intval($_GET['page_id']);
  else
	$page_id=1;
  $level=intval($_GET['level']);
  if($level<0 || $level>$level_max){
	header("Location: problemset.php");
    exit();
  }
  $addt_cond=" (has_tex&".PROB_LEVEL_MASK.")=".($level<<PROB_LEVEL_SHIFT);
  if(check_priv(PRIV_PROBLEM))
	$addt_cond.=" and defunct='N' ";
  $range="limit ".(($page_id-1)*100).",100";
  if(isset($_SESSION['user'])){
	$user_id=$_SESSION['user'];
	$result=mysqli_query($con,"SELECT problem_id,title,accepted,submit,source,defunct,res,saved.pid from problem LEFT JOIN (select problem_id as pid,MIN(result) as res from solution where user_id='$user_id' group by problem_id) as solved on(solved.pid=problem_id) left join (select problem_id as pid from saved_problem where user_id='$user_id') as saved on(saved.pid=problem_id) where $addt_cond order by problem_id $range");
  }else{
	$result=mysqli_query($con,"select problem_id,title,accepted,submit,source,defunct from problem where $addt_cond order by problem_id $range");
  }
}else{
if(isset($_GET['page_id']))
  $page_id=intval($_GET['page_id']);
else if(isset($_SESSION['view']))
  $page_id=intval($_SESSION['view']/100);
else
  $page_id=10;
$row=mysqli_fetch_row(mysqli_query($con,'select max(problem_id) from problem'));
$maxpage=intval($row[0]/100);
if($page_id<10){
  header("Location: problemset.php");
  exit();
}
else if($page_id>$maxpage){
  if($maxpage==0) die('题库中还没有题目哦...');
  else {
    header("Location: problemset.php?page_id=$maxpage");
    exit();
  }
}

if(check_priv(PRIV_PROBLEM))
  $addt_cond='';
else
  $addt_cond=" defunct='N' and ";
$range="between $page_id"."00 and $page_id".'99';
if(isset($_SESSION['user'])){
  $user_id=$_SESSION['user'];
  $result=mysqli_query($con,"SELECT problem_id,title,accepted,submit,source,defunct,res,saved.pid from problem LEFT JOIN (select problem_id as pid,MIN(result) as res from solution where user_id='$user_id' and problem_id $range group by problem_id) as solved on(solved.pid=problem_id) left join (select problem_id as pid from saved_problem where user_id='$user_id') as saved on(saved.pid=problem_id) where $addt_cond problem_id $range order by problem_id");
}else{
  $result=mysqli_query($con,"select problem_id,title,accepted,submit,source,defunct from problem where $addt_cond problem_id $range order by problem_id");
}
}
$inTitle='题库';
$Title=$inTitle .' - '. $oj_name;
//$Title="Problemset $page_id";
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>

  <body>
    <?php require 'page_header.php' ?>
    <div class="container">
      <div class="row">
		<div class="col-xs-12 text-center">
		<?php if(!isset($level)){?>
		  <ul class="pagination">
		  <?php
			if($maxpage>10){
			  for($i=10;$i<=$maxpage;++$i)
				if($i!=$page_id)
				  echo '<li><a href="problemset.php?page_id=',$i,'">',$i,'</a></li>';
				else
				  echo '<li class="active"><a href="problemset.php?page_id=',$i,'">',$i,'</a></li>';
			}?>
			<li><a href="problemset.php?level=0"><i class="fa fa-fw fa-list-ul"></i> 按等级分类 &raquo;</a></li>
		  </ul>
		  <?php }else{?>  
		  <ul class="pagination">
			<li><a href="problemset.php">&laquo; <i class="fa fa-fw fa-th-list"></i> 所有等级</a></li>
            <?php
              for($i=0;$i<=$level_max;++$i){
                if($i!=$level)
                  echo '<li>';
                else
                  echo '<li class="active">';
                echo '<a href="problemset.php?level=',$i,'">',$i,'</a></li>';
              }
            ?>
		  </ul>
		  <?php }?>
		</div>
      </div>
      <div class="row">
        <div class="col-xs-12">
		  <div class="table-responsive">
		    <table class="table table-striped table-bordered" id="problemset_table">
			<thead>
			  <tr>
				<th style="width:6%">ID</th>
				<?php 
				if(isset($_SESSION['user']))
				  echo '<th colspan="3">标题</th>';
				else
				  echo '<th>标题</th>';?>
				<th style="width:10%">AC比例</th>
				<th style="width:10%">通过率</th>
				<th style="width:25%">题目标签</th>
			  </tr>
			</thead>
		    <tbody>
			<?php 
			while($row=mysqli_fetch_row($result)){
			  echo '<tr><td>',$row[0],'</td>';
			  if(isset($_SESSION['user'])){
				echo '<td class="width-for-2x-icon"><i class=', is_null($row[6]) ? '"fa fa-fw fa-2x fa-remove" style="visibility:hidden"' : ($row[6]? '"fa fa-fw fa-2x fa-remove" style="color:red"' : '"fa fa-fw fa-2x fa-check" style="color:green"'), '></i>', '</td>';
				echo '<td style="text-align:left;border-left:0;">';
			  }else{
				echo '<td style="text-align:left">';
			  }
			  echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[1];
			  if($row[5]=='Y')echo '&nbsp;&nbsp;<span class="label label-danger">已删除</span>';
			  echo '</a>';
			  if(isset($_SESSION['user'])){
				echo '<td class="width-for-2x-icon" style="border-left:0;"><i data-pid="',$row[0],'" class="', is_null($row[7]) ? 'fa fa-star-o' : 'fa fa-star', ' fa-2x text-warning save_problem" style="cursor:pointer;"></i></td>';
			  }
			  echo '</td><td><a href="record.php?result=0&amp;problem_id=',$row[0],'">',$row[2],'</a>/';
			  echo '<a href="record.php?problem_id=',$row[0],'">',$row[3],'</a></td>';
			  echo '<td>',$row[3] ? intval($row[2]/$row[3]*100) : 0,'%</td>';
			  echo '<td style="text-align:left;">',$row[4],'</td></tr>';
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
              if($page_id>10) echo 'href="problemset.php?page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
            <?php }else{?>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php
              if($page_id>1) echo 'href="problemset.php?level='.$level.'&page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
            <?php }?>
          </li>
          <li>
             <?php if(!isset($_GET['level'])){?>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php 
              if($page_id<$maxpage) echo 'href="problemset.php?page_id='.($page_id+1).'"';
            ?>>下一页<i class="fa fa-fw fa-angle-right"></i></a>
            <?php }else{?>
            <a class="pager-pre-link shortcut-hint" title="Alt+D" <?php
              if(mysqli_num_rows($result)==100) echo 'href="problemset.php?level='.$level.'&page_id='.($page_id+1).'"';
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
        change_type(1);
        $('#nav_set').parent().addClass('active');
        $('#problemset_table').click(function(E){
          var $target = $(E.target);
          if($target.is('i.save_problem')){
            var pid = $target.attr('data-pid');
            var op;
            if($target.hasClass('fa-star'))
              op='rm_saved';
            else
              op='add_saved';
            $.get('ajax_mark.php?type=1&prob='+pid+'&op='+op,function(result){
              if(/success/.test(result)){
                $target.toggleClass('fa-star-o')
                $target.toggleClass('fa-star')
              }
            });
          }
        });
      });
    </script>
  </body>
</html>
