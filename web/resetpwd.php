<?php 
require 'inc/global.php';
require 'inc/ojsettings.php';
$_SESSION['resetpwd_flag']=0; 
$_SESSION['resetpwd_wrongnum']=0; //Incorrect Tries
$_SESSION['resetpwd_code']=rand(10000000,99999999); //Random verification code
?>
<!DOCTYPE html>
<html>
<?php 
$inTitle=_('Reset Password');
$Title=$inTitle .' - '. $oj_name;
require 'head.php';
?>
  <body style="background-image: url(<?php echo $loginimg?>)">
  <div class="container">
    <div class="row collapse" id="emailpage">
      <div class="panel panel-default panel-login">
		<div class="panel-body">
          <form id="form_email" action="#" method="post">
            <h1 class="text-center"><?php echo _('Reset Password')?></h1>
            <hr>
            <div id="email_ctl" class="form-group has-feedback">
			  <div class="form-group has-feedback" id="email_ctl">
                <input class="form-control" type="text" name="email" id="input_email" placeholder="<?php echo _('Email...')?>">
                <span class="form-control-feedback"><i class="fa fa-fw fa-envelope"></i></span>
              </div>
              <div id="ajax_emailresult" class="collapse alert alert-danger"></div>
              <div class="dropdown form-group">
			    <input type="button" id="email_nxt" class="btn btn-primary" value="<?php echo _('Next')?>"/>
				<a href="login.php" style="margin-left:8px"><?php echo _('Go Back...')?></a>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-fw fa-globe"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" id="nav_lang">
                  <li><a href="javascript:void(0)" onclick="return change_i18n('auto')"><?php echo _('Auto switch')?></a></li>
                  <li><a href="javascript:void(0)" onclick="return change_i18n('en_US')">English</a></li>
                  <li><a href="javascript:void(0)" onclick="return change_i18n('zh_CN')">简体中文</a></li>
                </ul>
              </div>
			</div>
          </form>
		 </div>
       </div>
	  </div>
	 
      <div class="row collapse" id="verifypage">
        <div class="panel panel-default panel-login">
          <div class="panel-body">
            <form id="form_verify" action="#" method="post">
              <h1 class="text-center"><?php echo _('Reset Password')?></h1>
              <hr>
              <div id="verify_ctl" class="form-group has-feedback">
                <p class="text-center"><?php echo _('We\'ve just sent a verification code to your email...')?></p>
                <div class="form-group has-feedback" id="verify_ctl">
                  <input class="form-control" type="text" name="verifyid" id="input_verifyid" placeholder="<?php echo _('Verification Code')?>">
				  <span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
                </div>
                <div id="ajax_verifyresult" class="collapse alert alert-danger"></div>
                <div class="dropdown form-group">
                  <input type="button" id="verify_nxt" class="btn btn-primary" value="<?php echo _('Next')?>"/>
				  <input type="button" id="resend_btn" class="btn btn-danger" style="margin-left:8px" value="<?php echo _('Resend')?>"/>
                  <a href="javascript:void(0)" onclick="return show_tip();" style="margin-left:8px"><?php echo _('Can\'t Recieve?')?></a>
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-fw fa-globe"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-right" id="nav_lang">
                    <li><a href="javascript:void(0)" onclick="return change_i18n('auto')"><?php echo _('Auto switch')?></a></li>
                    <li><a href="javascript:void(0)" onclick="return change_i18n('en_US')">English</a></li>
                    <li><a href="javascript:void(0)" onclick="return change_i18n('zh_CN')">简体中文</a></li>
                  </ul>
                </div>
                <div class="collapse" id="emailtip" style="text-align:left">
                  <?php echo _('<p>Our email will arrive in a few minutes.<br>If you can\'t recieve, these steps might help:</p><ul><li>Resend an email.</li><li>Check out your junk mail folder.</li><li>Contact Administrators.</li></ul>');?>
                </div>
              </div>
            </form> 
          </div>
        </div>
      </div>
      
      <div id="pwdpage" class="row collapse">
        <div class="panel panel-default panel-login">
          <div class="panel-body">
            <form id="form_pwd" action="#" method="post">
              <h1 class="text-center"><?php echo _('Reset Password')?></h1>
              <hr>
              <div id="pwd_ctl" class="form-group has-feedback">
                <div class="form-group has-feedback" id="newpwd_ctl">
                  <input class="form-control" type="password" id="input_newpwd" name="newpwd" placeholder="<?php echo _('New Password')?>">
				  <span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
                </div>
                <div class="form-group has-feedback" id="reppwd_ctl">
                  <input class="form-control" type="password" id="input_reppwd" placeholder="<?php echo _('Retype Password')?>">
                  <span class="form-control-feedback"><i class="fa fa-fw fa-refresh"></i></span>
                </div>
                <div id="ajax_pwdresult" class="collapse alert alert-danger"></div>
                <div class="dropdown form-group">
                  <span id="pwd_save" class="btn btn-primary"><?php echo _('Next')?></span>
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-fw fa-globe"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-right" id="nav_lang">
                    <li><a href="javascript:void(0)" onclick="return change_i18n('auto')"><?php echo _('Auto switch')?></a></li>
                    <li><a href="javascript:void(0)" onclick="return change_i18n('en_US')">English</a></li>
                    <li><a href="javascript:void(0)" onclick="return change_i18n('zh_CN')">简体中文</a></li>
                  </ul>
                </div>
              </div>
            </form> 
          </div>
        </div>
      </div>
	</div>
    <script type="text/javascript">
    function change_i18n(e){
        $.post('/ajax_globe.php',{i18n:e},function(msg){if(msg=='success') window.location.reload();});
    }
	var ct=60; 
	function settime(e){
        if(ct == 0){
            e.removeAttribute("disabled");
			e.value = "<?php echo _('Resend')?>";
			ct = 60; 
			return 0;
		}else{
			e.setAttribute("disabled", true); 
			e.value = "<?php echo _('Reset Password')?> (" + ct + ")"; 
			ct--; 
        }
        setTimeout(function(){settime(resend_btn)},1000);
    } 
    function switch_verify(){
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
			if(!$.trim($('#input_email').val())){
                $('#input_email').addClass('error');
                a=true;
            }else{
                $('#input_email').removeClass('error');
            }
			if(!a){
				email_nxt.setAttribute("disabled", true);
				email_nxt.value = "<?php echo _('Please wait...')?>";
                $.ajax({
                    type:"POST",
                    url:'ajax_resetpwd.php',
                    data:{"type":'verify',"user":$.trim($('#input_userid').val()),"email":$.trim($('#input_email').val())},
                    success:function(msg){
                        email_nxt.removeAttribute("disabled");
                        email_nxt.value = "<?php echo _('Next')?>";
                        if(msg=='success'){
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
			resend_btn.value = "<?php echo _('Please wait...')?>";
			$.ajax({
                type:"POST",
                url:'ajax_resetpwd.php',
                data:{"type":'resend'},
                success:function(msg){
                    if(msg=='success'){
                        $('#ajax_verifyresult').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Email resent successfully...')?>').slideDown();
                        settime(resend_btn);
                    }else if(msg=='timeout'){
                        $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                        window.setTimeout("window.location='resetpwd.php'",2000); 
                    }else{
                        $('#ajax_verifyresult').html(msg).show();
                        resend_btn.removeAttribute("disabled");
                        resend_btn.value = "<?php echo _('Resend')?>";
                    }
                }
            });
		});
		$('#verify_nxt').click(function(){
            $('#ajax_verifyresult').hide();
            var a=false;
			if(!$.trim($('#input_verifyid').val())){
                $('#input_verifyid').addClass('error');
                a=true;
            }else{
                $('#input_verifyid').removeClass('error');
            }
			if(!a){
				verify_nxt.setAttribute("disabled", true);
				verify_nxt.value = '<?php echo _('Please wait...')?>';
				$.ajax({
                    type:"POST",
                    url:'ajax_resetpwd.php',
                    data:{"type":'match',"usercode":$.trim($('#input_verifyid').val())},
                    success:function(msg){
                        if (msg=='success'){
                            switch_pwd();
                        }else if(msg=='timeout'){
                            $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                            window.setTimeout("window.location='resetpwd.php'",2000); 
                        }else if(msg=='fail'){
                            $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Wrong Verification Code...')?>').slideDown();
                            verify_nxt.removeAttribute("disabled");
                            verify_nxt.value = "<?php echo _('Next')?>";
                        }else if(msg=='fuckyou'){
                            $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Too many failed attempts...')?>').slideDown();
                            window.setTimeout("window.location='resetpwd.php'",2000); 
                        }else{
                            $('#ajax_verifyresult').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                            verify_nxt.removeAttribute("disabled");
                            verify_nxt.value = "<?php echo _('Next')?>";
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
                            $('#ajax_pwdresult').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Password reset successfully!')?>').slideDown();
                            window.setTimeout("window.location='index.php'",2000); 
                        }else if(msg=='timeout'){
                            $('#ajax_pwdresult').removeClass('alert-success').addClass('alert-danger');
                            $('#ajax_pwdresult').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                            window.setTimeout("window.location='resetpwd.php'",2000); 
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
