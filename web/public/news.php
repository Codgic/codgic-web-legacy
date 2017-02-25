<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';
require __DIR__.'/func/checklogin.php';

$page_id=1;
if(isset($_GET['page_id']))
    $page_id=intval($_GET['page_id']);
  
if($page_id<1){
    header("Location: news.php");
    exit();
}else{
    require __DIR__.'/../src/database.php';
    if(!isset($_SESSION['user'])){
        $maxq="select count(news_id) from news where news_id>0 and privilege=0";
        $newsq="select news_id,title,time,importance from news where news_id>0 and privilege=0 order by importance desc, news_id desc limit ".(($page_id-1)*20).",20";
    }else{
        $maxq="select count(news_id) from news where news_id>0 and ((privilege & ".$_SESSION['priv'].")<>0 or privilege=0)";
        $newsq="select news_id,title,time,importance from news where news_id>0 and ((privilege & ".$_SESSION['priv'].")<>0 or privilege=0) order by importance desc, news_id desc limit ".(($page_id-1)*20).",20";
    }

    if($row=mysqli_fetch_row(mysqli_query($con,$maxq)))
        $maxpage=intval($row[0]/20)+1;
    else
        $maxpage=1;
    $res=mysqli_query($con,$newsq);
}

$inTitle=_('News');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php';?>
    <body>
        <?php require __DIR__.'/inc/navbar.php';?>
    
        <div class="alert alert-danger collapse text-center alert-popup" id="alert_error"></div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <?php if(mysqli_num_rows($res)==0){?>
                        <div class="text-center none-text none-center">
                            <p><i class="fa fa-meh-o fa-4x"></i></p>
                            <p>
                                <b>Whoops</b>
                                <br>
                                <?php echo _('Looks like there\'s nothing here')?>
                            </p>
                        </div>
                    <?php }else{?>
                        <table class="table table-responsive table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:6%">No.</th>
                                    <th><?php echo _('Title')?></th>
                                    <th style="width:25%"><?php echo _('Date')?></th>
                                </tr>
                            </thead>
                            <tbody id="tab_record">
                                <?php
                                    while($row=mysqli_fetch_row($res)){
                                        $addt1='';
                                        $addt2='';
                                        if($row[3]=='1'){
                                            $row[1]=_('[Sticky] ').$row[1];
                                            $addt1='<b>';
                                            $addt2='</b>';
                                        }
                                        echo '<td><font size=3>',htmlspecialchars($row[0]),'</font></td>';
                                        echo '<td style="text-align:left"><font size=3><a href="javascript:void(0)" onclick="return click_news(',$row[0],')">',$addt1.htmlspecialchars($row[1]).$addt2,'</a></font></td>';
                                        echo '<td><font size=3>',htmlspecialchars($row[2]),'</font></td></tr>';
                                        echo "\n";
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
                        <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($page_id>1) echo 'href="news.php?page_id='.($page_id-1).'"';?>>
                            <i class="fa fa-fw fa-angle-left"></i> <?php echo _('Previous')?>
                        </a>
                    </li>
                    <li>
                        <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if(mysqli_num_rows($res)==20&&$page_id<$maxpage) echo 'href="news.php?page_id='.($page_id+1).'"';?>>
                            <?php echo _('Next')?> <i class="fa fa-fw fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </div> 
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
        
        <div class="modal fade" id="NewsModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="newstitle"></h4>
                    </div>
                    <div class="modal-body" id="newscontent"></div>
                    <div class="modal-footer">
                        <text class="pull-left" id="newstime"></text>
                        <?php 
                            if(check_priv(PRIV_SYSTEM))
                                echo '<a class="pull-left" href="admin.php#news">',_('Edit'),'</a>\n';
                        ?> 
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
                    </div>
                </div>
            </div>
        </div>

        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript"> 
            function click_news(newsid){
                if(newsid){
                    $.ajax({
                        type:"POST",
                        url:"api/ajax_getnews.php",
                        data:{"newsid":newsid},
                        success:function(data){
                            if(data.success){
                                $('#newstitle').html(data.title);
                                $('#newscontent').html(data.content);
                                $('#newstime').html('<?php echo _('Date: ')?>'+data.time+'&nbsp;&nbsp;<?php echo _('Privilege: ')?>'+data.priv+'&nbsp;&nbsp;');
                                $('#NewsModal').modal('show');
                            }else{
                                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+data.content).fadeIn();
                                setTimeout(function(){$('#alert_error').fadeOut();},2000);
                            }
                        }
                    });
                };
            };
        </script>
    </body>
</html>