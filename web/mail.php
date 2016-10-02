<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/lib/mail_flags.php';

if(isset($_GET['page_id']))
	$page_id=intval($_GET['page_id']);
else
	$page_id=1;

if(isset($_GET['mailbox'])){
    $mailbox=intval($_GET['mailbox']);
    if($mailbox<1||$mailbox>3){
        header("Location: mail.php");
        exit();
    }
}
else
    $mailbox=1;

if(isset($_GET['starred']))
	$cond_starred='and (flags &'.MAIL_FLAG_STAR.')';
else
	$cond_starred='';

if(!isset($_SESSION['user']))
	$info = _('Please login first');
else{
	require __DIR__.'/conf/database.php';
	$user_id=$_SESSION['user'];
	if($mailbox==1){
		$row=mysqli_fetch_row(mysqli_query($con,"select count(1) from mail where to_user='$user_id' and defunct='N'"));
		$result=mysqli_query($con,"select mail_id,title,from_user,new_mail,in_date,flags,usremail from mail LEFT JOIN (select user_id as uid,email as usremail from users) as fuckzk on (uid=from_user) where to_user='$user_id' and UPPER(defunct)='N' $cond_starred order by mail_id desc limit ".(($page_id-1)*20).",20");
    }else if($mailbox==2){
		$row=mysqli_fetch_row(mysqli_query($con,"select count(1) from mail where from_user='$user_id' and UPPER(defunct)='N' $cond_starred"));
		$result=mysqli_query($con,"select mail_id,title,to_user,new_mail,in_date,flags,usremail from mail LEFT JOIN (select user_id as uid,email as usremail from users) as fuckzk on (uid=to_user) where from_user='$user_id' and UPPER(defunct)='N' $cond_starred order by mail_id desc limit ".(($page_id-1)*20).",20");
    }else if($mailbox==3){
		$row=mysqli_fetch_row(mysqli_query($con,"select count(1) from mail where to_user='$user_id' and UPPER(defunct)='Y' $cond_starred"));
		$result=mysqli_query($con,"select mail_id,title,from_user,new_mail,in_date,flags,usremail from mail LEFT JOIN (select user_id as uid,email as usremail from users) as fuckzk on (uid=from_user) where to_user='$user_id' and UPPER(defunct)='Y' $cond_starred order by mail_id desc limit ".(($page_id-1)*20).",20");
    }
    $maxpage=intval($row[0]/20)+1;
    
    if($page_id<1||$page_id>$maxpage){
        header("Location: mail.php");
        exit();
    }
}

function get_next_link(){
	global $cond_starred,$page_id;
	parse_str($_SERVER["QUERY_STRING"],$arr); 
	if(!empty($cond_starred))
		$arr['star']=1;
	$arr['page_id']=$page_id+1;
	return http_build_query($arr);
}

function get_pre_link(){
	global $cond_starred,$page_id;
	parse_str($_SERVER["QUERY_STRING"],$arr); 
	if(!empty($cond_starred))
		$arr['star']=1;
	$arr['page_id']=$page_id-1;
	return http_build_query($arr); 
}

