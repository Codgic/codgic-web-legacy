<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';

if(isset($_GET['page_id']))
    $page_id=intval($_GET['page_id']);
else
    $page_id=1;
  
if(isset($_GET['online'])){
    $online=intval($_GET['online']);
    if($online<0||$online>1) 
        $online=0;
}else
    $online=0;

if(!isset($con)) 
    require __DIR__.'/conf/database.php';
$row=mysqli_fetch_row(mysqli_query($con,'select count(*) from users'));
$maxpage=intval($row[0]/20)+1;
if($page_id<1){
    header("Location: ranklist.php");
    exit();
}else if($page_id>$maxpage){
    header("Location: ranklist.php?page_id=$maxpage");
    exit();
}

function get_next_link(){
    global $online,$page_id;
    parse_str($_SERVER["QUERY_STRING"],$arr); 
    if($online){
        $arr['online']=1;
    }
    $arr['page_id']=$page_id+1;
    return http_build_query($arr);
}

function get_pre_link(){
    global $online,$page_id;
    parse_str($_SERVER["QUERY_STRING"],$arr); 
    if($online)
        $arr['online']=1;
    $arr['page_id']=$page_id-1;
    return http_build_query($arr); 
}

$rank=($page_id-1)*20;
if($online==0) 
    $result=mysqli_query($con,"SELECT user_id,nick,solved,submit,score,accesstime,experience_titles.title FROM (SELECT user_id,nick,solved,submit,score,accesstime,MAX(experience_titles.experience) AS m FROM (SELECT user_id,nick,solved,submit,score,accesstime,experience from users order by score desc,experience desc,solved desc,submit desc)t,experience_titles where t.experience>=experience_titles.experience GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience order by score desc,experience desc,solved desc,submit desc limit $rank,20");
else
      $result=mysqli_query($con,"SELECT user_id,nick,solved,submit,score,accesstime,experience_titles.title FROM (SELECT user_id,nick,solved,submit,score,accesstime,MAX(experience_titles.experience) AS m FROM (SELECT user_id,nick,solved,submit,score,accesstime,experience from users order by score desc,experience desc,solved desc,submit desc)t,experience_titles where t.experience>=experience_titles.experience and (NOW()-accesstime)<=300 GROUP BY user_id)t1 LEFT JOIN experience_titles ON t1.m=experience_titles.experience order by score desc,experience desc,solved desc,submit desc limit $rank,20");

