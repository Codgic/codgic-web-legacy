<?php
require 'inc/global.php';
require 'inc/ojsettings.php';
require 'inc/privilege.php';

function check_id(&$str,&$t)
{
  if($t==1) $type='problem';
  else if($t==2) $type='contest';
  else return;
  require 'inc/database.php';
  if(preg_match('/\D/',$str))
    return;
  $num=intval($str);
  if(check_priv(PRIV_PROBLEM))
	  $addt_cond='';
  else
      $addt_cond="defunct='N' and ";
  if(mysqli_num_rows(mysqli_query($con,'select '.$type.'_id from '.$type.' where '.$addt_cond.$type.'_id='.$num))){
      header('location: '.$type.'page.php?'.$type.'_id='.$num);
      exit();
  }
}
if(!isset($_GET['q'])||empty($_GET['q']))
  $req='';
else{
  $req=urlencode($_GET['q']);
}
if(!isset($_GET['t'])){
  header("Location:search.php?t=1&q=$req");
  exit();
}else $type=intval($_GET['t']);
if($type<1||$type>3){
  header("Location:search.php?t=1&q=$req");
  exit();
}
if(isset($_GET['page_id']))
  $page_id=intval($_GET['page_id']);
else
  $page_id=1;
  
if(!isset($_GET['q'])||empty($_GET['q']))
  $info=_('Please enter a keyword first');
