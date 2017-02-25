<?php 
require __DIR__.'/inc/init.php';
require __DIR__.'/lib/result_type.php';
require __DIR__.'/lib/lang.php';
require __DIR__.'/lib/problem_flags.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
if(!isset($con))
    require __DIR__.'/../src/database.php';
require __DIR__.'/func/text.php';
require_once __DIR__.'/../src/textparser.php';
require_once __DIR__ . '/../src/mathjax.php';

$is_contest=false;
if(isset($_GET['contest_id'])){
    //If in contest mode
    $is_contest=true;
    $cont_id=intval($_GET['contest_id']);
    $inTitle=_('Contest')." #$cont_id";
    $query="SELECT title,start_time,end_time,defunct,cnt.place from contest
            LEFT JOIN (select place from contest_problem where contest_id=$cont_id order by place desc limit 1) as cnt on (1=1)
            where contest_id=$cont_id";
    $result=mysqli_query($con,$query);
    $row_cont=mysqli_fetch_row($result);
    if(!$row_cont)
        $info=_('There\'s no such contest');
    else{
        //Get contest status.
        if(strtotime($row_cont[1])>time()){
            //Contest has not started.
            $cont_status=0;
            //Check if current user is contest owner.
            if(isset($_SESSION['user']))
                if(mysqli_num_rows(mysqli_query($con, "select 1 from contest_owner where contest_id=$cont_id and user_id='".$_SESSION['user']."' limit 1"))>0)
                    $is_owner=true;
            if(isset($is_owner) && time()<strtotime($row_cont[1])){
                header("Location: contestpage.php?contest_id=$cont_id");
                exit();
            }
        }else if(time()>strtotime($row_cont[2])){
            $cont_status=2;
        }else{
            $cont_status=1;
            $enrolled=false;
            //Check if user has enrolled.
            if(isset($_SESSION['user'])){
                $t_row=mysqli_fetch_row(mysqli_query($con, "select 1, leave_time from contest_status where contest_id=$cont_id and user_id='".$_SESSION['user']."' limit 1"));
                if(isset($t_row[0])){
                    $enrolled=true;
                    if(!is_null($t_row[1]))
                        $user_quit=true;
                }
            }
            if(isset($is_owner) &&! $enrolled){
                header("Location: contestpage.php?contest_id=$cont_id");
                exit();
            }
            $rem_time=strtotime($row_cont[2])-time();
        }
        if(isset($_GET['prob'])){
            $prob_num=intval($_GET['prob']);
            if($prob_num<1||$prob_num>$row_cont[4]+1){
                header("Location: problempage.php?contest_id=".$cont_id);
                exit();
            }
        }else
            $prob_num=1;
        //Fetch current problem id.
        $t_row=mysqli_fetch_row(mysqli_query($con, "select problem_id from contest_problem where contest_id=$cont_id and place=".($prob_num-1)." limit 1"));
        $prob_id=$t_row[0];
        unset($t_row);
    }
}

else if(isset($_GET['problem_id']))
    $prob_id=intval($_GET['problem_id']);
else if(isset($_SESSION['view'])){
    $view_arr=unserialize($_SESSION['view']);
    $prob_id=$view_arr['prob'];
}else
    $prob_id=1000;

if(!isset($inTitle))
    $inTitle=_('Problem')." #$prob_id";

$query="select title,description,input,output,sample_input,sample_output,hint,source,case_time_limit,memory_limit,case_score,defunct,has_tex,compare_way from problem where problem_id=$prob_id";
$result=mysqli_query($con,$query);
$row_prob=mysqli_fetch_row($result);
if(!$row_prob&&!isset($info))
    $info=_('There\'s no such problem');
