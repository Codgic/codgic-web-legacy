<?php
require 'inc/ojsettings.php';
require ('inc/checklogin.php');
 
if(!isset($_SESSION['user'])){
  $info='<div style="text-align: center">然而你并没有登录。</div>';
}else{
  require_once 'inc/database.php';
  $user_id=$_SESSION['user'];
  $result=mysqli_query($con,'select email,nick,school from users where user_id=\''.$user_id."'");
  $row=mysqli_fetch_row($result);
}
$inTitle='资料';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body>
    <?php require('page_header.php'); ?>  
          
    <div class="container-fluid">
      <div class="row-fluid">
      <?php
      if(isset($info)){
        echo $info;
      }else{
      ?>
        <div class="span8 offset3">
          <form class="form-horizontal" id="form_profile" action="#" method="post">
            <input type="hidden" value="profile" name="type">
            <fieldset>
              <div class="control-group">
                <label class="control-label">用户名</label>
                <div class="controls">
                  <span class="input-xlarge uneditable-input"><?php echo $user_id?></span>
                </div>
              </div>
              <div class="control-group" id="oldpwd_ctl">
                <label class="control-label" for="input_oldpwd">旧密码(*)</label>
                <div class="controls">
                  <input class="input-xlarge" id="input_oldpwd" name="oldpwd" type="password">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_nick">昵称</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="nick" id="input_nick" value="<?php echo htmlspecialchars($row[1])?>">
                </div>
              </div>
              <div class="control-group" id="newpwd_ctl">
                <label class="control-label" for="input_newpwd">新密码</label>
                <div class="controls">
                  <input class="input-xlarge" type="password" id="input_newpwd" name="newpwd">
                  <br/><span>若你不打算更改密码无需填写此栏。</span>
                </div>
              </div>
              <div class="control-group" id="reppwd_ctl">
                <label class="control-label" for="input_reppwd">重复密码</label>
                <div class="controls">
                  <input class="input-xlarge" type="password" id="input_reppwd">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_email">邮箱</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="email" id="input_email" value="<?php echo htmlspecialchars($row[0])?>">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_school">学校</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="school" id="input_school" value="<?php echo htmlspecialchars($row[2])?>">
                </div>
              </div>
              <div class="row-fluid">
                <div class="span9 center">
                  <span id="ajax_result" class="alert hide"></span>
                </div>
              </div>
              <div class="span3 offset3">
                <span id="save_btn" class="btn btn-primary">保存更改</span>
              </div>
            </fieldset>
          </form>
        </div>
      <?php } ?>
      </div>
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/common.js"></script>

    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#save_btn').click(function(){
          var b=false,pwd;
          if($('#input_oldpwd').val()==''){
            $('#oldpwd_ctl').addClass('error');
            b=true;
          }else{
            $('#oldpwd_ctl').removeClass('error');
          }
          pwd=$('#input_newpwd').val();
          if(pwd!='' && $('#input_reppwd').val()!=pwd){
            b=true;
            $('#newpwd_ctl').addClass('error');
            $('#reppwd_ctl').addClass('error');
          }else{
            $('#newpwd_ctl').removeClass('error');
            $('#reppwd_ctl').removeClass('error');
          }
          if(!b){
            $.ajax({
              type:"POST",
              url:"ajax_profile.php",
              data:$('#form_profile').serialize(),
              success:function(msg){
                $('#ajax_result').html(msg).removeClass('alert-success').show();
                if(/success/.test(msg)){
                  $('#ajax_result').addClass('alert-success');
                  setTimeout(function(){location.href='ranklist.php';},500);
                }
              }
            });
          }
        });
        $('#ret_url').val("profile.php");
      });
    </script>
  </body>
</html>
