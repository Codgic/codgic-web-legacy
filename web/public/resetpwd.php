<?php 
require __DIR__.'/inc/init.php';
$_SESSION['resetpwd_flag']=0; 
$_SESSION['resetpwd_wrongnum']=0; //Incorrect Tries
$_SESSION['resetpwd_code']=rand(10000000,99999999); //Random verification code

$inTitle=_('Reset Password');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
<?php require __DIR__.'/inc/head.php';?>
    <body class="bigbg">
        <div class="container">
            <div class="row collapse" id="emailpage">
                <div class="panel panel-default panel-login">
                    <div class="panel-body">
                        <form id="form_email" action="#" method="post">
                            <input type="hidden" value="verify" name="type">
                            <h1 class="text-center">
                                <?php echo _('Reset Password')?>
                            </h1>
                            <hr>
                            <div id="email_ctl" class="form-group has-feedback">
                                <div class="form-group has-feedback" id="ctl_email">
                                    <input class="form-control" type="text" name="email" id="input_email" placeholder="<?php echo _('Email...')?>">
                                    <span class="form-control-feedback"><i class="fa fa-fw fa-envelope"></i></span>
                                </div>
                                <div id="ajax_emailres" class="collapse alert alert-danger"></div>
                                <div class="form-group">
                                    <input type="button" id="email_nxt" class="btn btn-primary" value="<?php echo _('Next')?>"/>
                                    <a href="login.php" style="margin-left:8px">
                                        <?php echo _('Go Back...')?>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
     
            <div class="row collapse" id="verifypage">
                <div class="panel panel-default panel-login">
                    <div class="panel-body">
                        <h1 class="text-center">
                            <?php echo _('Reset Password')?>
                        </h1>
                        <hr>
                        <form id="form_verify" action="#" method="post">
                            <input type="hidden" value="match" name="type">
                            <div id="verify_ctl" class="form-group has-feedback">
                                <p class="text-center"><?php echo _('We\'ve just sent a verification code to your email...')?></p>
                                <div class="form-group has-feedback" id="ctl_vid">
                                    <input class="form-control" type="text" name="usercode" id="input_vid" placeholder="<?php echo _('Verification Code')?>">
                                    <span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
                                </div>
                                <div id="ajax_verifyres" class="collapse alert alert-danger"></div>
                                <div class="form-group">
                                    <input type="button" id="verify_nxt" class="btn btn-primary" value="<?php echo _('Next')?>"/>
                                    <input type="button" id="resend_btn" class="btn btn-danger" style="margin-left:8px" value="<?php echo _('Resend')?>"/>
                                    <a href="javascript:void(0)" onclick="return show_tip();" style="margin-left:8px">
                                        <?php echo _('Can\'t Recieve?')?>
                                    </a>
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
                        <h1 class="text-center">
                            <?php echo _('Reset Password')?>
                        </h1>
                        <hr>
                        <form id="form_pwd" action="#" method="post">
                            <input type="hidden" value="update" name="type">
                            <div id="pwd_ctl" class="form-group has-feedback">
                                <div class="form-group has-feedback" id="ctl_newpwd">
                                    <input class="form-control" type="password" id="input_newpwd" name="newpwd" autocomplete="off" placeholder="<?php echo _('New Password')?>">
                                    <span class="form-control-feedback"><i class="fa fa-fw fa-key"></i></span>
                                </div>
                                <div class="form-group has-feedback" id="ctl_reppwd">
                                    <input class="form-control" type="password" id="input_reppwd" autocomplete="off" placeholder="<?php echo _('Retype Password')?>">
                                    <span class="form-control-feedback"><i class="fa fa-fw fa-refresh"></i></span>
                                </div>
                                <div id="ajax_pwdres" class="collapse alert alert-danger"></div>
                                <div class="form-group">
                                    <span id="pwd_save" class="btn btn-primary"><?php echo _('Next')?></span>
                                </div>    
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
            var ct=60; 
            function settime(e){
                if(ct==0){
                    e.removeAttribute("disabled");
                    e.value = "<?php echo _('Resend')?>";
                    ct = 60; 
                    return 0;
                }else{
                    e.setAttribute("disabled", true); 
                    e.value = "<?php echo _('Resend')?> (" + ct + ")"; 
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
                $('#email_nxt').click(function(){
                    $('#ajax_emailres').slideUp();
                    var a=false;
                    if(!$.trim($('#input_email').val())){
                        $('#ctl_email').addClass('has-error');
                        a=true;
                    }else
                        $('#ctl_email').removeClass('has-error');
                    if(!a){
                        email_nxt.setAttribute("disabled", true);
                        email_nxt.value = "<?php echo _('Please wait...')?>";
                        $.ajax({
                            type:"POST",
                            url:'api/ajax_resetpwd.php',
                            data:$('#form_email').serialize(),
                            success:function(msg){
                                email_nxt.removeAttribute("disabled");
                                email_nxt.value = "<?php echo _('Next')?>";
                                if(msg=='success'){
                                    switch_verify();
                                    settime(resend_btn);
                                }else 
                                    $('#ajax_emailres').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                            }
                        });
                    };
                });
                $('#resend_btn').click(function(){
                    $('#ajax_verifyres').slideUp();
                    resend_btn.setAttribute("disabled", true);
                    resend_btn.value = "<?php echo _('Please wait...')?>";
                    $.ajax({
                        type:"POST",
                        url:'api/ajax_resetpwd.php',
                        data:{"type":'resend'},
                        success:function(msg){
                            if(msg=='success'){
                                $('#ajax_verifyres').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Email resent successfully...')?>').slideDown();
                                settime(resend_btn);
                            }else if(msg=='timeout'){
                                $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                                window.setTimeout("window.location='resetpwd.php'",2000); 
                            }else{
                                $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                                resend_btn.removeAttribute("disabled");
                                resend_btn.value = "<?php echo _('Resend')?>";
                            }
                        }
                    });
                });
                $('#verify_nxt').click(function(){
                    $('#ajax_verifyres').slideUp();
                    var a=false;
                    if(!$.trim($('#input_vid').val())){
                        $('#ctl_vid').addClass('has-error');
                        a=true;
                    }else
                        $('#ctl_vid').removeClass('has-error');
                    if(!a){
                        verify_nxt.setAttribute("disabled", true);
                        verify_nxt.value = '<?php echo _('Please wait...')?>';
                        $.ajax({
                            type:"POST",
                            url:'api/ajax_resetpwd.php',
                            data:$('#form_verify').serialize(),
                            success:function(msg){
                                if (msg=='success')
                                    switch_pwd();
                                else if(msg=='timeout'){
                                    $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                                    window.setTimeout("window.location='resetpwd.php'",2000); 
                                }else if(msg=='fail'){
                                    $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Wrong Verification Code...')?>').slideDown();
                                    verify_nxt.removeAttribute("disabled");
                                    verify_nxt.value = "<?php echo _('Next')?>";
                                }else if(msg=='fuckyou'){
                                    $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Too many failed attempts...')?>').slideDown();
                                    window.setTimeout("window.location='resetpwd.php'",2000); 
                                }else{
                                    $('#ajax_verifyres').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                                    verify_nxt.removeAttribute("disabled");
                                    verify_nxt.value = "<?php echo _('Next')?>";
                                }
                            }
                        });
                    };
                });    
                $('#pwd_save').click(function(){
                    $('#ajax_pwdres').slideUp();
                    var b=false;
                    var pwd=$('#input_newpwd').val();
                    if(pwd=='' || $('#input_reppwd').val()!=pwd){
                        b=true;
                        $('#ctl_newpwd').addClass('has-error');
                        $('#ctl_reppwd').addClass('has-error');
                    }else{
                        $('#ctl_newpwd').removeClass('has-error');
                        $('#ctl_reppwd').removeClass('has-error');
                    }
                    if(!b){
                        $.ajax({
                            type:"POST",
                            url:'api/ajax_resetpwd.php',
                            data:$('#form_pwd').serialize(),
                            success:function(msg){
                                if(msg == 'success'){
                                    $('#ajax_pwdres').removeClass('alert-danger').addClass('alert-success');
                                    $('#ajax_pwdres').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Password reset successfully!')?>').slideDown();
                                    window.setTimeout("window.location='login.php'",2000); 
                                }else if(msg=='timeout'){
                                    $('#ajax_pwdres').removeClass('alert-success').addClass('alert-danger');
                                    $('#ajax_pwdres').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Session expired...')?>').slideDown();
                                    window.setTimeout("window.location='resetpwd.php'",2000); 
                                }else{
                                    $('#ajax_pwdres').removeClass('alert-success').addClass('alert-danger');
                                    $('#ajax_pwdres').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                                }
                            }
                        });    
                    }
                });
            });
        </script>
    </body>
</html>
