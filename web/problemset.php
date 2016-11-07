<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/conf/database.php';
require __DIR__.'/lib/problem_flags.php';

if(isset($_GET['level'])){
    //If request level page
    $level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else
        $page_id=1;
    $level=intval($_GET['level']);
    if($level<0 || $level>$level_max){
        header("Location: problemset.php");
        exit();
    }
    
    $addt_cond=" (has_tex&".PROB_LEVEL_MASK.")=".($level<<PROB_LEVEL_SHIFT);
    if(!check_priv(PRIV_PROBLEM))
        $addt_cond.="and defunct=0 ";
    if(!check_priv(PRIV_INSIDER))
        $addt_cond.="and (has_tex&".PROB_IS_HIDE.")=0 ";
        
    $range="limit ".(($page_id-1)*100).",100";
    if(isset($_SESSION['user'])){
        $user_id=$_SESSION['user'];
        $result=mysqli_query($con,"SELECT problem_id,title,accepted,submit,source,defunct,res,saved.pid from problem 
        LEFT JOIN (select problem_id as pid,MIN(result) as res from solution where user_id='$user_id' group by problem_id) as solved on(solved.pid=problem_id) 
        left join (select problem_id as pid from saved_problem where user_id='$user_id') as saved on(saved.pid=problem_id) 
        where $addt_cond order by problem_id $range");
    }else{
        $result=mysqli_query($con,"select problem_id,title,accepted,submit,source,defunct from problem where $addt_cond order by problem_id $range");
    }
    if(mysqli_num_rows($result)==0) 
        $info=_('There\'s no problem of this level');
}else{
    //If request problemset page
    //Determine page_id
    if(isset($_GET['page_id']))
        $page_id=intval($_GET['page_id']);
    else if(isset($_SESSION['view'])){
        $view_arr=unserialize($_SESSION['view']);
        $page_id=intval($view_arr['prob']/100);
    }else
        $page_id=10;
        
    $addt_cond='';
    if(!check_priv(PRIV_PROBLEM))
        $addt_cond.="and defunct=0 ";
    if(!check_priv(PRIV_INSIDER))
        $addt_cond.="and (has_tex&".PROB_IS_HIDE.")=0 ";

    $row=mysqli_fetch_row(mysqli_query($con,"select max(problem_id) from problem where 1=1 $addt_cond"));

    $maxpage=intval($row[0]/100);
    if($page_id<10){
        header("Location: problemset.php");
        exit();
    }else if($page_id>$maxpage){
        if($maxpage==0){
            $info=_('Looks like there\'s no problem here');
        }else{
            header("Location: problemset.php?page_id=$maxpage");
            exit();
        }
    }

    $range="between $page_id"."00 and $page_id".'99';
    if(isset($_SESSION['user'])){
        $user_id=$_SESSION['user'];
        $result=mysqli_query($con,"SELECT problem_id,title,accepted,submit,source,defunct,res,saved.pid from problem 
        LEFT JOIN (select problem_id as pid,MIN(result) as res from solution 
        where user_id='$user_id' and problem_id $range group by problem_id) as solved on(solved.pid=problem_id) 
        left join (select problem_id as pid from saved_problem where user_id='$user_id') as saved on(saved.pid=problem_id) 
        where problem_id $range $addt_cond order by problem_id");
    }else{
        $result=mysqli_query($con,"select problem_id,title,accepted,submit,source,defunct from problem where problem_id $range $addt_cond order by problem_id");
    }
}

$inTitle=_('Problems');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>

    <body>
        <?php require __DIR__.'/inc/navbar.php';?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <?php if(!isset($level)){?>
                        <ul class="pagination">
                            <?php
                                if($maxpage>10){
                                    for($i=10;$i<=$maxpage;++$i)
                                        if($i!=$page_id)
                                            echo '<li><a href="problemset.php?page_id=',$i,'">',$i,'</a></li>';
                                        else
                                            echo '<li class="active"><a href="problemset.php?page_id=',$i,'">',$i,'</a></li>';
                                }
                            ?>
                            <li><a href="problemset.php?level=0"><i class="fa fa-fw fa-list-ul"></i> <?php echo _('Levels')?> <i class="fa fa-angle-double-right"></i></a></li>
                        </ul>
                    <?php }else{?>  
                        <ul class="pagination">
                            <li><a href="problemset.php"><i class="fa fa-angle-double-left"></i> <i class="fa fa-fw fa-th-list"></i> <?php echo _('All')?></a></li>
                                <?php
                                    for($i=0;$i<=$level_max;++$i){
                                        if($i!=$level)
                                            echo '<li>';
                                        else
                                            echo '<li class="active">';
                                            echo '<a href="problemset.php?level=',$i,'">',$i,'</a></li>';
                                    }
                                ?>
                        </ul>
                    <?php }?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
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
                        <table class="table table-striped table-bordered" id="problemset_table">
                            <thead>
                                <tr>
                                    <th class="col-xs-2 col-sm-1">ID</th>
                                    <?php 
                                        if(isset($_SESSION['user']))
                                            echo '<th class="col-xs-8 col-sm-5" colspan="3">';
                                        else
                                            echo '<th>';
                                        echo _('Title'),'</th>';
                                    ?>
                                    <th class="col-sm-2 col-md-1 hidden-xs"><?php echo _('AC/Submit')?></th>
                                    <th class="col-xs-2 col-md-1"><?php echo _('AC Ratio')?></th>
                                    <th class="col-sm-3 col-md-4 hidden-xs"><?php echo _('Tags')?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    while($row=mysqli_fetch_row($result)){
                                        echo '<tr><td>',$row[0],'</td>';
                                        if(isset($_SESSION['user'])){
                                            echo '<td><i class=', is_null($row[6]) ? '"fa fa-fw fa-2x fa-remove" style="visibility:hidden"' : ($row[6]? '"fa fa-fw fa-2x fa-remove" style="color:red"' : '"fa fa-fw fa-2x fa-check" style="color:green"'), '></i>', '</td>';
                                            echo '<td style="text-align:left;border-left:0;">';
                                        }else{
                                            echo '<td style="text-align:left">';
                                        }
                                        echo '<a href="problempage.php?problem_id=',$row[0],'">',$row[1];
                                        if($row[5]==1)
                                            echo '&nbsp;&nbsp;<span class="label label-danger">',_('Deleted'),'</span>';
                                        echo '</a>';
                                        if(isset($_SESSION['user'])){
                                            echo '<td style="border-left:0;"><i data-pid="',$row[0],'" class="', is_null($row[7]) ? 'fa fa-star-o' : 'fa fa-star', ' fa-2x text-warning save_problem" style="cursor:pointer;"></i></td>';
                                        }
                                        echo '</td><td class="hidden-xs"><a href="record.php?result=0&amp;problem_id=',$row[0],'">',$row[2],'</a>/';
                                        echo '<a href="record.php?problem_id=',$row[0],'">',$row[3],'</a></td>';
                                        echo '<td>',$row[3] ? intval($row[2]/$row[3]*100) : 0,'%</td>';
                                        echo '<td class="hidden-xs" style="text-align:left">',$row[4],"</td></tr>\n";
                                    }
                                ?>
                            </tbody>
                        </table>
                    <?php }?>
                </div>
            </div>
            <div class="row">
                <ul class="pager">
                    <li>
                        <?php if(!isset($_GET['level'])){?>
                            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>10) echo 'href="problemset.php?page_id='.($page_id-1).'"';?>>
                                <i class="fa fa-fw fa-angle-left"></i><?php echo _('Previous')?>
                            </a>
                        <?php }else{?>
                            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="problemset.php?level='.$level.'&page_id='.($page_id-1).'"';?>>
                                <i class="fa fa-fw fa-angle-left"></i><?php echo _('Previous')?>
                            </a>
                        <?php }?>
                    </li>
                    <li>
                        <?php if(!isset($_GET['level'])){?>
                            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if($page_id<$maxpage) echo 'href="problemset.php?page_id='.($page_id+1).'"';?>>
                                <?php echo _('Next')?><i class="fa fa-fw fa-angle-right"></i>
                            </a>
                        <?php }else{?>
                            <a class="pager-pre-link shortcut-hint" title="Alt+D" <?php if(mysqli_num_rows($result)==100) echo 'href="problemset.php?level='.$level.'&page_id='.($page_id+1).'"';?>>
                                <?php echo _('Next')?><i class="fa fa-fw fa-angle-right"></i>
                            </a>
                        <?php }?>
                    </li>
                </ul>
            </div>
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript"> 
            $(document).ready(function(){
                change_type(1);
                $('#nav_set').parent().addClass('active');
                $('#problemset_table').click(function(E){
                    var $target = $(E.target);
                    if($target.is('i.save_problem')){
                        var pid = $target.attr('data-pid'),op;
                        if($target.hasClass('fa-star'))
                            op='rm_saved';
                        else
                            op='add_saved';
                        $.get('api/ajax_mark.php?type=1&prob='+pid+'&op='+op,function(result){
                            if(/success/.test(result)){
                                $target.toggleClass('fa-star-o')
                                $target.toggleClass('fa-star')
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>
