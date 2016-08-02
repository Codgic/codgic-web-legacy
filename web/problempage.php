<?php 
require 'inc/ojsettings.php';
require 'inc/result_type.php';
require 'inc/lang_conf.php';
require 'inc/problem_flags.php';
require 'inc/checklogin.php';
require 'inc/database.php';
require 'inc/privilege.php';
$is_contest=false;
if(isset($_GET['contest_id'])){
	$cont_id=intval($_GET['contest_id']);
	$is_contest=true;
	$query="select title,start_time,end_time,defunct,num,problems,description,source,has_tex from contest where contest_id=$cont_id";
	$result=mysqli_query($con,$query);
	$row_cont=mysqli_fetch_row($result);
    if(!$row_cont)
        $info='看起来这场比赛不存在';
    if(time()<strtotime($row_cont[1])){
        header("Location: contestpage.php?contest_id=$cont_id");
        exit();
    }
	$rem_time=strtotime($row_cont[2])-time();
	$prob_arr=unserialize($row_cont[5]);
	$prob_num=0;
	if(isset($_GET['prob'])){
		$prob_num=intval($_GET['prob']);
		if($prob_num<1||$prob_num>$row_cont[4]){
            header("Location: problempage.php?contest_id=".$cont_id);
            exit();
        }else $prob_id=$prob_arr[$prob_num-1];
	}else{
		$prob_id=$prob_arr[0];
        $prob_num=1;
	}
}
else if(isset($_GET['problem_id']))
  $prob_id=intval($_GET['problem_id']);
else if(isset($_SESSION['view']))
  $prob_id=$_SESSION['view'];
else
  $prob_id=1000;
  
$query="select title,description,input,output,sample_input,sample_output,hint,source,case_time_limit,memory_limit,case_score,defunct,has_tex,compare_way from problem where problem_id=$prob_id";
$result=mysqli_query($con,$query);
$row_prob=mysqli_fetch_row($result);
if(!$row_prob)
  $info='看起来这道题目不存在';
switch ($row_prob[13] >> 16) {
  case 0:
    $comparison='Traditional';
    break;
  case 1:
    $comparison='Real, precision: '.($row_prob[13] & 65535);
    break;
  case 2:
    $comparison='Integer';
    break;
  case 3:
    $comparison='Special Judge';
    break;
}

if($row_prob[11]=='Y' && !check_priv(PRIV_PROBLEM))
  $forbidden=true;
else if($row_prob[12] & PROB_IS_HIDE && !check_priv(PRIV_INSIDER))
  $forbidden=true;
