<?php 
require 'inc/ojsettings.php';
function check_permission($prob_id,$opened,$user)
{
  if(!isset($_SESSION['user']))
    return "然而你并没有登录";
  if(strcmp($user,$_SESSION['user'])==0 || isset($_SESSION['source_browser']))
    return TRUE;
  require 'inc/database.php';
  require 'inc/problem_flags.php';
  if($opened){
    $row = mysqli_fetch_row(mysqli_query($con,"select has_tex from problem where problem_id=$prob_id"));
    if(!$row)
      return "不具备该问题";
    $prob_flag = $row[0];
    if(($prob_flag & PROB_IS_HIDE) && !isset($_SESSION['insider']))
      return '你没有权限访问该题目';
    if($prob_flag & PROB_DISABLE_OPENSOURCE)
      return "访问拒绝";
    else if($prob_flag & PROB_SOLVED_OPENSOURCE){
      $query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$prob_id group by problem_id";
      $user_status=mysqli_query($con,$query);
      $row=mysqli_fetch_row($user_status);
      if($row && $row[0]==0)
        return TRUE;
      return "快滚回去等你自己写出来了再给你看！";
    }
    return TRUE;
  }
  return '你不被允许看这份代码。';
}
require('inc/result_type.php');
require('inc/lang_conf.php');
if(!isset($_GET['solution_id']))
    die('Wrong argument.');
$sol_id=intval($_GET['solution_id']);

require ('inc/checklogin.php');
require('inc/database.php');
$result=mysqli_query($con,"select user_id,time,memory,result,language,code_length,problem_id,public_code from solution where solution_id=$sol_id");
$row=mysqli_fetch_row($result);
if(!$row)
  die('No such solution.');

$ret = check_permission($row[6], $row[7], $row[0]);
if($ret === TRUE)
  $allowed = TRUE;
else{
  $allowed = FALSE;
  $info=$ret;
}


if($allowed){
  $result=mysqli_query($con,"select source from source_code where solution_id=$sol_id");
  if($tmp=mysqli_fetch_row($result))
    $source=$tmp[0];
  else
    $info = '源代码不可用';
}
if(isset($_GET['raw'])){
  if(isset($info)){
    echo $info;
  }else{
    header("Content-Type: text/html; charset=utf-8");
    echo "<plaintext>",$source;
  }
  exit(0);
}

$inTitle="源代码#$sol_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body onload="prettyPrint()">
    <?php require('page_header.php'); ?>  
          
    <div class="container-fluid" style="font-size:13px">
      <?php
      if(isset($info))
        echo '<p><div class="row-fluid center">',$info,'</p>','<p><a href="javascript:history.back(-1);">返回上一页...</a></p><div>';
      else{
      ?>
        <div class="row-fluid center">
            用户:<?php echo $row[0];?>
        </div>
        <div class="row-fluid center">
            题目:<?php echo $row[6];?>&nbsp;&nbsp;
            结果:<?php echo $RESULT_TYPE[$row[3]];?>
        </div>
        <div class="row-fluid center">
            大小:<?php echo $row[5];?>&nbsp;&nbsp;
            语言:<?php echo $LANG_NAME[$row[4]];?>
        </div>
        <div class="row-fluid center">
            运行时间:<?php echo $row[1];?>&nbsp;ms&nbsp;
            运行内存:<?php echo $row[2];?>&nbsp;KB
        </div>
        <div class="row-fluid">
          <div class="span10 offset1">
			<p><a href="javascript:history.back(-1);" class="btn btn"><< 返回上一页</a>
            <a class="btn btn" target="_blank" href="sourcecode.php?raw=1&amp;solution_id=<?php echo $sol_id?>" onclick="return show_raw();">RAW</a></p>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span10 offset1" id="div_code">
              <pre class="prettyprint linenums"><?php echo htmlspecialchars($source);?></pre>
          </div>
        </div>
      <?php } ?>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>

    </div>

    <script src="/assets/js/google-code-prettify/prettify.js"></script>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
	<script src="/assets/js/jquery.zclip.js"></script>
    <script src="/assets/js/common.js"></script>
    <script type="text/javascript"> 
      var solution_id=<?php echo $sol_id?>;
      $(document).ready(function(){
        $('#ret_url').val("sourcecode.php?solution_id="+solution_id);
      });
      function doajax(fun){
        $.ajax({type:"GET",url:("sourcecode.php?raw=1&solution_id="+solution_id),success:fun});
      }
      function show_raw(){
        return true; /*****************************/
        doajax(function(msg){
          $('#div_code').html('<pre>'+htmlEncode(msg)+'</pre>');
        });
        return false;
      }
    </script>
  </body>
</html>
