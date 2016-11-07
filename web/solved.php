<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/conf/database.php';

$cond='';
if(isset($_GET['q']) && strlen($search=trim($_GET['q'])))
    $cond='and user_id=\''.mysqli_real_escape_string($con,$search).'\'';
$result=mysqli_query($con,"select solution_id,user_id,solution.problem_id,score,solution.in_date,title from solution LEFT JOIN problem USING(problem_id) where valid=1 $cond order by solution_id desc limit 100");

$inTitle=_('Recent AC');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>

    <body>
        <?php require __DIR__.'/inc/navbar.php'; ?>       
        <div class="container">
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <table class="table table-hover table-bordered" style="margin-bottom:0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th><?php echo _('User')?></th>
                                <th><?php echo _('Problem')?></th>
                                <th><?php echo _('Score')?></th>
                                <th><?php echo _('Date')?></th>
                            </tr>
                        </thead>
                        <tbody id="userlist">
                            <?php 
                                while($row=mysqli_fetch_row($result)){
                                    echo '<tr><td><a href="record.php?solution_id=',$row[0],'">',$row[0],'</a></td>';
                                    echo '<td><a href="#linkU">',$row[1],'</a></td>';
                                    echo '<td style="text-align:left"><a href="problempage.php?problem_id=',$row[2],'">',$row[2],' -- ',$row[5],'</a></td>';
                                    echo '<td>',$row[3],'</td>';
                                    echo '<td>',$row[4],'</td>';
                                    echo "</tr>\n";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>  
            </div>
            <?php require __DIR__.'/inc/footer.php';?>
        </div>
        
        <div class="modal fade" id="UserModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?php echo _('User Profile')?></h4>
                    </div>
                    <div class="modal-body" id="user_status"></div>
                    <div class="modal-footer">
                        <form action="mail.php" method="post">
                            <input type="hidden" name="touser" id="um_touser">
                            <?php if(isset($_SESSION['user'])){?>
                                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> <?php echo _('Send Mail')?></button>
                            <?php }?>
                        </form>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
                    </div>
                </div>
            </div>
        </div>
    
        <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                change_type(3);
                $('#userlist').click(function(Event){
                    var $target=$(Event.target);
                    if($target.is('a') && $target.attr('href')=='#linkU'){
                        $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load("api/ajax_user.php?user_id="+Event.target.innerHTML).scrollTop(0);
                        $('#um_touser').val(Event.target.innerHTML);
                        $('#UserModal').modal('show');
                        return false;
                    }
                });
            }); 
        </script>
    </body>
</html>
