<?php
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
 
if(!isset($_SESSION['user'])){
  $info='你还没有登录';
}else{
  require 'inc/database.php';
  $user_id=$_SESSION['user'];
}
$inTitle='设置';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require 'head.php'; ?>
  <body>
    <?php require 'page_header.php'; ?>  
          
	<div class="container control_panel">
	  <div class="row">
      <?php if(isset($info)){?>
        <div class="text-center none-text none-center">
          <p><i class="fa fa-meh-o fa-4x"></i></p>
          <p><b>Whoops</b><br>
          <?php echo $info?></p>
        </div>
      <?php }else{?>
	  <div class="col-xs-12">
		<h2>偏好设置</h2>
		<form id="form_preferences" action="#" method="post" style="margin-top:10px">
		<div class="row">
		  <div class="form-group col-xs-6 col-sm-3">
			<label for="night">主题模式</label>
			<select class="form-control" name="night" id="slt_night">
			  <option value="auto">自动切换</option>
		  	  <option value="off">日间模式</option>
		 	  <option value="on">夜间模式</option>
		    </select>
		    <script>
			  $('#slt_night').val("<?php echo $pref->night?>");
		    </script>
		  </div>
		  <div class="form-group col-xs-6 col-sm-3">
			<label for="edrmode">编辑器模式</label>
			  <select class="form-control" name="edrmode" id="slt_edrmode">
			  <option value="default">默认</option>
			  <option value="vim">Vim</option>
			  <option value="emacs">Emacs</option>
			  <option value="sublime">Sublime</option>
              <option value="off">不使用编辑器</option>
		    </select>
		    <script>
			  $('#slt_edrmode').val("<?php echo $pref->edrmode?>");
		    </script>
		  </div>
		</div>
		<div class="row">
		  <div class="col-xs-12"> 
		    <div class="checkbox">
			  <label>
				<input name="sharecode" type="checkbox" <?php if($pref->sharecode=='on')echo 'checked'?>> 默认分享我的代码
			  </label>
			</div>
		    <input type="submit" class="btn btn-default" value="保存">
		  </div>
		</div>
		</form>
		<h2>备份我的代码</h2>
          <div class="row">
			<div class="col-xs-12">
			你可以通过这里下载所有你AC了的题目最后一次提交的代码。<br>你每周只能执行一次该操作。
            <?php
            if(!is_null($pref->backuptime))
              echo "<br><strong>最近一次备份时间: ",date('Y-m-d H:i:s', $pref->backuptime),"</strong>";
            ?>
			<br><button class="btn btn-default" id="download_btn" style="margin-top:8px">备份并下载</button>
			</div>
          </div>
          
          <h2>开源我的代码</h2>
			<div class="row">
			  <div class="col-xs-12" style="margin-top:10px">
                <strong>为什么开源?</strong>
				  <ol>
					<li>开源是最有影响力的信息技术文化之一，开源软件丰富并奠基了我们的网站，网络以及世界。</li>
					<li>如果你开放了你的代码，大家便都可以阅读、使用、发布、理解并提升他们自己的程序，这也在无形中帮助原作者进步。</li>
					<li>虽然OI题目的代码都相对较短，但其算法往往都不易理解。 开源代码可以让其它的OIer在奋斗过程中少一些烦恼，毕竟我们都曾一样。</li>
				  </ol>
				  <button class="btn btn-default" id="open_source">开源所有代码</button>
				</div>
			  </div>  
		   </div>
      <?php }?>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
    <script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#form_preferences').submit(function(){
            $.ajax({
              type:"POST",
              url:"ajax_preferences.php",
              success:function(msg){location.reload();},
              data:$('#form_preferences').serialize()
            });
            return false;
        });
        $('#download_btn').click(function(){
          $('body>iframe').remove();
          $('<iframe>').hide().attr('src','backupcode.php').appendTo('body');
        });
        $('#open_source').click(function(){
          if(!window.confirm("确定要开源你所有的代码?"))
            return false;
          $.post('ajax_opensource.php',{id:'all'});
        });
      });
    </script>
  </body>
</html>
