<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

if(!check_priv(PRIV_PROBLEM) && !check_priv(PRIV_SYSTEM))
    include __DIR__.'/inc/403.php';
else{
    require __DIR__.'/../src/database.php';
    if(isset($_POST['paswd'])){
        if(!function_exists('my_rsa'))
        require __DIR__.'/func/checkpwd.php';
        if(password_right($_SESSION['user'], $_POST['paswd'])){
            $_SESSION['admin_tfa']=1;
            if(isset($_SESSION['admin_retpage'])){
                $ret=$_SESSION['admin_retpage'];
            }else
                $ret = "index.php";
            unset($_SESSION['admin_retpage']);  
            header("Location: $ret");
            exit();
        }
    }

$inTitle=_('Admin Verification');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>
    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>  
          
        <div class="container admin-page">
            <div class="row">
                <div class="col-xs-12">
                    <form class="form-inline text-center" method="post">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" class="form-control" autofoucs id="input_adminpass" name="paswd" placeholder="<?php echo _('Your pasword...')?>">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default"><?php echo _('Go')?></button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">
        $(document).ready(function(){
            $('#input_adminpass').focus();
        });
        </script>
    </body>
</html>
<?php }?>