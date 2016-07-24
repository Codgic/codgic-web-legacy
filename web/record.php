<?php
require 'inc/ojsettings.php';
require 'inc/result_type.php';
require 'inc/lang_conf.php';
require 'inc/checklogin.php';
require 'inc/database.php';

$cond="";
$user_id="";
$problem_id=0;
$result=-1;
$lang=-1;
$way="none";
$public_code=false;
$rank_mode=false;

if(isset($_GET['problem_id'])){
  $problem_id=intval($_GET['problem_id']);
}
if(isset($_GET['way']) && !preg_match('/\W/',$_GET['way'])){
  $way=$_GET['way'];
  if($way=="time"||$way=="memory"){
    $rank_mode=true;
    $result=0;
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
if(!$rank_mode){
  $filter=$cond;
  if(isset($_GET['solution_id'])){
    $solution_id=intval($_GET['solution_id']);
    $cond=" and solution_id<=".$solution_id.$cond;
}
  else $solution_id=2100000000;
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

if(!$rank_mode){
  $res=mysqli_query($con,"select solution_id,problem_id,user_id,result,score,time,memory,code_length,language,in_date,public_code from solution $sql limit 20");
}else{
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

function get_next_link()
{
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
function get_pre_link()
{
  require 'inc/database.php';
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
$inTitle='记录';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body style="margin-left:0; margin-right:0">
    <?php require 'page_header.php'; ?>
    <div class="container">
      <div class="row">
          <form action="record.php" method="get" id="form_filter">
			<div class="form-group col-xs-6 col-md-3 col-lg-2">
			<label>题目:</label>
			  <div class="input-group">
				<span class="input-group-addon">
                  <input <?php if($public_code)echo 'checked'?> id="chk_public" type="checkbox" name="public"> 开源
				</span>
				<input type="number" class="form-control" name="problem_id" id="ipt_problem_id" value="<?php echo $problem_id?>">
			  </div>  
			</div>
			<div class="form-group col-xs-6 col-md-2 col-lg-2">
			  <label>用户:</label>
			  <div class="input-group">
                <input type="text" class="form-control" name="user_id" id="ipt_user_id" value="<?php echo $user_id?>">
                <?php if(isset($_SESSION['user'])) echo'<span class="input-group-addon btn btn-default" id="filter_me" data-myuid="',$_SESSION['user'],'">自己</span>' ?>
			  </div>  
			</div>
			<div class="form-group col-xs-4 col-sm-4 col-md-2 col-lg-2">
			  <label>结果:</label>
              <select class="form-control" name="result" id="slt_result">
                <option value="-1">所有</option>
                <?php foreach ($RESULT_TYPE as $type => $str)
                  echo '<option value="',$type,'">',$str,'</option>';
                ?>
              </select>
			</div>
			<div class="form-group col-xs-4 col-sm-3 col-md-2 col-lg-2">
              <label>语言:</label>
              <select class="form-control" name="lang" id="slt_lang">
                <option value="-1">所有</option>
                <?php foreach ($LANG_NAME as $langid => $lang_name)
                echo '<option value="',$langid,'">',$lang_name,'</option>';
                ?>
              </select>
			</div>
			<div class="form-group col-xs-4 col-sm-3 col-md-2 col-lg-2">
              <label>排序:</label>
              <select class="form-control" name="way" id="slt_way">
                <option value="none">提交时间</option>
                <option value="time">运行时间</option>
                <option value="memory">运行内存</option>
              </select>
			</div>
			<div class="form-group col-xs-12 col-sm-2 col-md-1 col-lg-2" style="max-width:200px">
			  <label class="hidden-xs">操作:</label>
			  <input class="form-control btn btn-danger" id="btn_reset" type="button" value="重置">
			</div>  
		  </form>
      </div>
	  <br>  
      <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class=" table table-hover table-bordered">
              <thead><tr>
                <th style="width:6%">ID</th>
                <th style="width:7%">题目</th>
                <th style="width:12%">用户</th>
                <th style="width:11%">结果</th>
                <th style="width:8%">得分</th>
                <th style="width:10%">运行时间</th>
                <th style="width:10%">运行内存</th>
                <th style="width:8%">文件大小</th>
                <th style="width:11%">语言</th>
                <th style="width:17%">提交时间</th>
              </tr></thead>
              <tbody id="tab_record">
              <?php
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
                  echo '<td><a href="sourcecode.php?solution_id=',$row[0],'">',$LANG_NAME[$row[8]],'</a>';
                  echo ' <a href="#sw_open_',$row[0],'"><i class=', ($row[10] ? '"fa fa-eye text-success"' : '"fa fa-eye-slash muted"'), '></i></a> </td>';
                  echo '<td>',$row[9],'</td>';
                  echo '</tr>';
                }
              ?>
              </tbody>
            </table
        </div>  
      </div>
      <div class="row">
        <ul class="pager">
          <li>
            <a class="pager-pre-link shortcut-hint" title="Alt+A" <?php if($_SERVER['QUERY_STRING']!=htmlspecialchars(get_pre_link())) echo 'href="record.php?'.htmlspecialchars(get_pre_link()).'"'?>><i class="fa fa-angle-left"></i> 上一页</a>
          </li>
          <li>
            <a class="pager-next-link shortcut-hint" title="Alt+D" <?php if((!$rank_mode&&$solution_id>1020)||($rank_mode&&intval($start_id/20)<intval($maxpage/20))) echo 'href="record.php?'.htmlspecialchars(get_next_link()).'"'?>>下一页 <i class="fa fa-angle-right"></i></a>
          </li>
        </ul>
      </div>
      <div class="modal fade" id="UserModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">用户信息</h4>
            </div>
            <div class="modal-body" id="user_status">
              <p>信息不可用……</p>
            </div>
            <div class="modal-footer">
              <form action="mail.php" method="post">
                <input type="hidden" name="touser" id="input_touser">
                <?php if(isset($_SESSION['user'])){?>
                <button type="submit" class="btn btn-default pull-left"><i class="fa fa-fw fa-envelope-o"></i> 发私信</button>
                <?php }?>
              </form>
              <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
          </div>
        </div>
      </div>
	  <hr>
	  <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#slt_lang>option[value=<?php echo $lang;?>]').prop('selected',true);
        $('#slt_result>option[value=<?php echo $result?>]').prop('selected',true);
        $('#slt_way>option[value="<?php echo $way?>"]').prop('selected',true);
        $('#nav_record').parent().addClass('active');
        $('#nav_record_text').removeClass('hidden-sm');

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
              url:"ajax_opensource.php",
              data:{"id":$target.attr('href').substr(9)},
              success:function(msg){if(/success/.test(msg))toggle_s($target.find('i'));}
            });
            return false;
          }else if(h=='#uid'){
            $('#user_status').html("<p>正在加载...</p>").load("ajax_user.php?user_id="+E.target.innerHTML).scrollTop(0);
            $('#input_touser').val(E.target.innerHTML);
            $('#UserModal').modal('show');
            return false;
          }
        });
        function fun_submit(){$('#form_filter').submit();}
        $('#slt_result').change(function(){
          $('#slt_way').val('none');
          fun_submit();
        });
        $('#slt_lang').change(fun_submit);
        $('#slt_way').change(fun_submit);
        $('#chk_public').change(fun_submit);
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
