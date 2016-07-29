<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';

if(isset($_GET['page_id']))
  $page_id=intval($_GET['page_id']);
else
  $page_id=1;
  
if(isset($_GET['online'])){
  $online=intval($_GET['online']);
  if($online<0||$online>1) $online=0;
}else
  $online=0;

require_once 'inc/database.php';
$row=mysqli_fetch_row(mysqli_query($con,'select count(*) from users'));
$maxpage=intval($row[0]/20)+1;
if($page_id<1){
  header("Location: ranklist.php");
  exit();
}
else if($page_id>$maxpage){
  header("Location: ranklist.php?page_id=$maxpage");
  exit();
}

function get_next_link()
{
  global $online,$page_id;
  parse_str($_SERVER["QUERY_STRING"],$arr); 
  if($online){
      $arr['online']=1;
  }
  $arr['page_id']=$page_id+1;
  return http_build_query($arr);
}

function get_pre_link()
{
  global $online,$page_id;
  parse_str($_SERVER["QUERY_STRING"],$arr); 
  if($online){
      $arr['online']=1;
  }
  $arr['page_id']=$page_id-1;
  return http_build_query($arr); 
}

$rank=($page_id-1)*20;
if($online==0) 
  $result=mysqli_query($con,"SELECT user_id,nick,solved,submit,score,accesstime,experience_titles.title FROM (SELECT user_id,nick,solved,submit,score,accesstime,MAX(experience_titles.experience) AS m FROM (SELECT user_id,nick,solved,submit,score,accesstime,experience from users order by score desc,experience desc,solved desc,submit desc)t,experience_titles where t.experience>=experience_titles.experience GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience order by score desc,experience desc,solved desc,submit desc limit $rank,20");
else
  $result=mysqli_query($con,"SELECT user_id,nick,solved,submit,score,accesstime,experience_titles.title FROM (SELECT user_id,nick,solved,submit,score,accesstime,MAX(experience_titles.experience) AS m FROM (SELECT user_id,nick,solved,submit,score,accesstime,experience from users order by score desc,experience desc,solved desc,submit desc)t,experience_titles where t.experience>=experience_titles.experience and (NOW()-accesstime)<=300 GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience order by score desc,experience desc,solved desc,submit desc limit $rank,20");

