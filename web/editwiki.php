<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

if(!isset($_SESSION['user']))
    include __DIR__.'/inc/403.php';
else{
    require __DIR__.'/lib/problem_flags.php';
    require __DIR__.'/conf/database.php';
    if(!isset($_GET['wiki_id'])){
        $p_type='add';
        $inTitle=_('New Wiki');
        $wiki_id=1;
        $result=mysqli_query($con,'select max(wiki_id) from wiki');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $wiki_id=intval($row[0])+1;
    }else{
        $p_type='edit';
        $wiki_id=intval($_GET['wiki_id']);
        $inTitle=_('Edit Wiki')." #$wiki_id";
        if($wiki_id<1)
            $info=_('There\'s no such wiki');
        else{
            $query="select title,content,tags,privilege,defunct from wiki where wiki_id=$wiki_id and is_max=1";
            $result=mysqli_query($con,$query);
            $row=mysqli_fetch_row($result);
            if(!$row)
                $info=_('There\'s no such wiki');
            else{
                if($row[3]==1)
                    $option_hide=1;
                else
                    $option_hide=0;
            }
        }
    }

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
    <link rel="stylesheet" href="/assets/css/simplemde.min.css" type="text/css" />
    <body>
        <?php
            require __DIR__.'/inc/navbar.php';
        ?>
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
                    <input type="hidden" name="wiki_id" value="<?php echo $wiki_id?>">
                    <div class="row">
                        <div class="form-group col-xs-12" id="ctl_title">
                            <label class="control-label" for="input_title">
                                <?php echo _('Title')?>
                            </label>
                            <input type="text" class="form-control" name="title" id="input_title" placeholder="<?php echo _('Please enter wiki title...')?>" value="<?php if($p_type=='edit') echo $row[0]?>">
                        </div>
                    </div>
                    <?php if(check_priv(PRIV_PROBLEM)){?>
                    <div class="row">
                        <div class="form-group col-xs-6 col-sm-4">
                            <label class="control-label">
                                <?php echo _('Options')?>
                            </label>
                            <div class="checkbox">
                                <label>
                                    <input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_cont"><?php echo _('Hide')?>
                                </label>
                            </div>  
                        </div>
                    </div>
                    <?php }?>
                    <div class="row">
                        <div class="form-group col-xs-12" id="ctl_des">
                            <label class="control-label" for="input_des">
                                <?php echo _('Content')?>
                            </label>
                            <textarea class="form-control col-xs-12" id="input_des" name="content" rows="14" placeholder="<?php echo _('Let\'s start sharing knowledge here...')?>"><?php if($p_type=='edit') echo htmlspecialchars($row[1])?></textarea>
                        </div>
                    </div>       
                    <div class="row">
                        <div class="form-group col-xs-12">
                             <label class="control-label" for="input_tags">
                                <?php echo _('Tags')?>
                            </label>
                            <input class="form-control col-xs-12" id="input_tags" type="text" name="tags" placeholder="<?php echo _('Please specify some tags for your wiki...')?>" value="<?php if($p_type=='edit') echo htmlspecialchars($row[2])?>">
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
        <script src="/assets/highlight/highlight.pack.js"></script>
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
                $('#edit_form textarea').focus(function(e){cur=e.target;});
                $('#edit_form').submit(function(){
                    var b=false;
                    $('#alert_error').slideUp;
                    if(!$.trim($('#input_title').val())){
                        $('#ctl_title').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_title').removeClass('has-error');
                    if(!$.trim($('#input_des').val())){
                        $('#ctl_des').addClass('has-error');
                        b=true;
                    }else
                        $('#ctl_des').removeClass('has-error');
                    if(b)
                        $('html, body').animate({scrollTop:0}, '200');
                    else{
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_editwiki.php",
                            data:$('#edit_form').serialize(),
                            success:function(msg){
                                if (msg.success){
                                    window.location = "wikipage.php?wiki_id=" + msg.wikiID;
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