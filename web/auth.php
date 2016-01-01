<?php
require('inc/database.php');
require('inc/ojsettings.php');
?>
<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1" /> 
<html>
  <?php 
  require('head.php');
  require('inc/database.php');
  if($_COOKIE["SID"]){
	  echo"<script language=\"javascript\">
      window.location= \"index.php\";
      </script>";};
  $Title='欢迎来到'.$oj_name;?>
  <body>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span center" style="padding:20px">
          <h1 id="top_title"></h1>
        </div>
      </div>
	</div>
      <div id="loginpage" class="row-fluid">
        <div style="width:560px;margin:0 auto;">
          <form class="form-horizontal well" id="form_login" action="login.php" method="post">
            <h1 class="center">首先，你需要登录</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <div id="uid_ctl" class="control-group">
              <label class="control-label" for="uid">用户名</label>
              <div class="controls">
                <input class="input-large" autofocus="autofocus" type="text" id="uid" name="uid" placeholder="请输入用户名...">
              </div>
            </div>
            <div id="pwd_ctl" class="control-group">
                <label class="control-label" for="pwd">密码</label>
                <div class="controls">
                  <input class="input-large" id="pwd" name="pwd" type="password" placeholder="请输入密码...">
                </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <label class="checkbox">
                  <p><input type="checkbox" name="remember">&nbsp;记住我英俊的面庞</p>
                </label>
                <input id="signin" type="submit" value="登录" class="btn btn-primary"><br />
                <a href="#" onclick="return switch_page();" style="line-height:40px">申请账号</a>&nbsp;&nbsp;&nbsp;
				<!--<a href="#" onclick="return switch_pwd();" style="line-height:40px">忘记密码</a>&nbsp;&nbsp;&nbsp;-->
				<a href="#" onclick="return switch_contact();" style="line-height:40px">联系管理员</a>
              </div>
            </div>
            <input id="ret_url" name="url" value="index.php" type="hidden">
          </form>
        </div>
      </div>

      <div id="regpage" class="hide row-fluid">
        <div style="width:560px;margin:0 auto;">
          <form class="form-horizontal well" id="form_profile" action="#" method="post">
		    <h1 class="center">申请账号</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <input type="hidden" value="reg" name="type">
            <fieldset>
              <div class="control-group" id="userid_ctl">
                <label class="control-label">用户名</label>
                <div class="controls">
                  <input class="input-large" type="text" name="userid" id="input_userid" placeholder="字母，数字或下划线,20位以内">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_nick">昵称</label>
                <div class="controls">
                  <input class="input-large" type="text" name="nick" id="input_nick" placeholder="任意字符,20位以内">
                </div>
              </div>
              <div class="control-group" id="newpwd_ctl">
                <label class="control-label" for="input_newpwd">密码</label>
                <div class="controls">
                  <input class="input-large" type="password" id="input_newpwd" name="newpwd" placeholder="字母，数字或下划线,至少6位">
                </div>
              </div>
              <div class="control-group" id="reppwd_ctl">
                <label class="control-label" for="input_reppwd">重复密码</label>
                <div class="controls">
                  <input class="input-large" type="password" id="input_reppwd" placeholder="请重复输入一遍密码">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_email">邮箱</label>
                <div class="controls">
                  <input class="input-large" type="text" name="email" id="input_email" placeholder="请输入最常用的邮箱">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_school">学校</label>
                <div class="controls">
                  <input class="input-large" type="text" name="school" id="input_school" placeholder="请输入真实信息">
                </div>
              </div>
              <div class="center">
                <span id="save_btn" class="btn btn-primary">提交</span>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return go_back();" style="line-height:40px">返回登录页</a>
              </div>
			  
              <div class="row-fluid">
                <p><span id="ajax_result" class="hide span6 offset3 alert alert-error center" style="margin-top:20px"></span></p>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
	  
	  <div id="pwdpage" class="hide row-fluid">
        <div style="width:560px;margin:0 auto;">
          <form class="form-horizontal well" id="form_pwd" action="#" method="post">
            <h1 class="center">忘记密码</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <div id="uid_ctl" class="control-group">
            <fieldset>
              <div class="control-group" id="fuserid_ctl">
                <label class="control-label">用户名</label>
                <div class="controls">
                  <input class="input-large" type="text" name="userid" id="finput_userid" placeholder="请输入用户名...">
                </div>
              </div>
			   <div class="control-group" id="femail_ctl">
                <label class="control-label">邮箱</label>
                <div class="controls">
                  <input class="input-large" type="text" name="email" id="finput_email" placeholder="请输入邮箱...">
                </div>
              </div>
              <div class="center">
                <span id="pwd_nxt" class="btn btn-primary">下一步</span>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return go_back();" style="line-height:40px">返回登录页</a>
              </div>
			  
              <div class="row-fluid">
                <span id="" class="hide span6 offset3 alert alert-error center" style="margin-top:20px"></span>
              </div>
            </fieldset>
			</div>
         </form> 
         </div>
      </div>
	  
	  
	  <div id="contactpage" class="hide row-fluid">
        <div style="width:560px;margin:0 auto;">
          <form class="form-horizontal well center" id="form_pwd" action="#" method="post">
		      <h1>联系管理员</h1>
              <hr style="border-bottom-color: #E5E5E5;">
			  <a href=mailto:cwojadmin@126.com>发送邮件</a><br /><br />
			  <a href="#" onclick="return go_back();" style="line-height:40px">返回登录页</a>
		   </form>
	     </div>
         <hr>
      </div>
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/common.js"></script>
    <script type="text/javascript">
	if(screen.width <= 640){
      var formWidth = document.body.clientWidth * .75;
      document.querySelector("#loginpage form").parentNode.style.margin = "0px";
      document.querySelector("#loginpage form").setAttribute("style","width:" + formWidth + "px!important;margin-right:" + (screen.width - formWidth)/4 + "px!important;margin-left:" + (screen.width - formWidth)/4 + "px!important;");
	  document.querySelector("#regpage form").parentNode.style.margin = "0px";
      document.querySelector("#regpage form").setAttribute("style","width:" + formWidth + "px!important;margin-right:" + (screen.width - formWidth)/4 + "px!important;margin-left:" + (screen.width - formWidth)/4 + "px!important;");
      document.querySelector("#contactpage form").parentNode.style.margin = "0px";
      document.querySelector("#contactpage form").setAttribute("style","width:" + formWidth + "px!important;margin-right:" + (screen.width - formWidth)/4 + "px!important;margin-left:" + (screen.width - formWidth)/4 + "px!important;");
	  document.querySelector("#pwdpage form").parentNode.style.margin = "0px";
      document.querySelector("#pwdpage form").setAttribute("style","width:" + formWidth + "px!important;margin-right:" + (screen.width - formWidth)/4 + "px!important;margin-left:" + (screen.width - formWidth)/4 + "px!important;");
	  } //Thanks to Nota!

      function switch_page() {
        $('#loginpage').hide();
        $('#regpage').show();
        return false;
      }
	  function switch_contact() {
        $('#loginpage').hide();
        $('#contactpage').show();
        return false;
      }
	  function switch_pwd() {
        $('#loginpage').hide();
        $('#pwdpage').show();
        return false;
      }
	  function go_back() {
		$('#contactpage').hide();
		$('#regpage').hide();
		$('#pwdpage').hide();
        $('#loginpage').show();
        return false;
      }
      $(document).ready(function() {
		$('#pwd_nxt').click(function(){
			var a=false;
			if(!$.trim($('#finput_userid').val())) {
            $('#fuserid_ctl').addClass('error');
            a=true;
            }else{
            $('#fuserid_ctl').removeClass('error');
            };
			if(!$.trim($('#finput_email').val())) {
            $('#femail_ctl').addClass('error');
            a=true;
            }else{
            $('#femail_ctl').removeClass('error');
            };
			if(!a){
				var user = $.trim($('#finput_userid').val());
				var email= $.trim($('#finput_email').val());
				window.location= "resetpwd.php?user="+user+"&&email="+email;
			};
		});
		
        $('#save_btn').click(function(){
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
                if(/created/.test(msg)){
	              $('#ajax_result').html(msg).show();
                  window.alert('你的注册申请已被发送给管理员\n请耐心等待审核通过～');
                  window.location="index.php";
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