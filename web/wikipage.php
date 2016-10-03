<?php
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/lib/Parsedown.php';
require __DIR__.'/conf/database.php';

if(isset($_GET['wiki_id']))
    $wiki_id=intval($_GET['wiki_id']);
//else if(isset($_SESSION['view']))
//    $wiki_id=$_SESSION['view'];
else
    $wiki_id=1;
$query="select title,content,tags,author,revision,in_date,privilege,defunct from wiki where wiki_id=$wiki_id and is_max='Y'";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
    $info=_('There\'s no such wiki');
require __DIR__.'/func/privilege.php';
$forbidden=false;
if($row[6]!=0 && !($_SESSION['priv'] & $row[6]))
    $forbidden=true;
if($row[7]=='Y' && !check_priv(PRIV_PROBLEM))
    $forbidden=true;
if($forbidden)
    $info=_('Looks like you can\'t access this page');
    
$inTitle=_('Wiki')." #$wiki_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php';?>
    <body>
        <?php require __DIR__.'/inc/navbar.php';?>
        <div id="probdisp" class="container">
            <?php if(isset($info)){?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="text-center none-text none-center">
                            <p><i class="fa fa-meh-o fa-4x"></i></p>
                            <p>
                                <b>Whoops</b>
                                <br>
                                <?php echo $info?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php }else{?>
                <div class="row">
                    <div class="col-xs-12" id="leftside" style="font-size:16px">
                        <div class="text-center">
                            <h2><?php echo '#'.$wiki_id,' ',$row[0];if($row[7]=='Y')echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">',_('Deleted'),'</span>';?></h2>
                        </div>
                        <br>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Content')?></h5>
                            </div>
                            <div class="panel-body">
                                <?php echo Parsedown::instance()->text($row[1]);?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3 collapse" id="rightside">
                        <div class="row">
                            <div class="col-xs-12">
                                <button id="btn_hide" title="Alt+H" class="btn btn-primary shortcut-hint pull-right"><i class="fa fa-fw fa-toggle-on"></i> <?php echo _('Hide Sidebar')?></button>
                            </div>
                        </div>
                        <br> 
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <table class="table table-condensed table-striped" style="margin-bottom:0px">
                                            <tbody>
                                                <td>Table of Contents</td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="status" class="panel panel-default" style="margin-top:10px">
                                    <div class="panel-body">
                                        <table class="table table-condensed table-striped" style="margin-bottom:0px">
                                            <tbody>
                                                <td>Coming Soon...</td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php if(check_priv(PRIV_PROBLEM)){?>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <div class="panel panel-default problem-operation" style="margin-top:10px">
                                    <div class="panel-body">
                                        <a href="editwiki.php?wiki_id=<?php echo $wiki_id?>" class="btn btn-primary"><?php echo _('Edit')?></a>
                                        <span id="action_delete" class="btn btn-danger"><?php echo $row[7]=='N' ? _('Delete') : _('Recover');?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
            <?php }?>
        </div>
        <?php require __DIR__.'/inc/footer.php';?>
    </div>
	
    <div id="show_tool" class="bottom-right">
        <span id="btn_show" title="Alt+H" class="btn btn btn-primary shortcut-hint"><i class="fa fa-fw fa-toggle-off"></i> <?php echo _('Show Sidebar')?></span>
    </div>
    
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript">
    var wiki=<?php echo $wiki_id?>,hide_info=1;
    change_type(4);
      $(document).ready(function(){
        $('#action_delete').click(function(){
          alert('Unfinished');
          return;
          $.ajax({
            type:"POST",
            url:"ajax_editwiki.php",
            data:{op:'del',wiki_id:cont},
            success:function(msg){
              if(/success/.test(msg)){
                location.reload();
              }else{
                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                setTimeout(function(){$('#alert_error').fadeOut();},2000);
              }
            }
          });
        });
        function toggle_info(){
          if(hide_info) {
			$('#leftside').addClass('col-sm-9');
            $('#rightside').fadeIn(300);
            $('#show_tool').fadeOut(300);
            hide_info=0;
          }else {
            $('#rightside').fadeOut(300);
            $('#show_tool').fadeIn(300);
			setTimeout("$('#leftside').addClass('col-xs-12').removeClass('col-sm-9')", 300);
            hide_info=1;
          }
        }
        $('#btn_hide').click(toggle_info);
        $('#btn_show').click(toggle_info);
        reg_hotkey(72, toggle_info); //Alt+H
      });
    </script>
  </body>
</html>
