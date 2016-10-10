<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

if(!check_priv(PRIV_PROBLEM) && !check_priv(PRIV_SYSTEM)){
	include __DIR__.'/inc/403.php';
}else if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
	$_SESSION['admin_retpage'] = $_SERVER['PHP_SELF'];
	header("Location: admin_auth.php");
	exit();
}else{
	require __DIR__.'/conf/database.php';
	$res=mysqli_query($con,'select content from news where news_id=0 limit 1');
	$index_text=($res && ($row=mysqli_fetch_row($res))) ? str_replace('<br>', "\n", $row[0]) : '';
	$res=mysqli_query($con,"select content from user_notes where id=0 limit 1");
	$category=($res && ($row=mysqli_fetch_row($res))) ? str_replace('<br>', "\n", $row[0]) : '';

$inTitle=_('Administration');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php require __DIR__.'/inc/head.php'; ?>
	<body>
		<?php require __DIR__.'/inc/navbar.php';?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<ul class="nav nav-pills" id="nav_tab">
						<li class="active"><a href="#home" data-toggle="tab"><i class="fa fa-fw fa-home"></i> <span class="hidden-xs"><?php echo _('Home')?></span></a></li>
						<?php if(check_priv(PRIV_SYSTEM)){?>
							<li><a href="#news" data-toggle="tab"><i class="fa fa-fw fa-newspaper-o"></i> <span class="hidden-xs"><?php echo _('News')?></span></a></li>
							<li><a href="#experience" data-toggle="tab"><i class="fa fa-fw fa-diamond"></i> <span class="hidden-xs"><?php echo _('Experience')?></span></a></li>
							<li><a href="#user" data-toggle="tab"><i class="fa fa-fw fa-users"></i> <span class="hidden-xs"><?php echo _('Users')?></span></a></li>
						<?php }?>
					</ul>
					<br>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="home">
							<div class="row">
								<div class="col-xs-12 col-sm-3">
									<h3 class="text-center"><?php echo _('Getting Started')?></h3><br>
									<ul class="nav nav-pills nav-stacked">
										<li><a href="editproblem.php"><i class="fa fa-fw fa-coffee"></i> <?php echo _('New Problem...')?></a></li>
										<li><a href="editcontest.php"><i class="fa fa-fw fa-compass"></i> <?php echo _('New Contest...')?></a></li>
										<li><a href="#" data-toggle="modal" data-target="#CategoryModal"><i class="fa fa-fw fa-th-list"></i> <?php echo _('Problem Categories...')?></a></li>
										<li><a href="#" data-toggle="modal" data-target="#RejudgeModal"><i class="fa fa-fw fa-refresh"></i> <?php echo _('Rejudge...')?></a></li>
									</ul>
								</div>
								<hr class="visible-xs">
								<div class="col-xs-12 col-sm-5">
									<h3 class="text-center"><?php echo _('Home')?></h3><br>
									<form action="#" method="post" id="form_index">
										<input type="hidden" name="op" value="update_index">
										<div class="form-group">  
											<textarea class="form-control" name="text" rows="10"><?php echo htmlspecialchars($index_text)?></textarea>
										</div>  
										<div class="alert alert-success collapse" id="alert_result"></div>
										<div class="pull-right">
											<input type="submit" class="btn btn-default" value="<?php echo _('Save')?>">
										</div>
									</form>
								</div>
								<hr class="visible-xs">
								<div class="col-xs-12 col-sm-4">
									<h3 class="text-center" id="meter_title"><?php echo _('System Information')?></h3>
									<br>
									<label>CPU:</label>
									<div class="progress">
										<div class="progress-bar progress-bar-striped active" id="pg_cpu"></div>
									</div>
									<label>RAM:</label>
									<div class="progress">
										<div class="progress-bar progress-bar-striped active" id="pg_mem"></div>
									</div>
									<label>Daemon:</label>
									<div id="pg_daemon"></div>
								</div>
							</div>
						</div>
						<?php if(check_priv(PRIV_SYSTEM)){?>
							<div class="tab-pane fade" id="news">
								<div class="row">
									<div class="col-xs-12">
										<div class="pull-right">
											<button class="btn btn-primary" id="new_news"><i class="fa fa-fw fa-file-text-o"></i> <?php echo _('Add News...')?></buttton>
										</div>
										<div id="table_news">
											<div class="alert alert-info col-sm-6"><i class="fa fa-circle-o-notch fa-spin"></i> <?php echo _('Loading...')?></div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="experience">
								<div class="row">
									<div class="col-xs-12 col-sm-6">
										<div id="table_experience_title"></div>
										<form action="admin.php" method="post" class="form-inline" id="form_experience_title">
											<input type="text" id="input_experience" name="experience" class="form-control" placeholder="<?php echo _('Experience')?>&nbsp;&ge;">&nbsp;&nbsp;
											<input type="text" id="input_experience_title" name="title" class="form-control" placeholder="<?php echo _('Title')?>">&nbsp;&nbsp;
											<input type="submit" class="btn btn-default" value="<?php echo _('Add')?>">
											<input type="hidden" name="op" value="add_experience_title">
										</form>
									</div>
									<hr class="visible-xs">
									<div class="col-xs-12 col-sm-6">
										<form action="admin.php" method="post" id="form_level_experience">
											<div id="table_level_experience"></div>
											<input type="submit" class="btn btn-default" value="<?php echo _('Save')?>">
											<input type="hidden" name="op" value="update_level_experience">
										</form>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="user">
								<div class="row">
									<div class="col-xs-4 pull-left">
										<div class="btn-group">
											<a class="btn btn-default" id="btn_emailall"><i class="fa fa-fw fa-envelope"></i> <?php echo _('Send Email to All')?></a>
										</div>
									</div>
									<div class="col-xs-8 col-sm-5 col-md-3 pull-right">
										<div class="form-group has-feedback">
											<input class="form-control" id="user_q" name="q" type="text" placeholder="<?php echo _('Search User...')?>">
											<span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive" id="table_usr"></div>
									</div>
								</div>
								<div class="row">
									<ul class="pager">
										<li>
											<a class="pager-pre-link shortcut-hint" title="Alt+A" href="#" id="usr_pre">
												<i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?>
											</a>
										</li>
										<li>
											<a class="pager-next-link shortcut-hint" title="Alt+D" href="#" id="usr_nxt">
												<?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i>
											</a>
										</li>
									</ul>
								</div>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
			<?php require __DIR__.'/inc/footer.php';?>
		</div>
      
		<div class="modal fade" id="CategoryModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><?php echo _('Problem Categories')?></h4>
					</div>
					<form action="#" method="post" id="form_category"> 
						<div class="modal-body">
							<div class="form-group">
								<textarea class="form-control" id="input_category" rows="16" name="source" placeholder="<?php echo _('Please input HTML code...')?>"><?php echo $category?></textarea>
							</div>
							<div class="alert alert-danger collapse" id="addcategory_res"></div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" type="submit"><?php echo _('Save')?></button>
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
						</div>
					</form> 
				</div>
			</div>
		</div>
    
		<div class="modal fade" id="RejudgeModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><?php echo _('Rejudge')?></h4>
					</div>
					<form action="#" method="post" id="form_rejudge">
						<input type="hidden" name="op" value="rejudge">
						<div class="modal-body">
							<div class="form-group">
								<label><?php echo _('Please enter the Problem ID:')?></label>
								<input class="form-control" id="input_rejudge" type="number" name="problem" placeholder="1000~9999">
							</div>
							<div class="alert alert-danger collapse" id="rejudge_res"></div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" type="submit"><?php echo _('Rejudge')?></button>
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
						</div>
					</form> 
				</div>
			</div>
		</div>
      
		<?php if(check_priv(PRIV_SYSTEM)){?>
			<div class="modal fade" id="NewsModal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"></h4>
						</div>
						<form action="#" method="post" id="form_news">
							<input type="hidden" id="news_op" name="op" value="add_news">
							<input type="hidden" id="news_id" name="news_id" value="0">  
							<div class="modal-body">
								<div class="form-group">
									<input type="text" class="form-control" id="input_newstitle" name="title" placeholder="<?php echo _('Please enter News Title...')?>">
								</div>
								<div class="form-group">
									<textarea class="form-control" id="input_newscontent" rows="14" name="content" placeholder="<?php echo _('Please enter News Content (Optional)...')?>"></textarea>
								</div>
								<div class="alert alert-danger collapse" id="news_res"></div>
							</div>
							<div class="modal-footer">
								<div class="checkbox" style="display:inline-block">
									<label><input type="checkbox" name="importance" id="is_top"><?php echo _('Sticky')?></label>
								</div>
								<div class="btn-group pull-left">
									<button class="pull-left btn btn-danger collapse" id="btn_delnews"><?php echo _('Delete')?></button>
									<button class="pull-left btn btn-default" id="btn_upload"><?php echo _('Upload Image')?></button>
									<button class="pull-left btn btn-default dropdown-toggle" id="btn_newspriv" data-toggle="dropdown"><?php echo _('Need Privilege')?> <span class="caret"></span></button>
									<ul class="dropdown-menu dropdown-menu-right">
										<li><a href="#0"><input type="checkbox" id="news_0" name="0"> <?php echo _('Insider')?></a></li>
										<li><a href="#1"><input type="checkbox" id="news_1" name="1"> <?php echo _('Source')?></a></li>
										<li><a href="#2"><input type="checkbox" id="news_2" name="2"> <?php echo _('Problems')?></a></li>
									</ul>
								</div>
								<button class="btn btn-primary" type="submit"><?php echo _('Save')?></button>
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
							</div>
						</form> 
					</div>
				</div>
			</div>
      
			<div class="modal fade" id="EmailModal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"></h4>
						</div>
						<form action="#" method="post" id="form_email">
							<input type="hidden" name="op" id="email_op" value="sendemail">
							<input type="hidden" id="email_touser" name="to_user" value="">  
							<div class="modal-body">
								<div class="form-group">
									<input type="text" class="form-control" id="input_emailtitle" name="title" placeholder="<?php echo _('Please enter Email Title...')?>">
								</div>
								<div class="form-group">
									<textarea class="form-control" id="input_emailcontent" rows="14" name="content" placeholder="<?php echo _('Please enter Email Content...')?>"></textarea>
								</div>
								<div class="alert alert-danger collapse" id="email_res"></div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-primary" type="submit"><?php echo _('Send')?></button>
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
							</div>
						</form> 
					</div>
				</div>
			</div>
      
			<div class="modal fade" id="PrivModal">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"></h4>
						</div>
						<form action="#" method="post" id="form_priv">
							<input type="hidden" name="op" value="update_priv">
							<input type="hidden" id="priv_uid" name="user_id" value="">  
							<div class="modal-body">
								<div class="checkbox">
									<label><input type="checkbox" name="0" id="chk_insider"> <?php echo _('Insider'),'(',PRIV_INSIDER,')'?></label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="1" id="chk_source"> <?php echo _('Source'),'(',PRIV_SOURCE,')'?></label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="2" id="chk_problem"> <?php echo _('Problems'),'(',PRIV_PROBLEM,')'?></label>
								</div>
								<div class="checkbox">
									<label><input type="checkbox" name="3" id="chk_system"> <?php echo _('System'),'(',PRIV_SYSTEM,')'?></label>
								</div>
								<div class="alert alert-danger collapse" id="priv_res"></div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-primary" type="submit"><?php echo _('Save')?></button>
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
							</div>
						</form> 
					</div>
				</div>
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
		<?php }?>
					
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript">
		<?php if(check_priv(PRIV_SYSTEM)){?>
			var getlevellist=function(){$('#table_level_experience').load('api/ajax_admin.php',{op:'list_level_experience'});};
			var gettitlelist=function(){$('#table_experience_title').load('api/ajax_admin.php',{op:'list_experience_title'});};
			var getnewslist=function(){$('#table_news').load('api/ajax_admin.php',{op:'list_news'});};
			var getusrlist=function(e=1,q=''){$('#table_usr').load('api/ajax_admin.php',{op:'list_usr',page_id:e,q:q});};
			var cnt=-1,kw='',upid=1;
		<?php }?>
		function update_chart(){
			$.getJSON('api/ajax_usage.php',function(data){
				if(data&&"number"==typeof(data.cpu)){
					$('#pg_cpu').css('width',data.cpu+'%');
					$('#pg_cpu').html(data.cpu+'%');
					if(data.cpu<=80){
						$('#pg_cpu').removeClass('progress-bar-danger');
						$('#pg_cpu').addClass('progress-bar-success');
					}else{
						$('#pg_cpu').removeClass('progress-bar-success');
						$('#pg_cpu').addClass('progress-bar-danger');  
					}
				}
				if(data&&"number"==typeof(data.mem)){
					$('#pg_mem').css('width',data.mem+'%');
					$('#pg_mem').html(data.mem+'%')
					if(data.mem<=80) {
						$('#pg_mem').removeClass('progress-bar-danger');
						$('#pg_mem').addClass('progress-bar-success');
					}else{
						$('#pg_mem').removeClass('progress-bar-success');
						$('#pg_mem').addClass('progress-bar-danger');  
					}
				}
				if(data&&"number"==typeof(data.daemon)){
					if(data.daemon==1)
						$('#pg_daemon').html('<font color=green><?php echo _('Running...')?></font>');
					else 
						$('#pg_daemon').html('<font color=red><?php echo _('Not Running...')?></font>');
				}
				setTimeout('update_chart()',3000);
			});
		}
			
		$(document).ready(function(){
			$(function(){
				var hash = window.location.hash;
				hash && $('ul.nav a[href="' + hash + '"]').tab('show');
				if(hash=='#news')
					getnewslist();
				else if(hash=='#user')
					getusrlist();
				else if(hash=='#experience'){
					getlevellist();
					gettitlelist();
				}
				$('#nav_tab a').click(function (e){
					if(this.hash=='#news')
						getnewslist();
					else if(this.hash=='#user')
						getusrlist(upid,kw);
					else if(this.hash=='#experience'){
						getlevellist();
						gettitlelist();
					}
					$(this).tab('show');
					window.location.hash = this.hash;
				});
			});
			update_chart();
			$('#form_rejudge').submit(function(){
				$('#rejudge_res').hide();
				if($.trim($('#input_rejudge').val())){
					$('#input_rejudge').removeClass('error');   
					$.ajax({
						type:"POST",
						url:"api/ajax_submit.php",
						data: $('#form_rejudge').serialize(),
						success:function(msg){
							if(msg=='success')
								$('#RejudgeModal').modal('hide');
							else 
								$('#rejudge_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
						}
					});
				}else
					$('#input_rejudge').addClass('error');  
				return false;
			});
			<?php if(check_priv(PRIV_SYSTEM)){?>
				$('#form_category').submit(function(){
					$('#addcategory_res').hide();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:{"op":'update_category',"content":$.trim($('#input_category').val())},
						success:function(msg){
							if(msg=='success')
								$('#CategoryModal').modal('hide');
							else 
								$('#addcategory_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
						}
					});
					return false;
				});
				$('#table_news').click(function(E){
					E.preventDefault();
					var news_title,news_content;
					var jq=$(E.target);
					if(jq.is('i')){
						var jq_id=jq.parent().parent().prev().prev().prev();
						cnt = jq_id.html();
						$.ajax({
							type:"POST",
							url:"api/ajax_admin.php",
							data:{op:'get_news',news_id:jq_id.html()},
							success:function(data){
								var obj=eval("("+data+")");
								$('#news_op').val('edit_news');
								$('#news_id').val(cnt);
								$('#NewsModal .modal-title').html('<?php echo _('Edit News')?>');
								$('#NewsModal').modal('show');
								if(obj.importance==0) 
									var a=false;
								else
									a=true;
								$('#is_top').prop('checked', a);
								$('#news_0').prop('checked', obj.priv&<?php echo PRIV_INSIDER?>);
								$('#news_1').prop('checked', obj.priv&<?php echo PRIV_SOURCE?>);
								$('#news_2').prop('checked', obj.priv&<?php echo PRIV_PROBLEM?>);
								$('#btn_delnews').show();
								$('#input_newstitle').val(obj.title);
								$('#input_newscontent').val(obj.content);
							}
						});
					}
					return false;
				});
				$('#NewsModal .dropdown-menu a').click(function(E){
					E.preventDefault();
					var jq=$('#news_'+$(E.target).attr('href').substring(1,2));
					if(jq.prop('checked'))
						jq.prop('checked',false);
					else
						jq.prop('checked',true);
					return false;
				});
				$('#new_news').click(function(){
					$('#NewsModal .modal-title').html('<?php echo _('Add News')?>');
					$('#news_op').val('add_news');
					$('#input_newstitle').val('');
					$('#input_newscontent').val('');
					$('#btn_delnews').hide();
					$('#is_top').prop('checked',false);
					$('#NewsModal').modal('show');
				});
				$('#form_news').submit(function(){
					var title,content;
					$('#news_res').hide();
					var a=false;
					if(!$.trim($('#input_newstitle').val())) {
						$('#input_newstitle').addClass('error');
						a=true;
					}else{
						$('#input_newstitle').removeClass('error');
					}
					if(!a){
						$.ajax({
							type:"POST",
							url:"api/ajax_admin.php",
							data: $('#form_news').serialize(),
							success:function(msg){
								if(msg=='success') $('#NewsModal').modal('hide');
								else $('#news_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
							}
						});
					}
					getnewslist();
					return false;
				});
				$('#btn_delnews').click(function(){
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:{"op":'del_news',"news_id":cnt},
						success:function(msg){
							if(msg=='success')
								$('#NewsModal').modal('hide');
							else
								$('#news_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).show();
						}
					});
					getnewslist();
					return false;
				});
				$('#table_experience_title').click(function(E){
					E.preventDefault()
					var $i=$(E.target);
					if($i.is('i.fa-remove')){
						var id=$i.data('id');
						$.post('api/ajax_admin.php',{'op':'del_title','id':id},function(){
							gettitlelist();
						})
					}
				});
				$('#form_experience_title').submit(function(E){
					E.preventDefault();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:$(this).serialize(),
						success:gettitlelist
					});
				});
				$('#form_level_experience').submit(function(E){
					E.preventDefault();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:$(this).serialize(),
						success:getlevellist
					});
				});
				$('#btn_emailall').click(function(){
					$('#email_op').val('sendemail_all');
					$('#EmailModal .modal-title').html('<?php echo _('Send Email: All Users')?>');
					$('#email_res').hide();
					$('#EmailModal').modal('show');
				});
				$('#table_usr').click(function(E){
					E.preventDefault();
					var jq=$(E.target);
					if(jq.is('a')){
						var uid=jq.parents('tr').first().children().first().next().children().children().contents().text();
						switch($(jq).attr('href')){
							case '#email':
								$('#email_op').val('sendemail');
								$('#email_touser').val(uid);
								$('#EmailModal .modal-title').html('<?php echo _('Send Email')?>: '+uid);
								$('#email_res').hide();
								$('#EmailModal').modal('show');
							break;
							case '#priv':
								$('#priv_uid').val(uid);
								$('#PrivModal .modal-title').html('<?php echo _('Edit Privilege')?>: '+uid);
								$('#priv_res').hide();
								var p=jq.parents('tr').first().children().first().next().next().children('span').contents().text();
								$('#chk_insider').prop('checked', p&<?php echo PRIV_INSIDER?>);
								$('#chk_source').prop('checked', p&<?php echo PRIV_SOURCE?>);
								$('#chk_problem').prop('checked', p&<?php echo PRIV_PROBLEM?>);
								$('#chk_system').prop('checked', p&<?php echo PRIV_SYSTEM?>);
								$('#PrivModal').modal('show');
							break;
							case '#del':
								$.ajax({
									type:"POST",
									url:"api/ajax_admin.php",
									data:{op:'toggle_usr',user_id:uid},
									success:getusrlist(upid,kw)
								});
							break;
							case '#linkU':
								$('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load("api/ajax_user.php?user_id="+uid).scrollTop(0);
								$('#input_touser').val(uid);
								$('#UserModal').modal('show');
							break;
						}
					}
					return false;
				});
				$('#usr_pre').click(function(){
					if(upid>1){
						upid--;
						getusrlist(upid,kw);
					}
					return false;
				});
				$('#usr_nxt').click(function(){
					if($('#table_usr').children('table').length){
						upid++;
						getusrlist(upid,kw);
					}
					return false;
				});
				$('#user_q').on("change keyup paste",function(){
					kw=$('#user_q').val();
					upid=1;
					getusrlist(upid,kw);
				});
				$('#btn_upload').click(function(){
					var loffset=window.screenLeft+200;
					var toffset=window.screenTop+200;
					window.open("upload.php",'upload_win2','left='+loffset+',top='+toffset+',width=400,height=300,toolbar=no,resizable=no,menubar=no,location=no,status=no');
					return false;
				});
				$('#form_index').submit(function(E){
					E.preventDefault();
					$('#alert_result').hide();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:$('#form_index').serialize(),
						success:function(msg){
							if(/success/.test(msg)){
								$('#alert_result').removeClass("alert-danger");
								$('#alert_result').addClass("alert-success");
								$('#alert_result').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Saved successfully!')?>').slideDown();
							}else{
								$('#alert_result').removeClass("alert-success");
								$('#alert_result').addClass("alert-danger");
								$('#alert_result').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
							}
						}
					});
					return false;
				});
				$('#form_email').submit(function(E){
					E.preventDefault();
					$('#email_res').removeClass('alert-danger').addClass('alert-info').html('<i class="fa fa-circle-o-notch fa-fw fa-spin"></i> <?php echo _('Sending...')?>').slideDown();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:$('#form_email').serialize(),
						success:function(msg){
							if(/success/.test(msg)) 
								$('#EmailModal').modal('hide');
							else
								$('#email_res').addClass('alert-danger').removeClass('alert-info').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
						}
					});
					return false;
				});
				$('#form_priv').submit(function(E){
					E.preventDefault();
					$('#priv_res').hide();
					$.ajax({
						type:"POST",
						url:"api/ajax_admin.php",
						data:$('#form_priv').serialize(),
						success:function(msg){
							if(/success/.test(msg)){
								$('#PrivModal').modal('hide');
								getusrlist(upid,kw);
							}else
								$('#priv_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
						}
					});
					return false;
				});
			<?php }?>
			});
		</script>
	</body>
</html>
<?php }?>