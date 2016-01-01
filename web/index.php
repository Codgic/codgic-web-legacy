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
$res=mysql_query("select content from news where news_id=0");
$index_text=($row=mysql_fetch_row($res)) ? $row[0] : '';
$inTitle='主页';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
<?php require('head.php');?>
  <body>
    <?php require('page_header.php');
    if($holiday=='christmas') echo '<audio autoplay="autoplay"><source src="/images/christmas_bgm.mp3"></audio>';?>  
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
      <div class="center">
        <h1>题目分类目录</h1><br />
		<p><font size=3 >按算法分类:&nbsp;
		<a href="search.php?q=%E5%9F%BA%E7%A1%80%E8%AF%AD%E6%B3%95">基础语法</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E5%AD%97%E7%AC%A6%E4%B8%B2">字符串</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E6%A8%A1%E6%8B%9F">模拟</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E5%8A%A8%E6%80%81%E8%A7%84%E5%88%92">动态规划</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E8%B4%AA%E5%BF%83">贪心</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E6%90%9C%E7%B4%A2">搜索</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E6%95%B0%E8%AE%BA">数论</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E4%BA%8C%E5%88%86%E6%9F%A5%E6%89%BE">二分查找</a></font><br /></p>
		
		<p><font size=3 ><a href="search.php?q=%E6%95%B0%E6%8D%AE%E7%BB%93%E6%9E%84">数据结构</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E6%A0%91%E7%BB%93%E6%9E%84">树结构</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E5%9B%BE%E7%BB%93%E6%9E%84">图结构</a></font></p>
        <p><font size=3 >按难度分类:&nbsp;
		<a href="level.php?level=1">普及</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=2">普及+</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=3">提高</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=4">提高+</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=5">省选-</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=6">省选</a>&nbsp;&nbsp;&nbsp;
		<a href="level.php?level=7">省选+</a></font></p>
        <p><font size=3 >按来源分类:&nbsp;
		<a href="search.php?q=%E6%99%AE%E5%8F%8A%E7%BB%84">NOIP普及组</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E6%8F%90%E9%AB%98%E7%BB%84">NOIP提高组</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E7%9C%81%E9%80%89">省选</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=NOI2">NOI</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=IOI">IOI</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=UESTC">UESTC</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=USACO">USACO</a>&nbsp;&nbsp;&nbsp;
		<a href="search.php?q=%E5%8E%9F%E5%88%9B">原创</a></font></p>
	  </div>
	  <div class="row-fluid">
        <p></p>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>
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
