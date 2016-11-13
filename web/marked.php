<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';

$type='problem';
if(!isset($_SESSION['user']))
    $info = _('Please login first');
else{
    if(isset($_GET['type']))
        if($_GET['type']=='wiki'||$_GET['type']=='contest'||$_GET['type']=='problem')
            $type=$_GET['type'];
        else{
            header("Location: marked.php");
            exit();
        }
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else
        $page_id=1;
        
    require __DIR__.'/conf/database.php';
    $user_id=$_SESSION['user'];
    if($type=='problem'){
        $type_num=1;
        $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from saved_problem where user_id='$user_id'"));
        $maxpage=intval($row[0]/20)+1;
        if($page_id<1||$page_id>$maxpage){
            header("Location: marked.php");
            exit();
        }
        $result=mysqli_query($con,"SELECT saved_problem.problem_id,title,savetime from saved_problem inner join problem using (problem_id) where user_id='$user_id' order by savetime desc limit ".(($page_id-1)*20).",20");
    }else if($type=='contest'){
        $type_num=2;
        $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from saved_contest where user_id='$user_id'"));
        $maxpage=intval($row[0]/20)+1;
        if($page_id<1||$page_id>$maxpage){
            header("Location: marked.php");
            exit();
        }
        $result=mysqli_query($con,"SELECT saved_contest.contest_id,title,savetime from saved_contest inner join contest using (contest_id) where user_id='$user_id' order by savetime desc limit ".(($page_id-1)*20).",20");
    }else{
        $type_num=3;
        $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from saved_wiki where user_id='$user_id'"));
        $maxpage=intval($row[0]/20)+1;
        if($page_id<1||$page_id>$maxpage){
            header("Location: marked.php");
            exit();
        }
        $result=mysqli_query($con,"SELECT saved_wiki.wiki_id,title,savetime from saved_wiki inner join wiki using (wiki_id) where is_MAX and user_id='$user_id' order by savetime desc limit ".(($page_id-1)*20).",20");
    }
    if(mysqli_num_rows($result)==0) $info=_('Looks like there\'s nothing here');
}

$inTitle=_('Marked');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>  
    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>
        <div class="container">
            <?php if(!isset($_SESSION['user'])){?>
                <div class="text-center none-text none-center">
                    <p><i class="fa fa-meh-o fa-4x"></i></p>
                    <p>
                        <b>Whoops</b>
                        <br>
                        <?php echo $info?>
                    </p>
                </div>
            <?php }else{?>
                <div class="row">
                    <div class="col-xs-12">
                        <ul class="nav nav-pills">
                            <li <?php if($type=='problem') echo 'class="active"'?>><a href="marked.php"><i class="fa fa-fw fa-coffee"></i> <?php echo _('Problems')?></a></li>
                            <li <?php if($type=='contest') echo 'class="active"'?>><a href="marked.php?type=contest"><i class="fa fa-fw fa-compass"></i> <?php echo _('Contests')?></a></li>
                            <li <?php if($type=='wiki') echo 'class="active"'?>><a href="marked.php?type=wiki"><i class="fa fa-fw fa-magic"></i> <?php echo _('Wiki')?></a></li>
                        </ul>
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
                            <br>
                            <table class="table table-responsive table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="col-xs-2 col-sm-1">No.</th>
                                        <th class="col-xs-8 col-sm-6"><?php echo _('Title')?></th>
                                        <th class="col-sm-4 hidden-xs"><?php echo _('Date')?></th>
                                        <th class="col-xs-2 col-sm-1"><?php echo _('Delete')?></th>
                                    </tr>
                                </thead>
                                <tbody id="marked_list">
                                    <?php
                                        while($row=mysqli_fetch_row($result)){?>
                                            <tr>
                                                <td><?php echo $row[0] ?></td>
                                                <td style="text-align:left"><a href="<?php echo $type?>page.php?<?php echo $type?>_id=<?php echo $row[0]?>" ><?php echo $row[1] ?></a></td>
                                                <td class="hidden-xs"><?php echo $row[2] ?></td>
                                                <td><i data-pid="<?php echo $row[0] ?>" style="cursor:pointer;" class="text-error fa fa-remove"></i></td>
                                            </tr>
                                    <?php }?>
                                </tbody>
                            </table>
                        <?php }?>
                    </div>
                </div>
                <div class="row">
                    <ul class="pager">
                        <li>
                            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="marked.php?page_id='.($page_id-1).'"';?>>
                                <i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?>
                            </a>
                        </li>
                        <li>
                            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo 'href="marked.php?page_id='.($page_id+1).'"';?>>
                                <?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php }
            require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript"> 
            var del=0;
            $(document).ready(function(){
                $('#marked_list').click(function(E){
                    var $target = $(E.target);
                    if($target.is('i')){
                        var pid = $target.attr('data-pid');
                        $.get('api/ajax_mark.php?prob='+pid+'&op=rm_saved&type='+'<?php echo $type_num?>',function(result){
                            if(/success/.test(result)){
                                $target.parents('tr').remove();
                                del++;
                                if(del==<?php echo mysqli_num_rows($result)?>)
                                    location.reload();
                            }
                        });
                    }
                });
            }); 
        </script>
    </body>
</html>
