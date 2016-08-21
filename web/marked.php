<?php
require 'inc/global.php';
require 'inc/ojsettings.php';
require 'inc/checklogin.php';

$type='problem';
if(!isset($_SESSION['user']))
    $info = _('Please login first');
else{
    if(isset($_GET['type']))
        if($_GET['type']=='contest'||$_GET['type']=='problem')
            $type=$_GET['type'];
        else{
            header("Location: marked.php");
            exit();
        }
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else
        $page_id=1;
        
    require 'inc/database.php';
    $user_id=$_SESSION['user'];
    if($type=='problem'){
        $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from saved_problem where user_id='$user_id'"));
        $maxpage=intval($row[0]/20)+1;
        if($page_id<1||$page_id>$maxpage){
            header("Location: marked.php");
            exit();
        }
        $result=mysqli_query($con,"SELECT saved_problem.problem_id,title,savetime,problem_flag_to_level(has_tex) from saved_problem inner join problem using (problem_id) where user_id='$user_id' order by savetime desc limit ".(($page_id-1)*20).",20");
        $t=1;
    }else{
        $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from saved_contest where user_id='$user_id'"));
        $maxpage=intval($row[0]/20)+1;
        if($page_id<1||$page_id>$maxpage){
            header("Location: marked.php");
            exit();
        }
        $result=mysqli_query($con,"SELECT saved_contest.contest_id,title,savetime,problem_flag_to_level(has_tex) from saved_contest inner join contest using (contest_id) where user_id='$user_id' order by savetime desc limit ".(($page_id-1)*20).",20");
        $t=2;
    }
    if(mysqli_num_rows($result)==0) $info=_('Looks like there\'s nothing here');
}

$inTitle=_('Marked');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>  
	<body>
      <?php require 'page_header.php'; ?>
      <div class="container">
        <?php if(!isset($_SESSION['user'])){?>
          <div class="text-center none-text none-center">
            <p><i class="fa fa-meh-o fa-4x"></i></p>
            <p><b>Whoops</b><br>
            <?php echo $info?></p>
          </div>
        <?php }else{?>
        <div class="row">
          <div class="col-xs-12">
            <ul class="nav nav-pills">
              <li <?php if($type=='problem') echo 'class="active"'?>><a href="marked.php"><i class="fa fa-fw fa-coffee"></i> <?php echo _('Problems')?></a></li>
			  <li <?php if($type=='contest') echo 'class="active"'?>><a href="marked.php?type=contest"><i class="fa fa-fw fa-compass"></i> <?php echo _('Contests')?></a></li>
            </ul>
			<?php if(isset($info)){?>
              <div class="text-center none-text none-center">
                <p><i class="fa fa-meh-o fa-4x"></i></p>
                <p><b>Whoops</b><br>
                <?php echo $info?></p>
              </div>
			<?php }else{?>
              <br>
              <table class="table table-responsive table-hover table-bordered">
                <thead>
                  <tr>
                    <th style="width:6%">No.</th>
                    <th><?php echo _('Problem')?></th>
                    <th style="width:8%"><?php echo _('Level')?></th>
                    <th style="width:25%"><?php echo _('Date')?></th>
                    <th style="width:10%"><?php echo _('Delete')?></th>
                  </tr>
                </thead>
                <tbody id="marked_list">
                  <?php while($row=mysqli_fetch_row($result)){?>
                  <tr>
                    <td><?php echo $row[0] ?></td>
                    <td style="text-align:left"><a href="<?php echo $type?>page.php?problem_id=<?php echo $row[0]?>" ><?php echo $row[1] ?></a></td>
                    <td><?php echo $row[3] ?></td>
                    <td><?php echo $row[2] ?></td>
                    <td><i data-pid="<?php echo $row[0] ?>" style="cursor:pointer;" class="text-error fa fa-remove"></i></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <?php }?>
            </div>
          </div>
          
          <div class="row">
            <ul class="pager">
              <li>
                <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php
                if($page_id>1) echo 'href="marked.php?page_id='.($page_id-1).'"';
                ?>><i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?></a>
              </li>
              <li>
                <a class="pager-next-link shortcut-hint" title="Alt+D" <?php
                if($page_id<$maxpage) echo 'href="marked.php?page_id='.($page_id+1).'"';
                ?>><?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
              </li>
            </ul>
          </div>
          <?php }?>
          <hr>
          <footer>
            <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
          </footer>
		</div>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript"> 
        var del=0;
        $(document).ready(function(){
          $('#marked_list').click(function(E){
            var $target = $(E.target);
              if($target.is('i')){
                var pid = $target.attr('data-pid');
                  $.get('ajax_mark.php?prob='+pid+'&op=rm_saved&type='+'<?php echo $t?>',function(result){
                    if(/success/.test(result)){
                      $target.parents('tr').remove();
                      del++;
                      if(del==<?php echo mysqli_num_rows($result)?>) location.reload();
                    }
                  });
                }
              });
			}); 
        </script>
    </body>
</html>
