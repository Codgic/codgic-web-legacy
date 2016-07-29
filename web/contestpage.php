<?php 
require 'inc/ojsettings.php';
require 'inc/result_type.php';
require 'inc/lang_conf.php';
require 'inc/problem_flags.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';

if($_GET['contest_id'])
  $cont_id=intval($_GET['contest_id']);
else if(isset($_SESSION['view']))
  $cont_id=$_SESSION['view'];
else
  $cont_id=1000;
  
$query="select title,description,problems,start_time,end_time,source,has_tex,defunct,judge_way,num from contest where contest_id=$cont_id";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
  die('Wrong Problem ID.');
switch ($row[8] >> 16) {
  case 0:
    $comparison='Traditional';
    break;
  case 1:
    $comparison='Real, precision: '.($row[13] & 65535);
    break;
  case 2:
    $comparison='Integer';
    break;
  case 3:
    $comparison='Special Judge';
    break;
}

if($row[7]=='Y' && !check_priv(PRIV_PROBLEM))
  $forbidden=true;
else if($row[6]&PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
  $forbidden=true;
else{
  $forbidden=false;
  $_SESSION['view']=$cont_id;

  if(isset($_SESSION['user'])){
    $query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$cont_id group by problem_id";
    $user_status=mysqli_query($con,$query);
    if(mysqli_num_rows($user_status)==0)
      $info = '<tr><td colspan="2" class="text-center muted" >您尚未参赛...</td></tr>';
    else{
      $statis=mysqli_fetch_row($user_status);
      if($statis[0]==0){
        $info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-check"></i> 比赛已经结束</td></tr>';
      }else{
        $info = '<tr><td colspan="2" class="gradient-red text-center"><i class="fa fa-remove"></i> 比赛尚未开始</td></tr>';
      }
    }
  }else{
    $info = '<tr><td colspan="2" class="text-center muted" >您尚未登录...</td></tr>';
  } 
  $result=mysqli_query($con,"select submit_user,solved,submit from problem where problem_id=$cont_id");
  $statis=mysqli_fetch_row($result);
  $submit_user=$statis[0];
  $solved_user=$statis[1];
  $total_submit=$statis[2];
  $prob_level=($row[6]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;

  $result=mysqli_query($con,"select result,count(*) as sum from solution where problem_id=$cont_id group by result");
  $arr=array();
  while($statis=mysqli_fetch_row($result))
    $arr[$statis[0]]=$statis[1];
  ksort($arr);  
}
$inTitle="比赛#$cont_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body>
    <div class="alert collapse text-center alert-popup alert-danger" id="alert_error"></div>
    <?php
    if($row[6]&PROB_HAS_TEX)
      require 'inc/mathjax_head.php';
	  require 'page_header.php';
    ?>
    <div id="probdisp" class="container">
      <?php 
      if($forbidden){
        echo '<div class="text-center none-text none-center"><p><i class="fa fa-meh-o fa-4x"></i></p><p><b>Whoops</b><br>看起来你无法访问该比赛</p></div>';      }else{ 
      ?>
      <div class="row">
        <div class="col-xs-12 col-sm-9" id="leftside" style="font-size:16px">
		  <div class="row">
            <div class="text-center">
              <h2><?php echo '#'.$cont_id,' ',$row[0];if($row[7]=='Y')echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">已删除</span>';?></h2>
            </div>
		  </div>
		  <br>
          <div class="row">
            <div class="col-xs-12">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h5 class="panel-title">比赛简介</h5>
				</div>
				<div class="panel-body">
				  <?php echo mb_ereg_replace('\r?\n','<br>',$row[1]);?>
				</div>
			  </div>
            </div>
          </div>
		  <div class="row">
            <div class="col-xs-12">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h5 class="panel-title">比赛题目</h5>
				</div>
				<div class="panel-body">
				  <?php //echo mb_ereg_replace('\r?\n','<br>',$row[2]);
                  $prob_arr=explode(',',$row[2]);
                  for($i=0;$i<$row[9];$i++)
                      echo '<a href="problempage.php?contest_id='.$cont_id.'&problem='.($i+1).'">'.$prob_arr[$i].'</a>&nbsp;';
                  ?>
				</div>
			  </div>
            </div>
          </div>
		  <div class="row">
            <div class="col-xs-12">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h5 class="panel-title">比赛时间</h5>
				</div>
				<div class="panel-body">
				  <?php echo mb_ereg_replace('\r?\n','<br>',$row[3].' ~ '.$row[4]);?>
				</div>
			  </div>
            </div>
          </div>  
          <div class="row">
            <div class="col-xs-12">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h5 class="panel-title">比赛标签</h5>
				</div>
				<div class="panel-body">
				  <?php echo mb_ereg_replace('\r?\n','<br>',$row[5]);?>
				</div>
			  </div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-3" id="rightside">
          <div class="row">
			<div class="col-xs-12">
			  <button id="btn_hide" title="Alt+H" class="btn btn-primary shortcut-hint pull-right"><i class="fa fa-fw fa-toggle-on"></i> 隐藏详情</button>
			</div>
		  </div>
		  <br> 
          <div class="row">
            <div class="col-xs-12">
              <div class="panel panel-default">
				<div class="panel-body">
                  <table class="table table-condensed table-striped" style="margin-bottom:0px">
					<tbody>
                      <tr><td style="text-align:left">比赛等级:</td><td><?php echo '1'?> ms</td></tr>
					  <tr><td style="text-align:left">剩余时间:</td><td><?php echo 'Unknown'?> ms</td></tr>
                      <tr><td style="text-align:left">每题分值:</td><td><?php echo '100'?></td></tr>
                      <tr><td style="text-align:left">评分方式:</td><td><?php echo $comparison?></td></tr>
                      <?php
                      if($prob_level)
                        echo '<tr><td style="text-align:left">等级:</td><td>',$prob_level,'</td></tr>';
                      ?>
                    </tbody>
                  </table>
				</div>
              </div>
            </div>
          </div>
          <div class="row">
		    <div class="col-xs-12">
              <div id="status" class="panel panel-default" style="margin-top:10px">
			    <div class="panel-body">
                  <table class="table table-condensed table-striped" style="margin-bottom:0px">
                    <tbody>
                    <?php echo $info ?>
                    <tr><td style="text-align:left">你的分数:</td><td><?php echo $solved_user?></td></tr>
                    <tr><td style="text-align:left">你的排名:</td><td><?php echo $total_submit?></td></tr>
                    <tr><td style="text-align:left">参赛人数:</td><td><?php echo $submit_user?></td></tr>
                    </tbody>
                  </table>
			    </div>
              </div>
            </div>
		  </div>
		  <div class="row">
		    <div class="col-xs-12 text-center">
		      <div id="function" class="panel panel-default problem-operation" style="margin-top:10px">
			    <div class="panel-body">
			      <a href="#" title="Alt+S" class="btn btn-primary shortcut-hint" id="btn_submit">参赛</a>
                  <a href="record.php?way=time&amp;problem_id=<?php echo $cont_id?>" class="btn btn-success">状态</a>
                  <a href="board.php?problem_id=<?php echo $cont_id;?>" class="btn btn-warning">讨论</a>
                </div>
              </div>
		    </div>
		  </div>  
          <?php if(check_priv(PRIV_PROBLEM)){?>
          <div class="row">
            <div class="col-xs-12 text-center">
              <div class="panel panel-default problem-operation" style="margin-top:10px">
				<div class="panel-body">
                  <a href="editcontest.php?contest_id=<?php echo $cont_id?>" class="btn btn-primary">编辑比赛</a>
                  <span id="action_delete" class="btn btn-danger"><?php echo $row[7]=='N' ? '删除比赛' : '恢复比赛';?></span>
                </div>
			  </div>
            </div>
          </div>
          <?php }?>
        </div>
        <?php }?>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
	
    <div id="show_tool" class="bottom-right collapse">
	<span id="btn_submit2" title="Alt+S" class="btn btn-primary shortcut-hint">参赛</span>
	<span id="btn_show" title="Alt+H" class="btn btn btn-primary shortcut-hint"><i class="fa fa-fw fa-toggle-off"></i> 显示详情</span>
  </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript">
    change_type(2);
      var hide_info = 0;
      $(document).ready(function(){
        var prob=<?php echo $cont_id?>;
        $('#action_delete').click(function(){
          $.ajax({
            url:"ajax_deleteprob.php?problem_id="+prob,
            dataType:"html",
            success:function(){location.reload();}
          });
        });
        function click_submit(){
          <?php if(!isset($_SESSION['user'])){?>
            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 您尚未登录...').fadeIn();
			setTimeout(function(){$('#alert_error').fadeOut();},2000);
          <?php }else{?>
            $('#prob_input').val(''+prob);
            $('#SubmitModal').modal('show');
			setTimeout("editor.refresh();editor.focus();", 200);
          <?php }?>
          return false;
        }
        $('#btn_submit').click(function(){alert('Coming Soon...')});
        $('#btn_submit2').click(function(){alert('Coming Soon...')});
        function toggle_info(){
          if(hide_info) {
			$('#leftside').addClass('col-sm-9');
            $('#rightside').fadeIn(300);
            $('#show_tool').fadeOut(300);
            hide_info=0;
          }else {
            $('#rightside').fadeOut(300);
            $('#show_tool').fadeIn(300);
			setTimeout("$('#leftside').addClass('col-xs-12').removeClass('col-sm-9')", 300);
            hide_info=1;
          }
        }
        $('#btn_hide').click(toggle_info);
        $('#btn_show').click(toggle_info);
        reg_hotkey(83, function(){ //Alt+S
          if($('#SubmitModal').is(":visible"))
            $('#form_submit').submit();
          else
            click_submit();
        });
        reg_hotkey(72, toggle_info); //Alt+H
      });
    </script>
  </body>
</html>