$inTitle=_('Mails');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php require __DIR__.'/inc/head.php';?>  
	<body>
		<?php require __DIR__.'/inc/navbar.php'; ?>
		<div class="container">
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
						<div class="btn-group">
							<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
								<span id="mailbox_text"></span> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="mail.php" id="slt_1"><i class="fa fa-fw fa-inbox"></i> <?php echo _('Inbox')?></a></li>
								<li><a href="mail.php?mailbox=2" id="slt_2"><i class="fa fa-fw fa-rocket"></i> <?php echo _('Sent')?></a></li>
								<li><a href="mail.php?mailbox=3" id="slt_3"><i class="fa fa-fw fa-trash"></i> <?php echo _('Trash')?></a></li>
							</ul>
						</div>
						<div class="btn-group">
							<button id="btn_newmail" class="btn btn-default shortcut-hint" title="Alt+N"><i class="fa fa-fw fa-send"></i> <?php echo _('Send...')?></button>
							<?php
								if($mailbox==1){
									if(!isset($_GET['starred'])){
										$star_mode=0;?>
										<button id="btn_star" class="btn btn-default"><i class="fa fa-fw fa-star-o"></i> <?php echo _('Show starred')?></button>
									<?php }else{
										$star_mode=1;?>
										<button id="btn_star" class="btn btn-default"><i class="fa fa-fw fa-star"></i> <?php echo _('Show all')?></button>
									<?php }
								}else 
									$star_mode=0;
								?>
						</div>
						<hr>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12" id="maillist">
						<ul class="list-unstyled">
						<?php if(mysqli_num_rows($result)==0){?>
							<div class="text-center none-text none-center">
								<p><i class="fa fa-meh-o fa-4x"></i></p>
								<p>
									<b>Whoops</b>
									<br>
									<?php echo _('Looks like there\'s nothing here')?>
								</p>
							</div>
						<?php }else{
							while($row=mysqli_fetch_row($result)){
								if(!function_exists("get_gravatar"))
									require __DIR__.'/func/userinfo.php';
								echo '<li class="mail-item" ',(($row[3]&&$mailbox==1) ? 'style="background-color: #FCF8E3;"' : ''),' id="mail',$row[0],'">';
								?>
								<div class="mail-container">
									<div class="mail-title">
										<a href="javascript:void(0)" onclick="return show_user('<?php echo $row[2]?>')"><img src="<?php echo get_gravatar($row[6],40)?>" class="img-circle" width="40" height="40"></a>
										<?php echo ' <a href="#title" class="msg-title">',htmlspecialchars($row[1]),'</a>';?>
									</div>
									<div class="mail-info">
										<strong><a href="javascript:void(0)" onclick="return show_user('<?php echo $row[2]?>')"><?php echo $row[2],'</a></strong> ',_('Date: '),substr($row[4],0,10);
										if($mailbox==1){?>
											<a href="#star" style="text-decoration:none">
												<i class="<?php echo ($row[5]&MAIL_FLAG_STAR)?'fa fa-star':'fa fa-star-o'?> fa-2x text-warning"></i>
											</a>
										<?php }?>
									</div>
									<div style="clear:both"></div>
									<div class="mail-content">
										<div class="mail-op">
											<?php
												echo _('Recieved Date: '),substr($row[4],-8);
												if($mailbox==1){?>
													<div class="btn-group">
														<a href="#rep" class="btn btn-sm btn-default"><i class="fa fa-fw fa-reply"></i> <?php echo _('Reply')?></a>
														<a href="#del" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-trash"></i> <?php echo _('Delete')?></a>
													</div>
											<?php }else if($mailbox==2){?>
												<a href="#edit" class="btn btn-sm btn-default"><i class="fa fa-fw fa-pencil"></i> <?php echo _('Edit')?></a>
												<span class="pull-right">
													<?php
														if($row[3]==0)
															echo _('Read');
														else
															echo _('Unread');
													?>
												</span>
											<?php }else if($mailbox==3){?>
												<a href="#del" class="btn btn-sm btn-danger"><i class="fa fa-fw fa-undo"></i> <?php echo _('Recover')?></a>
											<?php }?>
										</div>
										<pre></pre>
									</div>
								</div>
							</li>
						<?php 
							}
						}
						?>
						</ul>
					</div>
				</div>  
				<div class="row">
					<ul class="pager">
						<li>
							<a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="mail.php?'.htmlspecialchars(get_pre_link()).'"';?>>
							<i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?></a>
						</li>
						<li>
							<a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo 'href="mail.php?'.htmlspecialchars(get_next_link()).'"';?>>
							<?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
						</li>
					</ul>
				</div>
                
				<div class="modal fade" id="MailModal">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title" id="send_title"><?php echo _('Send mail')?></h4>
							</div>
							<form action="#" method="post" id="send_form">
								<div class="modal-body">
									<div class="row">
										<div class="form-group col-xs-4">
											<label>
												<?php echo _('To')?>
											</label>
											<input type="text" class="form-control" id="input_to" name="touser">
										</div>
										<div class="form-group col-xs-8">
											<label>
												<?php echo _('Title')?>
											</label>
											<input type="text" class="form-control" id="input_title" name="title">
										</div>
										<div class="form-group col-xs-12">
											<label>
												<?php echo _('Content')?>
											</label>
											<textarea class="form-control" id="input_content" rows="10" name="content"></textarea>
										</div>
										<div class="form-group col-xs-12">
											<div class="alert alert-danger collapse" id="send_result"></div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a href="#" class="btn btn-primary shortcut-hint" title="Alt+S" id="send_btn"><?php echo _('Send')?></a>
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
								<input type="hidden" name="touser" id="input_touser">
								<button class="btn btn-default pull-left" id="btn_mnewmail"><i class="fa fa-fw fa-envelope-o"></i> <?php echo _('Send mail')?></button>
								<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
							</div>
						</div>
					</div>
				</div>
			<?php }
			require __DIR__.'/inc/footer.php';?>
		</div>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript"> 
            var op,mailid;
            function show_user(usr){
                $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load('api/ajax_user.php?user_id='+usr);
                $('#input_touser').val(usr);
                $('#UserModal').modal('show');
                return false;
            };
			$(document).ready(function(){
                $('#mailbox_text').html($("#slt_<?php echo $mailbox?>").html());
				$('#maillist').click(function(E){
					var $a;
					if($(E.target).is('i'))
						$a=$(E.target).parent();
					else if(typeof(E.target.href)!='undefined')
						$a=$(E.target);
					else
						return;
					var j=$a.attr('href'),k,content;
					switch(j.substr(j.lastIndexOf('#')+1)){
						case 'title':
							k=$a.parents('.mail-container'); 
							mailid=k.parent().css('background-color','').attr('id').substr(4);
							content=k.children('.mail-content');
							if(content.is(":hidden")){
								$.get('api/ajax_mailfunc.php?op=show&mail_id='+mailid,function(data){
									if(typeof(window.fix_ie_pre)!='undefined')
										data=encode_space(data);
									content.children('pre').html(data);
								});
								content.show();
							}else{
								content.hide();
							}
							break;
						case 'del':
							k=$a.parents('li');
							$.ajax('api/ajax_mailfunc.php?op=delete&mail_id='+k.attr('id').substr(4));
							k.remove();
							break;
						case 'rep':
                            op='send';
							k=$a.parents('.mail-container');
							$('#input_to').val(k.children('.mail-info').find('a').html());
							$('#input_title').val('Re:'+k.find('a[href="#title"]').html());
							$('#send_result').hide();
							$('#MailModal').modal('show');
							break;
						case 'star':
							k=$a.parents('li');
							$.ajax('api/ajax_mailfunc.php?op=star&mail_id='+k.attr('id').substr(4));
							$a.find('i').toggleClass('fa-star').toggleClass('fa-star-o');
							break;
                        case 'edit':
                            op='edit';
                            $('#send_title').html('<?php echo _('Edit mail')?>');
                            k=$a.parents('.mail-container'); 
							mailid=k.parent().css('background-color','').attr('id').substr(4);
							$('#input_to').val(k.children('.mail-info').find('a').html());
							$('#input_title').val(k.find('a[href="#title"]').html());
                            $('#input_content').val(k.find('pre').html());
							$('#send_result').hide();
							$('#MailModal').modal('show');
                            break;
					}
					return false;
				});
				$('#send_btn').click(function(){
					$.ajax({
						type:"POST",
						url:"api/ajax_mailfunc.php?op="+op+"&mail_id="+mailid,
						data:$('#send_form').serialize(),
						success:function(msg){
							if(msg.indexOf('success')!=-1){
								$('#MailModal').modal('hide');
								setTimeout("location.reload()",200);
								return;
							}
							$('#send_result').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
						}
					});
				});
                $('#btn_mnewmail').click(function(){
                    $('#UserModal').modal('hide');
                    $('#btn_newmail').click();
                    $('#input_to').val($('#input_touser').val());
                });
				$('#btn_newmail').click(function(){
                    op='send';
                    $('#send_title').html('<?php echo _('Send mail')?>');
					$('#send_result').slideUp();
					$('#MailModal').modal('show');
					$('#input_to').focus();
				});
				$('#btn_star').click(function(E){
					var url_parm=GetUrlParms();
					url_parm['page_id']=1;
					if(<?php echo $star_mode?>==0)
						url_parm['starred']=1;
					else
						delete url_parm.starred;
					location.href='mail.php'+BuildUrlParms(url_parm);
				});
				reg_hotkey(78,function(){$('#btn_newmail').click()}); //Alt+N
		        reg_hotkey(83,function(){$('#send_btn').click()}); //Alt+S
                <?php if(isset($_POST['touser'])){?>
                $('#btn_newmail').click();
                $('#input_to').val('<?php echo $_POST['touser']?>');
                <?php }?>
			}); 
		</script>
	</body>
</html>
