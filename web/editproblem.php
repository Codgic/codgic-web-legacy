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
    <?php require __DIR__.'/inc/head.php'; ?>

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
                <div class="collapse" id="showtools">
                    <p><button class="btn btn-primary" id="btn_show"><?php echo _('Show Toolbar')?><i class="fa fa-fw fa-angle-right"></i></button></p>
                </div>
                <form action="#" method="post" id="edit_form" style="padding-top:10px">
                    <input type="hidden" name="op" value="<?php echo $p_type?>">
                    <?php if($p_type=='edit'){?>
                        <input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
                    <?php }?>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9" id="ctl_title">
                            <label class="control-label" for="input_title">
                                <?php echo _('Title')?>
                            </label>
                            <input type="text" name="title" id="input_title" class="form-control" value="<?php if($p_type=='edit') echo $row[0]?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-4 col-sm-3" id="ctl_time">
                            <label class="control-label" for="input_time">
                                <?php echo _('Time Limit (ms)')?>
                            </label>
                                <input id="input_time" name="time" id="input_time" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[8]; else echo '1000'?>">
                        </div>
                        <div class="form-group col-xs-4 col-sm-3" id="ctl_memory">
                            <label class="control-label" for="input_memory">
                                <?php echo _('Memory Limit (KB)')?>
                            </label>
                            <input id="input_memory" name="memory" id="input_memory" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[9]; else echo '65536'?>">
                        </div>  
                        <div class="form-group col-xs-4 col-sm-3" id="ctl_score">
                            <label class="control-label" for="input_score">
                                <?php echo _('Case Score (Full: 100)')?>
                            </label>
                            <input id="input_score" name="score" id="input_score" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[10]; else echo '10'?>">
                        </div>    
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-6 col-sm-4">
                            <label class="control-label" for="input_cmp">
                                <?php echo _('Comparison')?>
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
                            <span id="input_cmp_help" class="help-block"></span>
                        </div>
                        <div class="form-group col-xs-6 col-sm-4 collapse" id="div_cmp_pre">
                            <label class="control-label" for="input_cmp_pre">
                                <?php echo _('Precision')?>
                            </label>
                            <select name="precision" class="form-control" id="input_cmp_pre"></select>
                        </div>
                    </div>      
                    <div class="row">
                        <div class="form-group col-xs-6 col-sm-3"> 
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
                        <div class="form-group col-xs-6 col-sm-3">
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
                        <div class="form-group col-xs-12 col-sm-3">
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
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_des">
                                <?php echo _('Description')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_des" name="description" rows="13"><?php if($p_type=='edit') echo htmlspecialchars($row[1])?></textarea>
                        </div>
                    </div>       
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_input">
                                <?php echo _('Input')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_input" name="input" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[2])?></textarea>
                        </div>
                    </div>       
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_output">
                                <?php echo _('Output')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_output" name="output" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[3])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_sampinput">
                                <?php echo _('Sample Input')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_sampinput" name="sample_input" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[4])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_sampoutput">
                                <?php echo _('Sample Output')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_sampoutput" name="sample_output" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[5])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_hints">
                                <?php echo _('Hints')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_hints" name="hint" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[6])?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <label class="control-label" for="input_tags">
                                <?php echo _('Tags')?>    
                            </label>
                            <input class="form-control col-xs-12" id="input_tags" type="text" name="source" value="<?php if($p_type=='edit') echo htmlspecialchars($row[7])?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-xs-12 col-sm-9">
                            <div class="alert alert-danger collapse" id="alert_error"></div> 
                            <button type="submit" class="btn btn-primary"><?php echo _('Submit')?></button>
                        </div>
                    </div>
                </form>
            <?php }
            require __DIR__.'/inc/footer.php';?>
        </div>
        
        <div class="html-tools">
            <div class="panel panel-default" id="tools">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-fw fa-code"></i> <?php echo _('HTML Toolbar')?></h3>
                </div>
                <div class="panel-body">
                    <table class="table table-responsive table-bordered table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('Function')?></th>
                                <th><?php echo _('Code')?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><button class="btn btn-default" id="tool_less"><?php echo _('Smaller than(&lt;)')?></button></td>
                                <td>&amp;lt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_greater"><?php echo _('Greater than(&gt;)')?></button></td>
                                <td>&amp;gt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_img"><?php echo _('Image')?></button></td>
                                <td>&lt;img src=&quot;...&quot;&gt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_sup"><?php echo _('Superscript')?></button></td>
                                <td>&lt;sup&gt;...&lt;/sup&gt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_sub"><?php echo _('Subscript')?></button></td>
                                <td>&lt;sub&gt;...&lt;/sub&gt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_samp"><?php echo _('Monospace')?></button></td>
                                <td>&lt;samp&gt;...&lt;/samp&gt;</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_inline"><?php echo _('Inline TeX')?></button></td>
                                <td>[inline]...[/inline]</td>
                            </tr>
                            <tr>
                                <td><button class="btn btn-default" id="tool_tex"><?php echo _('TeX')?></button></td>
                                <td>[tex]...[/tex]</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btn-group text-center" style="margin-top:10px">
                        <button class="btn btn-success" id="btn_upload"><?php echo _('Upload Image')?></button>
                        <button class="btn btn-primary" id="btn_hide"><?php echo _('Hide Toolbar')?><i class="fa fa-fw fa-angle-left"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript"> 
            $(document).ready(function(){
                var loffset=window.screenLeft+200,toffset=window.screenTop+200;
                function show_help(way){
                    if(way=='float'){
                        $('#div_cmp_pre').show();
                        $('#input_cmp_help').html('<?php echo _('Output can ONLY contain Real Numbers. Please select precision.')?>');
                    }else{
                        $('#div_cmp_pre').hide();
                        if(way=='tra')
                            $('#input_cmp_help').html('<?php echo _('Generic comparsion. Trailing space is ignored.')?>');
                        else if(way=='int')
                            $('#input_cmp_help').html('<?php echo _('Output can ONLY contain Integers.')?>');
                        else if(way=='spj')
                            $('#input_cmp_help').html('<?php echo _('Please ensure there exists a "spj.cpp" in your data folder.')?>');
                    }
                }
                (function(){
                    var option='';
                    for(var i=0;i<10;i++)
                        option+='<option value="'+i+'">'+i+'</option>';
                    $('#input_cmp_pre').html(option);
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
                $('#btn_upload').click(function(){
                    window.open("upload.php",'upload_win2','left='+loffset+',top='+toffset+',width=400,height=300,toolbar=no,resizable=no,menubar=no,location=no,status=no');
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
            $('#tools').click(function(e){
                if(!($(e.target).is('button')))
                    return false;
                if(typeof(cur)=='undefined')
                    return false;
                var op=e.target.id,slt=GetSelection(cur);
                if(op=="tool_greater")
                    InsertString(cur,'&gt;');
                else if(op=="tool_less")
                    InsertString(cur,'&lt;');
                else if(op=="tool_img"){
                    var url=prompt('<?php echo _('Please enter the image link')?>',"");
                    if(url)
                        InsertString(cur,slt+'<img src="'+url+'">');
                }else if(op=="tool_inline"||op=="tool_tex"){
                    op=op.substr(5);
                    InsertString(cur,'['+op+']'+slt+'[/'+op+']');
                }else if(op=="btn_upload"||op=="btn_hide")
                    return false;
                else{
                    op=op.substr(5);
                    InsertString(cur,'<'+op+'>'+slt+'</'+op+'>');
                }
                return false;
            });
        });
    </script>
  </body>
</html>
<?php }?>
