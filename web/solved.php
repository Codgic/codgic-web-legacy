<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
header('Content-Type: text/html; charset=utf-8');

require('inc/database.php');
$cond='';
if(isset($_GET['q']) && strlen($search=trim($_GET['q'])))
  $cond='and user_id=\''.mysqli_real_escape_string($con,$search).'\'';

$result=mysqli_query($con,"select solution_id,user_id,solution.problem_id,score,solution.in_date,title from solution LEFT JOIN problem USING(problem_id) where valid=1 $cond order by solution_id desc limit 100");
$inTitle='AC记录';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body>
    <?php require('page_header.php'); ?>       
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span10 offset1">
          <div style="width:250px;margin:0 auto">
            <form id="searchuser_form" action="solved.php" method="get" class="form-inline" style="margin-bottom:10px;">
              <label for="searchuser_input" class="label-select">用户: </label>
              <div class="input-append"><input autofocus="autofocus" value="<?php if(isset($search))echo htmlspecialchars($search);?>" id="searchuser_input" type="text" name="q" class="input-medium" placeholder="搜索用户..."><span id="search_addon" class="add-on"><i class="icon-search"></i></span></div>
            </form>
          </div>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span10 offset1">
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
      <div class="modal fade hide" id="UserModal">
        <div class="modal-header">
          <a class="close" data-dismiss="modal">×</a>
          <h4>用户信息</h4>
        </div>
        <div class="modal-body" id="user_status" style="max-height:350px">
          <p>信息不可用</p>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </div>
      
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>

    </div>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>

    <script type="text/javascript">
      $(document).ready(function(){
        $('#userlist').click(function(Event){
          var $target=$(Event.target);
          if($target.is('a') && $target.attr('href')=='#linkU'){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
            var win=$('#UserModal');
            win.children('.modal-header').children('h4').html('用户信息');
            win.modal('show');
            return false;
          }
        });
        $('#search_addon').click(function(){$('#searchuser_form').submit();});

        $('#ret_url').val("solved.php");
      }); 
    </script>
  </body>
</html>
