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
require('inc/checklogin.php');
require('inc/database.php');
$res=mysqli_query($con,"select content from news where news_id=0");
$index_text=($row=mysqli_fetch_row($res)) ? $row[0] : '';
$res=mysqli_query($con,"select news_id,title from news where news_id>0 order by news_id desc");
$hasnews=0;
$newsrow=mysqli_fetch_row(mysqli_query($con,"select max(news_id) from news"));
if($newsrow[0]>0) $hasnews=1;
$categoryrow=mysqli_fetch_row(mysqli_query($con,"select content from user_notes where id=0"));
$category=$categoryrow[0];
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
            <div id="mainarea" style="display:">
                <?php echo $index_text?>
            </div>
			<div id="title" class="center" style="text-align:center; font-size:24px;cursor:pointer;">
				<h2><b><i class="icon-double-angle-up"></i></b></h2>
            </div>
          </div>
        </div>
      </div>
	  <div class="row-fluid">
		<div class="span5 offset1">
	    <h1>新闻<?php if($hasnews==1){?><a href="news.php" class="pull-right"><font size=2>更多历史新闻...</font></a></h1><br>
		  <ul class="nav">
                <?php 
                while($row=mysqli_fetch_row($res)){
					$num++;
					echo '<li><p><font size=3><a href="javascript:void(0);" onclick="click_news(',$row[0],')">',htmlspecialchars($row[1]),'</a></font></p></li>';
					if($num==$news_num) break;
                }
                ?>
		  </ul><?php }else{?></h1>
		  <br><br><p><font color='grey' size=10>:( 暂时没有发布过新闻~</font></p>
		  <?php }?>
	  </div>
      <div class="span6">
        <h1>分类</h1><br>
		<?php if($category!='') echo "$category";
		      else echo'<br><br><p><font color=\'grey\' size=10>:( 暂时没有发布题目分类目录~</font></p>';?>
	  </div>
	  </div>
	  <div class="row-fluid">
        <p></p>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>
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
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </form>
    </div>
	<?php if($pref->night=='on') echo "<canvas id=\"canvas\" style=\"position:fixed;top:0;z-index:-999\"></canvas>";?>
    <script type="text/javascript">
      <?php
        echo 'var ws_url="ws://',$_SERVER["SERVER_ADDR"],':6844/";';
        if(isset($_SESSION['user']))
          echo 'var userid="',encode_user_id('id-'.$_SESSION['user']),'";';
      ?>
    </script>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>
    <script src="../assets/js/chat.js"></script>
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
				  $('#ajax_newstime').html('发布时间：'+time).show();
                  $('#NewsModal').modal('show');
                }
              });
            };
        };
      $(document).ready(function(){
        $('#ret_url').val("index.php");
        var originColor = '#E3E3E3';
        $('#newspad #title').click(function(){
            $('#newspad #mainarea').slideToggle();
            $('#title i').toggleClass('icon-double-angle-down');
            $('#title i').toggleClass('icon-double-angle-up');
            /* change color, unnecessary in this theme
            var tmp = $('#newspad').css('background-color');
            $('#newspad').css('background-color', originColor);
            originColor = tmp;
             */
        });
      });
	  
	(function() {
    var requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.msRequestAnimationFrame ||
    function(callback) {
        window.setTimeout(callback, 1000 / 60);
    };
    window.requestAnimationFrame = requestAnimationFrame;
	})();
	
	var flakes = [],
    canvas = document.getElementById("canvas"),
    ctx = canvas.getContext("2d"),
    flakeCount = 100,
    mX = -100,
    mY = -100
    
	if(screen.width > screen.height){
		canvas.width = screen.width;
		canvas.height = screen.width;
	}
	else{
		canvas.width = screen.height;
	    canvas.height = screen.height;
	};
	function snow() {
		ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (var i = 0; i < flakeCount; i++) {
        var flake = flakes[i],
            x = mX,
            y = mY,
            minDist = 150,
            x2 = flake.x,
            y2 = flake.y;

        var dist = Math.sqrt((x2 - x) * (x2 - x) + (y2 - y) * (y2 - y)),
            dx = x2 - x,
            dy = y2 - y;

        if (dist < minDist) {
            var force = minDist / (dist * dist),
                xcomp = (x - x2) / dist,
                ycomp = (y - y2) / dist,
                deltaV = force / 2;

            flake.velX -= deltaV * xcomp;
            flake.velY -= deltaV * ycomp;

        } else {
            flake.velX *= .98;
            if (flake.velY <= flake.speed) {
                flake.velY = flake.speed
            }
            flake.velX += Math.cos(flake.step += .05) * flake.stepSize;
        }

        ctx.fillStyle = "rgba(255,255,255," + flake.opacity + ")";
        flake.y += flake.velY;
        flake.x += flake.velX;
            
        if (flake.y >= canvas.height || flake.y <= 0) {
            reset(flake);
        }


        if (flake.x >= canvas.width || flake.x <= 0) {
            reset(flake);
        }

        ctx.beginPath();
        ctx.arc(flake.x, flake.y, flake.size, 0, Math.PI * 2);
        ctx.fill();
    }
    requestAnimationFrame(snow);
	};
  function reset(flake) {
    flake.x = Math.floor(Math.random() * canvas.width);
    flake.y = 0;
    flake.size = (Math.random() * 3) + 2;
    flake.speed = (Math.random() * 1) + 0.5;
    flake.velY = flake.speed;
    flake.velX = 0;
    flake.opacity = (Math.random() * 0.5) + 0.3;
	}
  function init() {
    for (var i = 0; i < flakeCount; i++) {
        var x = Math.floor(Math.random() * canvas.width),
            y = Math.floor(Math.random() * canvas.height),
            size = (Math.random() * 3) + 2,
            speed = (Math.random() * 1) + 0.5,
            opacity = (Math.random() * 0.5) + 0.3;

        flakes.push({
            speed: speed,
            velY: speed,
            velX: 0,
            x: x,
            y: y,
            size: size,
            stepSize: (Math.random()) / 30,
            step: 0,
            angle: 180,
            opacity: opacity
        });
    }

    snow();
};

//canvas.addEventListener("mousemove", function(e) {
//    mX = e.clientX,
//    mY = e.clientY
//});

init();
</script>
</body>
</html>