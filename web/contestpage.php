<?php 
require 'inc/ojsettings.php';
require 'inc/result_type.php';
require 'inc/lang_conf.php';
require 'inc/problem_flags.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';

if(isset($_GET['contest_id']))
  $cont_id=intval($_GET['contest_id']);
else if(isset($_SESSION['view']))
  $cont_id=$_SESSION['view'];
else
  $cont_id=1000;
if(isset($_SESSION['user'])){
    $user_id=$_SESSION['user'];
    $query="select title,description,problems,start_time,end_time,source,has_tex,defunct,judge_way,num,enroll_user,result.scr from contest LEFT JOIN (select contest_id as cid, scores as scr from contest_status where user_id='$user_id' group by contest_id) as result on (result.cid=contest_id) where contest_id=$cont_id";
}else
    $query="select title,description,problems,start_time,end_time,source,has_tex,defunct,judge_way,num,enroll_user from contest where contest_id=$cont_id";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
  die('Wrong Contest ID.');
switch ($row[8]) {
  case 0:
    $judge_way='训练';
    break;
  case 1:
    $judge_way='比赛';
    break;

}

if($row[7]=='Y' && !check_priv(PRIV_PROBLEM))
  $forbidden=true;
else if($row[6]&PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
  $forbidden=true;
else{
  $forbidden=false;
  $_SESSION['view']=$cont_id;
  $prob_arr=unserialize($row[2]);
  
  if(isset($_SESSION['user'])){
    if(time()<strtotime($row[3])){
        $info = '<tr><td colspan="2" class="gradient-red text-center"><i class="fa fa-fw fa-remove"></i> 比赛尚未开始</td></tr>';
        $tot_score=0;
    }else if(time()>strtotime($row[4])){
        $info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-fw fa-check"></i> 比赛已经结束</td></tr>';
        $score_arr=unserialize($row[11]);
        $tot_score=array_sum($score_arr);
    }else{
        $info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-fw fa-cog fa-spin"></i> 比赛正在进行</td></tr>';
        $q="select score from solution where user_id='$user_id' and in_date>'".$row[3]."' and in_date<'".$row[4]."' and (";
        for($i=0;$i<$row[9];$i++){
            $q.=' problem_id='.$prob_arr[$i].' or';
        }
        $q=substr($q,0,strlen($q)-3);
        $q.=' )';
        $tot_score=0;
        $rq=mysqli_query($con,$q);
        while($scr_row=mysqli_fetch_row($rq))
            $tot_score+=$scr_row[0];
    }
  }else{
    $info = '<tr><td colspan="2" class="text-center muted" >您尚未登录...</td></tr>';
  } 
  //$result=mysqli_query($con,"select submit_user,solved,submit from problem where problem_id=$cont_id");
  //$statis=mysqli_fetch_row($result);
  //$submit_user=$statis[0];
  //$solved_user=$statis[1];
  //$total_submit=$statis[2];
  $cont_level=($row[6]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;

  //$result=mysqli_query($con,"select result,count(*) as sum from solution where problem_id=$cont_id group by result");
  //$arr=array();
  //while($statis=mysqli_fetch_row($result))
    //$arr[$statis[0]]=$statis[1];
  //ksort($arr);  
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
				  <?php
                  if(strtotime($row[3])-time()>=300){
                      echo '比赛开始前5分钟才可看到题目...';
                  }else if(!isset($row[11])){
                      echo '请您先<a href="javascript:void(0)" onclick="return join_cont();">参加比赛</a>...';
                  }else{
                    for($i=0;$i<$row[9];$i++)
                      echo '<a href="problempage.php?contest_id='.$cont_id.'&problem='.($i+1).'">'.$prob_arr[$i].'</a>&nbsp;';
                  }?>
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
          <div class="row">
            <div class="col-xs-12">
			  <div class="panel panel-default">
				<div class="panel-heading">
				  <h5 class="panel-title">比赛排名</h5>
				</div>
				<div class="panel-body">
				  <?php if(strtotime($row[4])>time()) echo '比赛结束后再来看吧~';
                  else echo 'Coming Not Soon...';?>
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
					  <tr><td style="text-align:left">剩余时间:</td><td><?php echo 'tUnknown'?> ms</td></tr>
                      <tr><td style="text-align:left">每题分值:</td><td><?php echo 't100'?></td></tr>
                      <tr><td style="text-align:left">评分方式:</td><td><?php echo $judge_way?></td></tr>
                      <tr><td style="text-align:left">比赛等级:</td><td><?php echo $cont_level?></td></tr>
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
                    <?php if(isset($_SESSION['user'])&&isset($row[11])){?>
                    <tr><td style="text-align:left">你的分数:</td><td><?php echo $tot_score?></td></tr>
                    <?php if(time()>strtotime($row[4])){?>
                    <tr><td style="text-align:left">你的排名:</td><td><?php echo 'tUnknown'?></td></tr>
                    <?php }}?>
                    <tr><td style="text-align:left">参赛人数:</td><td><?php echo $row[10]?></td></tr>
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
                  <a href="record.php?way=time&amp;problem_id=<?php echo $cont_id?>" class="btn btn-success disabled">状态</a>
                  <a href="board.php?problem_id=<?php echo $cont_id;?>" class="btn btn-warning disabled">讨论</a>
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
    var cont=<?php echo $cont_id?>;
    change_type(2);
      var hide_info = 0;
      function join_cont(){
        <?php if(!isset($_SESSION['user'])){?>
          $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 您尚未登录...').fadeIn();
          setTimeout(function(){$('#alert_error').fadeOut();},2000);
        <?php }else{?>
          $.post('ajax_contest.php', {op:'enroll',contest_id:cont}, function(msg){
            if(/success/.test(msg)){
              window.location.href='problempage.php?contest_id='+cont;
            }else{
              $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
              setTimeout(function(){$('#alert_error').fadeOut();},2000);
            }
          });
        <?php }?>
        return false;
      }
      $(document).ready(function(){
        $('#action_delete').click(function(){
          $.ajax({
            type:"POST",
            url:"ajax_editcontest.php",
            data:{op:'del',contest_id:cont},
            success:function(msg){
              if(/success/.test(msg)){
                location.reload();
              }else{
                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                setTimeout(function(){$('#alert_error').fadeOut();},2000);
              }
            }
          });
        });
        $('#btn_submit').click(function(){join_cont()});
        $('#btn_submit2').click(function(){join_cont()});
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
            alert('Coming Soon...');
        });
        reg_hotkey(72, toggle_info); //Alt+H
      });
    </script>
  </body>
</html>
