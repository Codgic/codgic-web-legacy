<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

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
    if(!isset($_GET['problem_id'])){
        $p_type='add';
        $inTitle=_('New Problem');
    }else{
        $p_type='edit';
        $prob_id=intval($_GET['problem_id']);  
        $inTitle=_('Edit Problem')." #$prob_id";
        $query="select title,description,input,output,sample_input,sample_output,hint,source,case_time_limit,memory_limit,case_score,compare_way,has_tex from problem where problem_id=$prob_id";
        $result=mysqli_query($con,$query);
        $row=mysqli_fetch_row($result);
        if(!$row)
            $info=_('There\'s no such problem');
        else{ 
            switch($row[11] >> 16){
                case 0:
                    $way='tra';
                    break;
                case 1:
                    $way='float';
                    $prec=($row[11] & 65535);
                break;
                case 2:
                    $way='int';
                    break;
                case 3:
                    $way='spj';
                    break;
            }
        }
    
        $option_opensource=0;
        if($row[12]&PROB_DISABLE_OPENSOURCE)
            $option_opensource=2;
        else if($row[12]&PROB_SOLVED_OPENSOURCE)
            $option_opensource=1;
        $option_level=($row[12]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
        $option_hide=(($row[12]&PROB_IS_HIDE)?'checked':'');
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
                        <?php echo $info?></p>
                </div>
            <?php }else{?>
                <form action="#" method="post" id="edit_form" style="padding-top:10px">
                    <input type="hidden" name="op" value="<?php echo $p_type?>">
                    <?php if($p_type=='edit'){?>
                        <input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
                    <?php }?>
                    <div class="row">
                        <div class="form-group col-xs-12" id="ctl_title">
                            <label class="control-label" for="input_title">
                                <?php echo _('Title')?>
                            </label>
                            <input type="text" name="title" id="input_title" class="form-control" placeholder="<?php echo _('Please enter problem title...')?>" value="<?php if($p_type=='edit') echo $row[0]?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4" id="ctl_time">
                            <label class="control-label" for="input_time">
                                <?php echo _('Time Limit (ms)')?>
                            </label>
                                <input id="input_time" name="time" id="input_time" class="form-control" type="number" placeholder="<?php echo _('Please enter time limit...')?>" value="<?php if($p_type=='edit') echo $row[8]; else echo '1000'?>">
                        </div>
                        <div class="form-group col-xs-4" id="ctl_memory">
                            <label class="control-label" for="input_memory">
                                <?php echo _('Memory Limit (KB)')?>
                            </label>
                            <input id="input_memory" name="memory" id="input_memory" class="form-control" type="number" placeholder="<?php echo _('Please enter memory limit...')?>" value="<?php if($p_type=='edit') echo $row[9]; else echo '65536'?>">
                        </div>  
                        <div class="form-group col-xs-4" id="ctl_score">
                            <label class="control-label" for="input_score">
                                <?php echo _('Case Score (<=100)')?>
                            </label>
                            <input id="input_score" name="score" id="input_score" class="form-control" type="number" placeholder="<?php echo _('Please enter case score...')?>" value="<?php if($p_type=='edit') echo $row[10]; else echo '10'?>">
                        </div>    
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-4" id="div_cmp">
                            <label class="control-label" for="input_cmp">
                                <?php echo _('Checker')?>
                                <a href="#" id="cmp_help" data-toggle="tooltip"><i class="fa fa-question-circle"></i></a>
                            </label>
                            <select class="form-control" name="compare" id="input_cmp">
                                <option value="tra"><?php echo _('Traditional')?></option>
                                <option value="int"><?php echo _('Integer')?></option>
                                <option value="float"><?php echo _('Real Number')?></option>
                                <option value="spj"><?php echo _('Special Judge')?></option>
                            </select>
                            <?php if($p_type=='edit'){?>
                                <script>
                                    $('#input_cmp').val("<?php echo $way?>");
                                </script>
                            <?php }?>
                        </div>
                        <div class="form-group col-xs-4 col-sm-2 col-md-1 collapse" id="div_cmp_pre">
                            <label class="control-label" for="input_cmp_pre">
                                <?php echo _('Precision')?>
                            </label>
                            <select name="precision" class="form-control" id="input_cmp_pre"></select>
                        </div>
                        <div class="form-group col-xs-6 col-sm-4"> 
                            <label class="control-label" for="input_osc">
                                <?php echo _('Open Source to')?>
                            </label>
                            <select class="form-control" name="option_osc" id="input_osc">
                                <option value="0"><?php echo _('Everyone')?></option>
                                <option value="1"><?php echo _('Solved Users')?></option>
                                <option value="2"><?php echo _('Nobody')?></option>
                            </select>
                            <?php if($p_type=='edit'){?>
                                <script>
                                    document.getElementById('input_osc').selectedIndex="<?php echo $option_opensource?>";
                                </script>
                                <?php }?>
                        </div>
                        <div class="form-group col-xs-6 col-sm-2">
                            <label class="control-label" for="input_level">
                                <?php echo _('Level')?>
                            </label>
                            <select class="form-control" name="option_level" id="input_level">
                                <script>
                                    <?php if($p_type=='add'){?>
                                        for(var i=0;i<=<?php echo $level_max?>;i++){
                                        document.write('<option value="'+i+'">'+i+'</option>');
                                        }
                                    <?php }else{?>
                                        for(var i=0;i<=<?php echo $level_max?>;i++){
                                            if(i==<?php echo $option_level?>)
                                                document.write('<option selected value="'+i+'">'+i+'</option>');
                                            else
                                                document.write('<option value="'+i+'">'+i+'</option>');
                                        }
                                    <?php }?>
                                </script>
                            </select>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2">
                            <label class="control-label">
                                <?php echo _('Options')?>
                            </label>
                            <div class="checkbox">
                                <label>
                                    <input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_prob"><?php echo _('Hide')?>
                                </label>
                            </div>  
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="input_des">
                                <?php echo _('Description')?>
                            </label>
                            <a href="#" data-toggle="tooltip" title="<?php echo _('Use [tex][/tex] for common formulas and [inline][/inline] for inline formulas.')?>"><i class="fa fa-question-circle"></i> <?php echo 'Mathjax'?></a>
                            <textarea class="form-control col-xs-12 simplemde" id="input_des" name="description" rows="13" placeholder="<?php echo _('Please create a description for your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[1])?></textarea>
                        </div>
                    </div>       
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_input">
                                <?php echo _('Input')?>
                            </label>
                            <textarea class="form-control col-xs-12 simplemde" id="input_input" name="input" rows="8" placeholder="<?php echo _('Please specify the input format for your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[2])?></textarea>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_output">
                                <?php echo _('Output')?>
                            </label>
                            <textarea class="form-control col-xs-12 simplemde" id="input_output" name="output" rows="8" placeholder="<?php echo _('Please specify the output format for your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[3])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_sampinput">
                                <?php echo _('Sample Input')?>
                            </label>
                            <textarea class="form-control col-xs-12 simplemde no-toolbar" id="input_sampinput" name="sample_input" rows="8" placeholder="<?php echo _('Please provide an input sample of your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[4])?></textarea>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6">
                            <label class="control-label" for="input_sampoutput">
                                <?php echo _('Sample Output')?>
                            </label>
                            <textarea class="form-control col-xs-12 simplemde no-toolbar" id="input_sampoutput" name="sample_output" rows="8" placeholder="<?php echo _('Please provide an output sample of your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[5])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="input_hints">
                                <?php echo _('Hints')?>
                            </label>
                            <textarea class="form-control col-xs-12 simplemde" id="input_hints" name="hint" rows="8" placeholder="<?php echo _('Please provide some hints of your problem...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[6])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <label class="control-label" for="input_tags">
                                <?php echo _('Tags')?>    
                            </label>
                            <input class="form-control col-xs-12" id="input_tags" type="text" name="source" placeholder="<?php echo _('Please specify some tags for your problem...')?>" value="<?php if($p_type=='edit') echo htmlspecialchars($row[7])?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12">
                            <div class="alert alert-danger collapse" id="alert_error"></div> 
                            <button type="submit" class="btn btn-primary"><?php echo _('Submit')?></button>
                        </div>
                    </div>
                </form>
            <?php }
            require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script src="/assets/js/simplemde.min.js"></script>
        <script src="/assets_webpack/highlight.js"></script>
        <script type="text/javascript"> 
            $(document).ready(function(){
                $("[data-toggle='tooltip']").tooltip();
                $('.simplemde').each(function() {
                    if($(this).hasClass('no-toolbar'))
                        var simplemde = new SimpleMDE({
                            element: this,
                            renderingConfig: {
                                codeSyntaxHighlighting: true,
                            },
                            indentWithTabs: false,
                            forceSync: true,
                            spellChecker: false,
                            status: false,
                            toolbar: false,
                            toolbarTips: false,
                            hideIcons: ["guide"]
                        });
                    else
                        var simplemde = new SimpleMDE({
                            element: this,
                            renderingConfig: {
                                codeSyntaxHighlighting: true,
                            },
                            indentWithTabs: false,
                            forceSync: true,
                            spellChecker: false,
                            status: false,
                            toolbarTips: false,
                            hideIcons: ["guide"]
                        });
                    simplemde.render();
                });
                function show_help(way){
                    if(way=='float'){
                        $('#div_cmp').removeClass('col-xs-12 col-sm-4').addClass('col-xs-8 col-sm-3');
                        $('#div_cmp_pre').show();
                        $('#cmp_help').attr('title','<?php echo _('Output can ONLY contain Real Numbers. Please select precision.')?>');
                    }else{
                        $('#div_cmp').removeClass('col-xs-8 col-sm-3').addClass('col-xs-12 col-sm-4');
                        $('#div_cmp_pre').hide();
                        if(way=='tra')
                            $('#cmp_help').attr('title','<?php echo _('Generic comparsion. Trailing space is ignored.')?>');
                        else if(way=='int')
                            $('#cmp_help').attr('title','<?php echo _('Output can ONLY contain Integers.')?>');
                        else if(way=='spj')
                            $('#cmp_help').attr('title','<?php echo _('Please ensure there exists a "spj.cpp" in your data folder.')?>');
                    }
                    $("[data-toggle='tooltip']").tooltip('fixTitle');
                }
                (function(){
                    var option='';
                    for(var i=0;i<10;i++)
                        option+='<option value="'+i+'">'+i+'</option>';
                    $('#input_cmp_pre').html(option);
                    <?php if(isset($prec)){?>
                        $('#input_cmp_pre').val(<?php echo $prec?>);
                    <?php }?>
                    show_help($('#input_cmp').val());
                })();
                $('#input_cmp').change(function(E){show_help($(E.target).val());});
                $('#btn_hide').click(function(){
                    $('#tools').fadeOut();
                    $('#showtools').fadeIn();
                });
                $('#btn_show').click(function(){
                    $('#tools').fadeIn();
                    $('#showtools').fadeOut();
                });
                $('#edit_form textarea').focus(function(e){cur=e.target;});
                $('#edit_form').submit(function(){
                    var b=false;
                    $('#alert_error').slideUp;
                    if(!$.trim($('#input_title').val())){
                        $('#ctl_title').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_title').removeClass('has-error');
                    if(!$.trim($('#input_memory').val())){
                        $('#ctl_memory').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_memory').removeClass('has-error');
                    if(!$.trim($('#input_time').val())){
                        $('#ctl_time').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_time').removeClass('has-error');
                    if(!$.trim($('#input_score').val())){
                        $('#ctl_score').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_score').removeClass('has-error');
                    if(b)
                        $('html, body').animate({scrollTop:0}, '200');
                    else{
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_editproblem.php",
                            data:$('#edit_form').serialize(),
                            success:function(msg){
                                if (msg.success){
                                    window.location = "problempage.php?problem_id=" + msg.problemID;
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
