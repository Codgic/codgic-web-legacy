<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
$inTitle='新闻';
$Title=$inTitle .' - '. $oj_name;
require('inc/database.php');
$res=mysqli_query($con,"select news_id,title,time from news where news_id>0 order by news_id desc");
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php') ?>
	<div class="container-fluid">
	<center><h1>还未开发完成！</h1></center><br>
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
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </form>
    </div>
	<hr>
	<footer>
       <p>&copy; <?php echo"{$year} {$copyright}";?></p>
    </footer>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script type="text/javascript"> 
	function click_news(newsid){
		  if(newsid){
            $.ajax({
              type:"POST",
              url:"ajax_getnews.php",
              data:{"newsid":newsid},
              success:function(msg){
				  var arr=msg.split("Z9EWKWRFE324@EWRFTFFWE443R854QSFDSUERWE4EFRDN");
				  var title=arr[0];
				  var content=arr[1];
				  if(!content) content='本条新闻内容为空...';
				  $('#ajax_newstitle').html(title).show();
				  $('#ajax_newscontent').html(content).show();
                  $('#NewsModal').modal('show');
                }
              });
            };
        };
    </script>
  </body>
</html>