else{
  $forbidden=false;
  $_SESSION['view']=$prob_id;

  if(isset($_SESSION['user'])){
    $query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$prob_id group by problem_id";
    $user_status=mysqli_query($con,$query);
    if(mysqli_num_rows($user_status)==0)
      $s_info = '<tr><td colspan="2" class="text-center muted" >你还没提交过哦...</td></tr>';
    else{
      $statis=mysqli_fetch_row($user_status);
      if($statis[0]==0){
        $s_info = '<tr><td colspan="2" class="gradient-green text-center"><i class="fa fa-fw fa-check"></i> 恭喜AC!</td></tr>';
      }else{
        $s_info = '<tr><td colspan="2" class="gradient-red text-center"><i class="fa fa-fw fa-remove"></i> 再试一次吧!</td></tr>';
      }
    }
    $current_user=$_SESSION['user'];
    $result=mysqli_query($con,"SELECT problem_id FROM saved_problem where user_id='$current_user' and problem_id=$prob_id");
    $mark_flag=mysqli_fetch_row($result);
    if(!($mark_flag)){
        $mark_icon_class='fa fa-star-o';
        $mark_btn_class='btn btn-default btn-block';
        $mark_btn_html='收藏题目';
    }else{
        $mark_icon_class='fa fa-star';
        $mark_btn_class='btn btn-danger btn-block';
        $mark_btn_html='取消收藏';
    }
    $result=mysqli_query($con,"SELECT content,tags FROM user_notes where user_id='$current_user' and problem_id=$prob_id");
    $row_note=mysqli_fetch_row($result);
    if(!$row_note){
      $note_content = '';
      $tags = '';
      $note_exist=false;
    }else{
      $note_content = $row_note[0];
      $tags = $row_note[1];
      $note_exist=true;
    }

  }else{
    $s_info = '您还没有登录';
  } 
  $result=mysqli_query($con,"select submit_user,solved,submit from problem where problem_id=$prob_id");
  $statis=mysqli_fetch_row($result);
  $submit_user=$statis[0];
  $solved_user=$statis[1];
  $total_submit=$statis[2];
  $prob_level=($row_prob[12]&PROB_LEVEL_MASK)>>PROB_LEVEL_SHIFT;

  $result=mysqli_query($con,"select result,count(*) as sum from solution where problem_id=$prob_id group by result");
  $arr=array();
  while($statis=mysqli_fetch_row($result))
    $arr[$statis[0]]=$statis[1];
  ksort($arr);  
}
if($forbidden) $info='看起来你无法访问该题目';
$inTitle="题目#$prob_id";
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php';?>
  
  <body>
    <?php if($pref->edrmode!='off'){
      echo '<link rel="stylesheet" href="/assets/css/codemirror.css" type="text/css" />';
      echo '<link rel="stylesheet" href="/assets/css/codemirror.fullscreen.css" type="text/css" />';
      if($t_night=='off') echo '<link rel="stylesheet" href="/assets/css/codemirror.eclipse.css" type="text/css" />';
      else echo '<link rel="stylesheet" href="/assets/css/codemirror.midnight.css" type="text/css" />';
      }
      require 'inc/mathjax_head.php';
	  require 'page_header.php';
    ?>
    <div class="alert collapse text-center alert-popup alert-danger" id="alert_error"></div>
	<?php if($is_contest){?>
	<div class="alert alert-danger text-center" style="top:50px;height:50px;width:100%;position:fixed;z-index:100;border-radius:0">
    <?php
        if($prob_num>1) echo '<a href="problempage.php?contest_id=',$cont_id,'&prob=',($prob_num-1),'" class="pull-left"><i class="fa fa-fw fa-angle-left"></i>上一题</a>';
        else echo '<span class="pull-left"><i class="fa fa-fw fa-angle-left"></i>上一题</span>';
        echo '题目: ',$prob_num,' / ',$row_cont[4],' &nbsp;&nbsp;';
        if($rem_time<0) echo '比赛已经结束';
        else echo '<span id="cont_st">剩余时间: <span id="tday"></span><span id="thour"></span><span id="tmin"></span><span id="tsec"></span></span>';
        if($prob_num<$row_cont[4]) echo '<a href="problempage.php?contest_id=',$cont_id,'&prob=',($prob_num+1),'" class="pull-right">下一题<i class="fa fa-fw fa-angle-right"></i></a>';
        else echo '<span class="pull-right">下一题<i class="fa fa-fw fa-angle-right"></i></span>';
	  ?>
	</div>
	<div style="height:50px"></div>
	<?php }?>
    <div id="probdisp" class="container">
      <?php if(isset($info)){?>
        <div class="row">
          <div class="col-xs-12">
            <div class="text-center none-text none-center">
              <p><i class="fa fa-meh-o fa-4x"></i></p><p><b>Whoops</b><br>
              <?php echo $info?></p>
            </div>
          </div>
        </div>
      <?php }else{?>
      <div class="row">
        <div class="col-xs-12 col-sm-9" id="leftside" style="font-size:16px">
          <div class="text-center">
            <h2><?php echo '#'.$prob_id,' ',$row_prob[0];
              if($row_prob[11]=='Y')echo ' <span style="vertical-align:middle;font-size:12px" class="label label-danger">已删除</span>';
              if($is_contest) echo '<a href="contestpage.php?contest_id=',$cont_id,'" class="btn btn-default pull-left"><i class="fa fa-fw fa-home"></i> 比赛主页</a>';?></h2>
          </div>
          <br>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">题目描述</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[1]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">输入格式</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[2]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">输出格式</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[3]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">样例输入
              <a herf="#" class="pull-right" id="copy_in" style="cursor:pointer" data-toggle="tooltip" data-trigger="manual" data-clipboard-action="copy" data-clipboard-target="#sample_input">[复制]</a></h5>
            </div>
            <div class="panel-body problem-sample" id="sample_input">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[4]);?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">样例输出
              <a herf="#" class="pull-right" id="copy_out" style="cursor:pointer" data-toggle="tooltip" data-trigger="manual" data-clipboard-action="copy" data-clipboard-target="#sample_output">[复制]</a></h5>
            </div>
            <div class="panel-body problem-sample" id="sample_output">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[5]);?>
            </div>
          </div>
          <?php if(strlen($row_prob[6])){ ?>
            <div class="panel panel-default">
              <div class="panel-heading">
                <h5 class="panel-title">题目提示</h5>
              </div>
              <div class="panel-body">
                <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[6]);?>
              </div>
            </div>
          <?php } ?>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h5 class="panel-title">题目标签</h5>
            </div>
            <div class="panel-body">
              <?php echo mb_ereg_replace('\r?\n','<br>',$row_prob[7]);?>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-3" id="rightside">
          <div class="row">
			<div class="col-xs-12">
			  <button id="btn_hide" title="Alt+H" class="btn btn-primary shortcut-hint pull-right"><i class="fa fa-fw fa-toggle-on"></i> 隐藏详情</button>
			</div>
		  </div>
		  <br> 
          <div class="row">
            <div class="col-xs-12">
              <div class="panel panel-default">
				<div class="panel-body">
                  <table class="table table-condensed table-striped" style="margin-bottom:0px">
					<tbody>
					  <tr><td style="text-align:left">时间限制:</td><td><?php echo $row_prob[8]?> ms</td></tr>
                      <tr><td style="text-align:left">内存限制:</td><td><?php echo $row_prob[9]?> KB</td></tr>
                      <tr><td style="text-align:left">每点分值:</td><td><?php echo $row_prob[10]?></td></tr>
                      <tr><td style="text-align:left">评判方式:</td><td><?php echo $comparison?></td></tr>
                      <tr><td style="text-align:left">题目等级:</td><td><?php echo $prob_level?></td></tr>
                    </tbody>
                  </table>
				</div>
              </div>
            </div>
          </div>
          <div class="row">
		    <div class="col-xs-12">
              <div id="status" class="panel panel-default" style="margin-top:10px">
			    <div class="panel-body">
                  <table class="table table-condensed table-striped" style="margin-bottom:0px">
                    <tbody>
                    <?php echo $s_info ?>
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
              </div>
            </div>
		  </div>
		  <div class="row">
		    <div class="col-xs-12 text-center">
		      <div id="function" class="panel panel-default problem-operation" style="margin-top:10px">
			    <div class="panel-body">
			      <a href="#" title="Alt+S" class="btn btn-primary shortcut-hint" id="btn_submit">提交</a>
                  <a href="record.php?way=time&amp;problem_id=<?php echo $prob_id?>" class="btn btn-success">状态</a>
                  <a href="board.php?problem_id=<?php echo $prob_id;?>" class="btn btn-warning">讨论</a>
                </div>
              </div>
		    </div>
		  </div>  
          <?php if(check_priv(PRIV_PROBLEM)){?>
          <div class="row">
            <div class="col-xs-12 text-center">
              <div class="panel panel-default problem-operation" style="margin-top:10px">
				<div class="panel-body">
                  <a href="editproblem.php?problem_id=<?php echo $prob_id?>" class="btn btn-primary">编辑题目</a>
                  <a href="testcase.php?problem_id=<?php echo $prob_id?>" class="btn btn-warning">测试数据</a>
                  <span id="action_delete" class="btn btn-danger"><?php echo $row_prob[11]=='N' ? '删除题目' : '恢复题目';?></span>
                </div>
			  </div>
            </div>
          </div>
          <?php }?>
          <?php if(isset($note_content)){ ?>
          <div class="row">
            <div class="col-xs-12" style="margin-bottom: 20px;">
              <div class="panel-group <?php if(!$note_exist) echo '隐藏'?>" id="note_panel" >
                <div class="panel panel-default">
                  <div class="panel-heading">
					<b>笔记</b>
					<a data-toggle="modal" href="#NoteModal" class="btn btn-xs btn-primary pull-right" id="action_edit_note">编辑</a>
                  </div>
                  <div class="panel-collapse in collapse">
					<div class="panel-body note-short" id="note_content">
					  <?php if(!empty($note_content)) echo htmlspecialchars($note_content);
							else echo '你还没在这道题上记过笔记哦...'; ?>
					</div>
					<div class="panel-body">
					  <p><strong>标签</strong></p>
                      <span id="user_tags"><?php echo htmlspecialchars($tags)?></span>
					  <a id="note_new_btn" class="btn btn-success btn-block <?php if($note_exist) echo 'collapse'?>" data-toggle="modal" href="#NoteModal">添加笔记或标签...</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
		  </div>
          <?php }?>
          <?php if(isset($mark_btn_class)){ ?>
          <div class="row">
            <div class="col-xs-12">
              <a href="#" class="<?php echo $mark_btn_class?>" id="action_mark">
              <i class="<?php echo $mark_icon_class ?>"></i>
              <span id="action_mark_html"><?php echo $mark_btn_html?></span>
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
	
  <div class="modal fade" id="SubmitModal" data-keyboard="false">
	<div class="modal-dialog" id="submit_dialog">
	  <div class="modal-content" id="submit_content">
		<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">代码提交: #<?php echo $prob_id?></h4>
		</div>
		<form method="post" id="form_submit">
		<input type="hidden" name="op" value="judge">
		<input type="hidden" id="prob_input" name="problem">
		<div class="modal-body">
		  <div class="form-group">
		    <textarea spellcheck="false" class="form-control" style="resize:none" id="detail_input" rows="14" name="source" placeholder="在这里敲出你的代码..."></textarea>
		  </div>
		  <?php if($pref->edrmode=='vim') echo '<samp>指令: <span id="vim_cmd"></span></samp>'?>
          <div class="alert alert-danger collapse" id="submit_res"></div>
		</div>
		<div class="modal-footer form-inline">
		  <div class="row">
			<div class="form-group col-xs-12 col-sm-4 pull-left" style="padding-left:15px">
			  <div class="input-group">
				<span class="input-group-addon">
                  <input type="checkbox" <?php if($pref->sharecode=='on')echo 'checked';?> name="public">开源
				</span>
				<select class="form-control" name="language" id="slt_lang" onchange="editor_changemode()">
				  <?php foreach ($LANG_NAME as $langid => $lang) {
					echo "<option value=\"$langid\" ";
					if(isset($_SESSION['lang']) && $_SESSION['lang']==$langid)
					echo 'selected="selected"';
					echo ">$lang</option>";
				  }?>
				</select>
			  </div>  
			</div>
			<div class="form-group col-xs-12 col-sm-8">
			  <a href="javascript:void(0)" onclick="return clreditor()" class="btn btn-default shortcut-hint" title="Alt+C">清空</a>
			  <button class="btn btn-primary shortcut-hint" title="Alt+S" type="submit">提交</button>
			  <a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>
			</div>
		</div>
		</div>
		</form>
	  </div>
	</div>
  </div>

  <div class="modal fade" id="NoteModal">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">&times;</button>
		  <h4 class="modal-title">笔记 - <?php echo $prob_id?></h4>
		</div>
		<form method="post" id="form_note"> 
		<div class="modal-body">
		  <div class="form-group">
		    <textarea class="form-control" style="resize:none" rows="14" placeholder="在这里写点什么吧..." name="content"><?php echo $note_content?></textarea>
		    <span class="help-block">这份笔记只能被你自己看见~</span>
		    <input type="hidden" name="problem_id" value="<?php echo $prob_id?>">
		  </div>
          <div class="alert alert-danger collapse" id="notes_res"></div>
		</div>
		<div class="modal-footer form-inline">
		  <div class="row">
			<div class="form-group col-xs-6 col-sm-7">
			<div class="input-group pull-left">
			  <span class="input-group-addon"><b>标签</b></span>
			  <input class="form-control" id="tags_edit" type="text" name="tags">
		    </div>
			</div>
			<div class="form-group col-xs-6 col-sm-5">
		      <button class="btn btn-primary" id="note_submit" type="submit">保存</button>
		      <a href="#" class="btn btn-default" data-dismiss="modal">关闭</a>
			</div>
		  </div>
		</div>
	    </form>
	  </div>
	</div>
  </div>
  <div id="show_tool" class="bottom-right collapse">
	<span id="btn_submit2" title="Alt+S" class="btn btn-primary shortcut-hint">提交</span>
	<span id="btn_show" title="Alt+H" class="btn btn btn-primary shortcut-hint"><i class="fa fa-fw fa-toggle-off"></i> 显示详情</span>
  </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script src="/assets/js/clipboard.min.js"></script>
    <?php if($pref->edrmode!='off'){?>
	<script src="/assets/js/codemirror.js"></script>
	<script src="/assets/js/codemirror.placeholder.js"></script>
	<script src="/assets/js/codemirror.fullscreen.js"></script>
	<script src="/assets/js/codemirror.clike.js"></script>
	<script src="/assets/js/codemirror.pascal.js"></script>
	<?php
	  if($pref->edrmode!='default') echo '<script src="/assets/js/codemirror.'.$pref->edrmode.'.js"></script>';}
	?>
    <script type="text/javascript">
    var prob = <?php echo $prob_id?>;
	<?php if($is_contest==true&&$rem_time>0){?> 
      var t = new Date(<?php echo strtotime($row_cont[2])*1000?>);
      var EndTime = t.getTime();
      var t1 = new Date(), t2 = new Date(<?php echo time()*1000?>);
      var SyncTime=t1.getTime()-t2.getTime();
      function GetRTime(){
        var NowTime = new Date();
		var nMS = EndTime - NowTime.getTime() + SyncTime;
		if (nMS < 0){
            $('#cont_st').html('比赛已经结束');
		}else{
            var nD = Math.floor(nMS/(1000 * 60 * 60 * 24));
            var nH = Math.floor(nMS/(1000*60*60)) % 24;
            var nM = Math.floor(nMS/(1000*60)) % 60;
            var nS = Math.floor(nMS/1000) % 60;
            if(!nD) $("#tday").hide();
            else $("#tday").text(nD+' 日 ');
		   $("#thour").text(nH+' 时 ');
		   $("#tmin").text(nM+ ' 分 ');
		   $("#tsec").text(nS+' 秒 ');
		}
      }
	<?php };if($pref->edrmode!='off'){?>
	var editor = CodeMirror.fromTextArea(document.getElementById('detail_input'), {
		theme: "<?php if($t_night=='on') echo 'midnight'; else echo 'eclipse'?>",
		mode: "text/x-c++src",
		<?php if($pref->edrmode!='default') {echo 'keyMap: "'.$pref->edrmode.'",';
		echo 'showCursorWhenSelecting: true,';
		}?>
		lineNumbers: true,
		extraKeys: {
        "Ctrl-F11": function(cm) {
		  if (cm.getOption("fullScreen")){
			toggle_fullscreen(1);
			cm.setOption("fullScreen", false);
		  }else{
			toggle_fullscreen(0);  
			cm.setOption("fullScreen", !cm.getOption("fullScreen"));
		  }  
        },
      }
	});
	<?php if($pref->edrmode=='vim'){?>
	CodeMirror.on(editor, 'vim-keypress', function(key) {
        $('#vim_cmd').html(key);
      });
      CodeMirror.on(editor, 'vim-command-done', function() {
        $('#vim_cmd').html('');
      });
	<?php }?>
	function editor_changemode() {
		var m=$("#slt_lang").val();
		if(m==1) editor.setOption("mode", "text/x-csrc");
		else if(m==2) editor.setOption("mode", "text/x-pascal");
		else editor.setOption("mode", "text/x-c++src");
	}
	function toggle_fullscreen(e){
		if(e==0){
		  $('#submit_dialog').css({
			'width': '101%','height': '100%','margin': '0','padding': '0'
		  });
	 	  $('#submit_content').css({'height': 'auto','min-height': '100%','border-radius': '0'
		  });
		}else{
			$('#submit_dialog').css({
			'width': '','height': '','margin': '','padding': ''
		  });
	 	  $('#submit_content').css({
			'height': '','min-height': '','border-radius': ''
		  });
        }
    }
    function clreditor(){
        editor.getDoc().setValue('');
        editor.focus();
    }
    <?php }else{?>
    function clreditor(){
        $('#detail_input').val('');
        $('#detail_input').focus();
    }
    <?php }?>
	var clipin = new Clipboard('#copy_in')
	var clipout = new Clipboard('#copy_out');
    clipin.on('success', function(e) {
        $('#copy_in').attr('title','复制成功!');
		$('#copy_in').tooltip('show');
		setTimeout("$('#copy_in').tooltip('destroy')",800);
    });
    clipin.on('error', function(e) {
        $('#copy_in').attr('title','请按Ctrl+C复制...');
		$('#copy_in').tooltip('show');
		setTimeout("$('#copy_in').tooltip('destroy')",800);
    });
	clipout.on('success', function(e) {
        $('#copy_out').attr('title','复制成功!');
		$('#copy_out').tooltip('show');
		setTimeout("$('#copy_out').tooltip('destroy')",800);
    });
	clipout.on('success', function(e) {
        $('#copy_out').attr('title','请按Ctrl+C复制...');
		$('#copy_out').tooltip('show');
		setTimeout("$('#copy_out').tooltip('destroy')",800);
    });
      var hide_info = 0;
      $(document).ready(function(){
        <?php if($is_contest==true&&$rem_time>0){?>
		var timer_rt = window.setInterval("GetRTime()", 1000);
		<?php }?>
        $('#action_delete').click(function(){
          $.ajax({
            type:"POST",
            url:"ajax_editproblem.php",
            data:{op:'del',problem_id:prob},
            success:function(msg){
              if(/success/.test(msg)){
                location.reload();
              }else{
                $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                setTimeout(function(){$('#alert_error').fadeOut();},2000);
              }
            }
          });
        });
        $('#form_submit').submit(function(){
          var code = $('#detail_input').val();
          if($.trim(code) == '' || code.length > 30000){
            $('#submit_res').html('<i class="fa fa-fw fa-remove"></i> 代码太短或太长...').slideDown();
          }else{
            $.ajax({
              type:"POST",
              url:"ajax_submit.php",
              data:$('#form_submit').serialize(),
              success:function(msg){
                if(msg.indexOf('success_')!=-1){
                    $('#submit_res').slideUp();
                    window.location.href='wait.php?key='+msg.substring(8,msg.length);
                }
                else $('#submit_res').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
              }
            });
          }
          return false;
        });
        $('#form_note').submit(function(){
          var data = $(this).serializeArray();
          $.post('ajax_usernote.php', data, function(res){
            if(/success/.test(res)){
              for (var i = data.length - 1; i >= 0; i--) {
                if(data[i].name=='content')
					if($.trim(data[i].value)=='') $('#note_content').html('你还没在这道题上记过笔记哦...');
					else $('#note_content').html(data[i].value);
                else if(data[i].name=='tags')
                  $('#user_tags').html(data[i].value);
              };
              $('#note_new_btn').hide();
              $('#note_panel').show();
              $('#NoteModal').modal('hide');
            }else{
              $('#notes_res').html('<i class="fa fa-fw fa-remove"></i> '+res).slideDown();
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
            $.get("ajax_mark.php?type=1&prob="+prob+"&op="+op,function(msg){
                if(/success/.test(msg)){
                    var tg=$("#action_mark");
                    tg.toggleClass("btn-danger");
                    tg.toggleClass("btn-default");
                    tg.find('i').toggleClass('fa-star-o').toggleClass('fa-star');
                    var tg=$("#action_mark_html");
                    if(tg.html()=="收藏题目")
                        tg.html("取消收藏");
                    else
                        tg.html("收藏题目");
                }else{
                    $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> '+msg).fadeIn();
                    setTimeout(function(){$('#alert_error').fadeOut();},2000);
                }
            });
            return false;
        });
        function click_submit(){
          <?php if(!isset($_SESSION['user'])){?>
            $('#alert_error').html('<i class="fa fa-fw fa-remove"></i> 您尚未登录...').fadeIn();
			setTimeout(function(){$('#alert_error').fadeOut();},2000);
          <?php }else{?>
            $('#prob_input').val(''+prob);
            $('#SubmitModal').modal('show');
          <?php if($pref->edrmode!='off'){?> 
            setTimeout("editor.refresh();editor.focus();", 200);
          <?php }else{?>
            $("#detail_input").focus();
          <?php }}?>
          return false;
        }
        $('#btn_submit').click(click_submit);
        $('#btn_submit2').click(click_submit);
        function toggle_info(){
          if(hide_info) {
			$('#leftside').addClass('col-sm-9');
            $('#rightside').fadeIn(300);
            $('#show_tool').fadeOut(300);
            hide_info=0;
          }else {
            $('#rightside').fadeOut(300);
            $('#show_tool').fadeIn(300);
			setTimeout("$('#leftside').addClass('col-xs-12').removeClass('col-sm-9')", 300);
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
