<?php 
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/sourcecode.php';
require __DIR__.'/lib/result_type.php';
require __DIR__.'/lib/lang.php';

$inTitle=_('Sourcecode');

if(!isset($_GET['solution_id']))
    $info=_('Please specify the solution id');
else{
    $sol_id=intval($_GET['solution_id']);
    require __DIR__.'/func/checklogin.php';
    require __DIR__.'/../src/database.php';
    $result=mysqli_query($con,"select user_id,time,memory,result,language,code_length,problem_id,public_code,malicious from solution where solution_id=$sol_id");
    $row=mysqli_fetch_row($result);
    
    //Check existence.
    if(!$row)
        die('No such solution.');
     
    //Check privilege.
    $ret = sc_check_priv($row[6], $row[7], $row[0]);
    if($ret === TRUE)
        $allowed = TRUE;
    else{
        $allowed = FALSE;
        $info=$ret;
    }
    if($allowed){
        $result=mysqli_query($con,"select source from source_code where solution_id=$sol_id");
        if($tmp=mysqli_fetch_row($result)){
            $source=$tmp[0];
            //If result == 'Compile Error', fetch Compile Info.
            if($row[3]==7){
                $result=mysqli_query($con, "select error from compileinfo where solution_id=$sol_id");
                if($tmp=mysqli_fetch_row($result))
                    $compileinfo=$tmp[0];
                else
                    $compileinfo=_('Compile Info is not available...');
            }      
        }else
            $info = _('Sourcecode not available');
    }
    $inTitle.=" #$sol_id";
}

