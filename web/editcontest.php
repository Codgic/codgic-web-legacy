<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/func/contest.php';

if(!check_priv(PRIV_PROBLEM))
    include __DIR__.'/inc/403.php';
else if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
    $_SESSION['admin_retpage'] = $_SERVER['REQUEST_URI'];
    header("Location: admin_auth.php");
    exit();
}else{
    require __DIR__.'/lib/problem_flags.php';
    if(!isset($con))
        require __DIR__.'/conf/database.php';
    $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
    if(!isset($_GET['contest_id'])){
        $p_type='add';
        $inTitle=_('New Contest');
    }else{
        $p_type='edit';
        $cont_id=intval($_GET['contest_id']);  
        $inTitle=_('Edit Contest')." #$cont_id";
        $query="select title,start_time,end_time,description,source,judge_way,has_tex,hide_source_code from contest where contest_id=$cont_id";
        $result=mysqli_query($con,$query);
        $row=mysqli_fetch_row($result);
        if(!$row)
            $info=_('There\'s no such contest');
        else{ 
            switch ($row[5]) {
                case 0:
                    $way='train';
                    break;
                case 1:
                    $way='cwoj';
                    break;
                case 2:
                    $way='acm-like';
                    break;
                case 3:
                    $way='oi-like';
                    break;
            }
        }
        $option_level=($row[6]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
        $option_hide=(($row[6]&PROB_IS_HIDE)?'checked':'');
        $option_hide_source=(($row[7]) ? 'checked' : '');
    }

    $Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php
        require __DIR__.'/inc/head.php';
    ?>
    <link rel="stylesheet" href="/assets/css/simplemde.min.css" type="text/css" />
    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>
        <div class="container edit-page">
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
                <form action="#" method="post" id="edit_form" style="padding-top:10px">
                    <input type="hidden" name="op" value="<?php echo $p_type?>">
                    <?php if($p_type=='edit'){?>
                        <input type="hidden" name="contest_id" value="<?php echo $cont_id?>">
                    <?php }?>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-12" id="ctl_title">
                            <label class="control-label" for="input_title">
                                <?php echo _('Title')?>
                            </label>
                            <input type="text" class="form-control" name="title" id="input_title" placeholder="<?php echo _('Pleae enter contest title...')?>" value="<?php if($p_type=='edit') echo $row[0]?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_probs">
                            <label class="control-label" for="input_probs">
                                <?php echo _('Problems'),_(' (Format: 1000,1001,...)')?>
                            </label>
                            <?php
                                echo '<input type="text" class="form-control" name="problems" id="input_probs" placeholder="',_('Please specify Problem IDs...'),'"';
                                if($p_type=='edit'){
                                    $text='';
                                    $t=mysqli_query($con, "select problem_id from contest_problem where contest_id=$cont_id order by place");
                                    while($t_row=mysqli_fetch_row($t))
                                        $text.=$t_row[0].',';
                                    echo ' value="',substr($text,0,-1),'"';
                                }
                                echo '>';
                            ?>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6" id="ctl_owners">
                            <label class="control-label" for="input_owners">
                                <?php echo _('Owners'),_(' (Format: user1,user2,...)')?>
                                <a href="#" data-toggle="tooltip" title="<?php echo _('Contest owners can view contest status without the need of enrollment. Leave it blank if you don\'t want any owners.')?>"><i class="fa fa-question-circle"></i></a>
                            </label>
                            <?php
                                echo '<input type="text" class="form-control" name="owners" id="input_owners" placeholder="',_('Please specify User IDs...'),'"';
                                if($p_type=='edit'){
                                    $text='';
                                    $t=mysqli_query($con, "select user_id from contest_owner where contest_id=$cont_id");
                                    while($t_row=mysqli_fetch_row($t))
                                        $text.=$t_row[0].',';
                                    echo ' value="',substr($text,0,-1),'"';
                                }else{
                                    echo ' value="',$_SESSION['user'],'"';
                                }
                                echo '>';
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-6" id="ctl_starttime">
                            <label class="control-label" for="input_starttime">
                                <?php echo _('Start Time')?>
                            </label>
                            <input type="text" name="start_time" id="input_starttime" class="form-control" placeholder="<?php echo _('yyyy-mm-dd hh:mm:ss')?>" value="<?php if($p_type=='edit') echo $row[1]; else echo date("Y-m-d H:i:s",time())?>">
                        </div>
                        <div class="form-group col-xs-6" id="ctl_endtime">
                            <label class="control-label" for="input_endtime">
                                <?php echo _('End Time')?>
                            </label>
                            <input type="text" name="end_time" id="input_endtime" class="form-control" placeholder="<?php echo _('yyyy-mm-dd hh:mm:ss')?>" value="<?php if($p_type=='edit') echo $row[2]; else echo date("Y-m-d H:i:s",time()+14400)?>">
                        </div> 
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-4">
                            <label class="control-label" for="input_cmp">
                                <?php echo _('Format')?>
                            </label>
                            <a href="#" id="btn_togglehelp"><i class="fa fa-arrow-circle-down"></i> Show help</a>
                            <select class="form-control" name="judge" id="input_cmp">
                                <option value="train"><?php echo _('Training')?></option>
                                <option value="cwoj"><?php echo _('CWOJ')?></option>
                                <option value="acm-like"><?php echo _('ACM-like')?></option>
                                <option value="oi-like"><?php echo _('OI-like')?></option>
                            </select>
                            <?php if($p_type=='edit'){?>
                                <script>
                                    $('#input_cmp').val("<?php echo $way?>");
                                </script>
                            <?php }?>
                        </div>
                        <div class="form-group col-xs-6 col-sm-3">
                            <label class="control-label" for="input_level">
                                <?php echo _('Level')?>
                            </label>
                            <select class="form-control" name="option_level" id="input_level">
                                <script>
                                    <?php if($p_type=='add'){?>
                                        for(var i=0;i<=<?php echo $level_max?>;i++){
                                            document.write('<option value="'+i+'">'+i+'</option>')
                                        }
                                    <?php }else{?>
                                        for(var i=0;i<=<?php echo $level_max?>;i++){
                                            if(i==<?php echo $option_level?>)
                                                document.write('<option selected value="'+i+'">'+i+'</option>')
                                            else
                                                document.write('<option value="'+i+'">'+i+'</option>')
                                        };
                                    <?php }?>
                                </script>
                            </select>
                        </div>
                        <div class="form-group col-xs-6 col-sm-5">
                            <label class="control-label">
                                <?php echo _('Options')?>
                            </label>
                            <div class="checkbox">
                                <label style="margin-right:10px;">
                                    <input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_cont"><?php echo _('Hide')?>
                                </label>
                                <label>
                                    <input <?php if(isset($option_hide_source)) echo $option_hide_source?> type="checkbox" name="hide_source"><?php echo _('Hide source code')?>
                                </label>
                            </div>  
                            <div class="checkbox">
                            </div>  
                        </div>
                        <div class="form-group col-xs-12 collapse"  id="div_judgehelp">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="input_des">
                                <?php echo _('Description')?>
                            </label>
                            <a href="#" data-toggle="tooltip" title="<?php echo _('Use [tex][/tex] for common formulas and [inline][/inline] for inline formulas.')?>"><i class="fa fa-question-circle"></i> <?php echo 'Mathjax'?></a>
                            <textarea class="form-control col-xs-12" name="description" id="input_des" rows="13" placeholder="<?php echo _('Please create a description for your contest...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[3])?></textarea>
                        </div>
                    </div>       
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="input_tags">
                                <?php echo _('Tags')?>
                            </label>
                            <input class="form-control col-xs-12" type="text" name="source" id="input_tags" placeholder="<?php echo _('Please specify some tags for your contest...')?>" value="<?php if($p_type=='edit') echo htmlspecialchars($row[4])?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <div class="alert alert-danger collapse" id="alert_error"></div>
                            <button class="btn btn-primary" type="submit"><?php echo _('Submit')?></button>
                        </div>
                    </div>
                </form>
            <?php }
            require __DIR__.'/inc/footer.php';?>
        </div>
        
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script src="/assets/js/simplemde.min.js?v=1"></script>
        <script src="/assets_webpack/highlight.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                var simplemde = new SimpleMDE({
                    element: document.getElementById("input_des"),
                    renderingConfig: {
                        codeSyntaxHighlighting: true,
                    },
                    indentWithTabs: false,
                    spellChecker: false,
                    status: false,
                    toolbarTips: false,
                    hideIcons: ["guide"]
                });
                $("[data-toggle='tooltip']").tooltip();
                function show_help(way){
                    if(way=='train')
                        $('#div_judgehelp span').html('<?php echo get_judgeway_destext(0);?>');
                    else if(way=='cwoj')
                        $('#div_judgehelp span').html('<?php echo get_judgeway_destext(1);?>');
                    else if(way=='acm-like')
                        $('#div_judgehelp span').html('<?php echo get_judgeway_destext(2);?>');
                    else if(way=='oi-like')
                        $('#div_judgehelp span').html('<?php echo get_judgeway_destext(3);?>');
                }
                (function(){
                    show_help($('#input_cmp').val());
                })();
                $('#btn_togglehelp').click(function(){
                    if($('#div_judgehelp').is(":visible"))
                        $('#btn_togglehelp').html('<i class="fa fa-arrow-circle-down"></i> <?php echo _('Show Help')?>');
                    else
                        $('#btn_togglehelp').html('<i class="fa fa-arrow-circle-up"></i> <?php echo _('Hide Help')?>');
                    $('#div_judgehelp').slideToggle();
                });
                $('#input_cmp').change(function(E){show_help($(E.target).val());});
                $('#edit_form textarea').focus(function(e){cur=e.target;});
                $('#edit_form').submit(function(){
                    var b=false;
                    $('#alert_error').slideUp;
                    if(!$.trim($('#input_title').val())){
                        $('#ctl_title').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_title').removeClass('has-error');
                    if(!$.trim($('#input_probs').val())){
                        $('#ctl_probs').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_probs').removeClass('has-error');
                    if(!$.trim($('#input_starttime').val())){
                        $('#ctl_starttime').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_starttime').removeClass('has-error');
                    if(!$.trim($('#input_endtime').val())){
                        $('#ctl_endtime').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_endtime').removeClass('has-error');
                    if(b)
                        $('html, body').animate({scrollTop:0}, '200');
                    else{
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_editcontest.php",
                            data:$('#edit_form').serialize(),
                            success:function(msg){
                                if (msg.success){
                                    window.location = "contestpage.php?contest_id=" + msg.contestID;
                                }else{
                                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg.message).slideDown();
                                }
                            }
                        });
                    }
                    return false;
                });
            });
        </script>
    </body>
</html>
<?php }?>