else if(!isset($info)){
    //Check if user is forbidden to this problem.
    if($row_prob[11] && !check_priv(PRIV_PROBLEM))
        $forbidden=true;
    else if($row_prob[12] & PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
        $forbidden=true;
    else{
        $forbidden=false;
        //Get comparsion method.
        switch($row_prob[13] >> 16){
            case 0:
                $comparison=_('Traditional');
                break;
            case 1:
                $comparison=_('Real, precision: ').($row_prob[13] & 65535);
                break;
            case 2:
                $comparison=_('Integer');
                break;
            case 3:
                $comparison=_('Special Judge');
                break;
        }
        //Update last visited records.
        if(!isset($_SESSION['view'])){
            if($is_contest)
                $view_arr=array('cont'=>$cont_id,'prob'=>$prob_id,'wiki'=>1);
            else
                $view_arr=array('cont'=>1000,'prob'=>$prob_id,'wiki'=>1);
            $_SESSION['view']=serialize($view_arr);
        }else{
            if(!isset($arr_view))
                $view_arr=unserialize($_SESSION['view']);
            if($is_contest)
                $view_arr['cont']=$cont_id;
            $view_arr['prob']=$prob_id;
            $_SESSION['view']=serialize($view_arr);
        }

        if(isset($_SESSION['user'])){
            $user_id=$_SESSION['user'];
            //Get problem status.
            $query="select min(result) from solution where user_id='$user_id' and problem_id=$prob_id group by problem_id";
            $user_status=mysqli_query($con,$query);
            if(mysqli_num_rows($user_status)==0)
                $s_info = '<tr><td colspan="2" class="label-re text-center"> '._('Give it a try...').'</td></tr>';
            else{
                $statis=mysqli_fetch_row($user_status);
                if($statis[0]==0){
                    $s_info = '<tr><td colspan="2" class="label-ac text-center"><i class="fa fa-fw fa-check"></i> '._('Congratulations!').'</td></tr>';
                }else{
                    $s_info = '<tr><td colspan="2" class="label-wa text-center"><i class="fa fa-fw fa-remove"></i> '._('Let\'s try again...').'</td></tr>';
                }
            }
            //Check if problem marked.
            $result=mysqli_query($con,"SELECT problem_id FROM saved_problem where user_id='$user_id' and problem_id=$prob_id");
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
            //Get notes.
            $result=mysqli_query($con,"SELECT content,tags FROM user_notes where user_id='$user_id' and problem_id=$prob_id");
            $row_note=mysqli_fetch_row($result);
            if(!$row_note){
                $note_content = '';
                $tags = '';
                $note_exist=false;
            }else{
                $note_content = $row_note[0];
                $tags = $row_note[1];
                $note_exist=true;
            }
        }else{
            $s_info = '<tr><td colspan="2" class="text-center muted"> '._('Please login first...').'</td></tr>';
        } 
        //Get related info.
        $result=mysqli_query($con,"select submit_user,solved,submit from problem where problem_id=$prob_id");
        $statis=mysqli_fetch_row($result);
        $submit_user=$statis[0];
        $solved_user=$statis[1];
        $total_submit=$statis[2];
        $prob_level=($row_prob[12]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;

        $result=mysqli_query($con,"select result,count(*) as sum from solution where problem_id=$prob_id group by result");
        $arr=array();
        while($statis=mysqli_fetch_row($result))
            $arr[$statis[0]]=$statis[1];
        ksort($arr);  
    }
    if($forbidden) 
        $info=_('Looks like you can\'t access this page');
}
    
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php
        require __DIR__.'/inc/head.php';
    ?>
    <body>
        <?php
			echo generate_mathjax_script();
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
                    <div class="col-xs-12 col-sm-9" id="leftside" style="font-size:16px">
                        <div class="text-center">
                            <h2 class="margin-0">
                                <?php 
                                    echo '#'.$prob_id,' ',$row_prob[0];
                                    if($row_prob[11])
                                        echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">',_('Deleted'),'</span>';
                                    if($is_contest){
                                        echo '<a href="contestpage.php?contest_id=',$cont_id,'" class="btn btn-default pull-left"><i class="fa fa-fw fa-home"></i> <span class="nav-text-alt">',_('Contest Home'),'</span></a>';
                                        echo '<div class="btn-group pull-right">';
                                        if($prob_num<2) 
                                            $addt='disabled';
                                        else 
                                            $addt='';
                                        echo '<a href="problempage.php?contest_id=',$cont_id,'&prob=',($prob_num-1),'" class="btn btn-default ',$addt,'"><i class="fa fa-fw fa-angle-left"></i> <span class="nav-text-alt">',_('Previous'),'</span></a>';
                                        if($prob_num>$row_cont[4])
                                            $addt='disabled';
                                        else
                                            $addt='';
                                        echo '<a href="problempage.php?contest_id=',$cont_id,'&prob=',($prob_num+1),'" class="btn btn-default ',$addt,'"><span class="nav-text-alt">',_('Next'),'</span> <i class="fa fa-fw fa-angle-right"></i></a>';
                                        echo '</div>';
                                    }
                                ?>
                            </h2>
                        </div>
                        <br>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Description')?></h5>
                            </div>
                            <div class="panel-body">
                                <?php echo HTMLPurifier::instance()->purify(parse_markdown($row_prob[1]));?>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Input')?></h5>
                            </div>
                            <div class="panel-body">
                                <?php echo HTMLPurifier::instance()->purify(parse_markdown($row_prob[2]));?>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Output')?></h5>
                            </div>
                            <div class="panel-body">
                                <?php echo HTMLPurifier::instance()->purify(parse_markdown($row_prob[3]));?>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Sample Input')?>
                                <a herf="#" class="pull-right" id="copy_in" style="cursor:pointer" data-toggle="tooltip" data-trigger="manual" data-clipboard-action="copy" data-clipboard-target="#sample_input"><?php echo _('[Copy]')?></a></h5>
                            </div>
                            <div class="panel-body problem-sample preserve-whitespace" id="sample_input">
                                <?php echo HTMLPurifier::instance()->purify($row_prob[4]); ?>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Sample Output')?>
                                <a herf="#" class="pull-right" id="copy_out" style="cursor:pointer" data-toggle="tooltip" data-trigger="manual" data-clipboard-action="copy" data-clipboard-target="#sample_output"><?php echo _('[Copy]')?></a></h5>
                            </div>
                            <div class="panel-body problem-sample preserve-whitespace" id="sample_output">
                                <?php echo HTMLPurifier::instance()->purify($row_prob[5]);?>
                            </div>
                        </div>
                        <?php if(strlen(trim($row_prob[6]))){ ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?php echo _('Hints')?></h5>
                                </div>
                                <div class="panel-body">
                                    <?php echo HTMLPurifier::instance()->purify(parse_markdown($row_prob[6]));?>
                                </div>
                            </div>
                        <?php }?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?php echo _('Tags')?></h5>
                            </div>
                            <div class="panel-body preserve-whitespace">
                                <?php echo HTMLPurifier::instance()->purify($row_prob[7]);?>
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
                        <?php if($is_contest){?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h2 class="text-center">
                                                <?php
                                                    if($cont_status==0){
                                                        echo _('Contest hasn\'t started');
                                                    }else if($cont_status==2) 
                                                        echo _('Contest has ended');
                                                    else if(isset($user_quit))
                                                        echo _('You have left');
                                                    else
                                                        echo '<span id="cont_st">','<span id="thour">--</span>:<span id="tmin">--</span>:<span id="tsec">--</span></span>';
                                                ?>
                                            </h2>
                                            <div class="text-center">
                                                <?php
                                                    echo _('Problem: '),$prob_num,' / ',($row_cont[4]+1);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <table class="table table-condensed table-striped" style="margin-bottom:0px">
                                            <tbody>
                                                <tr><td style="text-align:left"><?php echo _('Time Limit')?></td><td><?php echo $row_prob[8]?> ms</td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Memory Limit')?></td><td><?php echo $row_prob[9]?> KB</td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Case Score')?></td><td><?php echo $row_prob[10]?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Comparison')?></td><td><?php echo $comparison?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Level')?></td><td><?php echo $prob_level?></td></tr>
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
                                                <?php echo $s_info ?>
                                                <tr><td style="text-align:left"><?php echo _('User Submitted')?></td><td><?php echo $submit_user?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('User Accepted')?></td><td><?php echo $solved_user?></td></tr>
                                                <tr><td style="text-align:left"><?php echo _('Total Submits')?></td><td><?php echo $total_submit?></td></tr>
                                                <?php
                                                    foreach($arr as $type => $cnt){
                                                        if(isset($RESULT_TYPE[$type]))
                                                            echo '<tr><td style="text-align:left">',$RESULT_TYPE[$type],':</td><td>',$cnt,'</td></tr>';
                                                    }
                                                ?>
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
                                        <a href="#" title="Alt+S" class="btn btn-primary shortcut-hint btn-submit" id="btn_submit"><?php echo _('Submit')?></a>
                                        <a href="record.php?problem_id=<?php echo $prob_id?>" class="btn btn-success"><?php echo _('Record')?></a>
                                        <a href="board.php?problem_id=<?php echo $prob_id;?>" class="btn btn-warning"><?php echo _('Board')?></a>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <?php if(check_priv(PRIV_PROBLEM)){?>
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <div class="panel panel-default problem-operation" style="margin-top:10px">
                                        <div class="panel-body">
                                            <a href="editproblem.php?problem_id=<?php echo $prob_id?>" class="btn btn-primary"><?php echo _('Edit')?></a>
                                            <a href="testcase.php?problem_id=<?php echo $prob_id?>" class="btn btn-warning"><?php echo _('Test Cases')?></a>
                                            <span id="action_delete" class="btn btn-danger"><?php echo $row_prob[11] ? _('Recover') : _('Delete');?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        if(isset($note_content)){ ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel-group <?php if(!$note_exist) echo 'collapse'?>" id="note_panel">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <b><?php echo _('Notes')?></b>
                                                <a data-toggle="modal" href="#NoteModal" class="btn btn-xs btn-primary pull-right" id="action_edit_note"><?php echo _('Edit')?></a>
                                            </div>
                                            <div class="panel-collapse in collapse">
                                                <div class="panel-body note-short" id="note_content">
                                                    <?php echo htmlspecialchars($note_content);?>
                                                </div>
                                                <div class="panel-body">
                                                    <p><strong><?php echo _('Tags')?></strong></p>
                                                    <span id="user_tags"><?php if(isset($tags)) echo htmlspecialchars($tags)?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="#" class="btn btn-success form-control <?php if($note_exist) echo 'collapse'?>" style="margin-bottom:10px" id="btn_note" data-toggle="modal" data-target="#NoteModal">
                                        <i class="fa fa-fw fa-pencil"></i><?php echo _('Add Notes/Tags')?>
                                    </a>
                                </div>
                            </div>
                        <?php }
                        if(isset($mark_btn_class)){ ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="#" class="<?php echo $mark_btn_class?>" id="action_mark">
                                        <i class="<?php echo $mark_icon_class ?>"></i>
                                        <span id="action_mark_html"><?php echo $mark_btn_html?></span>
                                    </a>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                <?php }?>
            </div>
            <?php require __DIR__.'/inc/footer.php';?>
        </div>

        <div class="modal fade" id="SubmitModal" data-keyboard="false">
            <div class="modal-dialog" id="submit_dialog">
                <div class="modal-content" id="submit_content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo _('Submit')," #$prob_id"?></h4>
                    </div>
                    <form method="post" id="form_submit">
                        <input type="hidden" name="op" value="judge">
                        <input type="hidden" id="prob_input" name="problem" value="<?php echo $prob_id ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <textarea spellcheck="false" class="form-control" style="resize:none" id="detail_input" rows="14" name="source" placeholder="<?php echo _('Type your code here...')?>"></textarea>
                            </div>
                            <?php if($pref->edrmode=='vim') echo '<samp>',_('Command: '),'<span id="vim_cmd"></span></samp>'?>
                            <div class="alert alert-danger collapse" id="submit_res"></div>
                        </div>
                        <div class="modal-footer form-inline">
                            <div class="row">
                                <div class="form-group col-xs-12 col-sm-5">
                                    <div class="input-group pull-left">
                                        <span class="input-group-addon">
                                            <input type="checkbox" <?php if($pref->sharecode=='on')echo 'checked';?> name="public"><?php echo _('Open Source')?>
                                        </span>
                                        <select class="form-control" name="language" id="slt_lang">
                                            <?php
                                                foreach($LANG_NAME as $langid => $lang){
                                                    echo "<option value=\"$langid\" ";
                                                    if(isset($_SESSION['lang']) && $_SESSION['lang']==$langid)
                                                        echo 'selected="selected"';
                                                    echo ">$lang</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>  
                                </div>
                                <div class="form-group col-xs-12 col-sm-7">
                                    <button type="button" id="btn_clear" class="btn btn-danger shortcut-hint" title="Alt+C"><?php echo _('Clear')?></a>
                                    <button class="btn btn-primary shortcut-hint" title="Alt+S" type="submit"><?php echo _('Submit')?></button>
                                    <a href="#" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="NoteModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo _('Notes')," #$prob_id"?></h4>
                    </div>
                    <form method="post" id="form_note"> 
                        <div class="modal-body">
                            <div class="form-group">
                                <textarea class="form-control" style="resize:none" rows="14" placeholder="<?php echo _('Write something here...')?>" name="content"><?php if(isset($note_content)) echo $note_content?></textarea>
                                <span class="help-block"><?php echo _('Only you can read & edit your very own notes.')?></span>
                                <input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
                            </div>
                            <div class="alert alert-danger collapse" id="notes_res"></div>
                        </div>
                        <div class="modal-footer form-inline">
                            <div class="row">
                                <div class="form-group col-xs-6 col-sm-7">
                                    <div class="input-group pull-left">
                                        <span class="input-group-addon"><b><?php echo _('Tags')?></b></span>
                                        <input class="form-control" id="tags_edit" type="text" name="tags" value="<?php if(isset($tags)) echo $tags?>">
                                    </div>
                                </div>
                                <div class="form-group col-xs-6 col-sm-5">
                                    <button class="btn btn-primary" id="note_submit" type="submit"><?php echo _('Save')?></button>
                                    <a href="#" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
 
        <div id="show_tool" class="bottom-right collapse">
            <span id="btn_submit2" title="Alt+S" class="btn btn-primary shortcut-hint btn-submit"><?php echo _('Submit')?></span>
            <span id="btn_show" title="Alt+H" class="btn btn btn-primary shortcut-hint"><i class="fa fa-fw fa-toggle-off"></i> <?php echo _('Show Sidebar')?></span>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script src="/assets/js/clipboard.min.js"></script>
        <script src="assets_webpack/highlight.js"></script>

        <script>
            window.problemConfig = <?php echo json_encode(array('id' => $prob_id)) ?>;
            window.editorConfig = <?php echo json_encode(array('enabled' => $pref->edrmode != 'off', 'mode' => $pref->edrmode)) ?>;
        </script>
        <script src="assets_webpack/problempage.js"></script>
        <script type="text/javascript">
            var prob=<?php echo $prob_id?>;
            <?php if($is_contest==true && $cont_status==1 && !isset($user_quit)){?> 
                var t=new Date(<?php echo strtotime($row_cont[2])*1000?>);
                var EndTime=t.getTime();
                var t1=new Date(),t2=new Date(<?php echo time()*1000?>);
                var SyncTime=t1.getTime()-t2.getTime();
                function GetRTime(){
                    var NowTime=new Date();
                    var nMS=EndTime-NowTime.getTime()+SyncTime;
                    if(nMS<0)
                        $('#cont_st').html('<?php echo _('Contest has ended')?>');
                    else{
                        var nH=Math.floor(nMS/3600000),nM=Math.floor(nMS/60000)%60,nS=Math.floor(nMS/1000)%60;
                        if(nH<10) nH='0'+nH;
                        if(nM<10) nM='0'+nM;
                        if(nS<10) nS='0'+nS;
                        $("#thour").text(nH);
                        $("#tmin").text(nM);
                        $("#tsec").text(nS);
                    }
                }
            <?php }?>
            
            $(document).ready(function(){
                $('table').each(function(){
                    if(!$(this).hasClass('table'))
                        $('table').addClass('table table-bordered table-condensed');
                });
                var clipin = new Clipboard('#copy_in'),clipout = new Clipboard('#copy_out');
                clipin.on('success', function(e){
                    $('#copy_in').attr('title','<?php echo _('Copied!')?>');
                    $('#copy_in').tooltip('show');
                    setTimeout("$('#copy_in').tooltip('destroy')",800);
                });
                clipin.on('error', function(e){
                    $('#copy_in').attr('title','<?php echo _('Failed...')?>');
                    $('#copy_in').tooltip('show');
                    setTimeout("$('#copy_in').tooltip('destroy')",800);
                });
                clipout.on('success', function(e){
                    $('#copy_out').attr('title','<?php echo _('Copied!')?>');
                    $('#copy_out').tooltip('show');
                    setTimeout("$('#copy_out').tooltip('destroy')",800);
                });
                clipout.on('success', function(e){
                    $('#copy_out').attr('title','<?php echo _('Failed...')?>');
                    $('#copy_out').tooltip('show');
                    setTimeout("$('#copy_out').tooltip('destroy')",800);
                });
                var hide_info = 0;
                <?php if($is_contest==true&&isset($rem_time)&&$rem_time>0){?>
                    var timer_rt = window.setInterval("GetRTime()", 1000);
                <?php }?>
                $('#action_delete').click(function(){
                    $.ajax({
                        type:"POST",
                        url:"api/ajax_editproblem.php",
                        data:{op:'del',problem_id:prob},
                        success:function(msg){
                            if(msg.success){
                                window.location.reload();
                            }else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg.message).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        }
                    });
                });
                $('#form_submit').submit(function(){
                    var code = window.editor.getValue();
                    if($.trim(code) == '' || code.length > 30000)
                        $('#submit_res').html('<i class="fa fa-fw fa-remove"></i> <?php echo _('Your code is too long or too short...')?>').slideDown();
                    else{
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_submit.php",
                            data:$('#form_submit').serialize() + "&source=" + encodeURIComponent(code),
                            success:function(msg){
                                if(msg.indexOf('success_')!=-1){
                                    $('#submit_res').slideUp();
                                    window.location.href='wait.php?key='+msg.substring(8,msg.length);
                                }
                            else 
                                $('#submit_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
                            }
                        });
                    }
                    return false;
                });
                $('#form_note').submit(function(){
                    var data = $(this).serializeArray();
                    $.post('api/ajax_usernote.php', data, function(res){
                        if(/success/.test(res)){
                            var notag=0;
                            for(var i=data.length-1; i>=0; i--){
                                if(data[i].name=='tags'){
                                    if($.trim(data[i].value)=='')
                                        notag=1;
                                    else{
                                        notag=0;
                                        $('#user_tags').html(data[i].value);
                                    }
                                }else if(data[i].name=='content'){
                                    if($.trim(data[i].value)==''&&notag==1){
                                        $('#btn_note').css('display','inline-block');
                                        $('#note_panel').hide();
                                    }else{
                                        $('#note_content').html(data[i].value);
                                        $('#note_panel').show();
                                        $('#btn_note').css('display','none');
                                    }
                                }
                            };
                            $('#NoteModal').modal('hide');
                        }else
                            $('#notes_res').html('<i class="fa fa-fw fa-remove"></i> '+res).slideDown();
                    });
                    return false;
                });
                $('#NoteModal').on('show', function(){
                    $('#form_note textarea').val($('#note_content').text());
                    $('#tags_edit').val($('#user_tags').text());
                });
                $("#action_mark").click(function(){
                    var op;
                    if($('#action_mark_html').html()=='<?php echo _('Mark')?>')
                        op="add_saved";
                    else
                        op="rm_saved";    
                    $.get("api/ajax_mark.php?type=1&prob="+prob+"&op="+op,function(msg){
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
                function toggle_info(){
                    if(hide_info) {
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
                // not working
                reg_hotkey(83, function(){ //Alt+S
                    if($('#SubmitModal').is(":visible"))
                        $('#form_submit').submit();
                    else
                        click_submit();
                });
                reg_hotkey(72, toggle_info); //Alt+H
            });
        </script>
    </body>
</html>
