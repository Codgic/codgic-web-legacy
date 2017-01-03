<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/lib/result_type.php';
require __DIR__.'/lib/lang.php';
require __DIR__.'/lib/problem_flags.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
if(!isset($con))
    require __DIR__.'/conf/database.php';
require __DIR__.'/func/contest.php';
require __DIR__.'/lib/Parsedown.php';
require __DIR__.'/lib/ParsedownExtra.php';
require_once __DIR__.'/lib/HTMLPurifier/HTMLPurifier.auto.php';

//Determine contest_id
if(isset($_GET['contest_id']))
    $cont_id=intval($_GET['contest_id']);
else if(isset($_SESSION['view'])){
    $view_arr=unserialize($_SESSION['view']);
    $cont_id=$view_arr['cont'];
}else
    $cont_id=1000;
if(isset($_SESSION['user'])){
    $user_id=$_SESSION['user'];
    $query="select title,start_time,end_time,description,source,has_tex,defunct,judge_way,enroll_user,last_upd_time,need_update,contest_status.tot_score, contest_status.tot_time, contest_status.rank, contest_status.leave_time from contest
    LEFT JOIN (select tot_score, tot_time, rank, leave_time from contest_status where user_id='$user_id' and contest_id=$cont_id limit 1) as contest_status on (1=1)
    where contest_id=$cont_id";
    //Check if contest is marked: to be rewritten.
    $result=mysqli_query($con,"SELECT contest_id FROM saved_contest where user_id='$user_id' and contest_id=$cont_id");
    $mark_flag=mysqli_fetch_row($result);
    if(!($mark_flag)){
        $mark_icon_class='fa fa-fw fa-star-o';
        $mark_btn_class='btn btn-default form-control';
        $mark_btn_html=_('Mark');
    }else{
        $mark_icon_class='fa fa-fw fa-star';
        $mark_btn_class='btn btn-danger form-control';
        $mark_btn_html=_('Unmark');
    }
}else
    $query="select title,start_time,end_time,description,source,has_tex,defunct,judge_way,enroll_user,last_upd_time,need_update from contest
    where contest_id=$cont_id";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
    $info=_('There\'s no such contest');