$inTitle=_('Rank');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>

    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>      
        <div class="alert collapse text-center alert-popup alert-danger" id="alert_error"></div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12" style="margin-bottom:10px">
                    <?php if($online==0){?>
                        <a href="ranklist.php?online=1" class="btn btn-success" id="btn_online"><i class="fa fa-fw fa-car"></i> <?php echo _('Show Online')?></a>
                    <?php }else{?>
                        <a href="ranklist.php" class="btn btn-danger" id="btn_online"><i class="fa fa-fw fa-gamepad"></i> <?php echo _('Show All')?></a>
                    <?php }?>
                    <div class="btn-group dropdown">
                        <button class="btn btn-primary dropdown-toggle" id="btn_usrcmp_menu"><i class="fa fa-fw fa-users"></i> <?php echo _('User Compare')?> <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right" id="usrcmp_menu">
                            <li><input type="text" id="ipt_user1" class="form-control" placeholder="<?php echo _('User 1')?>"></li>
                            <li><input type="text" id="ipt_user2" class="form-control" placeholder="<?php echo _('User 2')?>"></li>
                            <li class="divider"></li>
                            <li><button id="btn_usrcmp" class="btn btn-small btn-primary pull-right" style="margin-right:9px;"><?php echo _('Compare')?></button></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php if(mysqli_num_rows($result)==0){?>
                    <div class="text-center none-text none-center">
                        <p><i class="fa fa-meh-o fa-4x"></i></p>
                        <p>
                            <b>Whoops</b>
                            <br>
                            <?php echo _('Looks like there\'s nothing here')?></p>
                    </div>
                <?php }else{?>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered " style="margin-bottom:0 margin-right:10px">
                                <thead>
                                    <tr>
                                        <th style="width:5%">No.</th>
                                        <th style="width:15%"><?php echo _('User')?></th>
                                        <th style="width:32%"><?php echo _('Nickname')?></th>
                                        <th style="width:8%"><?php echo _('Status')?></th>
                                        <th style="width:8%"><?php echo _('Level')?></th>
                                        <th style="width:8%"><?php echo _('Score')?></th>
                                        <th style="width:8%"><?php echo _('AC')?></th>
                                        <th style="width:8%"><?php echo _('Submit')?></th>
                                        <th style="width:8%"><?php echo _('AC Ratio')?></th>
                                    </tr>
                                </thead>
                                <tbody id="userlist">
                                    <?php 
                                        while($row=mysqli_fetch_row($result)){
                                            echo '<tr><td>',(++$rank),'</td>';
                                            echo '<td><a href="#linkU">',$row[0],'</a></td>';
                                            echo '<td>',htmlspecialchars($row[1]),'</td>';
                                            if(time()-strtotime($row[5])<=300) 
                                                echo '<td><label class="label label-success">',_('Online'),'</label></td>';
                                            else 
                                                echo '<td><label class="label label-danger">',_('Offline'),'</label></td>';
                                            echo '<td>',htmlspecialchars($row[6]),'</td>';
                                            echo '<td>',$row[4],'</td>';
                                            echo '<td><a href="record.php?user_id=',$row[0],'&amp;result=0">',$row[2],'</a></td>';
                                            echo '<td><a href="record.php?user_id=',$row[0],'">',$row[3],'</a></td>';
                                            echo '<td>',$row[3] ? intval($row[2]/$row[3]*100) : 0,'%</td>';
                                            echo "</tr>\n";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php }?>
            </div>
            <div class="row">
                <ul class="pager">
                    <li>
                        <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="ranklist.php?'.htmlspecialchars(get_pre_link()).'"'?>><i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?></a>
                    </li>
                    <li>
                        <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo'href="ranklist.php?'.htmlspecialchars(get_next_link()).'"'?>><?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i></a>
                    </li>
                </ul>
            </div>  
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
        
        <div class="modal fade" id="UserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title" id="modal_title"><?php echo _('User Profile')?></h4>
                    </div>
                    <div class="modal-body" id="user_status"></div>
                    <div class="modal-footer">
                        <form action="mail.php" method="post">
                            <input type="hidden" name="touser" id="um_touser">
                            <?php if(isset($_SESSION['user'])){?>
                                <button type="submit" class="btn btn-default pull-left" id="btn_mail"><i class="fa fa-fw fa-envelope-o"></i> <?php echo _('Send Mail')?></button>
                            <?php }?>
                        </form>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">
            function intersection(obj1,obj2,arr1,arr2,ist){
                for(var k in obj1){
                    if(obj2.hasOwnProperty(k)){
                        ist.push(parseInt(k));
                        delete obj2[k];
                    }else
                        arr1.push(parseInt(k));
                }
                for(var k in obj2)
                    arr2.push(parseInt(k));
            }
            var content='';
            function output(arr){
                arr.sort(function(a,b){return a-b;});
                for(var i in arr){
                    content+='<a target="_blank" href="problempage.php?problem_id=';
                    content+=arr[i]+'">'+arr[i]+'</a> ';
                }
            }
            function user_diff(id1,info1,id2,info2){
                var arr1=[],arr2=[],ist=[];
                content='<table class="table table-condensed table-left-aligned" style="margin-bottom:0px;">';
                intersection(info1.solved,info2.solved,arr1,arr2,ist);
                content+='<tr class="success"><td><?php echo _('Problems only solved by \'+id1+\'')?></td></tr><tr><td><samp>';
                output(arr1);
                content+='</samp></td></tr><tr class="success"><td><?php echo _('Problems only solved by \'+id2+\'')?></td></tr><tr><td><samp>';
                output(arr2);
                content+='</samp></td></tr><tr class="success"><td><?php echo _('Problems solved by both \'+id1+\' and \'+id2+\'')?></td></tr><tr><td><samp>';
                output(ist);
                content+='</samp></td></tr>';
                arr1=[];arr2=[];ist=[];
                intersection(info1.failed,info2.failed,arr1,arr2,ist);
                content+='<tr class="danger"><td><?php echo _('Problems only \'+id1+\' tried but failed')?></td></tr><tr><td><samp>';
                output(arr1);
                content+='</samp></td></tr><tr class="danger"><td><?php echo _('Problems only \'+id2+\' tried but failed')?></td></tr><tr><td><samp>';
                output(arr2);
                content+='</samp></td></tr><tr class="danger"><td><?php echo _('Problems both \'+id1+\' and \'+id2+\' tried but failed')?></td></tr><tr><td><samp>';
                output(ist);
                content+='</samp></td></tr></table>';
            }
            $(document).ready(function(){
                change_type(4);
                $('#userlist').click(function(Event){
                    var $target=$(Event.target);
                    if($target.is('a') && $target.attr('href')=='#linkU'){
                        $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load("api/ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
                        $('#um_touser').val(Event.target.innerHTML);
                        $('#modal_title').html('<?php echo _('User Profile')?>');
                        $('#btn_mail').show();
                        $('#UserModal').modal('show');
                        return false;
                    }
                });
                $('#btn_usrcmp_menu').click(function(E){
                    $(E.target).parent().toggleClass('open');
                });
                $('#btn_usrcmp').click(function(){
                    var user1=$.trim($('#ipt_user1').val());
                    var user2=$.trim($('#ipt_user2').val());
                    if(!user1||!user2){
                        $('#alert_error').html("<i class=\"fa fa-fw fa-remove\"></i> <?php echo _('There\'s only one user...')?>").fadeIn();
                        setTimeout(function(){$('#alert_error').fadeOut();},2000);
                        return;
                    }
                    if(user1==user2){
                        $('#alert_error').html("<i class=\"fa fa-fw fa-remove\"></i> <?php echo _('That\'s meaningless...')?>").fadeIn();
                        setTimeout(function(){$('#alert_error').fadeOut();},2000);
                        return;
                    }
                    $.getJSON("api/ajax_user.php?type=json&user_id="+user1, function(info1){
                        if(info1.hasOwnProperty('nobody')){
                            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('User "\'+user1+\'" does not exist...')?>').fadeIn();
                            setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            return;
                        }
                    $.getJSON("api/ajax_user.php?type=json&user_id="+user2, function(info2){
                        if(info2.hasOwnProperty('nobody')){
                            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('User "\'+user2+\'" does not exist...')?>').fadeIn();
                            setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            return;
                        }
                        $('#usrcmp_menu').parent().removeClass('open');
                        user_diff(user1,info1,user2,info2);
                        $('#user_status').html(content).scrollTop(0);
                        $('#modal_title').html(user1+' vs '+user2);
                        $('#btn_mail').hide();
                        $('#UserModal').modal('show');
                        return false;
                    });
                });
            });
            $('#nav_rank').parent().addClass('active');
        }); 
        </script>
    </body>
</html>