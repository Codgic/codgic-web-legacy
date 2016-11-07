<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';

if(!check_priv(PRIV_PROBLEM))
    require __DIR__.'/inc/403.php';
else{
    $inTitle=_('Test Cases');
    if(!isset($_GET['problem_id']))
        $info=_('Please specify the problem id');
    else{
        $prob_id=intval($_GET['problem_id']);
        require __DIR__.'/func/checklogin.php';

        if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
            $_SESSION['admin_retpage'] = "testcase.php?problem_id=$prob_id";
            header("Location: admin_auth.php");
            exit();
        }

        require __DIR__.'/conf/database.php';
        $result=mysqli_query($con,"select title from problem where problem_id=$prob_id");
        $row=mysqli_fetch_row($result);
        if(!$row)
            $info=_('There\'s no such problem');

        $inTitle.=" #$prob_id";
    }

$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
<?php require __DIR__.'/inc/head.php';?>
  
    <body>
        <link href="/assets/css/jquery-ui.min.css" rel="stylesheet"><link href="/assets/css/jquery.ui.plupload.css" rel="stylesheet" />
        <?php require __DIR__.'/inc/navbar.php';?>
        <div class="alert collapse text-center alert-popup" id="alert_error"></div>
        <div class="container">
            <?php if(isset($info)){?>
                <div class="row">
                    <div class="text-center none-text none-center">
                        <p><i class="fa fa-meh-o fa-4x"></i></p>
                        <p>
                            <b>Whoops</b>
                            <br>
                            <?php echo $info?>
                        </p>
                    </div>
                </div>
            <?php }else{?>
                <div class="row text-center">
                    <h2><?php echo '#',$prob_id,' ',$row[0]; ?></h2>
                </div>
                <div class="row">
                    <div class="col-xs-12" style="margin-bottom:10px">
                        <div class="btn-group">
                            <button class="btn btn-default" id="btn_refresh"><i class="fa fa-refresh fa-spin"></i> <?php echo _('Refresh')?></button>
                            <button class="btn btn-default" id="btn_uploader" data-toggle="modal"><i class="fa fa-upload"></i> <?php echo _('Upload')?></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" id="uploader_wrap" style="margin-bottom:10px">
                        <div id="html5_uploader"><?php echo _('Your browser does not support HTML5!')?></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-striped table-condensed" style="border-bottom: 1px solid #DDD;">
                            <tbody id="file_list"></tbody>
                        </table>
                    </div>
                </div>
            <?php }
            require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/jquery-ui.min.js"></script>
        <script src="/assets/js/plupload.full.min.js"></script>
        <script src="/assets/js/plupload.zh_CN.js"></script>
        <script src="/assets/js/jquery.ui.plupload.min.js"></script>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">
            var problem_id = <?php echo $prob_id?>;
            var uploader;
            function refresh_list(){
                $('#btn_refresh>i').addClass('fa-spin');
                $.getJSON('api/ajax_testcase.php?op=list&problem_id='+problem_id, function(obj){
                    if(obj.files){
                        var $list = $('#file_list').html('');
                        $.each(obj.files,function(index,val){
                            $list.append('<tr><td style="text-align:left">'+htmlEncode(val)+'</td><td><a href="javascript:void(0)"><i class="fa fa-remove"></i></a></td></tr>');
                        });
                        $('#btn_refresh>i').removeClass('fa-spin');
                    }
                });
            }
            $(document).ready(function(){
                uploader=$("#html5_uploader").plupload({
                    runtimes: 'html5',
                    url: 'api/ajax_testcase.php',
                    chunk_size: '2mb',
                    multipart: true,
                    multipart_params:{
                        "problem_id": problem_id
                    },
                    rename: true,
                    dragdrop: true,
                    complete:function(){
                        refresh_list();
                        $('#uploader_wrap').slideUp();
                    },
                    filters: [{
                        title: "<?php echo _('CWOJ Approved Extensions')?>", 
                        extensions: "in,out,cpp"
                    }],
                });
                refresh_list();
                $('#uploader_wrap').slideUp(); //FUCK MS EDGE!!!
                $('#btn_refresh').click(function(){
                    refresh_list();
                });
                $('#btn_uploader').click(function(){
                    $('#uploader_wrap').slideToggle();
                    return false;
                });
                $('#file_list').click(function(E){
                    var $obj = $(E.target);
                    if($obj.is('i')){
                        var name = $obj.parent().parent().prev().text();
                        if(!window.confirm("<?php echo _('Sure to delete ')?> "+name))
                            return false;
                        $.get('api/ajax_testcase.php'+BuildUrlParms({'op':'del','problem_id':problem_id,'filename':name}), function(r){
                            if(/success/.test(r))
                                refresh_list();
                            else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+r).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        });
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>
<?php }?>