else{
    //Check if user is forbidden from this contest.
    if($row[6] && !check_priv(PRIV_PROBLEM))
        $forbidden=true;
    else if($row[5]&PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
        $forbidden=true;
    else{
        $forbidden=false;
        //Check if need updating.
        if($row[10])
            update_cont_scr($cont_id);
        //Get contest type.
        switch($row[7]){
            case 0:
                $judge_way=_('Training');
                break;
            case 1:
                $judge_way=_('CWOJ');
                break;
            case 2:
                $judge_way=_('ACM-like');
                break;
            case 3:
                $judge_way=_('OI-like');
                break;
        }
        //Get contest level.
        $cont_level=($row[5]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
        //Update last visited records.
        if(!isset($_SESSION['view']))
            $view_arr=array('cont'=>$cont_id,'prob'=>1000,'wiki'=>1);
        else{
            $view_arr=unserialize($_SESSION['view']);
            $view_arr['cont']=$cont_id;
        }
        $_SESSION['view']=serialize($view_arr);
        
        //Check if current user is contest owner
        if(isset($_SESSION['user'])){
            if(mysqli_num_rows(mysqli_query($con, "select 1 from contest_owner where contest_id=$cont_id and user_id='".$_SESSION['user']."' limit 1"))>0)
                $is_owner=true;
            else
                $is_owner=false;
        }
        if(strtotime($row[1])>time()){
            //Contest has not started.
            $s_info = '<tr><td colspan="2" class="label-re text-center"><i class="fa fa-fw fa-car"></i> '._('Contest hasn\'t started').'</td></tr>';
            $cont_status=0;
            if($is_owner){
                $res=mysqli_query($con, "SELECT contest_problem.problem_id, contest_problem.place, problem.title from contest_problem
                                         LEFT JOIN (select title, problem_id from problem) as problem on (contest_problem.problem_id = problem.problem_id)
                                         where contest_id=$cont_id order by place");
            }
        }else if(time()>strtotime($row[2])){
            //Contest has ended.
             $s_info = '<tr><td colspan="2" class="label-wa text-center"><i class="fa fa-fw fa-ambulance"></i> '._('Contest has ended').'</td></tr>';
            $cont_status=2;
        }else{
            if(isset($row[14]) && !is_null($row[14])){
                //User unenrolled before contest ends.
                $s_info = '<tr><td colspan="2" class="label-wa text-center"><i class="fa fa-fw fa-ambulance"></i> '._('You have left').'</td></tr>';
                $user_quit = true;
            }else{
                //Contest is active.
                if(time()-strtotime($row[9])>20)
                    update_cont_scr($cont_id);
                $s_info = '<tr><td colspan="2" class="label-ac text-center"><i class="fa fa-fw fa-cog fa-spin"></i> '._('Contest is active').'</td></tr>';
            }
                $cont_status=1;
        }
        //If user has joined this contest.
        if(isset($row[13])){
            $res=mysqli_query($con, "SELECT contest_problem.problem_id, contest_problem.place, problem.title, contest_detail.result, contest_detail.score, contest_detail.time from contest_problem
                                     LEFT JOIN (select title, problem_id from problem) as problem on (contest_problem.problem_id = problem.problem_id)
                                     LEFT JOIN (select problem_id, result, score, time from contest_detail where user_id='".$_SESSION['user']."' and contest_id='$cont_id') as contest_detail on (contest_detail.problem_id = contest_problem.problem_id)
                                     where contest_id=$cont_id order by place");
        }else{
            $res=mysqli_query($con, "SELECT contest_problem.problem_id, contest_problem.place, problem.title from contest_problem
                                     LEFT JOIN (select title, problem_id from problem) as problem on (contest_problem.problem_id = problem.problem_id)
                                     where contest_id=$cont_id order by place");
        }
    }
    if($forbidden)
        $info=_('Looks like you can\'t access this page');
}

$inTitle=_('Contest')." #$cont_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php 
        require __DIR__.'/inc/head.php';
        //Load highlight-js theme.
        if($t_night=='off') 
            echo '<link rel="stylesheet" href="/assets/highlight/styles/xcode.css" type="text/css" />';
        else
            echo '<link rel="stylesheet" href="/assets/highlight/styles/androidstudio.css" type="text/css" />';
    ?>
    <body>
        <?php
            if($row[5]&PROB_HAS_TEX)
                require __DIR__.'/conf/mathjax.php';
            require __DIR__.'/inc/navbar.php';
        ?>
        <div class="alert collapse text-center alert-popup alert-danger" id="alert_error"></div>
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
                    <div class="col-xs-12 col-sm-9" id="leftside">
                        <div class="text-center">
                            <h2 class="margin-0"><?php echo '#'.$cont_id,' ',$row[0];if($row[6]) echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">',_('Deleted'),'</span>';?></h2>
                        </div>
                        <br>
                        <?php if(strlen(trim($row[3]))){?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?php echo _('Description')?></h5>
                                </div>
                                <div class="panel-body">
                                    <?php echo HTMLPurifier::instance()->purify(Parsedown::instance()->text($row[3]));?>
                                </div>
                            </div>
                        <?php }?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Problems')?></h5>
                            </div>
                            <?php
                                if($cont_status==0 && !$is_owner){
                                    echo '<div class="panel-body">',_('You can\'t see me before the contest starts...'),'</div>';
                                }else if(!isset($row[13]) && $cont_status!=2 && !$is_owner){
                                    echo '<div class="panel-body">',_('Please <a href="javascript:void(0)" onclick="return enroll_cont();">enroll in the contest</a> first...'),'</div>';
                                }else{?>
                                    <ul class="list-group">
                                        <?php 
                                            while($s_row=mysqli_fetch_row($res)){
                                                echo '<li class="list-group-item"><i class=', (!isset($s_row[3]) || is_null($s_row[3])) ? '"fa fa-fw fa-lg fa-question" style="color:grey"' : ($s_row[3] ? '"fa fa-fw fa-lg fa-remove" style="color:red"' : '"fa fa-fw fa-lg fa-check" style="color:green"'), '></i>';
                                                echo ' <a href="problempage.php?contest_id='.$cont_id.'&prob='.($s_row[1]+1).'">#'.$s_row[0].' - '.$s_row[2].'</a>';
                                                if(isset($row[11])){
                                                    echo '<span class="pull-right">';
                                                    if($row[9]==2){
                                                        if($s_row[3])
                                                            echo '<font color="green">',_('Accepted'),'</font>';
                                                        else
                                                            echo '<font color="red">',_('Not Accepted'),'</font>';
                                                    }else
                                                        echo $s_row[4];
                                                    echo '</span></li>';
                                                }
                                            }
                                        ?>
                                    </ul>
                            <?php }?>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo $judge_way?></h5>
                            </div>
                            <div class="panel-body">
                                <?php echo get_judgeway_destext($row[7])?>
                            </div>
                        </div>
                        <?php if(strlen(trim($row[4]))){?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?php echo _('Tags')?></h5>
                                </div>
                                <div class="panel-body">
                                    <?php echo HTMLPurifier::instance()->purify(mb_ereg_replace('\r?\n','<br>',$row[4]));?>
                                </div>
                            </div>
                        <?php }?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Rankings')?></h5>
                            </div>
                            <div class="panel-body" id="cont_rank">
                                <i class="fa fa-refresh fa-spin fa-fw"></i> <?php echo _('Loading...')?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3" id="rightside">
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
                                                <tr><td style="text-align:left"><?php echo _('Start Time')?></td><td><?php echo $row[1]?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('End Time')?></td><td><?php echo $row[2]?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Duration')?></td><td><?php echo get_time_text(strtotime($row[2])-strtotime($row[1]))?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Format')?></td><td><?php echo $judge_way?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Level')?></td><td><?php echo $cont_level?></td></tr>
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
                                                <?php echo $s_info;?>
                                                <?php if(isset($_SESSION['user'])&&isset($row[13])){?>
                                                    <tr><td style="text-align:left"><?php echo _('Your Score')?></td><td><?php echo $row[11]?></td></tr>
                                                    <tr><td style="text-align:left"><?php echo _('Time Penalty')?></td><td><?php echo get_time_text($row[12])?></td></tr>
                                                    <?php if($cont_status==2){?>
                                                        <tr><td style="text-align:left"><?php echo _('Your Rank')?></td><td><?php echo $row[13]?></td></tr>
                                                    <?php }
                                                }?>
                                                <tr><td style="text-align:left"><?php echo _('Competitors')?></td><td><?php echo $row[8]?></td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <div id="function" class="panel panel-default problem-operation" style="margin-top:10px">
                                    <div class="panel-body">
                                        <a href="#" title="Alt+S" class="btn btn-primary shortcut-hint" id="btn_submit"><?php if(!isset($row[13]) || isset($user_quit)) echo _('Enroll'); else echo _('Leave')?></a>
                                        <a href="#" class="btn btn-success" id="btn_rank"><?php echo _('Rankings')?></a>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <?php if(check_priv(PRIV_PROBLEM)){?>
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <div class="panel panel-default problem-operation" style="margin-top:10px">
                                        <div class="panel-body">
                                            <a href="editcontest.php?contest_id=<?php echo $cont_id?>" class="btn btn-primary"><?php echo _('Edit')?></a>
                                            <span id="action_delete" class="btn btn-danger"><?php echo $row[6] ? _('Recover') : _('Delete');?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(isset($mark_btn_class)){ ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <a href="#" class="<?php echo $mark_btn_class?>" id="action_mark">
                                            <i class="<?php echo $mark_icon_class ?>"></i>
                                            <span id="action_mark_html"><?php echo $mark_btn_html?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php }
                        }?>
                    </div>
                    <?php }?>
                </div>
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
    
        <div id="show_tool" class="bottom-right collapse">
            <span id="btn_submit2" title="Alt+S" class="btn btn-primary shortcut-hint"><?php if(!isset($row[13])) echo _('Enroll'); else echo _('Leave')?></span>
            <span id="btn_show" title="Alt+H" class="btn btn btn-primary shortcut-hint"><i class="fa fa-fw fa-toggle-off"></i> <?php echo _('Show Sidebar')?></span>
        </div>
    
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script src="/assets/highlight/highlight.pack.js"></script>
        <script type="text/javascript">
            hljs.initHighlightingOnLoad();
            var cont=<?php echo $cont_id?>,hide_info=0;
            change_type(2);
            function enroll_cont(){
                <?php if(!isset($_SESSION['user'])){?>
                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Please login first...')?>').fadeIn();
                    setTimeout(function(){$('#alert_error').fadeOut();},2000);
                <?php }else if($cont_status==2){?>
                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Contest has ended...')?>').fadeIn();
                    setTimeout(function(){$('#alert_error').fadeOut();},2000);
                <?php }else if(isset($user_quit)){?>
                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('You have left the contest...')?>').fadeIn();
                    setTimeout(function(){$('#alert_error').fadeOut();},2000);
                <?php }else if(!isset($row[13])){?>
                    $.post('api/ajax_contest.php', {op:'enroll',contest_id:cont}, function(msg){
                        if(msg.success){
                            window.location.href='problempage.php?contest_id='+cont;
                        }else{
                            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg.message).fadeIn();
                            setTimeout(function(){$('#alert_error').fadeOut();},2000);
                        }
                    });
                <?php }else{?>
                    $.post('api/ajax_contest.php', {op:'leave',contest_id:cont}, function(msg){
                        if(msg.success){
                            window.location.reload();
                        }else{
                            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg.message).fadeIn();
                            setTimeout(function(){$('#alert_error').fadeOut();},2000);
                        }
                    });
                <?php }?>
                return false;
            }
            $(document).ready(function(){
                $('table').each(function(){
                    if(!$(this).hasClass('table'))
                        $('table').addClass('table table-bordered table-condensed');
                });
                var cont=<?php echo $cont_id?>;
                $.post("api/ajax_contest.php",{op:'get_rank_table',contest_id:cont},function(data){
                    $('#cont_rank').html(data.message);
                })
                $('#action_delete').click(function(){
                    $.ajax({
                        type:"POST",
                        url:"api/ajax_editcontest.php",
                        data:{op:'del',contest_id:cont},
                        success:function(msg){
                            if(msg.success)
                                window.location.reload();
                            else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg.message).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        }
                    });
                });
                $("#action_mark").click(function(){
                    var op;
                    if($('#action_mark_html').html()=='<?php echo _('Mark')?>')
                        op="add_saved";
                    else
                        op="rm_saved";    
                    $.get("api/ajax_mark.php?type=2&prob="+cont+"&op="+op,function(msg){
                        if(/success/.test(msg)){
                            var tg=$("#action_mark");
                            tg.toggleClass("btn-danger");
                            tg.toggleClass("btn-default");
                            tg.find('i').toggleClass('fa-star-o').toggleClass('fa-star');
                            var tg=$("#action_mark_html");
                            if(tg.html()=='<?php echo _('Mark')?>')
                                tg.html('<?php echo _('Unmark')?>');
                            else
                                tg.html('<?php echo _('Mark')?>');
                        }else{
                            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                            setTimeout(function(){$('#alert_error').fadeOut();},2000);
                        }
                    });
                    return false;
                });
                $('#btn_submit').click(function(){enroll_cont()});
                $('#btn_submit2').click(function(){enroll_cont()});
                $('#btn_rank').click(function(){$("html,body").animate({scrollTop:$("#cont_rank").offset().top},200);});
                function toggle_info(){
                    if(hide_info){
                        $('#leftside').addClass('col-sm-9');
                        $('#rightside').fadeIn(300);
                        $('#show_tool').fadeOut(300);
                        hide_info=0;
                    }else{
                        $('#rightside').fadeOut(300);
                        $('#show_tool').fadeIn(300);
                        setTimeout("$('#leftside').addClass('col-xs-12').removeClass('col-sm-9')", 300);
                        hide_info=1;
                    }
                }
                $('#btn_hide').click(toggle_info);
                $('#btn_show').click(toggle_info);
                reg_hotkey(83, function(){enroll_cont()}); //Alt+S
                reg_hotkey(72, toggle_info); //Alt+H
            });
        </script>
    </body>
</html>