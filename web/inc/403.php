<?php
if(!defined('OJDIR'))
    require __DIR__.'/../inc/init.php';
if(!isset($oj_name))
    require __DIR__.'/../conf/ojsettings.php';
if(!isset($check_login))
    require __DIR__.'/../func/checklogin.php';
$inTitle='ERROR 403';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/head.php';?>
    <body>
        <?php require __DIR__.'/navbar.php';?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="text-center none-text none-center">
                        <p>
                            <i class="fa fa-meh-o fa-4x"></i>
                        </p>
                        <p>
                            <b>ERROR 403</b>
                            <br>
                            <?php echo _('Looks like you can\'t access this page')?>
                        </p>
                    </div>
                </div>
            </div>
        <?php require __DIR__.'/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    </body>
</html>
