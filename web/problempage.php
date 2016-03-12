<?php 
require 'inc/ojsettings.php';
require('inc/result_type.php');
require('inc/lang_conf.php');
require('inc/problem_flags.php');
require('inc/checklogin.php');

if(isset($_GET['problem_id']))
  $prob_id=intval($_GET['problem_id']);
else if(isset($_SESSION['view']))
  $prob_id=$_SESSION['view'];
else
  $prob_id=1000;
require('inc/database.php');

$query="select title,description,input,output,sample_input,sample_output,hint,source,case_time_limit,memory_limit,case_score,defunct,has_tex,compare_way from problem where problem_id=$prob_id";
$result=mysqli_query($con,$query);
$row=mysqli_fetch_row($result);
if(!$row)
  die('Wrong Problem ID.');
switch ($row[13] >> 16) {
  case 0:
    $comparison='Traditional';
    break;
  case 1:
    $comparison='Real, precision: '.($row[13] & 65535);
    break;
  case 2:
    $comparison='Integer';
    break;
  case 3:
    $comparison='Special Judge';
    break;
}

if($row[11]=='Y' && !isset($_SESSION['administrator']))
  $forbidden=true;
else if($row[12]&PROB_IS_HIDE && !isset($_SESSION['insider']))
  $forbidden=true;
