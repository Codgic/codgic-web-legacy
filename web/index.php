<?php 
require 'inc/ojsettings.php';
require 'inc/privilege.php';

function encode_user_id($user)
{
  if(!extension_loaded('openssl'))
    return false;
  $iv='7284565820000000';
  $key=hash('sha256','my)(password_xx0',true);
  return openssl_encrypt($user,'aes-256-cbc',$key,false,$iv);
}
require 'inc/checklogin.php';
require 'inc/database.php';
$res=mysqli_query($con,"select content from news where news_id=0 limit 1");
$index_text=($row=mysqli_fetch_row($res)) ? $row[0] : '';
$res=mysqli_query($con,"select news_id,title,importance from news where news_id>0 order by importance desc, news_id desc limit 0,$news_num");
$row=mysqli_fetch_row(mysqli_query($con,"select content from user_notes where id=0 limit 1"));
$category=$row[0];
$inTitle='主页';
$Title=$inTitle .' - '. $oj_name;
$num=0;
?>
<!DOCTYPE html>
<html>
<?php require 'head.php';?>
  <body>
    <?php require 'page_header.php';?> 
    
    <div class="alert alert-danger collapse text-center alert-popup" id="alert_error"></div>
    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <div id="newspad" class="panel panel-default" style="background-color: transparent">
			<div class="panel-body">
              <div id="title" class="text-center" style="cursor:pointer">
				<h1>公告栏</h1>
              </div> 
              <div id="mainarea">
				<?php echo $index_text?>
              </div>
			  <div id="title" class="text-center" style="font-size:24px;cursor:pointer;">
				<h2><b><i class="fa fa-fw fa-angle-double-up"></i></b></h2>
			  </div>
            </div>
          </div>
        </div>
      </div>
	  <div class="row">
		<div class="col-xs-12 col-sm-6">
	    <h1><i class="fa fa-fw fa-newspaper-o"></i> 新闻<?php if(mysqli_num_rows($res)!=0){?><a href="news.php" class="pull-right"><font size=2>更多历史新闻...</font></a></h1>
		  <ul class="list-group" style="margin-top:10px;font-size:16px">
            <?php 
             while($row=mysqli_fetch_row($res)){
				$num++;
				$addt1='';
				$addt2='';
				if($row[2]=='1'){
                    $row[1]='[顶置] '.$row[1];
                    $addt1='<b>';
                    $addt2='</b>';
				}
				echo '<li class="list-group-item"><a href="javascript:void(0)" onclick="return click_news(',$row[0],');">',$addt1.htmlspecialchars($row[1]).$addt2,'</a></li>';
				}
			?>
		  </ul>
		  <?php }else{?>
		  <div class="text-center none-text none-center">
              <p><i class="fa fa-meh-o fa-4x"></i></p>
              <p><b>Whoops</b><br>
              还什么大新闻呢</p>
          </div>
		  <?php }?>
	  </div>
      <div class="col-xs-12 col-sm-6">
        <h1><i class="fa fa-fw fa-th"></i> 分类</h1>
		<div class="panel-group" id="accordion" style="margin-top:10px;font-size:16px">
		<?php if(trim($category)!='') echo $category; else{?>
        <div class="text-center none-text none-center">
          <p><i class="fa fa-meh-o fa-4x"></i></p>
          <p><b>Whoops</b><br>
          尚未发布分类信息</p>
        </div>
        <?php }?>
		</div>
	  </div>
	  </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
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
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
         </div>
		</div>
	  </div>
	</div>
	
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
                if(obj.type=='success'){
                  $('#newstitle').html(obj.title);
                  $('#newscontent').html(obj.content);
                  $('#newstime').html('发布时间：'+obj.time+'&nbsp;&nbsp;');
                  $('#NewsModal').modal('show');
                }else{
                  $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+obj.content).fadeIn();
                  setTimeout(function(){$('#alert_error').fadeOut();},2000);
                }
              }
            });
          };
      };
      $(document).ready(function(){
        var originColor = '#E3E3E3';
        $('#newspad #title').click(function(){
            $('#newspad #mainarea').slideToggle();
            $('#title i').toggleClass('fa fa-fw fa-angle-double-down');
            $('#title i').toggleClass('fa fa-fw fa-angle-double-up');
        });
      });
</script>
</body>
</html>
