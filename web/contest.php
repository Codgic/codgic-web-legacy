<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/conf/database.php';

if(isset($_GET['level'])){
    //If request level page
    require __DIR__.'/lib/problem_flags.php';
    $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else
        $page_id=1;
    $level=intval($_GET['level']);
    if($level<0 || $level>$level_max){
        header("Location: contest.php");
        exit();
    }
    $addt_cond=" (has_tex&".PROB_LEVEL_MASK.")=".($level<<PROB_LEVEL_SHIFT);
    if(!check_priv(PRIV_PROBLEM))
        $addt_cond.=" and defunct='N' ";
    $range="limit ".(($page_id-1)*100).",100";
    if(isset($_SESSION['user'])){
        $user_id=$_SESSION['user'];
        $result=mysqli_query($con,"SELECT contest_id,title,start_time,end_time,defunct,num,text.source,judge_way,has_tex,joined.res,saved.cid from contest
        LEFT JOIN (select contest_id as cid,1 as res from contest_status where user_id='$user_id' group by contest_id) as joined on(joined.cid=contest_id) 
        left join (select contest_id as cid from saved_contest where user_id='$user_id') as saved on (saved.cid=contest_id) 
        where $addt_cond order by contest_id desc $range");
    }else{
        $result=mysqli_query($con,"select contest_id,text.title,start_time,end_time,defunct,num,text.source,judge_way from contest
        LEFT JOIN (select contest_id,title,source from contest_text group by contest_id) as text on (text.contest_id=contest_id)
        where $addt_cond order by contest_id desc $range");
    }
    if(mysqli_num_rows($result)==0) $info=_('There\'s no contest of this level');
}else{
    //If request contest page
    if(check_priv(PRIV_PROBLEM)){
        $addt_cond1='';
        $addt_cond='';
    }else{
        $addt_cond1="where defunct='N'";
        $addt_cond=" defunct='N' and ";
    }
    $row=mysqli_fetch_row(mysqli_query($con,"select max(contest_id) from contest $addt_cond1"));
    $maxpage=intval($row[0]/100);
    //Determine page_id
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else if(isset($_SESSION['view'])){
        $view_arr=unserialize($_SESSION['view']);
        $page_id=intval($view_arr['cont']/100);
    }else
        $page_id=10;

    if($page_id<10){
        header("Location: contest.php");
        exit();
    }else if($page_id>$maxpage){
        if($maxpage==0) 
            $info=_('Looks like there\'s no contest here');
        else{
            header("Location: contest.php?page_id=$maxpage");
            exit();
        }
    }
    $range="between $page_id"."00 and $page_id".'99';
    if(isset($_SESSION['user'])){
        $user_id=$_SESSION['user'];
        $result=mysqli_query($con,"SELECT contest_id,title,start_time,end_time,defunct,num,source,has_tex,joined.res,saved.cid from contest LEFT JOIN (select contest_id as cid,1 as res from contest_status where user_id='$user_id' group by contest_id) as joined on (joined.cid=contest_id) left join (select contest_id as cid from saved_contest where user_id='$user_id') as saved on(saved.cid=contest_id) where $addt_cond contest_id $range order by contest_id desc");
    }else{
        $result=mysqli_query($con,"select contest_id,title,start_time,end_time,defunct,num,source from contest where $addt_cond contest_id $range order by contest_id desc");
    }
}

$inTitle=_('Contests');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require __DIR__.'/inc/head.php';?>

	<body>
		<?php require __DIR__.'/inc/navbar.php';?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 text-center">
					<?php if(!isset($level)){?>
						<ul class="pagination">
							<?php 
								if($maxpage>10){
									for($i=$maxpage;$i>=10;--$i){
										if($i!=$page_id)
											echo '<li><a href="contest.php?page_id=',$i,'">',$i,'</a></li>';
										else
											echo '<li class="active"><a href="contest.php?page_id=',$i,'">',$i,'</a></li>';
									}
								}
							?>
							<li><a href="contest.php?level=0"><i class="fa fa-fw fa-list-ul"></i> <?php echo _('Levels')?> <i class="fa fa-angle-double-right"></i></a></li>
						</ul>
					<?php }else{?>  
						<ul class="pagination">
							<li><a href="contest.php"><i class="fa fa-angle-double-left"></i> <i class="fa fa-fw fa-th-list"></i> <?php echo _('All')?></a></li>
							<?php
								for($i=0;$i<=$level_max;++$i){
									if($i!=$level)
										echo '<li>';
									else
										echo '<li class="active">';
										echo '<a href="contest.php?level=',$i,'">',$i,'</a></li>';
								}
							?>
						</ul>
					<?php }?>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
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
						<table class="table table-striped table-bordered" id="contest_table">
							<thead>
								<tr>
									<th class="col-xs-2 col-sm-1">ID</th>
									<?php 
										if(isset($_SESSION['user']))
											echo '<th class="col-xs-8 col-sm-5" colspan="3">';
										else
											echo '<th>';
										echo _('Title'),'</th>';
										echo '<th class="col-md-2 hidden-xs hidden-sm">',_('Start Time'),'</th>';  
										echo '<th class="col-xs-2 col-sm-1">',_('Status'),'</th>';  
										echo '<th class="col-sm-5 col-md-3 hidden-xs">',_('Tags'),'</th>';
									?>
								</tr>
							</thead>
							<tbody>
							<?php 
								while($row=mysqli_fetch_row($result)){
									if(time()>strtotime($row[3])) 
										$cont_status='<span class="label label-wa">'._('Ended').'</span>';
									else if(time()<strtotime($row[2])) 
										$cont_status='<span class="label label-re">'._('Upcoming').'</span>';
									else 
										$cont_status='<span class="label label-ac">'._('In Progress').'</span>';
									echo '<tr>';
									echo '<td>',$row[0],'</td>';
									if(isset($_SESSION['user'])){
										echo '<td><i class=', is_null($row[8]) ? '"fa fa-fw fa-remove fa-2x" style="visibility:hidden"' : '"fa fa-fw fa-2x fa-paper-plane" style="color:steelblue"', '></i>', '</td>';
										echo '<td style="text-align:left;border-left:0;">';
									}else
										echo '<td style="text-align:left">';
									echo '<a href="contestpage.php?contest_id=',$row[0],'">',$row[1];
									if($row[4]=='Y')
										echo '&nbsp;&nbsp;<span class="label label-danger">',_('Deleted'),'</span>';
									echo '</a>';
									if(isset($_SESSION['user']))
										echo '<td style="border-left:0;"><i data-pid="',$row[0],'" class="', is_null($row[9]) ? 'fa fa-star-o' : 'fa fa-star', ' fa-fw fa-2x text-warning save_problem" style="cursor:pointer;"></i></td>';
									echo'</td><td class="hidden-xs hidden-sm">',$row[2],'</a></td>';
									echo '<td>',$cont_status,'</td>';
									echo '<td class="hidden-xs" style="text-align:left">',$row[6],"</td></tr>\n";
								}
							?>
							</tbody>
						</table>
					<?php }?>
				</div>
			</div>
			<div class="row">
				<ul class="pager">
					<li>
						<?php if(!isset($_GET['level'])){?>
							<a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>10) echo 'href="contest.php?page_id='.($page_id-1).'"';?>>
								<i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?>
							</a>
						<?php }else{?>
							<a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="contest.php?level='.$level.'&page_id='.($page_id-1).'"';?>>
								<i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?>
							</a>
						<?php }?>
					</li>
					<li>
						<?php if(!isset($_GET['level'])){?>
							<a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo 'href="contest.php?page_id='.($page_id+1).'"';?>>
								<?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i>
							</a>
						<?php }else{?>
							<a class="pager-pre-link shortcut-hint" title="Alt+D" <?php if(mysqli_num_rows($result)==100) echo 'href="contest.php?level='.$level.'&page_id='.($page_id+1).'"';?>>
							<?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
						<?php }?>
					</li>
				</ul>
			</div>
			<?php require __DIR__.'/inc/footer.php';?>
		</div>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript"> 
		$(document).ready(function(){
			change_type(2);
			var cur_page=<?php echo $page_id ?>;
			$('#nav_cont').parent().addClass('active');
			$('#contest_table').click(function(E){
				var $target = $(E.target);
				if($target.is('i.save_problem')){
					var pid = $target.attr('data-pid'),op;
					if($target.hasClass('fa-star'))
						op='rm_saved';
					else
						op='add_saved';
					$.get('api/ajax_mark.php?type=2&prob='+pid+'&op='+op,function(result){
						if(/success/.test(result)){
							$target.toggleClass('fa-star-o')
							$target.toggleClass('fa-star')
						}
					});
				}
			});
		});
    </script>
  </body>
</html>