<?php 
require 'inc/ojsettings.php';
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
require 'head.php';
?>
  <body style="background-image: url(<?php echo $loginimg?>)">
  <div class="container">
    <div class="row collapse" id="emailpage">
      <div class="panel panel-default panel-login">
		<div class="panel-body">
		<form id="form_email" action="#" method="post">
          <h1 class="text-center"> 重置密码</h1>
          <hr>
            <div id="email_ctl" class="form-group has-feedback">
              <div class="form-group has-feedback" id="userid_ctl">
                  <input class="form-control" type="text" name="userid" id="input_userid" placeholder="用户名">
				  <span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
              </div>
			  <div class="form-group has-feedback" id="email_ctl">
                  <input class="form-control" type="text" name="email" id="input_email" placeholder="邮箱">
				  <span class="form-control-feedback"><i class="fa fa-fw fa-envelope"></i></span>
              </div>
              <div id="ajax_emailresult" class="collapse alert alert-danger"></div>
              <div class="form-group text-center">
			    <input type="button" id="email_nxt" class="btn btn-primary" value="下一步"/>&nbsp;&nbsp;&nbsp;
				<a href="login.php">返回登录页</a>
              </div>
			</div>
         </form>
		 </div>
       </div>
	  </div>
	 
	<div id="verifypage" class="row collapse" style="margin-top:50px">
        <div class="panel panel-default panel-login">
          <form id="form_verify" action="#" method="post">
            <h1 class="text-center">重置密码</h1>
            <hr style="border-bottom-color: #E5E5E5;">
            <div id="verify_ctl" class="form-group has-feedback">
			<p class="text-center">我们发送了一封包含验证码的邮件，请查收...</p>
              <div class="form-group has-feedback" id="verify_ctl">
                  <input class="form-control" type="text" name="verifyid" id="input_verifyid" placeholder="验证码">
				  <span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
              </div>
              <div id="ajax_verifyresult" class="collapse alert alert-danger"></div>
              <div class="form-group text-center">
				<input type="button" id="verify_nxt" class="btn btn-primary" value="下一步"/>&nbsp;&nbsp;&nbsp;
				<input type="button" id="resend_btn" class="btn btn-danger" value="重新发送"/>&nbsp;&nbsp;&nbsp;
				<a href="javascript:void(0)" onclick="return show_tip();" style="line-height:40px">无法收到?</a>
              </div>
              <div class="collapse" id="emailtip" style="text-align:left">
				<br>
				<p>在某些情况下邮件可能需要几分钟才能到达。<br>
                若您仍未收到，请尝试以下步骤：</p>
				<p>1. 重新发送一封邮件</p>
				<p>2. 去您邮箱的垃圾邮件栏里看一看</p>
				<p>3. 联系管理员</p>
			  </div>
		    </div>
          </form> 
         </div>
      </div>
      
    <div id="pwdpage" class="row collapse" style="margin-top:50px">
      <div class="panel panel-default panel-login">
		<form id="form_pwd" action="#" method="post">
          <h1 class="text-center">重置密码</h1>
          <hr style="border-bottom-color: #E5E5E5;">
          <div id="pwd_ctl" class="form-group has-feedback">
            <div class="form-group has-feedback" id="newpwd_ctl">
                <input class="form-control" type="password" id="input_newpwd" name="newpwd" placeholder="新密码">
				<span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
            </div>
            <div class="form-group has-feedback" id="reppwd_ctl">
                <input class="form-control" type="password" id="input_reppwd" placeholder="重复密码">
				<span class="form-control-feedback"><i class="fa fa-fw fa-refresh"></i></span>
            </div>
            <div id="ajax_pwdresult" class="collapse alert alert-danger"></div>
            <div class="form-group text-center">
              <span id="pwd_save" class="btn btn-primary">下一步</span>&nbsp;&nbsp;&nbsp;
            </div>
		  </div>
        </form> 
       </div>
	</div>
	</div>
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
		  $('#emailpage').fadeIn();
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
				  else $('#ajax_emailresult').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
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
					  $('#ajax_verifyresult').html('<i class="fa fa-fw fa-check"></i> 邮件重新发送成功!').show();
					  settime(resend_btn);
				  }
				  else if(msg == 'timeout'){
						  $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> 身份验证过期，请重新开始...').show();
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
				verify_nxt.value = '请稍后...';
				$.ajax({
                  type:"POST",
                  url:'ajax_resetpwd.php',
                  data:{"type":'match',"usercode":$.trim($('#input_verifyid').val())},
                  success:function(msg){
					  if (msg=='success'){
                        switch_pwd();
					  } 
					  else if(msg=='timeout'){
                        $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> 身份验证过期，请重新开始...').slideDown();
                        window.setTimeout("window.location='resetpwd.php'",2000); 
					  }
					  else if(msg=='fail'){
					   $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> 验证码错误...').slideDown();
                        verify_nxt.removeAttribute("disabled");
			        verify_nxt.value = "下一步";
					  }
					  else if(msg=='fuckyou') {
                        $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> 错误次数过多，请重新开始...').slideDown();
                        window.setTimeout("window.location='resetpwd.php'",2000); 
				     }
               else {
                 $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> 未知错误...').slideDown();
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
                    $('#ajax_pwdresult').removeClass('alert-danger').addClass('alert-success');
                    $('#ajax_pwdresult').html('<i class="fa fa-fw fa-check"></i> 密码重置成功，即将跳转至首页...').slideDown();
                    window.setTimeout("window.location='index.php'",2000); 
                }else{
                  $('#ajax_pwdresult').removeClass('alert-success').addClass('alert-danger');
                  $('#ajax_pwdresult').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
              }
            }
        });
	  }
	});
});
</script>
</body>
</html>
