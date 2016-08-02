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
if(!isset($_GET['contest_id'])){
  $p_type='add';
  $inTitle='新建比赛';
  $cont_id=1000;
	$result=mysqli_query($con,'select max(contest_id) from contest');
	if( ($row=mysqli_fetch_row($result)) && intval($row[0]))
		$cont_id=intval($row[0])+1;
}else{
  $p_type='edit';
  $cont_id=intval($_GET['contest_id']);  
  $inTitle="编辑比赛#$cont_id";
  
  $query="select title,start_time,end_time,problems,description,source,judge_way,has_tex from contest where contest_id=$cont_id";
  $result=mysqli_query($con,$query);
  $row=mysqli_fetch_row($result);
  if(!$row)
    $info = '看起来该比赛不存在';
  else { 
    switch ($row[6]) {
      case 0:
        $way='train';
        break;
      case 1:
        $way='contest';
        break;
    }
  }

  $option_level=($row[7]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;
  $option_hide=(($row[7]&PROB_IS_HIDE)?'checked':'');
}

$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>

  <body>
    <?php require 'page_header.php'; ?>
    <div class="container edit-page">
      <?php if(isset($info)){?>
        <div class="text-center none-text none-center">
          <p><i class="fa fa-meh-o fa-4x"></i></p>
          <p><b>Whoops</b><br>
          <?php echo $info?></p>
        </div>
      <?php }else{?>
	  <div class="collapse" id="showtools">
	    <p><button class="btn btn-primary" id="btn_show">显示工具栏<i class="fa fa-fw fa-angle-right"></i></button></p>
	  </div>
      <form action="#" method="post" id="edit_form" style="padding-top:10px">
        <input type="hidden" name="op" value="<?php echo $p_type?>">
		<input type="hidden" name="contest_id" value="<?php echo $cont_id?>">
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
            <label>比赛标题: </label>
			<input type="text" class="form-control" name="title" id="input_title" value="<?php if($p_type=='edit') echo $row[0]?>">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
            <label>比赛题目 (逗号隔开，如:1000,1001): </label>
			<input type="text" class="form-control" name="problems" value="<?php if($p_type=='edit'){ $prob_arr=unserialize($row[3]);echo implode(',', $prob_arr);}?>">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-6 col-sm-4">
            <label>开始时间 (yyyy-mm-dd hh:mm:ss): </label>
			<input id="input_time" name="start_time" class="form-control" type="text" value="<?php if($p_type=='edit') echo $row[1]; else echo date("Y-m-d H:i:s",time())?>">
          </div>
		  <div class="form-group col-xs-6 col-sm-4">
            <label>结束时间 (yyyy-mm-dd hh:mm:ss): </label>
			<input id="input_memory" name="end_time" class="form-control" type="text" value="<?php if($p_type=='edit') echo $row[2]; else echo date("Y-m-d H:i:s",time()+10800)?>">
          </div> 
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-6">
            <label>计分方式: </label>
              <select class="form-control" name="judge" id="input_cmp">
                <option value="train">训练模式</option>
                <option value="contest">比赛模式</option>
              </select>
              <?php if($p_type=='edit'){?>
              <script>
                $('#input_cmp').val("<?php echo $way?>");
              </script>
              <?php }?>
              <span id="input_cmp_help" class="help-block"></span>
          </div>
        </div>      
        <div class="row">
          <div class="form-group col-xs-6 col-sm-3">
            <label>比赛难度: </label>
            <select class="form-control" name="option_level" id="option_level">
              <script>
              <?php if($p_type=='add'){?>
                for (var i = 0; i <= <?php echo $level_max?>; i++) {
                  document.write('<option value="'+i+'">'+i+'</option>')
                };
              <?php }else{?>
                for (var i = 0; i <= <?php echo $level_max?>; i++) {
                  if(i==<?php echo $option_level?>)
                    document.write('<option selected value="'+i+'">'+i+'</option>')
                  else
                    document.write('<option value="'+i+'">'+i+'</option>')
                };
              <?php }?>
              </script>
            </select>
          </div>
          <div class="form-group col-xs-6 col-sm-4">
			<label>比赛选项: </label>
			<div class="checkbox">
			  <label>
				<input <?php if($p_type=='edit') echo $option_hide?> type="checkbox" name="hide_cont">隐藏比赛
			  </label>
			</div>  
		  </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
              <label>比赛简介:</label>
              <textarea class="form-control col-xs-12" name="description" rows="13"><?php if($p_type=='edit') echo htmlspecialchars($row[4])?></textarea>
          </div>
        </div>       
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
              <label>比赛标签:</label>
              <input class="form-control col-xs-12" type="text" name="source" value="<?php if($p_type=='edit') echo htmlspecialchars($row[5])?>">
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-9">
            <div class="alert alert-danger collapse" id="alert_error"></div>  
            <button class="btn btn-primary" type="submit">提交</button>
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
      <div class="panel panel-default" id="tools">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-fw fa-code"></i> HTML代码工具</h3>
        </div>
        <div class="panel-body">
          <table class="table table-responsive table-bordered table-condensed table-striped">
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
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        var loffset=window.screenLeft+200;
        var toffset=window.screenTop+200;
        function show_help(way){
          if(way=='train')
            $('#input_cmp_help').html('总分即时间内每道题得分之和。');
          else if(way=='contest')
            $('#input_cmp_help').html('每一次没有AC的提交会导致该题得分*90%。');
        }
        (function(){
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
        $('#edit_form input').blur(function(e){
          e.target.value=$.trim(e.target.value);
          var o=$(e.target);
          if(!e.target.value)
            o.addClass('error');
          else
            o.removeClass('error');
        });
        $('#edit_form').submit(function(){
          var str=$('#input_title').val();
          if(!str||str==''){
            $('html, body').animate({scrollTop:0}, '200');
            return false;
          }
		  $.ajax({
            type:"POST",
            url:"ajax_editcontest.php",
            data:$('#edit_form').serialize(),
            success:function(msg){
              if(/success/.test(msg)) window.location="contestpage.php?contest_id=<?php echo $cont_id?>";
              else $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 错误: '+msg).slideDown();
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
