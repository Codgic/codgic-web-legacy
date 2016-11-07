<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';

if(!isset($_SESSION['user'])){
    $info=_('Please login first');
}else{
    if(!isset($con))
        require __DIR__.'/conf/database.php';
    $user_id=$_SESSION['user'];
    $result=mysqli_query($con,'select email,nick,school,motto,user_id from users where user_id=\''.$user_id."'");
    $row=mysqli_fetch_row($result);
}

$inTitle=_('Profile');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>
    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>

        <div class="container">
            <div class="row">
                <?php if(isset($info)){?>
                    <div class="text-center none-text none-center">
                        <p><i class="fa fa-meh-o fa-4x"></i></p>
                        <p>
                            <b>Whoops</b>
                            <br>
                            <?php echo $info?>
                        </p>
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
                            <text class="motto-text" id="user_motto">
                                <?php echo $row[3]?>
                            </text>
                            <br>
                            <text class="help-block">
                                <?php echo _('Avatar powered by Gravatar.')?>
                            </text>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <form id="form_profile" action="#" method="post">
                        <input type="hidden" value="profile" name="type">
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_nick">
                                <?php echo _('Nickname')?>
                            </label>
                            <input class="form-control" type="text" name="nick" id="input_nick" value="<?php echo htmlspecialchars($row[1])?>" placeholder="<?php echo _('Your new cool nickname...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_motto">
                                <?php echo _('Motto')?>
                            </label>
                            <input class="form-control" id="input_motto" name="motto" type="text" value="<?php echo htmlspecialchars($row[3])?>" placeholder="<?php echo _('Leave it blank if slience is gold...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_newpwd">
                            <label class="control-label" for="input_newpwd">
                                <?php echo _('New Password')?>
                            </label>
                            <input class="form-control" type="password" id="input_newpwd" name="newpwd" placeholder="<?php echo _('A new password if you like...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_reppwd">
                            <label class="control-label" for="input_reppwd">
                                <?php echo _('Retype Password')?>
                            </label>
                            <input class="form-control" type="password" id="input_reppwd" placeholder="<?php echo _('Retype your brand new password...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_email">
                            <label class="control-label" for="input_email">
                                <?php echo _('Email')?>
                            </label>
                            <input class="form-control" type="text" name="email" id="input_email" value="<?php echo htmlspecialchars($row[0])?>" placeholder="<?php echo _('A vaild email is required for convenience...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_school">
                                <?php echo _('School')?>
                            </label>
                            <input class="form-control" type="text" name="school" id="input_school" value="<?php echo htmlspecialchars($row[2])?>" placeholder="<?php echo _('Make your school proud...')?>">
                        </div>
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_oldpwd">
                            <label class="control-label" for="input_oldpwd">
                                <?php echo _('Current password')?>(*)
                            </label>
                            <input class="form-control" id="input_oldpwd" name="oldpwd" type="password" placeholder="<?php echo _('Required before changing anything...')?>">
                        </div>
                        <div class="col-xs-12">
                            <div id="ajax_result" class="alert col-xs-12 collapse"></div>
                            <button id="save_btn" class="btn btn-primary" type="submit"><?php echo _('Save')?></button>
                        </div>
                    </form>
                <?php } ?>
            </div>
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#form_profile').submit(function(){
                    var b=false,pwd;
                    if($('#input_oldpwd').val()==''){
                        $('#ctl_oldpwd').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_oldpwd').removeClass('has-error');
                    pwd=$('#input_newpwd').val();
                    if(pwd!='' && $('#input_reppwd').val()!=pwd){
                        b=true;
                        $('#ctl_newpwd').addClass('has-error');
                        $('#ctl_reppwd').addClass('has-error');
                    }else{
                        $('#ctl_newpwd').removeClass('has-error');
                        $('#ctl_reppwd').removeClass('has-error');
                    }
                    if($('#input_email').val()==''){
                        $('#ctl_email').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_email').removeClass('has-error');
                    if(!b){
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_profile.php",
                            data:$('#form_profile').serialize(),
                            success:function(msg){
                                if(msg=='success'){
                                    $('#ajax_result').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Profile updated successfully!')?>').removeClass('alert-danger').addClass('alert-success').slideDown();
                                    $('#user_motto').text($('#input_motto').val());
                                    $('#input_oldpwd').val('');
                                }else
                                    $('#ajax_result').html('<i class="fa fa-fw fa-remove"></i> '+msg).removeClass('alert-success').addClass('alert-danger').slideDown();
                            }
                        });
                    }
                    return false;
                });
            });
        </script>
    </body>
</html>
