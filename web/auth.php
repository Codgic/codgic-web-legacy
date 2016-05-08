<?php
require('inc/database.php');
require('inc/ojsettings.php');
?>
<!DOCTYPE html>
<html>
  <?php 
  $Title='欢迎来到'.$oj_name;
  require('head.php');
  require('inc/database.php');
  require ('inc/cookie.php');
  if(check_cookie()){
    header("Location: index.php");
    exit();
	};
  ?>
    <body style="background-image: url(<?php echo $loginimg?>)">
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span center" style="padding:20px">
          <h1 id="top_title"></h1>
        </div>
      </div>
	</div>
      <div id="loginpage" class="hide row-fluid">
        <div style="display:table;margin:auto;">
          <form class="form-vertical well" id="form_login" action="login.php" method="post">
            <h1 class="center">欢迎回家</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <div id="uid_ctl" class="control-group">
              <div class="controls" style="white-space:nowrap">
                <input class="input-xlarge" autofocus="autofocus" type="text" id="uid" name="uid" placeholder="用户名">
				<span class="icon-user" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
              </div>
            </div>
            <div id="pwd_ctl" class="control-group" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" id="pwd" name="pwd" type="password" placeholder="密码">
				  <span class="icon-key" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <label class="checkbox">
                  <p><input type="checkbox" name="remember">&nbsp;记住我英俊的面庞</p>
                </label>
                <input id="signin" type="submit" value="登录" class="btn btn-primary">&nbsp;&nbsp;&nbsp;
                <a href="#" onclick="return switch_page();" style="line-height:40px">申请账号</a>&nbsp;&nbsp;&nbsp;
				<a href="resetpwd.php" style="line-height:40px">忘记密码</a>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return switch_contact();" style="line-height:40px">联系管理员</a>
              </div>
            </div>
            <input id="ret_url" name="url" value="index.php" type="hidden">
			<div class="hide" id="contact" style="text-align:left">
				<br>
			    <p><b>联系管理员: <a href="mailto:<?php echo $contact_email?>"><?php echo $contact_email?></a></b></p><br>
		    </div>
			<br>
          </form>
        </div>
      </div>

      <div id="regpage" class="hide row-fluid">
        <div style="display:table;margin:auto;">
          <form class="form-vertical well" id="form_profile" action="#" method="post">
		    <h1 class="center">申请账号</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <input type="hidden" value="reg" name="type">
            <fieldset>
              <div class="control-group" id="userid_ctl" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="text" autofocus="autofocus" name="userid" id="input_userid" placeholder="用户名">
				  <span class="icon-user" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="control-group" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="text" autofocus="autofocus" name="nick" id="input_nick" placeholder="昵称">
				  <span class="icon-pencil" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="control-group" id="newpwd_ctl" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="password" autofocus="autofocus" id="input_newpwd" name="newpwd" placeholder="密码">
				  <span class="icon-key" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="control-group" id="reppwd_ctl" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="password" autofocus="autofocus" id="input_reppwd" placeholder="重复密码">
				  <span class="icon-refresh" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="control-group" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="text" autofocus="autofocus" name="email" id="input_email" placeholder="邮箱">
				  <span class="icon-envelope" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="control-group" style="white-space:nowrap">
                <div class="controls">
                  <input class="input-xlarge" type="text" autofocus="autofocus" name="school" id="input_school" placeholder="学校">
				  <span class="icon-home" style="margin-left:-20px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="center" style="white-space:normal">
                <span id="save_btn" class="btn btn-primary" style="margin-left:60px">提交</span>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return go_back();" style="line-height:40px;margin-right:60px">返回登录页</a>
              </div>
              <div class="center" style="margin-top:20px" style="white-space:normal">
                <p><span id="ajax_result" class="hide alert alert-error center"></span></p>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/common.js"></script>
    <script type="text/javascript">
      function switch_page() {
        $('#loginpage').hide();
        $('#regpage').fadeIn();
        return false;
      }
	  function go_back() {
		$('#regpage').hide();
        $('#loginpage').fadeIn();
        return false;
      }
	  function switch_contact() {
		$('#contact').slideToggle();
        return false;
      }
      $(document).ready(function() {
		$('#loginpage').fadeIn();
        $('#save_btn').click(function(){
		  $('#ajax_result').hide();
          var b=false,pwd;
          if(!$.trim($('#input_userid').val())) {
            $('#userid_ctl').addClass('error');
            b=true;
          }else{
            $('#userid_ctl').removeClass('error');
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
                if(msg=='success'){
					var c = <?php echo $require_confirm?>;
					if(c == 1) $('#ajax_result').html('你的注册申请将被审核...').show();
					else $('#ajax_result').html('你的账户已经成功注册...').show();
					window.setTimeout("window.location='index.php'",2000); 
                }else{
                  $('#ajax_result').html(msg).show();
                }
              }
            });
          }
        });
      });
    </script>
  </body>
</html>
