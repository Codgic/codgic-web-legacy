<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

if(!isset($_SESSION['user']))
    include __DIR__.'/inc/403.php';
else{
    require __DIR__.'/lib/problem_flags.php';
    require __DIR__.'/conf/database.php';
    if(!isset($_GET['wiki_id'])){
        $p_type='add';
        $inTitle=_('New Wiki');
        $wiki_id=1;
        $result=mysqli_query($con,'select max(wiki_id) from wiki');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $wiki_id=intval($row[0])+1;
    }else{
        $p_type='edit';
        $wiki_id=intval($_GET['wiki_id']);
        $inTitle=_('Edit Wiki')." #$wiki_id";
        if($wiki_id<1)
            $info=_('There\'s no such wiki');
        else{
            $query="select title,content,tags,privilege,defunct from wiki where wiki_id=$wiki_id and is_max='Y'";
            $result=mysqli_query($con,$query);
            $row=mysqli_fetch_row($result);
            if(!$row)
                $info=_('There\'s no such wiki');
            else{
                if($row[3]==1)
                    $option_hide=1;
                else
                    $option_hide=0;
            }
        }
    }

$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php require __DIR__.'/inc/head.php'; ?>
	<body>
		<?php
			//Load CodeMirror
			if($pref->edrmode!='off'){
				echo '<link rel="stylesheet" href="/assets/css/codemirror.css" type="text/css" />';
				echo '<link rel="stylesheet" href="/assets/css/codemirror.fullscreen.css" type="text/css" />';
				//Load CodeMirror Theme
				if($t_night=='off') 
					echo '<link rel="stylesheet" href="/assets/css/codemirror.eclipse.css" type="text/css" />';
				else
					echo '<link rel="stylesheet" href="/assets/css/codemirror.midnight.css" type="text/css" />';
			}
			require __DIR__.'/inc/navbar.php';
		?>
		<div class="container edit-page">
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
				<form action="#" method="post" id="edit_form" style="padding-top:10px">
					<input type="hidden" name="op" value="<?php echo $p_type?>">
					<input type="hidden" name="wiki_id" value="<?php echo $wiki_id?>">
					<div class="row">
						<div class="form-group col-xs-12">
							<label>
								<?php echo _('Title')?>
							</label>
							<input type="text" class="form-control" name="title" id="input_title" value="<?php if($p_type=='edit') echo $row[0]?>">
						</div>
					</div>
                    <?php if(check_priv(PRIV_PROBLEM)){?>
					<div class="row">
						<div class="form-group col-xs-6 col-sm-4">
							<label>
								<?php echo _('Options')?>
							</label>
							<div class="checkbox">
								<label>
									<input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_cont"><?php echo _('Hide')?>
								</label>
							</div>  
						</div>
					</div>
                    <?php }?>
					<div class="row">
						<div class="form-group col-xs-12">
							<label>
								<?php echo _('Content')?>
							</label>
							<textarea class="form-control col-xs-12" name="content" rows="20" id="detail_input"><?php if($p_type=='edit') echo htmlspecialchars($row[1])?></textarea>
                            <?php if($pref->edrmode=='vim') echo '<samp>',_('Command: '),'<span id="vim_cmd"></span></samp>'?>
						</div>
					</div>       
					<div class="row">
						<div class="form-group col-xs-12">
							<label>
								<?php echo _('Tags')?>
							</label>
							<input class="form-control col-xs-12" type="text" name="tags" value="<?php if($p_type=='edit') echo htmlspecialchars($row[2])?>">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12">
							<div class="alert alert-danger collapse" id="alert_error"></div>  
							<button class="btn btn-primary" type="submit"><?php echo _('Submit')?></button>
						</div>
					</div>
				</form>
			<?php }
			require __DIR__.'/inc/footer.php';?>
		</div>
		
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <?php //Load CodeMirror
			if($pref->edrmode!='off'){
				echo '<script src="/assets/js/codemirror.js"></script>';
				echo '<script src="/assets/js/CodeMirror/addon/placeholder.js"></script>';
				echo '<script src="/assets/js/CodeMirror/addon/fullscreen.js"></script>';
                echo '<script src="/assets/js/CodeMirror/addon/overlay.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/markdown.js"></script>';
				echo '<script src="/assets/js/CodeMirror/mode/gfm.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/clike.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/pascal.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/css.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/javascript.js"></script>';
                echo '<script src="/assets/js/CodeMirror/mode/htmlmixed.js"></script>';
				if($pref->edrmode!='default')
					echo '<script src="/assets/js/CodeMirror/addon/'.$pref->edrmode.'.js"></script>';
			}
		?>
		<script type="text/javascript">
			$(document).ready(function(){
                var editor = CodeMirror.fromTextArea(document.getElementById('detail_input'),{
					theme: "<?php if($t_night=='on') echo 'midnight'; else echo 'eclipse'?>",
					mode: "gfm",
					<?php
						if($pref->edrmode!='default'){
							echo 'keyMap:"'.$pref->edrmode.'",';
						echo 'showCursorWhenSelecting: true,';
						}
					?>
					lineNumbers:true,
                    viewportMargin: Infinity,
					extraKeys:{
						"Ctrl-F11": function(cm){
							if(cm.getOption("fullScreen")){
								toggle_fullscreen(1);
								cm.setOption("fullScreen",false);
							}else{
								toggle_fullscreen(0);  
								cm.setOption("fullScreen", !cm.getOption("fullScreen"));
							}  
						},
					}
				});
				<?php if($pref->edrmode=='vim'){?>
					CodeMirror.on(editor,'vim-keypress',function(key){
						$('#vim_cmd').html(key);
					});
					CodeMirror.on(editor,'vim-command-done',function(){
						$('#vim_cmd').html('');
					});
				<?php }?>
				function toggle_fullscreen(e){
					if(e == 0){
						$('#submit_dialog').css({
							'width': '101%','height': '100%','margin': '0','padding': '0'
						});
						$('#submit_content').css({
							'height': 'auto','min-height': '100%','border-radius': '0'
						});
					}else{
						$('#submit_dialog').css({
							'width': '','height': '','margin': '','padding': ''
						});
						$('#submit_content').css({
							'height': '','min-height': '','border-radius': ''
						});
					}
				}
				$('#edit_form textarea').focus(function(e){cur=e.target;});
				$('#edit_form input').blur(function(e){
					e.target.value=$.trim(e.target.value);
					var o=$(e.target);
					if(!e.target.value)
						o.addClass('error');
					else
						o.removeClass('error');
				});
				$('#edit_form').submit(function(){
					var str=$('#input_title').val();
					if(!str||str==''){
						$('html, body').animate({scrollTop:0}, '200');
						return false;
					}
					$.ajax({
						type:"POST",
						url:"api/ajax_editwiki.php",
						data:$('#edit_form').serialize(),
						success:function(msg){
							if(/success/.test(msg)) 
								window.location="wikipage.php?wiki_id=<?php echo $wiki_id?>";
							else
								$('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
							}
					});
					return false;
				});
			});
		</script>
	</body>
</html>
<?php }?>