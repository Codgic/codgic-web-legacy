<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
 
if(!isset($_SESSION['user'])){
  $info='你还没有登录';
}else{
  if(!isset($con)) require 'inc/database.php';
  $user_id=$_SESSION['user'];
  $result=mysqli_query($con,'select email,nick,school,motto,user_id from users where user_id=\''.$user_id."'");
  $row=mysqli_fetch_row($result);
}
$inTitle='档案';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body>
    <?php require 'page_header.php'; ?>  
      
    <div class="container">
      <div class="row">
      <?php if(isset($info)){?>
      <div class="text-center none-text none-center">
        <p><i class="fa fa-meh-o fa-4x"></i></p>
        <p><b>Whoops</b><br>
        <?php echo $info?></p>
      </div>
      <?php }else{?>
      <div class="media col-xs-12">
        <a class="pull-left">
          <img src="<?php echo get_gravatar($_SESSION['email'],100)?>" class="media-object img-circle" width="100" height="100">
        </a>
        <div class="media-body">
          <h1 class="media-heading">
            <?php echo $row[4]?>
          </h1>
          <text class="motto-text" id="user_motto"><?php echo $row[3]?></text>
          <br>
          <text class="help-block">Avatar powered by Gravatar.</text>
          </div>
      </div>
      </div>
      <hr>
      <div class="row">
        <form id="form_profile" action="#" method="post">
          <input type="hidden" value="profile" name="type">
          <div class="form-group col-xs-12 col-sm-6" id="oldpwd_ctl">
            <label>旧密码(*)</label>
            <input class="form-control" id="input_oldpwd" name="oldpwd" type="password">
          </div>
          <div class="form-group col-xs-12 col-sm-6">
            <label>签名</label>
            <input class="form-control" id="input_motto" name="motto" type="text" value="<?php echo htmlspecialchars($row[3])?>">
          </div>
          <div class="form-group col-xs-12 col-sm-6">
            <label>昵称</label>
            <input class="form-control" type="text" name="nick" id="input_nick" value="<?php echo htmlspecialchars($row[1])?>">
          </div>
          <div class="form-group col-xs-12 col-sm-6" id="newpwd_ctl">
            <label>新密码</label>
            <input class="form-control" type="password" id="input_newpwd" name="newpwd">
            <span class="help-block">若你不打算更改密码无需填写此栏。</span>
          </div>
          <div class="form-group col-xs-12 col-sm-6" id="reppwd_ctl">
            <label>重复密码</label>
            <input class="form-control" type="password" id="input_reppwd">
          </div>
          <div class="form-group col-xs-12 col-sm-6">
            <label>邮箱</label>
            <input class="form-control" type="text" name="email" id="input_email" value="<?php echo htmlspecialchars($row[0])?>">
          </div>
          <div class="form-group col-xs-12 col-sm-6">
            <label>学校</label>
            <input class="form-control" type="text" name="school" id="input_school" value="<?php echo htmlspecialchars($row[2])?>">
          </div>
          <div class="col-xs-12">
			<div id="ajax_result" class="alert col-xs-12 collapse"></div>
			<button id="save_btn" class="btn btn-primary" type="submit">保存更改</button>
		  </div>
          </form>
      <?php } ?>
      </div>
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#form_profile').submit(function(){
          var b=false,pwd;
          if($('#input_oldpwd').val()==''){
            $('#input_oldpwd').addClass('error');
            b=true;
          }else{
          $('#input_oldpwd').removeClass('error');
        }
        pwd=$('#input_newpwd').val();
        if(pwd!='' && $('#input_reppwd').val()!=pwd){
          b=true;
          $('#input_newpwd').addClass('error');
          $('#input_reppwd').addClass('error');
        }else{
          $('#input_newpwd').removeClass('error');
          $('#input_reppwd').removeClass('error');
        }
        if(!b){
          $.ajax({
            type:"POST",
            url:"ajax_profile.php",
            data:$('#form_profile').serialize(),
            success:function(msg){
                if(msg=='success'){
                  $('#ajax_result').html('<i class="fa fa-fw fa-check"></i> 用户信息更新成功!').removeClass('alert-danger').addClass('alert-success').slideDown();
                  $('#user_motto').text($('#input_motto').val());
                }
                else $('#ajax_result').html('<i class="fa fa-fw fa-remove"></i> '+msg).removeClass('alert-success').addClass('alert-danger').slideDown();
            }
          });
        }
        return false;
      });
    });
    </script>
  </body>
</html>
