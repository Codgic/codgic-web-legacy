<?php 
require 'inc/ojsettings.php';
function encode_user_id($user)
{
  if(!extension_loaded('openssl'))
    return false;
  $iv='7284565820000000';
  $key=hash('sha256','my)(password_xx0',true);
  return openssl_encrypt($user,'aes-256-cbc',$key,false,$iv);
}
require ('inc/checklogin.php');
require('inc/database.php');
$res=mysqli_query($con,"select content from news where news_id=0");
$index_text=($row=mysqli_fetch_row($res)) ? $row[0] : '';
$res=mysqli_query($con,"select news_id,title,importance from news where news_id>0 order by importance desc, news_id desc limit 0,$news_num");
$row=mysqli_fetch_row(mysqli_query($con,"select content from user_notes where id=0"));
$category=$row[0];
$inTitle='主页';
$Title=$inTitle .' - '. $oj_name;
$num=0;
?>
<!DOCTYPE html>
<html>
<?php require('head.php');?>
  <body>
    <?php require('page_header.php');?>  
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span10 offset1">
          <div id="newspad" class="well-index" style="font-size:16px;padding:19px 40px;background-color:none">
            <div id="title" class="center" style="text-align:center; cursor: pointer;">
				<h1>公告栏</h1>
            </div> 
            <div id="mainarea">
                <?php echo $index_text?>
            </div>
			<div id="title" class="center" style="text-align:center; font-size:24px;cursor:pointer;">
				<h2><b><i class="fa fa-fw fa-angle-double-up"></i></b></h2>
            </div>
          </div>
        </div>
      </div>
	  <div class="row-fluid">
		<div class="span5 offset1">
	    <h1><i class="fa fa-fw fa-newspaper-o"></i> 新闻<?php if(mysqli_num_rows($res)!=0){?><a href="news.php" class="pull-right"><font size=2>更多历史新闻...</font></a></h1>
		  <ul class="nav" style="margin-top:10px;font-size:16px">
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
				echo '<li style="line-height:32px"><a href="javascript:void(0);" onclick="click_news(',$row[0],')">',$addt1.htmlspecialchars($row[1]).$addt2,'</a></li>';
				}
			?>
		  </ul>
		  <?php }else{?>
		  <br><p><font color='grey' size=5>:( 暂时没有发布过新闻~</font></p>
		  <?php }?>
	  </div>
      <div class="span5">
        <h1><i class="fa fa-fw fa-th"></i> 分类</h1>
		<div class="accordion" id="index_category" style="margin-top:12px;font-size:16px">
		<?php if(trim($category)!='') echo "$category";
		      else echo'<p><font color="grey" size=5>:( 暂时没有发布题目分类目录~</font></p>';?>
		</div>
	  </div>
	  </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
	<div class="modal fade hide" id="NewsModal">
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
		  <?php if(isset($_SESSION['administrator'])) echo '<a class="pull-left" href="admin.php?page=news">编辑</a>'?>
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </form>
    </div>
    <script src="/assets/js/common.js"></script>
    <script src="/assets/js/chat.js"></script>
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
	           			  $('#ajax_newstitle').html(obj.title).show();
                      if($.trim(obj.content)=='') $('#ajax_newscontent').html('本条新闻内容为空...').show();
          				  $('#ajax_newscontent').html(obj.content).show();
           				  $('#ajax_newstime').html('发布时间：'+obj.time+'&nbsp;&nbsp;').show();
                   }
              });
            };
        };
      $(document).ready(function(){
        $('#ret_url').val("index.php");
        var originColor = '#E3E3E3';
        $('#newspad #title').click(function(){
            $('#newspad #mainarea').slideToggle();
            $('#title i').toggleClass('fa fa-fw fa-angle-double-down');
            $('#title i').toggleClass('fa fa-fw fa-angle-double-up');
            /* change color, unnecessary in this theme
            var tmp = $('#newspad').css('background-color');
            $('#newspad').css('background-color', originColor);
            originColor = tmp;
             */
        });
      });
</script>
</body>
</html>
