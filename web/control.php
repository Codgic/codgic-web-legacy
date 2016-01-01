<?php
require 'inc/ojsettings.php';
require('inc/checklogin.php');
 
if(!isset($_SESSION['user'])){
  $info='<div class="center">然而你尚未登录...</div>';
}else{
  require('inc/database.php');
  $user_id=$_SESSION['user'];
}
$inTitle='设置';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>
  <body>
    <?php require('page_header.php'); ?>  
          
    <div class="container-fluid control_panel">
      <div class="row-fluid">
      <?php
      if(isset($info)){
        echo $info;
      }else{
      ?>
        <div class="span6 offset3">
          <h2>偏好设置</h2>
         <form id="form_preferences" action="ajax_preferences.php" method="post">
            <label class="checkbox">
              <input name="hidehotkey" type="checkbox" <?php if($pref->hidehotkey=='on')echo 'checked'; ?> > 隐藏快捷键提示
            </label>
            <label class="checkbox">
              <input name="sharecode" type="checkbox" <?php if($pref->sharecode=='on')echo 'checked'; ?> > 默认分享我的代码
            </label>
			<label class="checkbox">
              <input name="autonight" type="checkbox" <?php if($pref->autonight=='on')echo 'checked'; ?> > 自动切换夜间模式 (实验功能)
            </label>
      <?php if($pref-> autonight=='off'){
			echo "<label class=\"checkbox\"> <input name=\"night\" type=\"checkbox\"";
              if($pref->night=='on') echo 'checked'; 
              echo" > 夜间模式 (实验功能) </label>";}?><p></p>
            <input type="submit" class="btn" value="保存">
          </form>

          <h2>备份我的代码</h2>
          <p>
            你可以通过这里下载所有你AC了的题目最后一次提交的代码。<br>你每周只能执行一次该操作。
            <?php
            if(!is_null($pref->backuptime))
              echo "<br><strong>最近一次备份时间: ",date('Y-m-d H:i:s', $pref->backuptime),"</strong>";
            ?>
          </p>
          <button class="btn" id="download_btn">备份并下载</button>

          <h2>开源我的代码</h2>
          <p>
            <strong>为什么开源?</strong>
            <ol>
              <li>开源是最有影响力的信息技术文化之一，开源软件丰富并奠基了我们的网站，网络以及世界。</li>
			  <li>如果你开放了你的代码，大家便都可以阅读、使用、发布、理解并提升他们自己的程序，这也在无形中帮助原作者进步。</li>
			  <li>虽然OI题目的代码都相对较短，但其算法往往都不易理解。 开源代码可以让其它的OIer在奋斗过程中少一抹烦恼，毕竟我们都曾一样。</li>
            </ol>
          </p>
          <button class="btn" id="open_source">开源所有代码</button>
		  
		  <h2>版本信息</h2>
          <p>
            <strong>CWOJ版本: <?php echo"{$oj_ver}";?></strong>
          </p>
        </div>
      <?php }?>
      </div>
      <hr>
      <footer>
        <p>&copy; <?php echo"{$year} {$copyright}";?></p>
      </footer>
    </div>

    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/common.js"></script>

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
        $('#ret_url').val("control.php");
      });
    </script>
  </body>
</html>