else if(strlen($req)>600){
  $info=_('The keyword entered is too long');
}else{
  require 'inc/checklogin.php';
  require 'inc/database.php';
  $keyword=mysqli_real_escape_string($con,trim(urldecode($req)));
  if(check_priv(PRIV_PROBLEM))
	$addt_cond='';
  else
    $addt_cond="defunct='N' and ";
    
  switch($type){
    case 1:
    check_id($req,$type);
    if(isset($_SESSION['user'])){
      $user_id=$_SESSION['user'];
      $result=mysqli_query($con,"SELECT problem_id,title,source,res,tags from
      (select problem.problem_id,title,source,tags,defunct from problem left join user_notes on (user_id='$user_id' and user_notes.problem_id=problem.problem_id)  )pt
      LEFT JOIN (select problem_id as pid,MIN(result) as res from solution where user_id='$user_id' and problem_id group by problem_id) as temp on(pid=problem_id)
      where ".$addt_cond."(title like '%$keyword%' or source like '%$keyword%' or tags like '%$keyword%')
      order by problem_id limit ".(($page_id-1)*20).",20");
    }else{
      $result=mysqli_query($con,"SELECT problem_id,title,source,defunct from
      problem
      where defunct='N' and (title like '%$keyword%' or source like '%$keyword%')
      order by problem_id limit ".(($page_id-1)*20).",20");
    }
    break;
    case 2:
    check_id($req,$type);
    if(isset($_SESSION['user'])){
      $user_id=$_SESSION['user'];
      $result=mysqli_query($con,"SELECT contest_id,title,source,res,start_time,end_time,defunct from contest
      LEFT JOIN (select contest_id as cid,1 as res from contest_status where user_id='$user_id') as fuckzk on (cid=contest_id)
      where ".$addt_cond."(title like '%$keyword%' or source like '%$keyword%')
      order by contest_id limit ".(($page_id-1)*20).",20");
    }else{
      $result=mysqli_query($con,"SELECT contest_id,title,source,defunct,start_time,end_time from contest
      where ".$addt_cond."(title like '%$keyword%' or source like '%$keyword%')
      order by contest_id limit ".(($page_id-1)*20).",20");
    }
    break;
    case 3:
    $result=mysqli_query($con,"select user_id,nick,solved,submit,accesstime from users 
    where user_id like '%$keyword%' or nick like '%$keyword%'
    order by solved desc limit ".(($page_id-1)*20).",20");
    break;
  }
  if(mysqli_num_rows($result)==0) $info=_('Looks like we can\'t find what you want');
}
$inTitle=_('Search Result');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body>
    <?php require 'page_header.php'; ?>      
    <div class="container">
      <div class="row">
        <div class="col-xs-12 text-center">
		  <ul class="pagination">
			<li <?php if($type==1) echo 'class="active"'?>><a href="search.php?t=1&q=<?php echo $req?>"><i class="fa fa-fw fa-coffee"></i> <?php echo _('Problems')?></a></li>
			<li <?php if($type==2) echo 'class="active"'?>><a href="search.php?t=2&q=<?php echo $req?>"><i class="fa fa-fw fa-compass"></i> <?php echo _('Contests')?></a></li>
			<li <?php if($type==3) echo 'class="active"'?>><a href="search.php?t=3&q=<?php echo $req?>"><i class="fa fa-fw fa-user"></i> <?php echo _('Users')?></a></li>
		  </ul>
        </div>
      </div>
      <div class="row">
        <?php if(isset($info)){?>
        <div class="text-center none-text none-center">
            <p><i class="fa fa-meh-o fa-4x"></i></p>
            <p><b>Whoops</b><br>
            <?php echo $info?></p>
        </div>
        <?php }else{?>
        <div class="col-xs-12 table-responsive">
			<?php if($type==3){?>
			<table class="table table-hover table-bordered" style="margin-bottom:0">
              <thead>
              <tr>
                <th style="width:25%"><?php echo _('User')?></th>
                <th><?php echo _('Nickname')?></th>
                <th style="width:10%"><?php echo _('Status')?></th>
                <th style="width:10%"><?php echo _('AC')?></th>
                <th style="width:10%"><?php echo _('Submit')?></th>
              </tr>
              </thead>
              <tbody id="userlist">
                <?php 
              while($row=mysqli_fetch_row($result)){
                echo '<tr>';
                echo '<td><a href="#linkU">',$row[0],'</a></td>';
                echo '<td>',htmlspecialchars($row[1]),'</td>';
                if(time()-strtotime($row[4])<=300) echo '<td><label class="label label-success">',_('Online'),'</label></td>';
                else echo '<td><label class="label label-danger">',_('Offline'),'</label></td>';
                echo '<td><a href="record.php?user_id=',$row[0],'&amp;result=0">',$row[2],'</a></td>';
                echo '<td><a href="record.php?user_id=',$row[0],'">',$row[3],'</a></td>';
                echo "</tr>\n";
                  }
                ?>
              </tbody>
            </table>
			<?php }else{?>
            <table class="table table-hover table-bordered" style="margin-bottom:0">
              <thead><tr>
                <th style="width:7%">ID</th>
                <?php
				if(isset($_SESSION['user'])){
				  echo '<th colspan="2">',_('Title'),'</th>'; 
				  if($type==1) echo '<th style="width:20%">',_('Notes'),'</th>';
				  }else echo '<th>',_('Title'),'</th>';
				  if($type==2) echo '<th style="width:20%">',_('Start Time'),'</th><th style="width:5%">',_('Status'),'</th>';  
                 ?>
                <th style="width:30%"><?php echo _('Tags')?></th>
              </tr></thead>
              <tbody>
                <?php 
                if($type==1){
                    while($row=mysqli_fetch_row($result)){
                    echo '<tr><td>',$row[0],'</td>';
                    if(isset($_SESSION['user'])){
                      echo '<td style="width:36px"><i class=', is_null($row[3]) ? '"fa fa-fw fa-remove fa-2x" style="visibility:hidden"' : (($type==1&&$row[3])? '"fa fa-fw fa-remove fa-2x" style="color:red"' : '"fa fa-fw fa-2x fa-check" style="color:green"'), '></i>', '</td>';
                      echo '<td style="text-align:left;border-left:0;">';
                    }else{
                      echo '<td style="text-align:left;">';
                    }
                    echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[1],'</a></td>';
                    if(isset($_SESSION['user']))
                      echo '<td>',htmlspecialchars($row[4]),'</td>';
                      echo '<td>',$row[2],'</td>';
                      echo "</tr>\n";
                    }
                }else{  
                  while($row=mysqli_fetch_row($result)){
                    if(time()>strtotime($row[5])) $cont_status='<span class="label label-ac">'._('Ended').'</span>';
                    else if(time()<strtotime($row[4])) $cont_status='<span class="label label-wa">'._('Upcoming').'</span>';
                    else $cont_status='<span class="label label-re">'._('In Progress').'</span>';
                    echo '<tr><td>',$row[0],'</td>';
                    if(isset($_SESSION['user'])){
                      echo '<td style="width:36px"><i class=', is_null($row[3]) ? '"fa fa-fw fa-remove fa-2x" style="visibility:hidden"' : (($type==1&&$row[3])? '"fa fa-fw fa-remove fa-2x" style="color:red"' : '"fa fa-fw fa-2x fa-check" style="color:green"'), '></i>', '</td>';
                      echo '<td style="text-align:left;border-left:0;">';
                    }else{
                      echo '<td style="text-align:left;">';
                    }
                    echo '<a href="contestpage.php?contest_id=',$row[0],'">',$row[1],'</a></td>';
                    if(isset($_SESSION['user']))
                      echo '<td>',htmlspecialchars($row[4]),'</td>';
                      echo '<td>',$cont_status.'</td>';
                      echo '<td>',$row[2],'</td>';
                      echo "</tr>\n";
                    }
                }
                ?>
              </tbody>
            </table>
			<?php }?>
		  </div>
		<?php }?>
      </div>
      <div class="row">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php
              if($page_id>1) echo 'href="search.php?t='.$type.'&q='.$req.'&page_id='.($page_id-1).'"';
            ?>><i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?></a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php
              if(isset($result)&&mysqli_num_rows($result)==20) echo 'href="search.php?t='.$type.'&q='.$req.'&page_id='.($page_id+1).'"';
            ?>><?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
          </li>
        </ul>
      </div>
	  <?php if($type==3){?>
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
                <input type="hidden" name="touser" id="um_touser">
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
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        change_type(<?php echo $type?>);
		$('#search_input').val("<?php echo urldecode($req)?>");
        $('#search_type').val("<?php echo $type?>");
	  <?php if($type==3){?>
		$('#userlist').click(function(Event){
          var $target=$(Event.target);
          if($target.is('a') && $target.attr('href')=='#linkU'){
            $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load("ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
            $('#um_touser').val(Event.target.innerHTML);
            $('#UserModal').modal('show');
            return false;
          }
        });
	  <?php }?>
      });
    </script>
  </body>
</html>
