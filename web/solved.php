<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/database.php';

$cond='';
if(isset($_GET['q']) && strlen($search=trim($_GET['q'])))
  $cond='and user_id=\''.mysqli_real_escape_string($con,$search).'\'';

$result=mysqli_query($con,"select solution_id,user_id,solution.problem_id,score,solution.in_date,title from solution LEFT JOIN problem USING(problem_id) where valid=1 $cond order by solution_id desc limit 100");
$inTitle='AC记录';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>

  <body>
    <?php require 'page_header.php'; ?>       
    <div class="container">
      <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-hover table-bordered" style="margin-bottom:0">
              <thead><tr>
                <th>ID</th>
                <th>用户</th>
                <th>题目</th>
                <th>分数</th>
                <th>日期</th>
              </tr></thead>
              <tbody id="userlist">
                <?php 
                  while($row=mysqli_fetch_row($result)){
                    echo '<tr><td><a href="record.php?solution_id=',$row[0],'">',$row[0],'</a></td>';
                    echo '<td><a href="#linkU">',$row[1],'</a></td>';
                    echo '<td style="text-align:left"><a href="problempage.php?problem_id=',$row[2],'">',$row[2],' -- ',$row[5],'</a></td>';
                    echo '<td>',$row[3],'</td>';
                    echo '<td>',$row[4],'</td>';
                    echo "</tr>\n";
                  }
                ?>
              </tbody>
            </table>
        </div>  
      </div>
      <div class="modal fade" id="UserModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">用户信息</h4>
            </div>
            <div class="modal-body" id="user_status">
              <p>信息不可用……</p>
            </div>
            <div class="modal-footer">
              <form action="mail.php" method="post">
                <input type="hidden" name="touser" id="um_touser">
                <?php if(isset($_SESSION['user'])){?>
                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> 发私信</button>
                <?php }?>
              </form>
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        change_type(3);
        $('#userlist').click(function(Event){
          var $target=$(Event.target);
          if($target.is('a') && $target.attr('href')=='#linkU'){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
            $('#um_touser').val(Event.target.innerHTML);
            $('#UserModal').children('.modal-header').children('h4').html('用户信息');
            $('#UserModal').modal('show');
            return false;
          }
        });
      }); 
    </script>
  </body>
</html>
