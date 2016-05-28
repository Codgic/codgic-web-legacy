<?php 
require 'inc/ojsettings.php';
require ('inc/checklogin.php');
$inTitle='注册';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
  <?php require('head.php'); ?>

  <body>
    <?php require('page_header.php'); ?>  
          
    <div class="container-fluid">
      <div class="row-fluid">
      <?php 
      if(isset($_SESSION['user'])){
        echo '<div style="text-align: center">你已经登录了...</div>';
      }else{
      ?>
        <div class="span8 offset3">
          <form class="form-horizontal" id="form_profile" action="#" method="post">
            <input type="hidden" value="reg" name="type">
            <fieldset>
              <div class="control-group" id="userid_ctl">
                <label class="control-label">用户名</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="userid" id="input_userid">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_nick">昵称</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="nick" id="input_nick">
                </div>
              </div>
              <div class="control-group" id="newpwd_ctl">
                <label class="control-label" for="input_newpwd">密码</label>
                <div class="controls">
                  <input class="input-xlarge" type="password" id="input_newpwd" name="newpwd">
                </div>
              </div>
              <div class="control-group" id="reppwd_ctl">
                <label class="control-label" for="input_reppwd">重复密码</label>
                <div class="controls">
                  <input class="input-xlarge" type="password" id="input_reppwd">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_email">邮箱</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="email" id="input_email">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="input_school">学校</label>
                <div class="controls">
                  <input class="input-xlarge" type="text" name="school" id="input_school">
                </div>
              </div>
              <div class="row-fluid">
                <div class="span8 center">
                  <span id="ajax_result" class="alert hide"></span>
                </div>
              </div>
              <div class="span3 offset3">
                <span id="save_btn" class="btn btn-primary">提交</span>
              </div>
            </fieldset>
          </form>
        </div>
      <?php } ?>
      </div>
      <hr>
      <footer>
       <p>&copy; <?php echo"{$year} {$oj_copy}";?></p>
      </footer>
    </div>
	<script src="/assets/js/common.js"></script>
    <script type="text/javascript"> 
      $(document).ready(function(){
        $('#save_btn').click(function(){
          var b=false,pwd;
          if(!$.trim($('#input_userid').val())) {
            $('#userid_ctl').addClass('error');
            b=true;
          }else{
            $('#userid_ctl').removeClass('error');
          }
          pwd=$('#input_newpwd').val();
          if(pwd!='' && $('#input_reppwd').val()!=pwd){
            b=true;
            $('#newpwd_ctl').addClass('error');
            $('#reppwd_ctl').addClass('error');
          }else{
            $('#newpwd_ctl').removeClass('error');
            $('#reppwd_ctl').removeClass('error');
          }
          if(!b){
            $.ajax({
              type:"POST",
              url:"ajax_profile.php",
              data:$('#form_profile').serialize(),
              success:function(msg){
                if(msg=='success'){
			$('#ajax_result').addClass('alert-success');
			var c = <?php echo $require_confirm?>;
		       	if(c == 1) $('#ajax_result').html('你的注册申将被审核～').show();
			else $('#ajax_result').html('你的账户已经成功注册...').show();
			window.setTimeout("window.location='index.php'",2000); 
               }else{
                  $('#ajax_result').html(msg).show();
                  setTimeout(function(){location.href="index.php";},2000);
                }
              }
            });
          }
        });
        $('#ret_url').val("index.php");
      });
    </script>
  </body>
</html>
