<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
require('inc/database.php');
$page_id=0;
if(isset($_GET['start_id']))
  $page_id=intval($_GET['start_id']);
$row=mysqli_fetch_row(mysqli_query($con,'select count(*) from news'));
$total=($row[0]);
$total-=1;
if($page_id<0 || $page_id>=$total)
  die('Argument out of range.');

$inTitle='新闻';
$Title=$inTitle .' - '. $oj_name;
require('inc/database.php');
$res=mysqli_query($con,"select news_id,title,time from news where news_id>0 order by news_id desc limit $page_id,50");
?>
<!DOCTYPE html>
<html manifest="appcache.manifest">
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php') ?>
	<div class="container-fluid">
	  <div class="row-fluid">
        <div class="span10 offset1">
            <table class="table table-responsive table-striped table-bordered">
              <thead><tr>
                <th style="width:6%">No.</th>
                <th>标题</th>
                <th style="width:25%">日期</th>
              </tr></thead>
              <tbody id="tab_record">
              <?php
			  while($row=mysqli_fetch_row($res)){
					echo '<td><font size=3>',htmlspecialchars($row[0]),'</font></td>';
					echo '<td><font size=3><a href="javascript:void(0);" onclick="click_news(',$row[0],')">',htmlspecialchars($row[1]),'</a></font></td>';
					echo '<td><font size=3>',htmlspecialchars($row[2]),'</font></td></tr>';
					echo "\n";
                }
              ?>
              </tbody>
            </table>
        </div>  
      </div>
	<div class="row-fluid">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" href="news.php?start_id=<?php echo $page_id-50 ?>" id="btn-pre">&larr; 上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" href="news.php?start_id=<?php echo $page_id+50 ?>" id="btn-next">下一页 &rarr;</a>
          </li>
        </ul>
      </div> 
	</div>
	<div class="modal fade hide" id="NewsModal" style="margin-top:100px">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><span class="hide" id="ajax_newstitle"></span></h4>
      </div>
      <form class="margin-0" action="#" method="post" id="news_content">
        <div class="modal-body">
	        <span class="hide" id="ajax_newscontent"></span>
        </div>
        <div class="modal-footer form-inline">
          <div class="pull-left">
          </div>
		  <span class="pull-left hide" id="ajax_newstime"></span>
		  <?php if($_SESSION['administrator']) echo '<a class="pull-left" href="admin.php?page=news">编辑</a>'?>
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </form>
    </div>
	<hr>
	<footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
    </footer>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/common.js"></script>
    <script type="text/javascript"> 
	function click_news(newsid){
		  if(newsid){
            $.ajax({
              type:"POST",
              url:"ajax_getnews.php",
              data:{"newsid":newsid},
              success:function(msg){
				  var arr=msg.split("FuckZK1");
				  var title=arr[0];
				  var content=arr[1];
				  var arr=content.split("fUCKzk2");
				  var content=arr[0];
				  var time=arr[1];
				  if(!content) content='本条新闻内容为空...';
				  $('#ajax_newstitle').html(title).show();
				  $('#ajax_newscontent').html(content).show();
				  $('#ajax_newstime').html('发布时间：'+time+'&nbsp;&nbsp;').show();
                  $('#NewsModal').modal('show');
                }
              });
            };
        };
	$(document).ready(function(){
		var cur=<?php echo $page_id?>;
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
	});
    </script>
  </body>
</html>
