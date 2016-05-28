<?php 
require ('inc/ojsettings.php');
session_start(); 
$_SESSION['resetpwd_flag']=0;
$_SESSION['resetpwd_wrongnum']=0;
$_SESSION['resetpwd_code']=rand(10000000,99999999);
?>
<!DOCTYPE html>
<html>
<?php 
$inTitle='忘记密码';
$Title=$inTitle .' - '. $oj_name;
require ('head.php');
echo "<body style=\"background-image: url({$loginimg})\">";
?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span center" style="padding:20px">
          <h1 id="top_title"></h1>
        </div>
      </div>
	</div>
    <div id="emailpage" class="hide row-fluid">
      <div style="display:table;margin:auto;white-space:nowrap;">
		<form class="form-vertical well" id="form_email" action="#" method="post">
          <h1 class="center"> 重置密码</h1>
          <hr style="border-bottom-color: #E5E5E5;">
            <div id="email_ctl" class="control-group">
            <fieldset>
              <div class="control-group" id="userid_ctl">
                <div class="controls">
                  <input class="input-xlarge" type="text" name="userid" id="input_userid" placeholder="用户名">
				  <span class="fa fa-fw fa-user" style="margin-left:-25px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
			  <div class="control-group" id="email_ctl">
                <div class="controls">
                  <input class="input-xlarge" type="text" name="email" id="input_email" placeholder="邮箱">
				  <span class="fa fa-fw fa-envelope" style="margin-left:-25px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="center">
			    <input type="button" id="email_nxt" class="btn btn-primary" value="下一步"/>&nbsp;&nbsp;&nbsp;
				<a href="auth.php" style="line-height:40px">返回登录页</a>
              </div>
              <div class="center" style="margin-top:20px">
                <span id="ajax_emailresult" class="hide alert alert-error"></span>
              </div>
            </fieldset>
			</div>
         </form> 
       </div>
    </div>
	 
	<div id="verifypage" class="hide row-fluid">
        <div style="display:table;margin:auto;white-space:nowrap">
          <form class="form-vertical well" id="form_verify" action="#" method="post">
            <h1 class="center">重置密码</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <div id="verify_ctl" class="control-group">
			<center><p>我们发送了一封包含验证码的邮件，请查收...</p></center><br />
            <fieldset>
              <div class="control-group" id="verify_ctl">
                <div class="controls">
                  <input class="input-xlarge" type="text" name="verifyid" id="input_verifyid" placeholder="验证码">
				  <span class="fa fa-fw fa-key" style="margin-left:-25px;margin-top:7px;position:absolute"></span>
                </div>
              </div>
              <div class="center">
				<input type="button" id="verify_nxt" class="btn btn-primary" value="下一步"/>&nbsp;&nbsp;&nbsp;
				<input type="button" id="resend_btn" class="btn btn-danger" value="重新发送"/>&nbsp;&nbsp;&nbsp;
				<a href="#" onclick="return show_tip();" style="line-height:40px">无法收到?</a>
              </div>
              <div class="center" style="margin-top:20px;white-space:normal">
                <span id="ajax_verifyresult" class="hide alert alert-error"></span>
              </div>
              <div class="hide" id="emailtip" style="text-align:left">
				<br>
				<p>在某些情况下邮件可能需要几分钟才能到达。<br>
                若您仍未收到，请尝试以下步骤：</p>
				<p>1. 重新发送一封邮件</p>
				<p>2. 去您邮箱的垃圾邮件栏里看一看</p>
				<p>3. 联系管理员</p>
			  </div>
            </fieldset>
		    </div>
          </form> 
         </div>
      </div>
      
    <div id="pwdpage" class="hide row-fluid">
      <div style="display:table;margin:auto;white-space:nowrap">
		<form class="form-vertical well" id="form_pwd" action="#" method="post">
          <h1 class="center">重置密码</h1>
          <hr style="border-bottom-color: #E5E5E5;">
          <div id="pwd_ctl" class="control-group">
          <fieldset>
            <div class="control-group" id="newpwd_ctl">
              <div class="controls">
                <input class="input-xlarge" type="password" id="input_newpwd" name="newpwd" placeholder="新密码">
				<span class="fa fa-fw fa-key" style="margin-left:-25px;margin-top:7px;position:absolute"></span>
              </div>
            </div>
            <div class="control-group" id="reppwd_ctl">
              <div class="controls">
                <input class="input-xlarge" type="password" id="input_reppwd" placeholder="重复密码">
				<span class="fa fa-fw fa-refresh" style="margin-left:-25px;margin-top:7px;position:absolute"></span>
              </div>
            </div>
            <div class="center">
              <span id="pwd_save" class="btn btn-primary">下一步</span>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="center" style="margin-top:20px">
              <span id="ajax_pwdresult" class="hide alert alert-error"></span>
            </div>
          </fieldset>
		  </div>
        </form> 
       </div>
	</div>
    <script src="/assets/js/common.js"></script>
    <script type="text/javascript">
	function get_rand(begin, end) {
		return Math.floor(Math.random()*(end-begin))+begin;
		}
	var countdown = 60; 
	function settime(e) {
		if (countdown == 0) {
			e.removeAttribute("disabled");
			e.value = "重新发送";
			countdown = 60; 
			return 0;
		} else {
			e.setAttribute("disabled", true); 
			e.value = "重新发送(" + countdown + ")"; 
			countdown--; 
	} 
	setTimeout(function() { 
	settime(resend_btn) 
	},1000) 
	} 
     function switch_verify() {
        $('#emailpage').hide();
        $('#verifypage').fadeIn();
        return false;
		}
     function switch_pwd() {
        $('#verifypage').hide();
        $('#pwdpage').fadeIn();
        return false;
		}
	function show_tip() {
		$('#emailtip').slideToggle();
        return false;
	}
	  $(document).ready(function() {
		  $('#emailpage').fadeIn('slow');
		  var error = 0;
		  $('#email_nxt').click(function(){
			$('#ajax_emailresult').hide();
			var a=false;
			if(!$.trim($('#input_userid').val())) {
            $('#input_userid').addClass('error');
            a=true;
            }else{
            $('#input_userid').removeClass('error');
            }
			if(!$.trim($('#input_email').val())) {
            $('#input_email').addClass('error');
            a=true;
            }else{
            $('#input_email').removeClass('error');
            }
			if(!a){
				email_nxt.setAttribute("disabled", true);
				email_nxt.value = "请稍后...";
			$.ajax({
              type:"POST",
              url:'ajax_resetpwd.php',
              data:{"type":'verify',"user":$.trim($('#input_userid').val()),"email":$.trim($('#input_email').val())},
              success:function(msg){
				   email_nxt.removeAttribute("disabled");
			      email_nxt.value = "下一步";
                  if(msg == 'success') {
					  switch_verify();
					  settime(resend_btn);
				  }
				  else $('#ajax_emailresult').html(msg).show();
              }
            });
			};
		});
		$('#resend_btn').click(function(){
			$('#ajax_verifyresult').hide();
			resend_btn.setAttribute("disabled", true);
			resend_btn.value = "请稍后...";
			$.ajax({
              type:"POST",
              url:'ajax_resetpwd.php',
              data:{"type":'resend'},
              success:function(msg){
                  if(msg == 'success') {
					  $('#ajax_verifyresult').html('邮件重新发送成功!').show();
					  settime(resend_btn);
				  }
				  else if(msg == 'timeout'){
						  $('#ajax_verifyresult').html('身份验证过期，请重新开始...').show();
						  window.setTimeout("window.location='resetpwd.php'",2000); 
					  }
				  else {
					  $('#ajax_verifyresult').html(msg).show();
					  resend_btn.removeAttribute("disabled");
			          resend_btn.value = "重新发送";
				  }
              }
            });
		});
		$('#verify_nxt').click(function(){
			$('#ajax_verifyresult').hide();
			var a=false;
			if(!$.trim($('#input_verifyid').val())) {
            $('#input_verifyid').addClass('error');
            a=true;
            }else{
            $('#input_verifyid').removeClass('error');
            };
			if(!a){
				verify_nxt.setAttribute("disabled", true);
			   verify_nxt.value = "请稍后...";
				$.ajax({
                  type:"POST",
                  url:'ajax_resetpwd.php',
                  data:{"type":'match',"usercode":$.trim($('#input_verifyid').val())},
                  success:function(msg){
					  if (msg=='success'){
						   switch_pwd();
					  } 
					  else if(msg=='timeout'){
						  $('#ajax_verifyresult').html('身份验证过期，请重新开始...').show();
						  window.setTimeout("window.location='resetpwd.php'",2000); 
					  }
					  else if(msg=='fail'){
					   $('#ajax_verifyresult').html('验证码错误').show();
			    		 verify_nxt.removeAttribute("disabled");
			        verify_nxt.value = "下一步";
					  }
					  else if(msg=='fuckyou') {
							$('#ajax_verifyresult').html('错误次数过多，请重新开始...').show();
						  window.setTimeout("window.location='resetpwd.php'",2000); 
				     }
               else {
                 $('#ajax_verifyresult').html('未知错误 =.=').show();
                 verify_nxt.removeAttribute("disabled");
                 verify_nxt.value = "下一步";
               }
               }
           });
			};
		});	
		$('#pwd_save').click(function(){
		  $('#ajax_pwdresult').hide();
          var b=false;
          if(!$.trim($('#input_newpwd').val())) {
            $('#input_newpwd').addClass('error');
            b=true;
          }else{
            $('#input_newpwd').removeClass('error');
          }
		  if(!$.trim($('#input_reppwd').val())) {
            $('#input_reppwd').addClass('error');
            b=true;
          }else{
            $('#input_reppwd').removeClass('error');
          }
          if($('#input_newpwd').val()!='' && $('#input_reppwd').val() != $('#input_newpwd').val()){
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
              url:'ajax_resetpwd.php',
              data:{"type":'update',"newpwd":$.trim($('#input_newpwd').val())},
              success:function(msg){
                  if(msg == 'success'){
	              $('#ajax_pwdresult').html('密码重置成功，即将跳转至首页...').show();
	              window.setTimeout("window.location='index.php'",2000); 
                }else{
                    $('#ajax_pwdresult').html(msg).show();
              }
            }
        });
	  }
	});
});
</script>
</body>
</html>