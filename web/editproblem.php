<?php 
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
require 'inc/privilege.php';

if(!check_priv(PRIV_PROBLEM))
  include '403.php';
else if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
  $_SESSION['admin_retpage'] = $_SERVER['REQUEST_URI'];
  header("Location: admin_auth.php");
  exit;
}else{
require 'inc/problem_flags.php';
require 'inc/database.php';
$level_max=(PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);
if(!isset($_GET['problem_id'])){
  $p_type='add';
  $inTitle='新建题目';
  $prob_id=1000;
	$result=mysqli_query($con,'select max(problem_id) from problem');
	if( ($row=mysqli_fetch_row($result)) && intval($row[0]))
		$prob_id=intval($row[0])+1;
}else{
  $p_type='edit';
  $prob_id=intval($_GET['problem_id']);  
  $inTitle="编辑题目#$prob_id";
  
  $query="select title,description,input,output,sample_input,sample_output,hint,source,case_time_limit,memory_limit,case_score,compare_way,has_tex from problem where problem_id=$prob_id";
  $result=mysqli_query($con,$query);
  $row=mysqli_fetch_row($result);
  if(!$row)
    $info = '该问题不存在！';
  else { 
    switch ($row[11] >> 16) {
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
  <?php require 'head.php'; ?>

  <body>
    <?php require 'page_header.php'; ?>
    <div class="container edit-page">
      <?php
      if(isset($info))
        echo "<div class=\"text-center\">$info</div>";
      else{
      ?>
	  <div class="alert collapse text-center alert-popup" id="alert_error"></div>  
	  <div class="collapse" id="showtools">
	    <p><button class="btn btn-primary" id="btn_show">显示工具栏<i class="fa fa-fw fa-angle-right"></i></button></p>
	  </div>
      <form action="#" method="post" id="edit_form" style="padding-top:10px">
        <input type="hidden" name="op" value="<?php echo $p_type?>">
		<input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
            <label>题目标题: </label>
			<input type="text" class="form-control" name="title" value="<?php if($p_type=='edit') echo $row[0]?>">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-4 col-sm-3">
            <label>时间(ms): </label>
			<input id="input_time" name="time" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[8]; else echo '1000'?>">
          </div>
		  <div class="form-group col-xs-4 col-sm-3">
            <label>内存(KB): </label>
			<input id="input_memory" name="memory" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[9]; else echo '65536'?>">
          </div>  
		  <div class="form-group col-xs-4 col-sm-3">
			<label>每点分值: </label>
			<input id="input_score" name="score" class="form-control" type="number" value="<?php if($p_type=='edit') echo $row[10]; else echo '10'?>">
		  </div>    
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-6">
            <label>评测方式: </label>
              <select class="form-control" name="compare" id="input_cmp">
                <option value="tra">Traditional</option>
                <option value="int">Integer</option>
                <option value="float">Real Number</option>
                <option value="spj">Special Judge</option>
              </select>
              <select name="precision" class="collapse form-control" id="input_cmp_pre"></select>
              <span id="input_cmp_help" class="help-block"></span>
          </div>
        </div>      
        <div class="row">
		  <div class="form-group col-xs-12">
			<label>题目选项: </label>
			<div class="checkbox">
			  <label>
				<input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_prob">隐藏题目
			  </label>
			</div>  
		  </div>
          <div class="form-group col-xs-4 col-sm-3"> 
			<span>开源代码可见: </span>
                <select class="form-control" name="option_open_source" id="option_open_source">
                  <option value="0">所有人</option>
                  <option value="1">AC了的人</option>
                  <option value="2">没有人</option>
                </select>
				<?php if($p_type=='edit'){?>
				<script>
				  document.getElementById('option_open_source').selectedIndex="<?php echo $option_opensource?>"
                </script>
				<?php }?>
			</div>
			<div class="form-group col-xs-4 col-sm-3">
                <span>题目等级 </span>
                <select class="form-control" name="option_level" id="option_level">
                  <option value="0">无难度</option>
                  <script>
				  <?php if($p_type=='add'){?>
                  for (var i = 1; i <= <?php echo $level_max?>; i++) {
                    document.write('<option value="'+i+'">'+i+'</option>')
                  };
				  <?php }else{?>
				  for (var i = 1; i <= <?php echo $level_max?>; i++) {
                    if(i==<?php echo $option_level?>)
                      document.write('<option selected value="'+i+'">'+i+'</option>')
                    else
                      document.write('<option value="'+i+'">'+i+'</option>')
                  };  
				  <?php }?>
                  </script>
                </select>
			</div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
              <label>题目描述:</label>
              <textarea class="form-control col-xs-12" name="description" rows="13"><?php if($p_type=='edit') echo htmlspecialchars($row[1])?></textarea>
          </div>
        </div>       
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
              <label>输入格式:</label>
              <textarea class="form-control col-xs-12" name="input" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[2])?></textarea>
          </div>
        </div>       
        <div class="row">
          <div class="form-group col-xs-12 col-md-9">
              <label>输出格式:</label>
              <textarea class="form-control col-xs-12" name="output" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[3])?></textarea>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-md-9">
              <label>输入样例:</label>
              <textarea class="form-control col-xs-12" name="sample_input" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[4])?></textarea>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-md-9">
              <label>输出样例:</label>
              <textarea class="form-control col-xs-12" name="sample_output" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[5])?></textarea>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-md-9">
              <label>题目提示:</label>
              <textarea class="form-control col-xs-12" name="hint" rows="8"><?php if($p_type=='edit') echo htmlspecialchars($row[6])?></textarea>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-md-9">
              <label>题目标签:</label>
              <input class="form-control col-xs-12" type="text" name="source" value="<?php if($p_type=='edit') echo htmlspecialchars($row[7])?>">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-md-9 text-center">
            <input type="submit" class="btn btn-primary" value="提交">
          </div>
        </div>
      </form>
      <?php } ?>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <div class="html-tools">
      <div class="well well-small margin-0" id="tools">
        <table class="table table-responsive table-bordered table-condensed table-striped">
          <caption><p>HTML代码工具</p></caption>
          <thead>
            <tr>
              <th>功能</th>
              <th>代码</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><button class="btn btn-default" id="tool_less">小于(&lt;)</button></td>
              <td>&amp;lt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_greater">大于(&gt;)</button></td>
              <td>&amp;gt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_img">图片</button></td>
              <td>&lt;img src=&quot;...&quot;&gt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_sup">上标</button></td>
              <td>&lt;sup&gt;...&lt;/sup&gt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_sub">下标</button></td>
              <td>&lt;sub&gt;...&lt;/sub&gt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_samp">单间隔</button></td>
              <td>&lt;samp&gt;...&lt;/samp&gt;</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_inline">公式</button></td>
              <td>[inline]...[/inline]</td>
            </tr>
            <tr>
              <td><button class="btn btn-default" id="tool_tex">居中公式</button></td>
              <td>[tex]...[/tex]</td>
            </tr>
          </tbody>
        </table>
		  <div class="text-center" style="margin-top:10px">
            <button class="btn btn-success" id="btn_upload">上传图片...</button>
			<button class="btn btn-primary" id="btn_hide">隐藏工具栏<i class="fa fa-fw fa-angle-left"></i></button>
          </div>
      </div>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        var loffset=window.screenLeft+200;
        var toffset=window.screenTop+200;
        function show_help(way){
          if(way=='float'){
            $('#input_cmp_pre').show();
            $('#input_cmp_help').html('输出只能包含实数。请选择精度:');
          }else{
            $('#input_cmp_pre').hide();
            if(way=='tra')
              $('#input_cmp_help').html('标准的评判方式，忽略尾部空格。');
            else if(way=='int')
              $('#input_cmp_help').html('输出只能包含实整数。');
            else if(way=='spj')
              $('#input_cmp_help').html('请确保在测试数据文件夹里存在"spj.exe"(windows)或是"spj.cpp"(linux)。');
          }
        }
        (function(){
          var option='';
          for(var i=0;i<10;i++){
            option+='<option value="'+i+'">'+i+'</option>';
          }
          $('#input_cmp_pre').html(option);
          show_help($('#input_cmp').val());
        })();
        $('#input_cmp').change(function(E){show_help($(E.target).val());});
		$('#btn_hide').click(function(){
          $('#tools').hide();
		  $('#showtools').show();
        });
		$('#btn_show').click(function(){
          $('#tools').show();
		  $('#showtools').hide();
        });
        $('#btn_upload').click(function(){
          window.open("upload.php",'upload_win2','left='+loffset+',top='+toffset+',width=400,height=300,toolbar=no,resizable=no,menubar=no,location=no,status=no');
        });
        $('#edit_form textarea').focus(function(e){cur=e.target;});
        $('#edit_form input').blur(function(e){
          e.target.value=$.trim(e.target.value);
          var o=$(e.target);
          if(!e.target.value||(/\D/.test(e.target.value)))
            o.addClass('error');
          else
            o.removeClass('error');
        });
        $('#edit_form').submit(function(){
          var str=$('#input_memory').val();
          if(!str||(/\D/.test(str))){
            window.location.hash='#edit_form';
            return false;
          }
          str=$('#input_time').val();
          if(!str||(/\D/.test(str))){
            window.location.hash='#edit_form';
            return false;
          }
          str=$('#input_score').val();
          if(!str||(/\D/.test(str))){
            window.location.hash='#edit_form';
            return false;
		  }
		  $.ajax({
            type:"POST",
            url:"ajax_editproblem.php",
            data:$('#edit_form').serialize(),
            success:function(msg){
              if(/success/.test(msg))
                window.location="problempage.php?problem_id=<?php echo $prob_id?>";
              else{
				$('#alert_error').addClass('alert-danger');  
                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 错误: '+msg).fadeIn();
				setTimeout(function(){$('#alert_error').fadeOut();},2000);
               }
            }
          });
          return false;
        });
        $('#tools').click(function(e){
          if(!($(e.target).is('button')))return false;
          if(typeof(cur)=='undefined')return false;
          var op=e.target.id;
          var slt=GetSelection(cur);
          if(op=="tool_greater")
            InsertString(cur,'&gt;');
          else if(op=="tool_less")
            InsertString(cur,'&lt;');
          else if(op=="tool_img"){
            var url=prompt("请输入图片链接:","");
            if(url){
              InsertString(cur,slt+'<img src="'+url+'">');
            }
          }else if(op=="tool_inline"||op=="tool_tex"){
            op=op.substr(5);
            InsertString(cur,'['+op+']'+slt+'[/'+op+']');
          }else if(op=="btn_upload"||op=="btn_hide"){
            return false;
          }else{
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
