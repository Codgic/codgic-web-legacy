<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';

$type='problem';
if(!isset($_SESSION['user']))
  $info = '你还没有登录';
else{
  if(isset($_GET['type']))
    if($_GET['type']=='contest'||$_GET['type']=='problem')
      $type=$_GET['type'];
    else{
      header("Location: marked.php");
      exit();
  }   
  require 'inc/database.php';
  $user_id=$_SESSION['user'];
  if($type=='problem'){
    $result=mysqli_query($con,"SELECT saved_problem.problem_id,title,savetime,problem_flag_to_level(has_tex) from saved_problem inner join problem using (problem_id) where user_id='$user_id' order by savetime desc");
    if(mysqli_num_rows($result)==0) $info='你还没收藏过题目哦';
  }else{
    $result=mysqli_query($con,"SELECT saved_contest.contest_id,title,savetime,problem_flag_to_level(has_tex) from saved_contest inner join contest using (contest_id) where user_id='$user_id' order by savetime desc");
    if(mysqli_num_rows($result)==0) $info='你还没收藏过比赛哦';
  }
}
$inTitle='收藏';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>  
	<body>
      <?php require 'page_header.php'; ?>
      <div class="container">
        <div class="row">
          <div class="col-xs-12">
            <ul class="nav nav-pills">
              <li <?php if($type=='problem') echo 'class="active"'?>><a href="marked.php"><i class="fa fa-fw fa-th-list"></i> 题目</a></li>
			  <li <?php if($type=='contest') echo 'class="active"'?>><a href="marked.php?type=contest"><i class="fa fa-fw fa-compass"></i> 比赛</a></li>
            </ul>
			<?php if(isset($info)){?>
              <div class="text-center none-text none-center">
                <p><i class="fa fa-meh-o fa-4x"></i></p>
                <p><b>Whoops</b><br>
                <?php echo $info?></p>
              </div>
			<?php }else{?>
              <br>
              <table class="table table-responsive table-hover table-bordered">
                <thead>
                  <tr>
                    <th style="width:6%">No.</th>
                    <th>题目</th>
                    <th style="width:8%">等级</th>
                    <th style="width:25%">收藏时间</th>
                    <th style="width:10%">删除</th>
                  </tr>
                </thead>
                <tbody id="marked_list">
                  <?php while($row=mysqli_fetch_row($result)){?>
                  <tr>
                    <td><?php echo $row[0] ?></td>
                    <td style="text-align:left"><a href="<?php echo $type?>page.php?problem_id=<?php echo $row[0]?>" ><?php echo $row[1] ?></a></td>
                    <td><?php echo $row[3] ?></td>
                    <td><?php echo $row[2] ?></td>
                    <td><i data-pid="<?php echo $row[0] ?>" style="cursor:pointer;" class="text-error fa fa-remove"></i></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>  
          <?php } ?>
          <hr>
          <footer>
            <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
          </footer>
		</div>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript"> 
        var del=0;
        $(document).ready(function(){
          $('#marked_list').click(function(E){
            var $target = $(E.target);
              if($target.is('i')){
                var pid = $target.attr('data-pid');
                  $.get('ajax_mark.php?prob='+pid+'&op=rm_saved&type='+'<?php echo $type?>',function(result){
                    if(/success/.test(result)){
                      $target.parents('tr').remove();
                      del++;
                      if(del==<?php echo mysqli_num_rows($result)?>) location.reload();
                    }
                  });
                }
              });
			}); 
        </script>
    </body>
</html>
