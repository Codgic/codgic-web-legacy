<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/conf/database.php';
require __DIR__.'/func/cookie.php';

if(isset($_SESSION['user'])||check_cookie()){
    header("Location: /");
    exit();
}

$Title=_('Welcome to ').$oj_name;
?>
<!DOCTYPE html>
<html>
<?php require __DIR__.'/inc/head.php';?>
	<body style="background-image: url(<?php echo $loginimg?>)">
		<div class="container">
			<div class="row collapse">
				<div class="panel panel-default panel-login" style="display:table;margin:auto">
					<div class="panel-body">
						<form id="form_login" method="post">
							<h1 class="text-center"><?php echo _('Welcome Back')?></h1>
							<hr>
							<div class="form-group has-feedback" id="ctl_uid">
								<input class="form-control" autofocus="autofocus" id="input_uid" type="text" name="uid" placeholder="<?php echo _('Username...')?>">
								<span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
							</div>
							<div class="form-group has-feedback" id="ctl_pwd">
								<input class="form-control" id="input_pwd" name="pwd" type="password" placeholder="<?php echo _('Password...')?>">
								<span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
							</div>
							<div class="form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember" id="chk_remember"><?php echo _('Say you remember me...')?>
									</label>
								</div>
							</div>
							<div id="login_res" class="alert alert-danger collapse"></div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><?php echo _('Log in')?></button>
								<a href="signup.php" style="margin-left:8px"><?php echo _('Sign up')?></a>
								<a href="resetpwd.php" style="margin-left:8px"><?php echo _('Forgot?')?></a>
								<a href="javascript:void(0)" onclick="return $('#contact').slideToggle();" style="margin-left:8px"><?php echo _('Contact us...')?></a>
							</div>
						</form>
					</div>
					<div class="collapse" id="contact">
						<p class="text-center">
							<b><?php echo _('Contact mail:')?> <a href="mailto:<?php echo $contact_email?>"><?php echo $contact_email?></a></b>
						</p>
                        <br>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				var r="<?php 
					if(isset($_SESSION['login_redirect'])) 
						echo $_SESSION['login_redirect'];
					else
						echo 'index.php';
				?>";
				$('body .row').fadeIn();
				$('#form_login').submit(function(){
					$('#login_res').slideUp();
					var b=false,pwd;
					if(!$.trim($('#input_uid').val())){
						$('#ctl_uid').addClass('has-error');
						b=true;
					}else
						$('#ctl_uid').removeClass('has-error');
					if(!$.trim($('#input_pwd').val())){
						$('#ctl_pwd').addClass('has-error');
						b=true;
					}else
						$('#ctl_pwd').removeClass('has-error');
					if(!b){
						$.ajax({
							type:"POST",
							url:"api/ajax_login.php",
							data:$('#form_login').serialize(),
							success:function(msg){
								if(/success/.test(msg))
									window.location.href=r;
								else
									$('#login_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
							}
						});
					}
					return false;
				});
			});
		</script>
	</body>
</html>