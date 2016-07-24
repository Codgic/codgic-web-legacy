<?php
require 'inc/database.php';
require 'inc/ojsettings.php';
if(!isset($_SESSION)) session_start();
if(isset($_SESSION['user'])){
    header("Location: /");
    exit();
}
require 'inc/database.php';
require 'inc/cookie.php';
if(check_cookie()){
    header("Location: /");
    exit();
};
$Title='欢迎来到'.$oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php';?>
    <body style="background-image: url(<?php echo $loginimg?>)">
      <div class="row collapse" style="margin-top:50px">
        <div class="panel panel-default panel-login" style="display:table;margin:auto">
		  <div class="panel-body">
            <form id="form_login" method="post">
            <h1 class="text-center">欢迎回来</h1>
            <hr>
              <div class="form-group has-feedback" id="uid_ctl">
                <input class="form-control" autofocus="autofocus" type="text" id="input_uid" name="uid" placeholder="用户名">
                <span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
              </div>

              <div class="form-group has-feedback" id="pwd_ctl">
                <input class="form-control" id="input_pwd" name="pwd" type="password" placeholder="密码">
                <span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
              </div>
              <div class="form-group">
                <div class="checkbox">
                  <label><input type="checkbox" name="remember" id="chk_remember">记住英俊潇洒的我...</label>
                </div>
              </div>
              <div id="login_res" class="alert alert-danger collapse"></div>
              <div class="form-group center">
                <button type="submit" class="btn btn-primary">登录</button>
                <a href="signup.php" style="margin-left:8px">申请账号</a>
                <a href="resetpwd.php" style="margin-left:8px">忘记密码</a>
                <a href="javascript:void(0)" onclick="return $('#contact').slideToggle();" style="margin-left:8px">联系管理员</a>
              </div>
            </div>
		  	<div class="collapse" id="contact">
		  	    <p class="text-center"><b>联系管理员: <a href="mailto:<?php echo $contact_email?>"><?php echo $contact_email?></a></b></p>
		      </div>
		  	<br>
            </form>
          </div>
		</div>
      </div>

    <script type="text/javascript">
      $(document).ready(function(){
        var r="<?php if(isset($_SESSION['login_redirect'])) echo $_SESSION['login_redirect'];
        else echo 'index.php';?>";
        $('body .row').fadeIn();
        $('#form_login').submit(function(){
          $('#login_res').hide();
          var b=false,pwd;
          if(!$.trim($('#input_uid').val())){
            $('#input_uid').addClass('error');
            b=true;
          }else{
            $('#input_uid').removeClass('error');
          }
          if(!$.trim($('#input_pwd').val())){
            $('#input_pwd').addClass('error');
            b=true;
          }else{
            $('#input_pwd').removeClass('error');
          }
          if(!b){
            $.ajax({
              type:"POST",
              url:"ajax_login.php",
              data:$('#form_login').serialize(),
              success:function(msg){
                if(/success/.test(msg)){
                  window.location.href=r;
                }else{
                  $('#login_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                }
              }
            });
          }
          return false;
        });
      });
    </script>
  </body>
</html>