$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
   <?php
        require __DIR__.'/inc/head.php'; 
    ?>
    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>
        <div class="alert alert-danger collapse text-center alert-popup" id="alert_error"></div>
        <div class="container cm-autoheight">
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
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-header">
                            <h2><?php echo _('Sourcecode').' #'.$sol_id?></h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo '<span class="sc-info label '.$RESULT_STYLE[$row[3]].'" style="display:inline">'.$RESULT_TYPE[$row[3]].'</span>';?>
                        <span class="sc-info"><i class="fa fa-fw fa-coffee"></i> <?php echo '<a href="problempage.php?problem_id=',$row[6],'">',$row[6],'</a>'?></span>
                        <span class="sc-info"><i class="fa fa-fw fa-user"></i> <?php echo '<a href="javascript:void(0)" onclick="return show_user(\'',$row[0],'\');">',$row[0],'</a>'?></span>
                        <span class="sc-info"><i class="fa fa-fw fa-code"></i> <?php echo $LANG_NAME[$row[4]];?></span>
                        <span class="sc-info"><i class="fa fa-fw fa-clock-o"></i> <?php echo $row[1].' ms'?></span>
                        <span class="sc-info"><i class="fa fa-fw fa-database"></i> <?php echo $row[2].' KB'?></span>
                        <span class="sc-info"><i class="fa fa-fw fa-file-code-o"></i> <?php echo round($row[5]/1024,2).' KB'?></span>   
                        <span class="sc-info">
                            <?php 
                                if($row[7])
                                    echo '<i class="fa fa-fw fa-eye"></i> ',_('Open Source');
                                else
                                    echo '<i class="fa fa-fw fa-eye-slash"></i>', _('Close Source');
                            ?>
                        </span>
                        <?php if($row[8]){?>
                            <span class="sc-info" style="color:red"><i class="fa fa-fw fa-flag"></i> <?php echo _('Malicious!')?></span>
                        <?php }?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="btn-group">
                            <?php if($row[3]==7){?>
                                <button class="btn btn-default" data-toggle="modal" data-target="#CEModal">
                                    <i class="fa fa-fw fa-heartbeat"></i> <?php echo _('Compile Info')?>
                                </button>
                            <?php }if(check_priv(PRIV_PROBLEM) || check_priv(PRIV_SYSTEM)){?>
                                <button class="btn btn-default" id="btn_mark_mal">
                                    <?php if(!$row[8]){?>
                                        <i class="fa fa-fw fa-flag"></i> <?php echo _('Malicious!')?>
                                    <?php }else{?>
                                        <i class="fa fa-fw fa-flag-o"></i> <?php echo _('Not Malicious')?>
                                    <?php }?>
                                </button>
                            <?php }if(isset($_SESSION['user'])&&$row[0]==$_SESSION['user']){?>
                                <button class="btn btn-default" id="btn_osc">
                                    <?php if($row[7]){
                                        echo '<i class="fa fa-fw fa-eye-slash"></i> ',_('Close Source');
                                    }else{
                                        echo '<i class="fa fa-fw fa-eye"></i> ',_('Open Source');
                                    }?>
                                </button>
                            <?php }?>
                            <button class="btn btn-default" data-clipboard-action="copy" data-toggle="tooltip" data-trigger="manual" id="btn_copy">
                                <i class="fa fa-fw fa-clipboard"></i> <?php echo _('Copy')?>
                            </button>
                            <button class="btn btn-default" id="btn_fullscreen">
                                <i class="fa fa-fw fa-expand"></i> <?php echo _('Fullscreen')?> <span class="hidden-xs">(F11)</span>
                            </button>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-xs-12" id="div_code">
                        <textarea id="text_code"><?php echo htmlspecialchars($source);?></textarea>
                    </div>
                </div>
            </div>
            </div>
            <?php } 
            require __DIR__.'/inc/footer.php';?>
        </div>
        
        <?php if($row[3]==7){?>
            <div class="modal fade" id="CEModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><?php echo _('Compile Info')?></h4>
                        </div>
                        <div class="modal-body">
                            <pre><?php echo htmlspecialchars($compileinfo)?></pre>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
        
        <div class="modal fade" id="UserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo _('User Profile')?></h4>
                    </div>
                    <div class="modal-body" id="user_status"></div>
                    <div class="modal-footer">
                        <form action="mail.php" method="post">
                            <input type="hidden" name="touser" id="input_touser">
                            <?php if(isset($_SESSION['user'])){?>
                                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> <?php echo _('Send Mail')?></button>
                            <?php }?>
                        </form>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            var textMode = <?php 
                if($LANG_NAME[$row[4]]=='GCC')
                    $smode = 'text/x-csrc';
                if($LANG_NAME[$row[4]]=='Pascal')
                    $smode = 'text/x-pascal';
                if ($LANG_NAME[$row[4]]=='QBASIC')
                    $smode = 'text/x-basic';
                else 
                    $smode = 'text/x-c++src';
                echo json_encode($smode);
            ?>;
            window.editorConfig = <?php echo json_encode(array('enabled' => $pref->edrmode != 'off', 'mode' => $pref->edrmode)) ?>;
        </script>

        <script src="assets_webpack/sourcecode.js"></script>

        <script src="/assets/js/clipboard.min.js"></script>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">

            var clipboard = new Clipboard('#btn_copy', {
                text: function(){
                    return editor.getValue();
                }
            });
            clipboard.on('success', function(e){
                $('#btn_copy').attr('title','<?php echo _('Copied!')?>');
                $('#btn_copy').tooltip('show');
                setTimeout("$('#btn_copy').tooltip('destroy')",800);
            });
            clipboard.on('error', function(e){
                $('#btn_copy').attr('title','<?php echo _('Failed...')?>');
                $('#btn_copy').tooltip('show');
                setTimeout("$('#btn_copy').tooltip('destroy')",800);
            });
            function show_user(usr){
                $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load('api/ajax_user.php?user_id='+usr);
                $('#input_touser').val(usr);
                $('#UserModal').modal('show');
                return false;
            };
            var sol_id=<?php echo $sol_id?>;
            $(document).ready(function(){
                $('#btn_osc').click(function(){
                    $.ajax({
                        type:"POST",
                        url:"api/ajax_sourcecode.php",
                        data:{op:'osc',id:sol_id},
                        success:function(msg){
                            if(/success/.test(msg))
                                location.reload();
                            else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        }
                    });
                });
                <?php if(check_priv(PRIV_PROBLEM) || check_priv(PRIV_SYSTEM)){?>
                    $('#btn_mark_mal').click(function(){
                    $.ajax({
                        type:"POST",
                        url:"api/ajax_sourcecode.php",
                        data:{"op":'mark_mal',"id":sol_id},
                        success:function(msg){
                            if(/success/.test(msg))
                                location.reload();
                            else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        }
                    });
                });
                <?php }?>
            });
        </script>
    </body>
</html>
