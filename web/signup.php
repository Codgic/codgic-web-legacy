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
    header("Location: /index.php");
    exit();
}
$Title='注册'.$oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php';?>
    <body style="background-image: url(<?php echo $loginimg?>)">
	<div class="container">
      <div class="row collapse">
        <div class="panel panel-default panel-login" style="display:table;margin:auto">
		  <div class="panel-body">
            <form id="form_reg" method="post">
		      <h1 class="text-center">申请账号</h1>
              <hr style="border-bottom-color: #E5E5E5;">
              <input type="hidden" value="reg" name="type">
              <div class="form-group has-feedback" id="userid_ctl">
                <input class="form-control" type="text" autofocus="autofocus" name="userid" id="input_userid" placeholder="用户名">
                <span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
              </div>
              <div class="form-group has-feedback">
                <input class="form-control" type="text" autofocus="autofocus" name="nick" id="input_nick" placeholder="昵称">
                <span class="form-control-feedback"><i class="fa fa-fw fa-pencil"></i></span>
              </div>
              <div class="form-group has-feedback" id="newpwd_ctl">
                <input class="form-control" type="password" autofocus="autofocus" id="input_newpwd" name="newpwd" placeholder="密码">
                <span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
              </div>
              <div class="form-group has-feedback" id="reppwd_ctl">
                <input class="form-control" type="password" autofocus="autofocus" id="input_reppwd" placeholder="重复密码">
                <span class="form-control-feedback"><i class="fa fa-fw fa-refresh"></i></span>
              </div>
              <div class="form-group has-feedback">
                <input class="form-control" type="text" autofocus="autofocus" name="email" id="input_email" placeholder="邮箱">
                <span class="form-control-feedback"><i class="fa fa-fw fa-envelope"></i></span>
              </div>
              <div class="form-group has-feedback">
                <input class="form-control" type="text" autofocus="autofocus" name="school" id="input_school" placeholder="学校">
                <span class="form-control-feedback"><i class="fa fa-fw fa-home"></i></span>
              </div>
              <div class="checkbox">
                <label><input type="checkbox" name="lic" id="chk_lic">我已阅读并同意<a href="javascript:void(0)" onclick="return $('#EULAModal').modal('show');">许可协议</a></label>
              </div>
              <div id="reg_res" class="alert collapse"></div>
              <div class="form-group center">
                <button type="submit" class="btn btn-primary">提交</button>
                <a href="login.php" style="margin-left:8px">返回登录页</a>
              </div>
          </form>
		</div>
	  </div>
	</div>
	</div>
    <div class="modal fade" id="EULAModal">
	  <div class="modal-dialog">
		<div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">终端用户许可协议</h4>
         </div>
         <div class="modal-body">
            <?php include 'inc/eula.php'?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
         </div>
		</div>
	  </div>
	</div>
    <script type="text/javascript">
      $(document).ready(function(){
        $('body .row').fadeIn();
        $('#form_reg').submit(function(){
          $('#reg_res').hide();
          var b=false,pwd;
          if(!$.trim($('#input_userid').val())) {
            $('#input_userid').addClass('error');
            b=true;
          }else{
            $('#input_userid').removeClass('error');
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
              data:$('#form_reg').serialize(),
              success:function(msg){
                if(/success/.test(msg)){
		  		  	if(<?php echo $require_confirm?> == 1) {
                        $('#reg_res').removeClass("alert-danger alert-success").addClass("alert-info");
                        $('#reg_res').html('<i class="fa fa-fw fa-info"></i> 你的注册申请将被审核...').slideDown();
                    }else{
                        $('#reg_res').removeClass("alert-danger alert-info").addClass("alert-success");
                        $('#reg_res').html('<i class="fa fa-fw fa-check"></i> 你的账户已经成功注册...').slideDown();
                    }
		  		  	window.setTimeout("window.location.href='login.php'",2000); 
                }else{
                  $('#reg_res').removeClass("alert-danger alert-info").addClass("alert-danger");
                  $('#reg_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
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