//time()-strtotime($row[5])>300
$inTitle='排名';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>

  <body>
    <?php require 'page_header.php'; ?>      
	<div class="alert collapse text-center alert-popup alert-danger" id="alert_error"></div>
    <div class="container">
	  <div class="row">
        <div class="col-xs-12" style="margin-bottom:10px">
        
        <a href="solved.php" class="btn btn-success"><i class="fa fa-fw fa-clock-o"></i> 最近AC...</a>
        <?php if($online==0){?>
          <a href="ranklist.php?online=1" class="btn btn-primary" id="btn_online"><i class="fa fa-fw fa-car"></i> 在线用户</a>
        <?php }else{?>
          <a href="ranklist.php" class="btn btn-primary" id="btn_online"><i class="fa fa-fw fa-gamepad"></i> 所有用户</a>
        <?php }?>
        <div class="btn-group dropdown">
          <button class="btn btn-primary dropdown-toggle" id="btn_usrcmp_menu"><i class="fa fa-fw fa-users"></i> 用户比较 <span class="caret"></span></button>
          <ul class="dropdown-menu dropdown-menu-right" id="usrcmp_menu">
            <li><input type="text" id="ipt_user1" class="form-control" placeholder="用户1"></li>
            <li><input type="text" id="ipt_user2" class="form-control" placeholder="用户2"></li>
            <li class="divider"></li>
            <li><button id="btn_usrcmp" class="btn btn-small btn-primary pull-right" style="margin-right:9px;">比较</button></li>
          </ul>
        </div>
      </div>
	  </div>
      <br>
      <div class="row">
        <?php if(mysqli_num_rows($result)==0){?>
          <div class="text-center none-text none-center">
            <p><i class="fa fa-meh-o fa-4x"></i></p>
            <p><b>Whoops</b><br>
            看起来这里什么也没有</p>
          </div>
        <?php }else{?>
        <div class="col-xs-12 table-responsive">
            <table class="table table-hover table-bordered " style="margin-bottom:0 margin-right:10px">
              <thead><tr>
                <th style="width:5%">No.</th>
                <th style="width:15%">用户名</th>
                <th style="width:32%">昵称</th>
                <th style="width:8%">状态</th>
                <th style="width:8%">头衔</th>
                <th style="width:8%">分数</th>
                <th style="width:8%">AC</th>
                <th style="width:8%">提交数</th>
                <th style="width:8%">通过率</th>
              </tr></thead>
              <tbody id="userlist">
                <?php 
                  while($row=mysqli_fetch_row($result)){
                    echo '<tr><td>',(++$rank),'</td>';
                    echo '<td><a href="#linkU">',$row[0],'</a></td>';
                    echo '<td>',htmlspecialchars($row[1]),'</td>';
                    if(time()-strtotime($row[5])<=300) echo '<td><label class="label label-success">在线</label></td>';
                    else echo '<td><label class="label label-danger">离线</label></td>';
                    echo '<td>',htmlspecialchars($row[6]),'</td>';
                    echo '<td>',$row[4],'</td>';
                    echo '<td><a href="record.php?user_id=',$row[0],'&amp;result=0">',$row[2],'</a></td>';
                    echo '<td><a href="record.php?user_id=',$row[0],'">',$row[3],'</a></td>';
                    echo '<td>',$row[3] ? intval($row[2]/$row[3]*100) : 0,'%</td>';
                    echo "</tr>\n";
                  }
                ?>
              </tbody>
            </table>
        </div>
        <?php }?>
      </div>
      <div class="row">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="ranklist.php?'.htmlspecialchars(get_pre_link()).'"'?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo'href="ranklist.php?'.htmlspecialchars(get_next_link()).'"'?>>下一页<i class="fa fa-fw fa-angle-right"></i></a>
          </li>
        </ul>
      </div>  
      <div class="modal fade" id="UserModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">用户信息</h4>
            </div>
            <div class="modal-body" id="user_status">
              <p>信息不可用……</p>
            </div>
            <div class="modal-footer">
              <form action="mail.php" method="post">
                <input type="hidden" name="touser" id="um_touser">
                <?php if(isset($_SESSION['user'])){?>
                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> 发私信</button>
                <?php }?>
              </form>
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
		  </div>
        </div>
      </div>
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript">
      function intersection(obj1,obj2,arr1,arr2,ist){
        for(var k in obj1){
          if(obj2.hasOwnProperty(k)){
            ist.push(parseInt(k));
            delete obj2[k];
          }else{
            arr1.push(parseInt(k));
          }
        }
        for(var k in obj2)
          arr2.push(parseInt(k));
      }
      var content='';
      function output(arr){
        arr.sort(function(a,b){return a-b;});
        for(var i in arr){
          content+='<a target="_blank" href="problempage.php?problem_id=';
          content+=arr[i]+'">'+arr[i]+'</a> ';
        }
      }
      function user_diff(id1,info1,id2,info2)
	  {
        var arr1=[],arr2=[],ist=[];
        content='<table class="table table-condensed table-left-aligned" style="margin-bottom:0px;">';
        intersection(info1.solved,info2.solved,arr1,arr2,ist);
        content+='<tr class="success"><td>只有'+id1;
        content+='才AC的题目:</td></tr><tr><td><samp>';output(arr1);
        content+='</samp></td></tr><tr class="success"><td>只有'+id2;
        content+='才AC的题目:</td></tr><tr><td><samp>';output(arr2);
        content+='</samp></td></tr><tr class="success"><td>'+id1+'与'+id2;
        content+='同时AC的题目:</td></tr><tr><td><samp>';output(ist);
        content+='</samp></td></tr>';
        arr1=[];arr2=[];ist=[];
        intersection(info1.failed,info2.failed,arr1,arr2,ist);
        content+='<tr class="danger"><td>只有'+id1;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(arr1);
        content+='</samp></td></tr><tr class="danger"><td>只有'+id2;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(arr2);
        content+='</samp></td></tr><tr class="danger"><td>'+id1+'与'+id2;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(ist);
        content+='</samp></td></tr></table>';
      }
      $(document).ready(function(){
        change_type(3);
        $('#userlist').click(function(Event){
          var $target=$(Event.target);
          if($target.is('a') && $target.attr('href')=='#linkU'){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
            $('#um_touser').val(Event.target.innerHTML);
            $('#UserModal').children('.modal-header').children('h4').html('用户信息');
            $('#UserModal').modal('show');
            return false;
          }
        });
        $('#btn_usrcmp_menu').click(function(E){
          $(E.target).parent().toggleClass('open');
        });
        $('#btn_usrcmp').click(function(){
          var user1=$.trim($('#ipt_user1').val());
          var user2=$.trim($('#ipt_user2').val());
          if(!user1||!user2){
			  $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 人不齐怎么比...').fadeIn();
			  setTimeout(function(){$('#alert_error').fadeOut();},2000);
              return;
		  }
		  if(user1==user2){
			  $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 才不陪你玩呢...').fadeIn();
			  setTimeout(function(){$('#alert_error').fadeOut();},2000);
              return;
		  }
          $.getJSON("ajax_user.php?type=json&user_id="+user1, function(info1){
            if(info1.hasOwnProperty('nobody')){
			  $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 用户 "'+user1+'" 不存在...').fadeIn();
			  setTimeout(function(){$('#alert_error').fadeOut();},2000);
              return;
            }
            $.getJSON("ajax_user.php?type=json&user_id="+user2, function(info2){
              if(info2.hasOwnProperty('nobody')){
                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 用户 "'+user2+'" 不存在...').fadeIn();
			    setTimeout(function(){$('#alert_error').fadeOut();},2000);
				return;
              }
              $('#usrcmp_menu').parent().removeClass('open');
              user_diff(user1,info1,user2,info2);
              $('#user_status').html(content).scrollTop(0);
              $('#UserModal').children('.modal-header').children('h4').html(user1+' vs '+user2);
              $('#UserModal').modal('show');
              return false;
            });
          });
        });
        $('#nav_rank').parent().addClass('active');
        $('#nav_rank_text').removeClass('hidden-sm');
      }); 
    </script>
  </body>
</html>
