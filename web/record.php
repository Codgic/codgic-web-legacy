<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/lib/result_type.php';
require __DIR__.'/lib/lang.php';
require __DIR__.'/func/checklogin.php';
require __DIR__.'/conf/database.php';

$cond="";
$user_id="";
$problem_id=0;
$result=-1;
$lang=-1;
$way="none";
$public_code=false;
$rank_mode=false;
$malicious=false;

if(isset($_GET['problem_id']))
    $problem_id=intval($_GET['problem_id']);

if(isset($_GET['way']) && !preg_match('/\W/',$_GET['way'])){
    $way=$_GET['way'];
    if($way=="time"||$way=="memory"){
        $rank_mode=true;
        if(!$problem_id)
            $problem_id=1000;
        $cond.=" and result=0";
    }
}

if($problem_id)
    $cond.=" and problem_id=$problem_id";

if(isset($_GET['user_id'])){
    $user_id=trim($_GET['user_id']);
    if(strlen($user_id))
        $cond.=' and user_id=\''.mysqli_real_escape_string($con,$user_id).'\'';
}

if($result==-1 && isset($_GET['result'])){
    $result=intval($_GET['result']);
    if($result!=-1)
        $cond.=" and result=$result";
}

if(isset($_GET['lang'])){
    $lang=intval($_GET['lang']);
    if($lang!=-1)
        $cond.=" and language=$lang";
}

if(isset($_GET['public'])){
    $public_code=true;
    $cond.=' and public_code';
}

if(isset($_GET['malicious'])){
    $malicious=true;
    $cond.=' and malicious';
}

if(!$rank_mode){
    $filter=$cond;
    if(isset($_GET['solution_id'])){
        $solution_id=intval($_GET['solution_id']);
        $cond=" and solution_id<=".$solution_id.$cond;
    }
    else
        $solution_id=2100000000;
}

$sql="";
if(strlen($cond))
    $sql="where".substr($cond, 4);

if($way=="time")
    $sql.=" order by time,memory";
else if($way=="memory")
    $sql.=" order by memory,time";
else
    $sql.=" order by solution_id desc";

if(!$rank_mode)
    $res=mysqli_query($con,"select solution_id,problem_id,user_id,result,score,time,memory,code_length,language,in_date,public_code from solution $sql limit 20");
else{
    if(isset($_GET['start_id'])){
        $start_id=intval($_GET['start_id']);
        if($start_id<0)
            $start_id=0;
    }else    
        $start_id=0;
    $row=mysqli_fetch_row(mysqli_query($con,"select count(1) from solution $sql"));
    $maxpage=$row[0];
    $res=mysqli_query($con,"select solution_id,problem_id,user_id,result,score,time,memory,code_length,language,in_date,public_code from solution $sql limit $start_id,20");
}

if($problem_id==0)
    $problem_id="";

$max_solution=0;
$min_solution=2100000000;
$num=mysqli_num_rows($res);
if($num==0)
    $info=_('Looks like there\'s nothing here');

function get_next_link(){
    global $rank_mode,$min_solution,$num;
    parse_str($_SERVER["QUERY_STRING"],$arr); 
    if($rank_mode){
        global $start_id;
        $arr['start_id']=($num ? $start_id+20 : $start_id);
    }else{
        if($num)
            $arr['solution_id']=$min_solution-1;
    }
    return http_build_query($arr);
}

function get_pre_link(){
    require __DIR__.'/conf/database.php';
    global $rank_mode,$max_solution;
    parse_str($_SERVER["QUERY_STRING"],$arr); 
    if($rank_mode){
        global $start_id;
        $arr['start_id']=($start_id>=20 ? $start_id-20 : 0);
    }else{
        global $filter;
        $sql="select solution_id from solution where solution_id>$max_solution $filter order by solution_id limit 20";
        $res=mysqli_query($con,$sql);
        $num=mysqli_num_rows($res);
        if($num==0)
            $arr['solution_id']=$max_solution;
        else{
            while(--$num)
                mysqli_fetch_row($res);
            $row=mysqli_fetch_row($res);
            $arr['solution_id']=$row[0];
        }
    }
    return http_build_query($arr); 
}

