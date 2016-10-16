<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';

if(!isset($_SESSION['user'])){
    $addt_cond="privilege=0 and defunct='N'";
}else{
    $addt_cond="((privilege & ".$_SESSION['priv'].")<>0 or privilege=0)";
    require __DIR__.'/func/privilege.php';
    if(!check_priv(PRIV_PROBLEM))
        $addt_cond.=" and defunct='N'";
}

require __DIR__.'/conf/database.php';
if(!isset($_GET['page_id'])){
    $row=mysqli_fetch_row(mysqli_query($con,"select max(wiki_id) from wiki where $addt_cond"));
    $maxpage=intval($row[0]);
    if($maxpage==0)
        $info=_('Looks like there\'s no wiki here');
    else{
        $row=mysqli_fetch_row(mysqli_query($con,"select content from wiki where wiki_id=0 limit 1"));
    }
}else{
    $page_id=intval($_GET['page_id']);
    if($page_id<1){
        header("Location: /wikipage.php?page_id=1");
        exit();
    }
    $row=mysqli_fetch_row(mysqli_query($con,"select max(wiki_id) from wiki where $addt_cond"));
    $maxpage=intval($row[0]);
    if($maxpage==0){
        $info=_('Looks like there\'s no wiki here');
    }else{
        $maxpage=intval($row[0]/20)+1;
        if($page_id>$maxpage){
            header("Location: /wikipage.php?page_id=$maxpage");
            exit();
        }
    }
    if(isset($_SESSION['user'])){
        $user_id=$_SESSION['user'];
        $result=mysqli_query($con,"select wiki_id,title,content,tags,in_date,defunct,saved.wid from wiki
        LEFT JOIN (select wiki_id as wid from saved_wiki where user_id='$user_id') as saved on (saved.wid=wiki_id)
        where wiki_id>0 and is_max='Y' order by wiki_id limit ".(($page_id-1)*20).",20");
    }else{
        $result=mysqli_query($con,"select wiki_id,title,content,tags,in_date,defunct from wiki
        where wiki_id>0 and is_max='Y' order by wiki_id limit ".(($page_id-1)*20).",20");
    }
}

$inTitle=_('Wiki');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
<?php require __DIR__.'/inc/head.php';?>
	<body>
		<?php require __DIR__.'/inc/navbar.php';?>
		<div class="container">
			<?php if(!isset($_GET['page_id'])){?>
				<div class="row">
					<div class="col-xs-12 col-sm-8" style="font-size:16px">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h5 class="panel-title">
									<?php echo _('Quick Access')?>
								</h5>
							</div>
							<div class="panel-body">
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
									<div>
										<?php echo $row[0]?>
									</div>
								<?php }?>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-sm-4">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h5 class="panel-title">
									<?php echo _('Get Started')?>
								</h5>
							</div>
							<div class="panel-body">
								<ul class="nav nav-pills nav-stacked">
									<li><a href="javascript:void(0)" onclick="$('#nav_searchbtn').click();change_type(4);return;"><i class="fa fa-fw fa-search"></i> <?php echo _('Search Wiki...')?></a></li>
									<li><a href="/editwiki.php"><i class="fa fa-fw fa-magic"></i> <?php echo _('New Wiki...')?></a></li>
									<li><a href="/wiki.php?page_id=1"><i class="fa fa-fw fa-list"></i> <?php echo _('All Wikis...')?></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			<?php }else{?>
				<div class="row">
					<div class="col-xs-12">
						<div class="form-group">
							<a href="/wiki.php" class="btn btn-default"><i class="fa fa-fw fa-home"></i> <?php echo _('Wiki Home')?></a>
						</div>
					</div>	
				</div>
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-striped table-bordered" id="wiki_table">
							<thead>
								<tr>
									<th class="col-xs-2 col-sm-1">ID</th>
									<?php 
										if(isset($_SESSION['user']))
											echo '<th class="col-xs-8 col-sm-5" colspan="2">';
										else
											echo '<th>';
										echo _('Title'),'</th>';
										echo '<th class="col-xs-2 col-sm-1 col-md-2 hidden-xs hidden-sm">',_('Last Modified'),'</th>';  
										echo '<th class="col-sm-5 col-md-3 hidden-xs">',_('Tags'),'</th>';
									?>
								</tr>
							</thead>
							<tbody>
								<?php 
									while($row=mysqli_fetch_row($result)){
										echo '<tr>';
										echo '<td>',$row[0],'</td>';
										echo '<td style="text-align:left"><a href="wikipage.php?wiki_id=',$row[0],'">',$row[1],'</a>';
										if($row[5]=='Y')
											echo '&nbsp;&nbsp;<span class="label label-danger">',_('Deleted'),'</span>';
										echo '</a></td>';
										if(isset($_SESSION['user']))
											echo '<td style="border-left:0;width:1%"><i data-pid="',$row[0],'" class="', is_null($row[6]) ? 'fa fa-star-o' : 'fa fa-star', ' fa-fw fa-2x text-warning save_problem" style="cursor:pointer;"></i></td>';
										echo '<td class="hidden-xs hidden-sm">',$row[4],'</td>';
										echo '<td class="hidden-xs" style="text-align:left">',$row[3],"</td></tr>\n";
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<ul class="pager">
						<li>
							<a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="wiki.php?page_id=',($page_id-1),'"'?>><i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?></a>
						</li>
						<li>
							<a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo'href="wiki.php?page_id',($page_id+1),'"'?>><?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
						</li>
					</ul>
				</div> 
			<?php }
			require __DIR__.'/inc/footer.php';?>
		</div>
    
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#nav_wiki').parent().addClass('active');
                $('table').addClass('table');
				change_type(4);
				<?php if(isset($_GET['page_id'])){?>
					$('#wiki_table').click(function(E){
						var $target = $(E.target);
						if($target.is('i.save_problem')){
							var pid = $target.attr('data-pid');
							var op;
							if($target.hasClass('fa-star'))
								op='rm_saved';
							else
								op='add_saved';
							$.get('api/ajax_mark.php?type=3&prob='+pid+'&op='+op,function(result){
								if(/success/.test(result)){
									$target.toggleClass('fa-star-o')
									$target.toggleClass('fa-star')
								}
							});
						}
					});
				<?php }?>
			});
		</script>
	</body>
</html>