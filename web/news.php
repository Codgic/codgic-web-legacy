<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';

$page_id=1;
if(isset($_GET['page_id']))
  $page_id=intval($_GET['page_id']);

if($page_id>0){
require 'inc/database.php';
if($row=mysqli_fetch_row(mysqli_query($con,'select max(news_id) from news')))
  $maxpage=intval($row[0]/20);
else
  $maxpage=1;
$res=mysqli_query($con,"select news_id,title,time,importance from news where news_id>0 order by importance desc, news_id desc limit ".(($page_id-1)*20).",20");
}else{
header("Location: news.php");
exit();
}
$inTitle='新闻';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php';?>
  <body>
    <?php require 'page_header.php';?>
	<div class="container">
	  <div class="row">
        <div class="col-xs-12">
          <?php if(mysqli_num_rows($res)==0){?>
            <div class="text-center none-text none-center">
              <p><i class="fa fa-meh-o fa-4x"></i></p>
              <p><b>Whoops</b><br>
              看起来这里什么也没有</p>
            </div>
          <?php }else{?>
            <table class="table table-responsive table-striped table-bordered">
              <thead><tr>
                <th style="width:6%">No.</th>
                <th>标题</th>
                <th style="width:25%">日期</th>
              </tr></thead>
              <tbody id="tab_record">
              <?php
			  while($row=mysqli_fetch_row($res)){
            $addt1='';
            $addt2='';
            if($row[3]=='1'){
                 $row[1]='[顶置] '.$row[1];
                 $addt1='<b>';
                 $addt2='</b>';
            }
					echo '<td><font size=3>',htmlspecialchars($row[0]),'</font></td>';
					echo '<td style="text-align:left"><font size=3><a href="javascript:void(0)" onclick="return click_news(',$row[0],')">',$addt1.htmlspecialchars($row[1]).$addt2,'</a></font></td>';
					echo '<td><font size=3>',htmlspecialchars($row[2]),'</font></td></tr>';
					echo "\n";
                }
              ?>
              </tbody>
            </table>
          <?php }?>
        </div>  
      </div>
	<div class="row">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php
              if($page_id>1) echo 'href="news.php?page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i>上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php
              if(mysqli_num_rows($res)==20&&$page_id<$maxpage) echo 'href="news.php?page_id='.($page_id+1).'"';
            ?>>下一页<i class="fa fa-fw fa-angle-right"></i></a>
          </li>
        </ul>
      </div> 
	</div>
	<div class="modal fade" id="NewsModal">
	  <div class="modal-dialog">
		<div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="newstitle"></h4>
         </div>
         <div class="modal-body" id="newscontent"></div>
         <div class="modal-footer">
			<text class="pull-left" id="newstime"></text>
			<?php if(check_priv(PRIV_SYSTEM))
		     echo '<a class="pull-left" href="admin.php#news">编辑</a>';
			 ?> 
            <button type="button" class="btn btn-default" 
               data-dismiss="modal">关闭
            </button>
         </div>
		</div>
	  </div>
	</div>
	<hr>
	<footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
    </footer>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
	function click_news(newsid){
		  if(newsid){
            $.ajax({
              type:"POST",
              url:"ajax_getnews.php",
              data:{"newsid":newsid},
              success:function(data){
                      var obj=eval("("+data+")");
                      $('#NewsModal').modal('show');
	           			  $('#newstitle').html(obj.title).show();
                      if($.trim(obj.content)=='') $('#newscontent').html('本条新闻内容为空...').show();
          				  $('#newscontent').html(obj.content).show();
           				  $('#newstime').html('发布时间：'+obj.time+'&nbsp;&nbsp;').show();
                   }
              });
            };
        };
	$(document).ready(function(){
	});
    </script>
  </body>
</html>
