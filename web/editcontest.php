<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/func/contest.php';

if(!check_priv(PRIV_PROBLEM))
    include __DIR__.'/inc/403.php';
else if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
    $_SESSION['admin_retpage'] = $_SERVER['REQUEST_URI'];
    header("Location: admin_auth.php");
    exit();
}else{
    require __DIR__.'/lib/problem_flags.php';
    require __DIR__.'/conf/database.php';
    $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
    if(!isset($_GET['contest_id'])){
        $p_type='add';
        $inTitle=_('New Contest');
        $cont_id=1000;
        $result=mysqli_query($con,'select max(contest_id) from contest');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $cont_id=intval($row[0])+1;
    }else{
        $p_type='edit';
        $cont_id=intval($_GET['contest_id']);  
        $inTitle=_('Edit Contest')." #$cont_id";
        $query="select title,start_time,end_time,problems,description,source,judge_way,has_tex from contest where contest_id=$cont_id";
        $result=mysqli_query($con,$query);
        $row=mysqli_fetch_row($result);
        if(!$row)
            $info=_('There\'s no such contest');
        else{ 
            switch ($row[6]) {
                case 0:
                    $way='train';
                    break;
                case 1:
                    $way='cwoj';
                    break;
                case 2:
                    $way='acm-like';
                    break;
                case 3:
                    $way='oi-like';
                    break;
            }
        }
        $option_level=($row[7]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
        $option_hide=(($row[7]&PROB_IS_HIDE)?'checked':'');
    }

    $Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php require __DIR__.'/inc/head.php'; ?>

	<body>
		<?php require __DIR__.'/inc/navbar.php'; ?>
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
				<div class="collapse" id="showtools">
					<p><button class="btn btn-primary" id="btn_show"><?php echo _('Show Toolbar')?><i class="fa fa-fw fa-angle-right"></i></button></p>
				</div>
				<form action="#" method="post" id="edit_form" style="padding-top:10px">
					<input type="hidden" name="op" value="<?php echo $p_type?>">
					<input type="hidden" name="contest_id" value="<?php echo $cont_id?>">
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9" id="ctl_title">
							<label class="control-label" for="input_title">
								<?php echo _('Title')?>
							</label>
							<input type="text" class="form-control" name="title" id="input_title" value="<?php if($p_type=='edit') echo $row[0]?>">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9" id="ctl_probs">
							<label class="control-label" for="input_probs">
								<?php echo _('Problems (Format: a,b,c)')?>
							</label>
							<input type="text" class="form-control" name="problems" id="input_probs" value="<?php if($p_type=='edit'){ $prob_arr=unserialize($row[3]);echo implode(',', $prob_arr);}?>">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-6 col-sm-4" id="ctl_starttime">
                            <label class="control-label" for="input_starttime">
								<?php echo _('Start Time (yyyy-mm-dd hh:mm:ss)')?>
							</label>
							<input type="text" name="start_time" id="input_starttime" class="form-control" value="<?php if($p_type=='edit') echo $row[1]; else echo date("Y-m-d H:i:s",time())?>">
						</div>
						<div class="form-group col-xs-6 col-sm-4" id="ctl_endtime">
							<label class="control-label" for="input_endtime">
								<?php echo _('End Time (yyyy-mm-dd hh:mm:ss)')?>
							</label>
							<input type="text" name="end_time" id="input_endtime" class="form-control" value="<?php if($p_type=='edit') echo $row[2]; else echo date("Y-m-d H:i:s",time()+14400)?>">
						</div> 
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9">
							<label class="control-label" for="input_cmp">
								<?php echo _('Format')?>
							</label>
							<select class="form-control" name="judge" id="input_cmp">
								<option value="train"><?php echo _('Training')?></option>
								<option value="cwoj"><?php echo _('CWOJ')?></option>
								<option value="acm-like"><?php echo _('ACM-like')?></option>
								<option value="oi-like"><?php echo _('OI-like')?></option>
							</select>
							<?php if($p_type=='edit'){?>
								<script>
									$('#input_cmp').val("<?php echo $way?>");
								</script>
							<?php }?>
							<span id="input_cmp_help" class="help-block"></span>
						</div>
					</div>      
					<div class="row">
						<div class="form-group col-xs-6 col-sm-3">
							<label class="control-label" for="input_level">
								<?php echo _('Level')?>
							</label>
							<select class="form-control" name="option_level" id="input_level">
								<script>
									<?php if($p_type=='add'){?>
										for(var i=0;i<=<?php echo $level_max?>;i++){
											document.write('<option value="'+i+'">'+i+'</option>')
										}
									<?php }else{?>
										for(var i=0;i<=<?php echo $level_max?>;i++){
											if(i==<?php echo $option_level?>)
												document.write('<option selected value="'+i+'">'+i+'</option>')
											else
												document.write('<option value="'+i+'">'+i+'</option>')
										};
									<?php }?>
								</script>
							</select>
						</div>
						<div class="form-group col-xs-6 col-sm-4">
							<label class="control-label">
								<?php echo _('Options')?>
							</label>
							<div class="checkbox">
								<label>
									<input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_cont"><?php echo _('Hide')?>
								</label>
							</div>  
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9">
							<label class="control-label" for="input_des">
								<?php echo _('Description')?>
							</label>
							<textarea class="form-control col-xs-12" name="description" id="input_des" rows="13"><?php if($p_type=='edit') echo htmlspecialchars($row[4])?></textarea>
						</div>
					</div>       
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9">
							<label <label class="control-label" for="input_tags">
								<?php echo _('Tags')?>
							</label>
							<input class="form-control col-xs-12" type="text" name="source" id="input_tags" value="<?php if($p_type=='edit') echo htmlspecialchars($row[5])?>">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-sm-9">
							<div class="alert alert-danger collapse" id="alert_error"></div>  
							<button class="btn btn-primary" type="submit"><?php echo _('Submit')?></button>
						</div>
					</div>
				</form>
			<?php }
			require __DIR__.'/inc/footer.php';?>
		</div>
		
		<div class="html-tools">
			<div class="panel panel-default" id="tools">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-fw fa-code"></i> <?php echo _('HTML Toolbar')?></h3>
				</div>
				<div class="panel-body">
					<table class="table table-responsive table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th><?php echo _('Function')?></th>
								<th><?php echo _('Code')?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><button class="btn btn-default" id="tool_less"><?php echo _('Smaller than(&lt;)')?></button></td>
								<td>&amp;lt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_greater"><?php echo _('Greater than(&gt;)')?></button></td>
								<td>&amp;gt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_img"><?php echo _('Image')?></button></td>
								<td>&lt;img src=&quot;...&quot;&gt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_sup"><?php echo _('Superscript')?></button></td>
								<td>&lt;sup&gt;...&lt;/sup&gt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_sub"><?php echo _('Subscript')?></button></td>
								<td>&lt;sub&gt;...&lt;/sub&gt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_samp"><?php echo _('Monospace')?></button></td>
								<td>&lt;samp&gt;...&lt;/samp&gt;</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_inline"><?php echo _('Inline TeX')?></button></td>
								<td>[inline]...[/inline]</td>
							</tr>
							<tr>
								<td><button class="btn btn-default" id="tool_tex"><?php echo _('TeX')?></button></td>
								<td>[tex]...[/tex]</td>
							</tr>
						</tbody>
					</table>
					<div class="btn-group text-center" style="margin-top:10px">
						<button class="btn btn-success" id="btn_upload"><?php echo _('Upload Image')?></button>
						<button class="btn btn-primary" id="btn_hide"><?php echo _('Hide Toolbar')?><i class="fa fa-fw fa-angle-left"></i></button>
					</div>
				</div>
			</div>
		</div>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				var loffset=window.screenLeft+200,toffset=window.screenTop+200;
				function show_help(way){
					if(way=='train')
						$('#input_cmp_help').html('<?php echo get_judgeway_destext(0);?>');
					else if(way=='cwoj')
						$('#input_cmp_help').html('<?php echo get_judgeway_destext(1);?>');
					else if(way=='acm-like')
						$('#input_cmp_help').html('<?php echo get_judgeway_destext(2);?>');
					else if(way=='oi-like')
						$('#input_cmp_help').html('<?php echo get_judgeway_destext(3);?>');
				}
				(function(){
					show_help($('#input_cmp').val());
				})();
				$('#input_cmp').change(function(E){show_help($(E.target).val());});
				$('#btn_hide').click(function(){
					$('#tools').fadeOut();
					$('#showtools').fadeIn();
				});
				$('#btn_show').click(function(){
					$('#tools').fadeIn();
					$('#showtools').fadeOut();
				});
				$('#btn_upload').click(function(){
					window.open("upload.php",'upload_win2','left='+loffset+',top='+toffset+',width=400,height=300,toolbar=no,resizable=no,menubar=no,location=no,status=no');
				});
				$('#edit_form textarea').focus(function(e){cur=e.target;});
				$('#edit_form').submit(function(){
                    var b=false;
                    $('#alert_error').slideUp;
					if(!$.trim($('#input_title').val())){
                        $('#ctl_title').addClass('has-error');
						b=true;
                    }else
                        $('#ctl_title').removeClass('has-error');
                    if(!$.trim($('#input_probs').val())){
                        $('#ctl_probs').addClass('has-error');
						b=true;
                    }else
                        $('#ctl_probs').removeClass('has-error');
                    if(!$.trim($('#input_starttime').val())){
                        $('#ctl_starttime').addClass('has-error');
						b=true;
                    }else
                        $('#ctl_starttime').removeClass('has-error');
                    if(!$.trim($('#input_endtime').val())){
                        $('#ctl_endtime').addClass('has-error');
						b=true;
                    }else
                        $('#ctl_endtime').removeClass('has-error');
                    if(b)
                        $('html, body').animate({scrollTop:0}, '200');
                    else{
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_editcontest.php",
                            data:$('#edit_form').serialize(),
                            success:function(msg){
                                if(/success/.test(msg)) 
                                    window.location="contestpage.php?contest_id=<?php echo $cont_id?>";
                                else
                                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                                }
                        });
                    }
					return false;
				});
				$('#tools').click(function(e){
					if(!($(e.target).is('button')))
						return false;
					if(typeof(cur)=='undefined')
						return false;
					var op=e.target.id,slt=GetSelection(cur);
					if(op=="tool_greater")
						InsertString(cur,'&gt;');
					else if(op=="tool_less")
						InsertString(cur,'&lt;');
					else if(op=="tool_img"){
						var url=prompt('<?php echo _('Please enter the image link')?>:','');
						if(url)
							InsertString(cur,slt+'<img src="'+url+'">');
					}else if(op=="tool_inline"||op=="tool_tex"){
						op=op.substr(5);
						InsertString(cur,'['+op+']'+slt+'[/'+op+']');
					}else if(op=="btn_upload"||op=="btn_hide")
						return false;
					else{
						op=op.substr(5);
						InsertString(cur,'<'+op+'>'+slt+'</'+op+'>');
					}
					return false;
				});
			});
		</script>
	</body>
</html>
<?php }?>