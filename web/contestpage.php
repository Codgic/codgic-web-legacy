<?php 
require 'inc/ojsettings.php';
require 'inc/result_type.php';
require 'inc/lang_conf.php';
require 'inc/problem_flags.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';
if(!function_exists('get_time_text')) 
	require 'inc/functions.php';

if(isset($_GET['contest_id']))
  $cont_id=intval($_GET['contest_id']);
else if(isset($_SESSION['view']))
  $cont_id=$_SESSION['view'];
else
  $cont_id=1000;
if(isset($_SESSION['user'])){
    $user_id=$_SESSION['user'];
    $query="select title,description,problems,start_time,end_time,source,has_tex,defunct,judge_way,num,enroll_user,ranked,result.scr,result.res,result.rnk,result.tim from contest LEFT JOIN (select contest_id as cid, scores as scr, results as res, rank as rnk, times as tim from contest_status where user_id='$user_id' group by contest_id) as result on (result.cid=contest_id) where contest_id=$cont_id";
}else
    $query="select title,description,problems,start_time,end_time,source,has_tex,defunct,judge_way,num,enroll_user,ranked from contest where contest_id=$cont_id";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
  $info='看起来这场比赛不存在';
switch ($row[8]) {
  case 0:
    $judge_way='CWOJ赛制';
    break;
  case 1:
    $judge_way='类ACM赛制';
    break;
  case 2:
    $judge_way='类OI赛制';
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
  
  $tot_scores=0;
  $tot_times=0;
  if(strtotime($row[3])>time()){
    //Contest hasn't started
    $s_info = '<tr><td colspan="2" class="gradient-red text-center"><i class="fa fa-fw fa-remove"></i> 比赛尚未开始</td></tr>';
    $cont_status=0;
  }else{
    $prob_arr=unserialize($row[2]);
    if(time()>strtotime($row[4])){
      //Contest has ended
      $s_info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-fw fa-check"></i> 比赛已经结束</td></tr>';
      $cont_status=2;
      if($row[11]=='N'){
        //Contest needs updating
        update_cont_rank($cont_id);
        header("Location: contestpage.php?contest_id=$cont_id");
        exit();
      }else{
        //Check if need updating
        for($i=0;$i<$row[9];$i++){
          $s_row=mysqli_fetch_row(mysqli_query($con,'select title,rejudged from problem where problem_id='.$prob_arr[$i].' limit 1'));
          if($s_row[1]=='Y'){
            update_cont_rank($cont_id);
            header("Location: contestpage.php?contest_id=$cont_id");
            exit(); 
          }
          //Initialize arrays.
          $pname_arr[$i]=$s_row[0];
          $score_arr["$prob_arr[$i]"]=0;
          $res_arr["$prob_arr[$i]"]=NULL;
          $time_arr["$prob_arr[$i]"]=0;
        }
      }
      //Get scores from database directly
      if(isset($row[12])){
        $score_arr=unserialize($row[12]);
        $res_arr=unserialize($row[13]);
        $time_arr=unserialize($row[15]);
        $tot_scores=array_sum($score_arr);
        $tot_times=array_sum($time_arr);
      }
    }else{
      //Contest in progress: live data
      $s_info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-fw fa-cog fa-spin"></i> 比赛正在进行</td></tr>';
      $cont_status=1;
      if(isset($row[12])){
        for($i=0;$i<$row[9];$i++){
          $s_row=mysqli_fetch_row(mysqli_query($con,'select title from problem where problem_id='.$prob_arr[$i].' limit 1'));
          $pname_arr[$i]=$s_row[0];
          if($row[8]==2){ 
            //For judge ways that only recognize the first submit
            $s_row=mysqli_fetch_row(mysqli_query($con, "select score,result,in_date from solution where user_id='$user_id' and in_date>'".$row[3]."' and in_date<'".$row[4]."' and problem_id=".$prob_arr[$i].' order by in_date limit 1'));
            //Process score
            if(!isset($s_row[0]))
              $s_row[0]=0;
            $score_arr["$prob_arr[$i]"]=$s_row[0];
            $tot_scores+=$score_arr["$prob_arr[$i]"];
            //Process result
            if(!isset($s_row[1]))
              $s_row[1]=NULL;
            $res_arr["$prob_arr[$i]"]=$s_row[1];
            //Process time
            if(!isset($s_row[2]))
              $s_row[2]=0;
            $time_arr["$prob_arr[$i]"]=$s_row[2];
            $tot_times+=$time_arr["$prob_arr[$i]"];
          }else{
            //For judge ways that recognize max scores
            $s_row=mysqli_fetch_row(mysqli_query($con,"select max(score),count(score),min(result),max(in_date) from solution where user_id='$user_id' and in_date>'".$row[3]."' and in_date<'".$row[4]."' and problem_id=".$prob_arr[$i]));
            //Process scores
            if(!isset($s_row[0]))
              $s_row[0]=0;
            if($s_row[0]!=100&&$row[8]==1)
              $s_row[0]=0;
            $score_arr["$prob_arr[$i]"]=$s_row[0];
            $tot_scores+=$score_arr["$prob_arr[$i]"];
            //Process results
            if(!isset($s_row[2])) $s_row[2]=NULL;
            $res_arr["$prob_arr[$i]"]=$s_row[2];
            //Process times
            if(!isset($s_row[3]))
              $s_row[3]=0;
            if($s_row[0]==100)
              $time_arr["$prob_arr[$i]"]=strtotime($s_row[3])-strtotime($row[3])+1200*($s_row[1]-1);
            else 
              $time_arr["$prob_arr[$i]"]=1200*$s_row[1];
            $tot_times+=$time_arr["$prob_arr[$i]"];
          }
        }
      }
    }
  }
  $cont_level=($row[6]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
}
if($forbidden) $info='看起来你无法访问该比赛';

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
       <?php if(isset($info)){?>
        <div class="row">
          <div class="col-xs-12">
            <div class="text-center none-text none-center">
              <p><i class="fa fa-meh-o fa-4x"></i></p><p><b>Whoops</b><br>
              <?php echo $info?></p>
            </div>
          </div>
        </div>
      <?php }else{?>
      <div class="row">
        <div class="col-xs-12 col-sm-9" id="leftside" style="font-size:16px">
          <div class="text-center">
            <h2><?php echo '#'.$cont_id,' ',$row[0];if($row[7]=='Y')echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">已删除</span>';?></h2>
          </div>
		  <br>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">比赛简介</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row[1]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">比赛题目</h5>
            </div>
            <?php
              if($cont_status==0){
                echo '<div class="panel-body">比赛开始后才可看到题目...</div>';
              }else if(!isset($row[12]) && $cont_status!=2){
                echo '<div class="panel-body">请你先<a href="javascript:void(0)" onclick="return join_cont();">参加比赛</a>...</div>';
              }else{?>
              <ul class="list-group">
                <?php for($i=0;$i<$row[9];$i++){
                  echo '<li class="list-group-item"><i class=', is_null($res_arr["$prob_arr[$i]"]) ? '"fa fa-fw fa-lg fa-question" style="color:grey"' : ($res_arr["$prob_arr[$i]"] ? '"fa fa-fw fa-lg fa-remove" style="color:red"' : '"fa fa-fw fa-lg fa-check" style="color:green"'), '></i>';
                  echo ' <a href="problempage.php?contest_id='.$cont_id.'&prob='.($i+1).'">#'.$prob_arr[$i].' - '.$pname_arr[$i].'</a>';
                  if(isset($row[12])){
                    echo '<span class="pull-right">';
                    if($row[8]==1){
                      if($score_arr["$prob_arr[$i]"]==100) echo '<font color="green">已AC</font>';
                      else echo '<font color="red">未AC</font>';
                    }else echo $score_arr["$prob_arr[$i]"];
                    echo '</span></li>';
                  }
                }?>
              </ul>
            <?php }?>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title"><?php echo $judge_way?></h5>
            </div>
            <div class="panel-body">
              <?php echo get_judgeway_destext($row[8])?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">比赛标签</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row[5]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">比赛排名</h5>
            </div>
            <div class="panel-body" id="cont_rank"></div>
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
                      <tr><td style="text-align:left">开始时间:</td><td><?php echo $row[3]?></td></tr>
                      <tr><td style="text-align:left">结束时间:</td><td><?php echo $row[4]?></td></tr>
                      <tr><td style="text-align:left">持续时间:</td><td><?php echo get_time_text(strtotime($row[4])-strtotime($row[3]))?></td></tr>
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
                    <?php echo $s_info ?>
                    <?php if(isset($_SESSION['user'])&&isset($row[12])){?>
                    <tr><td style="text-align:left">你的分数:</td><td><?php echo $tot_scores?></td></tr>
					<tr><td style="text-align:left">你的罚时:</td><td><?php echo get_time_text($tot_times)?></td></tr>
                    <?php if($cont_status==2){?>
                    <tr><td style="text-align:left">你的排名:</td><td><?php echo $row[14]?></td></tr>
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
                  <a href="#" class="btn btn-success" id="btn_rank">排名</a>
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
        $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 你还没有登录...').fadeIn();
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
		$('#cont_rank').load('ajax_contest.php',{op:'get_rank_table',contest_id:<?php echo $cont_id?>});
        <?php if($cont_status==0){?>
            var t1=new Date(),t2=new Date(<?php echo time()*1000?>);
            var t=<?php echo (strtotime($row[3])-time())*1000?>-t1.getTime()+t2.getTime();
            setTimeout(function(){location.reload();},t);
        <?php }?>
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
        $('#btn_rank').click(function(){$("html,body").animate({scrollTop:$("#cont_rank").offset().top},200);});
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
