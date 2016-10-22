<?php 
require __DIR__.'/conf/ojsettings.php';
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
	require __DIR__.'/conf/database.php';
	$result=mysqli_query($con,"select user_id,time,memory,result,language,code_length,problem_id,public_code,malicious from solution where solution_id=$sol_id");
	$row=mysqli_fetch_row($result);
	if(!$row)
		die('No such solution.');
	$ret = sc_check_priv($row[6], $row[7], $row[0]);
	if($ret === TRUE)
		$allowed = TRUE;
	else{
		$allowed = FALSE;
		$info=$ret;
	}

	if($allowed){
		$result=mysqli_query($con,"select source from source_code where solution_id=$sol_id");
		if($tmp=mysqli_fetch_row($result))
			$source=$tmp[0];
		else
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
		if($t_night=='on') 
			echo '<link rel="stylesheet" href="/assets/css/codemirror.midnight.css">';
		else
			echo'<link rel="stylesheet" href="/assets/css/codemirror.eclipse.css">';
	?>
    <link rel="stylesheet" href="/assets/css/codemirror.css"> 
	<link rel="stylesheet" href="/assets/css/codemirror.fullscreen.css">
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
                    <div class="col-xs-12" style="font-size:16px">
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
                            <?php if(check_priv(PRIV_PROBLEM) || check_priv(PRIV_SYSTEM)){?>
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
                            <button class="btn btn-default" onclick="toggle_fullscreen(editor)">
								<i class="fa fa-fw fa-expand"></i> <?php echo _('Fullscreen')?> <span class="hidden-xs">(Ctrl+F11)</span>
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
		
		<script src="/assets/js/codemirror.js"></script>
		<script src="/assets/js/CodeMirror/addon/fullscreen.js"></script>
		<script src="/assets/js/CodeMirror/mode/clike.js"></script>
		<script src="/assets/js/CodeMirror/mode/pascal.js"></script>
		<script src="/assets/js/clipboard.min.js"></script>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript">
			var editor = CodeMirror.fromTextArea(document.getElementById('text_code'), {
				theme: "<?php 
				if($t_night=='on') 
					echo 'midnight'; 
				else
					echo 'eclipse'
				?>",
				mode: "<?php 
				if($LANG_NAME[$row[4]]=='GCC')
					echo 'text/x-csrc';
				if($LANG_NAME[$row[4]]=='Pascal')
					echo 'text/x-pascal';
				else 
					echo 'text/x-c++src'
				?>",
				lineNumbers: true,
				readOnly: 'nocursor',
				viewportMargin: Infinity
			});
			function toggle_fullscreen(cm){
				if(cm.getOption("fullScreen")){
					$('.navbar').css("z-index",1030);  
					cm.setOption("fullScreen", false);
				}else{
					$('.navbar').css("z-index",0);   
					cm.setOption("fullScreen", !cm.getOption("fullScreen"));
				}  
			};
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
				$(document).keydown(function(e){
					if(e.ctrlKey&&e.which==122)
						toggle_fullscreen(editor);
				});
			});
		</script>
	</body>
</html>