$inTitle=_('Record');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>
    
    <body style="margin-left:0; margin-right:0">
        <?php require __DIR__.'/inc/navbar.php'; ?>
        <div class="alert alert-danger collapse text-center alert-popup" id="alert_error"></div>
        <div class="container">
            <div class="row">
                <form action="record.php" method="get" id="form_filter">
                    <div class="form-group col-xs-6 col-md-2 col-lg-2">
                        <label>
                            <?php echo _('Problem')?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input <?php if($public_code)echo 'checked'?> id="chk_public" type="checkbox" name="public"><?php echo _('OSC');?>
                            </span>
                            <input type="number" class="form-control" name="problem_id" id="ipt_problem_id" value="<?php echo $problem_id?>">
                        </div>  
                    </div>
                    <div class="form-group col-xs-6 col-md-2 col-lg-2">
                        <label>
                            <?php echo _('User')?>
                        </label>
                        <?php if(isset($_SESSION['user'])){?>
                            <div class="input-group">
                                <input type="text" class="form-control" name="user_id" id="ipt_user_id" value="<?php echo $user_id?>">
                                <span class="input-group-addon btn btn-default" id="filter_me" data-myuid="<?php echo $_SESSION['user'];?>"><?php echo _('Me');?></span>
                            </div>  
                        <?php }else{?>
                            <input type="text" class="form-control" name="user_id" id="ipt_user_id" value="<?php echo $user_id?>">
                        <?php }?>
                    </div>
                    <div class="form-group col-xs-5 col-sm-4 col-md-3 col-lg-3">
                        <label>
                            <?php echo _('Result')?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input <?php if($malicious)echo 'checked'?> id="chk_malicious" type="checkbox" name="malicious"><?php echo _('Malicious');?>
                            </span>
                            <select class="form-control" name="result" id="slt_result">
                                <option value="-1"><?php echo _('All')?></option>
                                <?php
                                    foreach ($RESULT_TYPE as $type => $str)
                                        echo '<option value="',$type,'">',$str,'</option>';
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-xs-4 col-sm-3 col-md-2 col-lg-2">
                        <label>
                            <?php echo _('Language')?>
                        </label>
                        <select class="form-control" name="lang" id="slt_lang">
                            <option value="-1"><?php echo _('All')?></option>
                            <?php 
                                foreach ($LANG_NAME as $langid => $lang_name)
                                    echo '<option value="',$langid,'">',$lang_name,'</option>';
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-xs-3 col-sm-3 col-md-2 col-lg-2">
                        <label>
                            <?php echo _('Order by')?>
                        </label>
                        <select class="form-control" name="way" id="slt_way">
                            <option value="none"><?php echo _('Date')?></option>
                            <option value="time"><?php echo _('Run Time')?></option>
                            <option value="memory"><?php echo _('Run Memory')?></option>
                        </select>
                    </div>
                    <div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-1" style="max-width:200px">
                        <label class="hidden-xs">
                            <?php echo _('Operation')?>
                        </label>
                        <input class="form-control btn btn-danger" id="btn_reset" type="button" value="<?php echo _('Reset')?>">
                    </div>  
                </form>
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
                        <div class="table-responsive">
                            <table class=" table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width:6%">ID</th>
                                        <th style="width:9%"><?php echo _('Problem')?></th>
                                        <th style="width:12%"><?php echo _('User')?></th>
                                        <th style="width:11%"><?php echo _('Result')?></th>
                                        <th style="width:7%"><?php echo _('Score')?></th>
                                        <th style="width:10%"><?php echo _('Time')?></th>
                                        <th style="width:10%"><?php echo _('Memory')?></th>
                                        <th style="width:7%"><?php echo _('Size')?></th>
                                        <th style="width:11%"><?php echo _('Language')?></th>
                                        <th style="width:17%"><?php echo _('Date')?></th>
                                    </tr>
                                </thead>
                                <tbody id="tab_record">
                                    <?php
                                        require __DIR__.'/func/sourcecode.php';
                                        while($row=mysqli_fetch_row($res)){
                                            if($row[0]<$min_solution)
                                                $min_solution=$row[0];
                                            if($row[0]>$max_solution)
                                                $max_solution=$row[0];
                                            echo '<tr><td>',$row[0],'</td>';
                                            echo '<td><a href="problempage.php?problem_id=',$row[1],'">',$row[1],'</a></td>';
                                            echo '<td><a href="#uid">',$row[2],'</a></td>';
                                            echo '<td><span class="label ',$RESULT_STYLE[$row[3]],'">',$RESULT_TYPE[$row[3]],'</span></td>';
                                            echo '<td>',$row[4],'</td>';
                                            if($row[3])
                                                echo '<td></td><td></td>';
                                            else{
                                                echo '<td>',$row[5],' ms</td>';
                                                echo '<td>',$row[6],' KB</td>';
                                            }
                                            echo '<td>',round($row[7]/1024,2),' KB</td>';
                                            if(sc_check_priv($row[1], $row[10], $row[2]) === TRUE)
                                                echo '<td><a href="sourcecode.php?solution_id=',$row[0],'">',$LANG_NAME[$row[8]],'</a>';
                                            else
                                                echo '<td>',$LANG_NAME[$row[8]];
                                            if(isset($_SESSION['user'])&&$row[2]==$_SESSION['user'])
                                                echo ' <a href="#sw_open_',$row[0],'"><i class=', ($row[10] ? '"fa fa-eye text-success"' : '"fa fa-eye-slash muted"'), '></i></a></td>';
                                            else
                                                echo ' <i class=', ($row[10] ? '"fa fa-eye text-success"' : '"fa fa-eye-slash muted"'), '></i></td>';
                                            echo '<td>',$row[9],'</td>';
                                            echo '</tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php }?>
                </div>  
            </div>
            <div class="row">
                <ul class="pager">
                    <li>
                        <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($_SERVER['QUERY_STRING']!=htmlspecialchars(get_pre_link())) echo 'href="record.php?'.htmlspecialchars(get_pre_link()).'"'?>><i class="fa fa-angle-left"></i> <?php echo _('Previous')?></a>
                    </li>
                    <li>
                        <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if((!$rank_mode&&$solution_id>1020)||($rank_mode&&intval($start_id/20)<intval($maxpage/20))) echo 'href="record.php?'.htmlspecialchars(get_next_link()).'"'?>><?php echo _('Next')?> <i class="fa fa-angle-right"></i></a>
                    </li>
                </ul>
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
                            <input type="hidden" name="touser" id="input_touser">
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
                $('#slt_lang>option[value=<?php echo $lang;?>]').prop('selected',true);
                $('#slt_result>option[value=<?php echo $result?>]').prop('selected',true);
                $('#slt_way>option[value="<?php echo $way?>"]').prop('selected',true);
                $('#nav_record').parent().addClass('active');

                function toggle_s(obj){
                    if(obj.hasClass('fa-eye-slash')){
                        obj.removeClass('fa-eye-slash');
                        obj.addClass('fa-eye');
                        obj.removeClass('muted');
                        obj.addClass('text-success');
                    }else{
                        obj.removeClass('fa-eye');
                        obj.addClass('fa-eye-slash');
                        obj.removeClass('text-success');
                        obj.addClass('muted');
                    }
                }
                
                $('#tab_record').click(function(E){
                    var $target=$(E.target);
                    if(!$target.is('a')){
                        $target=$target.parent();
                        if(!$target || !$target.is('a'))
                            return;
                    }
                    var h=$target.attr('href');
                    if(h.substr(0,9)=='#sw_open_'){
                        $.ajax({
                            type:"POST",
                            url:"api/ajax_sourcecode.php",
                            data:{"op":'osc',"id":$target.attr('href').substr(9)},
                            success:function(msg){
                                if(/success/.test(msg))
                                    toggle_s($target.find('i'));
                                else{
                                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                                    setTimeout(function(){$('#alert_error').fadeOut();},2000);
                                }
                            }
                        });
                        return false;
                    }else if(h=='#uid'){
                        $('#user_status').html('<i class="fa fa-fw fa-refresh fa-spin"></i> <?php echo _('Loading...')?>').load("api/ajax_user.php?user_id="+E.target.innerHTML).scrollTop(0);
                        $('#input_touser').val(E.target.innerHTML);
                        $('#UserModal').modal('show');
                        return false;
                    }
                });
                
                function fun_submit(){
                    $('#form_filter').submit();
                }
                
                $('#slt_result').change(function(){
                    $('#slt_way').val('none');
                    fun_submit();
                });
                $('#slt_lang').change(fun_submit);
                $('#slt_way').change(fun_submit);
                $('#chk_public').change(fun_submit);
                $('#chk_malicious').change(fun_submit);
                $('#ipt_problem_id').keydown(function(E){
                    if(E.keyCode==13)fun_submit();
                });
                $('#ipt_user_id').keydown(function(E){
                    if(E.keyCode==13)fun_submit();
                });
                $('#filter_me').click(function(E){
                    $('#ipt_user_id').val($(this).data('myuid'));
                    fun_submit();
                })
                $('#btn_reset').click(function(){window.location="record.php?problem_id="+$("#ipt_problem_id").val()+"&user_id="+$("#ipt_user_id").val();});
            }); 
        </script>
    </body>
</html>
