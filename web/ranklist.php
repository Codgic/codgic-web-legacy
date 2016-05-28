<?php
require 'inc/ojsettings.php';
require ('inc/checklogin.php');

if(isset($_GET['start_id']))
  $page_id=intval($_GET['start_id']);
else
  $page_id=0;

require_once 'inc/database.php';
$row=mysqli_fetch_row(mysqli_query($con,'select count(*) from users'));
$total=($row[0]);
if($page_id<0 || $page_id>=$total)
  die('Argument out of range.');
$rank=$page_id;
$result=mysqli_query($con,"SELECT user_id,nick,solved,submit,score,experience_titles.title FROM (SELECT user_id,nick,solved,submit,score,MAX(experience_titles.experience) AS m FROM (SELECT user_id,nick,solved,submit,score,experience from users order by score desc,experience desc,solved desc,submit desc limit $page_id,50)t,experience_titles where t.experience>=experience_titles.experience GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience order by score desc,experience desc,solved desc,submit desc");
$inTitle='排名';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body>
    <?php require('page_header.php'); ?>       
    <div class="container-fluid">
	<div class="row-fluid">
      <div class="span10 offset1 form-inline" style="margin-bottom:10px">
        <label for="user_page">页数: </label>
        <select class="input-small" id="user_page"></select>
        <div class="pull-right">
          <form id="searchuser_form" action="searchuser.php" method="get" style="margin: 0 0">
            <div class="input-append"><input id="searchuser_input" autofocus="autofocus" type="text" name="q" class="input-medium" style="width:95px" placeholder="搜索用户..."><a href="javascript:su_submit;"><span id="search_addon" class="add-on"><i class="fa fa-search" style="color:grey"></i></span></a></div>
          </form>
        </div>
        <div class="btn-group pull-right" style="margin-right:9px">
          <button class="btn btn-info dropdown-toggle" id="btn_usrcmp_menu">用户比较 <span class="caret"></span></button>
          <ul class="dropdown-menu" id="usrcmp_menu">
            <li><input type="text" id="ipt_user1" placeholder="用户1"></li>
            <li><input type="text" id="ipt_user2" placeholder="用户2"></li>
            <li class="divider"></li>
            <li>
              <button id="btn_usrcmp" class="btn btn-small btn-primary pull-right" style="margin-right:9px;">比较</button>
            </li>
          </ul>
        </div>
        <div class="pull-right" style="margin-right:9px">
          <a href="solved.php" class="btn btn-success">最近AC...</a>
        </div>
        <div class="clearfix"></div>
      </div>
	  </div>
      <div class="row-fluid">
        <div class="span10 offset1">
		
            <table class="table table-responsive table-hover table-bordered " style="margin-bottom:0 margin-right:10px">
              <thead><tr>
                <th style="width:5%">No.</th>
                <th style="width:15%">用户名</th>
                <th style="width:40%">昵称</th>
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
                echo '<td>',htmlspecialchars($row[5]),'</td>';
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
      </div>
      <div class="row-fluid">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" href="ranklist.php?start_id=<?php echo $page_id-50 ?>" id="btn-pre">&larr; 上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" href="ranklist.php?start_id=<?php echo $page_id+50 ?>" id="btn-next">下一页 &rarr;</a>
          </li>
        </ul>
      </div>  
      <div class="modal  fade hide" id="UserModal">
        <div class="modal-header">
          <a class="close" data-dismiss="modal">×</a>
          <h4>用户信息</h4>
        </div>
        <div class="modal-body" id="user_status" style="max-height:350px">
          <p>信息不可用……</p>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </div>
      
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>

    </div><!--/.container-->
    <script src="/assets/js/common.js"></script>
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
        content='<table class="table table-condensed" style="margin-bottom:0px;">';
        content+='<caption>'+id1+' vs '+id2+'</caption>';
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
        content+='<tr class="error"><td>只有'+id1;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(arr1);
        content+='</samp></td></tr><tr class="error"><td>只有'+id2;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(arr2);
        content+='</samp></td></tr><tr class="error"><td>'+id1+'与'+id2;
        content+='提交过但没AC的题目:</td></tr><tr><td><samp>';output(ist);
        content+='</samp></td></tr></table>';
      }
      $(document).ready(function(){
        var i,o=$('#user_page'),cur=<?php echo $page_id?>;
        for(i=1;i<=<?php echo $total?>;i+=50){
          if(i-50<=cur && cur<i)
            o.append('<option id="page_selected" selected="selected">'+i+'</option>');
          else
            o.append('<option>'+i+'</option>');
        }
        $('#user_page').change(function(){
          var num=parseInt($(this).find("option:selected").text())-1;
          location.href='ranklist.php?start_id='+num;
        });
        $('#userlist').click(function(Event){
          var $target=$(Event.target);
          if($target.is('a') && $target.attr('href')=='#linkU'){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
            var win=$('#UserModal');
            win.children('.modal-header').children('h4').html('用户信息');
            win.modal('show');
            return false;
          }
        });
        $('#btn-next').click(function(){
          if(cur+1+50<=<?php echo $total?>)
            return true;
          return false;
        });
        $('#btn-pre').click(function(){
          if(cur+1-50>=1)
            return true;
          return false;
        });
        $('#searchuser_form').submit(function su_submit(){
          if($.trim($('#searchuser_input').val()).length==0)
            return false;
          return true;
        });
        $('#search_addon').click(function(){$('#searchuser_form').submit();});
        $('#btn_usrcmp_menu').click(function(E){
          $(E.target).parent().toggleClass('open');
        });
        $('#btn_usrcmp').click(function(){
          var user1=$.trim($('#ipt_user1').val());
          var user2=$.trim($('#ipt_user2').val());
          if(!user1||!user2)
            return;
          $.getJSON("ajax_user.php?type=json&user_id="+user1, function(info1){
            if(info1.hasOwnProperty('nobody')){
              alert('"'+user1+'" 不存在');
              return;
            }
            $.getJSON("ajax_user.php?type=json&user_id="+user2, function(info2){
              if(info2.hasOwnProperty('nobody')){
                alert('"'+user2+'" 不存在');
                return;
              }
              $('#usrcmp_menu').parent().removeClass('open');
              user_diff(user1,info1,user2,info2);
              $('#user_status').html(content).scrollTop(0);
              var win=$('#UserModal');
              win.children('.modal-header').children('h4').html('用户比较');
              win.modal('show');
              return false;
            });
          });
        });
        $('#nav_rank').parent().addClass('active');
        $('#ret_url').val("ranklist.php?start_id=<?php echo $page_id?>");
      }); 
    </script>
  </body>
</html>