else{
  $forbidden=false;
  $_SESSION['view']=$prob_id;

  if(isset($_SESSION['user'])){
    $query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$prob_id group by problem_id";
    $user_status=mysqli_query($con,$query);
    if(mysqli_num_rows($user_status)==0)
      $info = '<tr><td colspan="2" class="center muted" >你还没提交过哦</td></tr>';
    else{
      $statis=mysqli_fetch_row($user_status);
      if($statis[0]==0){
        $info = '<tr><td colspan="2" class="gradient-green center"><i class="icon-ok icon-white"></i> 恭喜AC !</td></tr>';
      }else{
        $info = '<tr><td colspan="2" class="gradient-red center"><i class="icon-remove icon-white"></i> 再试一次吧 !</td></tr>';
      }
    }
    $current_user=$_SESSION['user'];
    $result=mysqli_query($con,"SELECT problem_id FROM saved_problem where user_id='$current_user' and problem_id=$prob_id");
    $mark_flag=mysqli_fetch_row($result);
    if(!($mark_flag)){
        $mark_icon_class='icon-star-empty';
        $mark_btn_class='btn btn-default btn-block';
        $mark_btn_html='收藏题目';
    }else{
        $mark_icon_class='icon-star';
        $mark_btn_class='btn btn-danger btn-block';
        $mark_btn_html='取消收藏';
    }
    $result=mysqli_query($con,"SELECT content,tags FROM user_notes where user_id='$current_user' and problem_id=$prob_id");
    $note_row=mysqli_fetch_row($result);
    if(!$note_row){
      $note_content = '';
      $tags = '';
      $note_exist=false;
    }else{
      $note_content = $note_row[0];
      $tags = $note_row[1];
      $note_exist=true;
    }

  }else{
    $info = '<tr><td colspan="2" class="center muted" >然而你并没有登录。</td></tr>';
  } 
  $result=mysqli_query($con,"select submit_user,solved,submit from problem where problem_id=$prob_id");
  $statis=mysqli_fetch_row($result);
  $submit_user=$statis[0];
  $solved_user=$statis[1];
  $total_submit=$statis[2];
  $prob_level=($row[12]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;

  $result=mysqli_query($con,"select result,count(*) as sum from solution where problem_id=$prob_id group by result");
  $arr=array();
  while($statis=mysqli_fetch_row($result))
    $arr[$statis[0]]=$statis[1];
  ksort($arr);  
}
$inTitle="题目#$prob_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body>
    <?php
    if($row[12]&PROB_HAS_TEX)
      require('inc/mathjax_head.php');
    require('page_header.php');
    ?>
    <div id="probdisp" class="container-fluid">
      <?php 
      if($forbidden){
        echo '<div class="span12 center">Problem is not available!</div>';
      }else{ 
      ?>
      <div class="row-fluid">
        <div class="span9" id="leftside" style="font-size:16px">
          <div class="center problem-title">
            <h2>#<?php echo $prob_id,' ',$row[0];if($row[11]=='Y')echo '<span style="vertical-align:middle" class="label label-important">已删除</span>';?></h2>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">题目描述</h3>
              <div class="well well-small"><?php echo mb_ereg_replace('\r?\n','<br>',$row[1]);?></div>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">输入格式</h3>
              <div class="well well-small"><?php echo mb_ereg_replace('\r?\n','<br>',$row[2]);?></div>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">输出格式</h3>
              <div class="well well-small"><?php echo mb_ereg_replace('\r?\n','<br>',$row[3]);?></div>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">样例输入</h3>
              <div class="well well-small click-to-copy problem-sample"><?php echo ($row[4]);?></div>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">样例输出</h3>
              <div class="well well-small click-to-copy problem-sample"><?php echo ($row[5]);?></div>
            </div>
          </div>
          <?php if(strlen($row[6])){ ?>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">题目提示</h3>
              <div class="well well-small"><?php echo mb_ereg_replace('\r?\n','<br>',$row[6]);?></div>
            </div>
          </div>
          <?php } ?>
          <div class="row-fluid">
            <div class="span12">
              <h3 class="problem-subtitle">题目标签</h3>
              <div class="well well-small"><?php echo mb_ereg_replace('\r?\n','<br>',$row[7])?></div>
            </div>
          </div>
        </div>
        <div class="span3" id="rightside">
          <div class="row-fluid">
            <?php echo"<div style=\"text-align:right\"><span id=\"btn_hide\" title=\"Alt+H\" class=\"btn {$button_class} shortcut-hint\">隐藏详情 &raquo;</span></div>";?>
          </div>
          <div class="row-fluid">
            <h3 class="problem-subtitle">&nbsp;</h3>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <div class="well well-small">
                <table class="table table-condensed table-striped" style="margin-bottom:0px;">
                  <tbody>
                    <tr><td style="text-align:left">时间限制:</td><td><?php echo $row[8]?> ms</td></tr>
                    <tr><td style="text-align:left">内存限制:</td><td><?php echo $row[9]?> KB</td></tr>
                    <tr><td style="text-align:left">每点分值:</td><td><?php echo $row[10]?></td></tr>
                    <tr><td style="text-align:left">评判方式:</td><td><?php echo $comparison?></td></tr>
                    <?php
                    if($prob_level)
                      echo '<tr><td style="text-align:left">等级:</td><td>',$prob_level,'</td></tr>';
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row-fluid"><div class="span12">
            <div id="status" class="well well-small" style="margin-top:10px">
              <table class="table table-condensed table-striped" style="margin-bottom:0px">
                <tbody>
                <?php echo $info ?>
                <tr><td style="text-align:left">提交人数:</td><td><?php echo $submit_user?></td></tr>
                <tr><td style="text-align:left">通过人数:</td><td><?php echo $solved_user?></td></tr>
                <tr><td style="text-align:left">总提交数:</td><td><?php echo $total_submit?></td></tr>
                <?php
                  foreach ($arr as $type => $cnt) {
                    if(isset($RESULT_TYPE[$type]))
                      echo '<tr><td style="text-align:left">',$RESULT_TYPE[$type],':</td><td>',$cnt,'</td></tr>';
                  }
                ?>
                </tbody>
              </table>
            </div>
          </div></div>
          <div class="row-fluid"><div class="span12" style="text-align: center;">
            <div id="function" class="well well-small problem-operation" style="margin-top:10px">
              <a href="#" title="Alt+S" class="btn btn-primary shortcut-hint" id="action_submit">提交</a>
              <a href="record.php?way=time&amp;problem_id=<?php echo $prob_id?>" class="btn btn-info">状态</a>
              <a href="board.php?problem_id=<?php echo $prob_id;?>" class="btn btn-warning">讨论</a>
            </div>
          </div></div>
          <?php if(isset($_SESSION['administrator'])){?>
          <div class="row-fluid">
            <div class="span12" style="text-align:center">
              <div class="well well-small problem-operation" style="margin-top:10px">
                <a href="edit.php?problem_id=<?php echo $prob_id?>" class="btn btn-success">编辑题目</a>
                <a href="testcase.php?problem_id=<?php echo $prob_id?>" class="btn btn-warning">测试数据</a>
                <span id="action_delete" class="btn btn-danger"><?php echo $row[11]=='N' ? '删除题目' : '恢复题目';?></span>
              </div>
            </div>
          </div>
          <?php }?>
          <?php if(isset($note_content)){ ?>
          <div class="row-fluid">
            <div class="span12" style="margin-bottom: 20px;">
              <div class="accordion-group <?php if(!$note_exist) echo '隐藏'?>" id="note_panel" >
                <div class="accordion-heading panel-heading">
                  <div class="accordion-toggle" style="cursor: auto;">
                    <b>笔记</b>
                    <a data-toggle="modal" href="#NoteModal" class="btn btn-small btn-primary pull-right" id="action_edit_note">编辑</a>
                  </div>
                </div>
                <div class="accordion-body in collapse" style="height: auto;">
                  <div class="accordion-inner note-short" id="note_content"><?php echo htmlspecialchars($note_content);?></div>
                </div>
                <div class="accordion-body in collapse" style="height: auto;">
                  <div class="accordion-inner">
                  <strong>标签:</strong>
                  <span id="user_tags"><?php echo htmlspecialchars($tags)?></span>
                  </div>
                </div>
              </div>
              <a id="note_new_btn" class="btn btn-success btn-block <?php if($note_exist) echo 'hide'?>" data-toggle="modal" href="#NoteModal">添加笔记或标签...</a>
            </div>
          </div>
          <?php }?>
          <?php if(isset($mark_btn_class)){ ?>
          <div class="row-fluid">
            <div class="span12" style="margin-bottom: 20px;">
              <a href="#" class="<?php echo $mark_btn_class; ?>" id="action_mark">
              <i class="<?php echo $mark_icon_class;?>"></i>
              <span id="action_mark_html"><?php echo $mark_btn_html; ?></span>
              </a>
            </div>
          </div>
          <?php } ?>
        </div>
        <?php }?>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>

    </div>

    <div class="modal fade hide" id="SubmitModal">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>代码提交</h4>
      </div>
      <form class="margin-0" action="submit.php" method="post" id="form_submit">
	    <p></p>
        <div class="modal-body" style="padding-top:5px">
		  <textarea style="box-sizing: border-box;width:100%;resize:none" id="detail_input" rows="16" name="source" placeholder="在这里敲出你的代码..."></textarea>
          <div class="alert alert-error hide margin-0" id="submit_result"></div>
        </div>
        <div class="modal-footer form-inline">
          <div class="pull-left">
              <label class="control-label" for="prob_input">题目</label>
              <input type="text" class="input-mini" style="font-weight: bold;margin-bottom: 0;" id="prob_input" name="problem">
          </div>
          <label class="checkbox" style="margin-right:3px">
            <input type="checkbox" <?php if($pref->sharecode=='on')echo 'checked';?> name="public"> 公开源码  
          </label>
          <select name="language" id="slt_lang">
            <?php foreach ($LANG_NAME as $langid => $lang) {
              echo "<option value=\"$langid\" ";
              if(isset($_SESSION['lang']) && $_SESSION['lang']==$langid)
                echo 'selected="selected"';
              echo ">$lang</option>";
            } ?>
          </select>
          <button class="btn btn-primary shortcut-hint" title="Alt+S" type="submit">提交</button>
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
		<div class="hidden-phone" style="width:750px"></div>
      </form>
    </div>
    <div class="modal fade hide" id="NoteModal">
      <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4>笔记 - <?php echo $prob_id?></h4>
      </div>
      <form class="margin-0" action="#" method="post" id="form_note">
        <div class="modal-body">
          <textarea style="box-sizing: border-box;width: 100%;resize:none" rows="14" placeholder="在这里写点什么吧..." name="content"></textarea>
          <span class="help-block">这份笔记只能被你看见。</span>
          <input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
        </div>
        <div class="modal-footer form-inline">
          <div class="pull-left">
            <div class="input-prepend">
              <span class="add-on"><b>标签</b></span>
              <input class="span2" id="tags_edit" type="text">
            </div>
          </div>
          <button class="btn btn-primary" type="submit">保存</button>
          <a href="#" class="btn" data-dismiss="modal">关闭</a>
        </div>
      </form>
    </div>

    <div id="show_tool" class="bottom-right hide">
      <?php echo"<span id=\"btn_submit2\" title=\"Alt+S\" class=\"btn {$button_class} shortcut-hint\">提交</span>
	  <span id=\"btn_show\" title=\"Alt+H\" class=\"btn btn {$button_class} shortcut-hint\">&laquo; 显示详情</span>";?>
    </div>

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>

    <script type="text/javascript">
      var hide_info = 0;
      $(document).ready(function(){
        var prob=<?php echo $prob_id?>;
        $('#nav_prob').parent().addClass('active');
        $('#ret_url').val("problempage.php?problem_id="+prob);
        $('.click-to-copy').click(function(){
          var $self = $(this);
          var $parent = $self.parent();
          if($self.is('div') && !$self.children().length){
            $self.replaceWith(function(){
                return $("<textarea />", {
                  html: $self.html(),
                  //class: $self.prop('class'),
                  readonly: "readonly"
                });
            });
            var $new = $parent.find('textarea');
            $new.height(1).height($new.prop('scrollHeight')).select();
          }
        });
        $('#action_delete').click(function(){
          $.ajax({
            url:"ajax_deleteprob.php?problem_id="+prob,
            dataType:"html",
            success:function(){location.reload();}
          });
        });
        $('#form_submit').submit(function(){
          var code = $('#detail_input').val();
          if($.trim(code) == '' || code.length > 30000){
            $('#submit_result').html("代码太短或太长").show();
            return false;
          }else{
            $('#submit_result').hide();
            //$('#SubmitModal').modal('hide');
            return true;
          }
        });
        $('#form_note').submit(function(){
          var data = $(this).serializeArray();
          $.post('ajax_usernote.php', data, function(res){
            if(/__ok__/.test(res)){
              for (var i = data.length - 1; i >= 0; i--) {
                if(data[i].name=='content')
                  $('#note_content').text(data[i].value);
                else if(data[i].name=='tags')
                  $('#user_tags').text(data[i].value);
              };
              $('#note_new_btn').hide();
              $('#note_panel').show();
              $('#NoteModal').modal('hide');
            }
          });
          return false;
        });
        $('#NoteModal').on('show', function () {
          $('#form_note textarea').val($('#note_content').text());
          $('#tags_edit').val($('#user_tags').text());
        });
        $("#action_mark").click(function(){
            var op;
            if($('#action_mark_html').html()=="收藏题目")
                op="add_saved";
            else
                op="rm_saved";	
            $.get("ajax_saveproblem.php?prob="+prob+"&op="+op,function(result){
                if(/__ok__/.test(result)){
                    var tg=$("#action_mark");
                    tg.toggleClass("btn-danger");
                    tg.toggleClass("btn-default");
                    tg.find('i').toggleClass('icon-star-empty').toggleClass('icon-star');
                    var tg=$("#action_mark_html");
                    if(tg.html()=="收藏题目")
                        tg.html("取消收藏");
                    else
                        tg.html("收藏题目");
                }
            });
            return false;
        });
        function click_submit(){
          <?php if(!isset($_SESSION['user'])){?>
            alert("请先登录CWOJ...");
          <?php }else{?>
            $('#prob_input').val(''+prob);
            $('#SubmitModal').modal('show');
            $('#detail_input').focus();
          <?php }?>
          return false;
        }
        $('#action_submit').click(click_submit);
        $('#btn_submit2').click(click_submit);
        function toggle_info(){
          if(hide_info) {
			$('#leftside').addClass('span9').removeClass('span12');
            $('#rightside').fadeIn(300);
            $('#show_tool').fadeOut(300);
            hide_info=0;
          }else {
            $('#rightside').fadeOut(300);
            $('#show_tool').fadeIn(300);
			setTimeout("$('#leftside').addClass('span12').removeClass('span9')", 300);
            hide_info=1;
          }
        }
        $('#btn_hide').click(toggle_info);
        $('#btn_show').click(toggle_info);
        reg_hotkey(83, function(){ //Alt+S
          if($('#SubmitModal').is(":visible"))
            $('#form_submit').submit();
          else
            click_submit();
        });
        reg_hotkey(72, toggle_info); //Alt+H
      });
    </script>
  </body>
</html>
