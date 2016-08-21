<?php
require 'inc/global.php';
require 'inc/ojsettings.php';
require 'inc/checklogin.php';
 
if(!isset($_SESSION['user'])){
  $info=_('Please login first');
}else{
  require 'inc/database.php';
  $user_id=$_SESSION['user'];
}
$inTitle=_('Preferences');
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
		<h2><?php echo _('Preferences')?></h2>
		<form id="form_preferences" action="#" method="post" style="margin-top:10px">
		<div class="row">
		  <div class="form-group col-xs-6 col-sm-3">
			<label for="night"><?php echo _('Theme')?></label>
			<select class="form-control" name="night" id="slt_night">
			  <option value="auto"><?php echo _('Auto switch')?></option>
		  	  <option value="off"><?php echo _('Day mode')?></option>
		 	  <option value="on"><?php echo _('Night mode')?></option>
		    </select>
		    <script>
			  $('#slt_night').val("<?php echo $pref->night?>");
		    </script>
		  </div>
		  <div class="form-group col-xs-6 col-sm-3">
			<label for="edrmode"><?php echo _('Code editor')?></label>
			  <select class="form-control" name="edrmode" id="slt_edrmode">
			  <option value="default"><?php echo _('Default')?></option>
			  <option value="vim">Vim</option>
			  <option value="emacs">Emacs</option>
			  <option value="sublime">Sublime</option>
              <option value="off"><?php echo _('None')?></option>
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
				<input name="sharecode" type="checkbox" <?php if($pref->sharecode=='on')echo 'checked'?>> <?php echo _('Share my code by default.')?>
			  </label>
			</div>
		    <input type="submit" class="btn btn-default" value="<?php echo _('Save')?>">
		  </div>
		</div>
		</form>
		<h2><?php echo _('Backup my code')?></h2>
          <div class="row">
			<div class="col-xs-12">
			<?php echo _('Here you can download your last accepted submit of every problem. However, you can execute this not more than once a week.')?>
            <?php
            if(!is_null($pref->backuptime))
              echo "<br><strong>最近一次备份时间: ",date('Y-m-d H:i:s', $pref->backuptime),"</strong>";
            ?>
			<br><button class="btn btn-default" id="download_btn" style="margin-top:8px"><?php echo _('Backup & Download')?></button>
			</div>
          </div>
          
          <h2><?php echo _('Open my source code')?></h2>
			<div class="row">
			  <div class="col-xs-12" style="margin-top:10px">
                <strong><?php echo _('Why open source?')?></strong>
				  <ul>
                    <li><?php echo _('Open-source is an influential cultures which can date back to the early age of computer science\'s history; in addition, open-source softwares are the foundation of the web, the Internet, and our world.')?></li>
                    <li><?php echo _('If one shares his code, everyone would have the chance to use, distribute, understand and improve the programs, and thus helps the author in return.')?></li>
                    <li><?php echo _('Codes in OI are relatively short, nevertheless proned to be extremely obscure. Open-sourcing them can help other OIers struggling for solutions, whom we were once alike.')?></li>
				  </ul>
                  <div class="alert collapse" id="opensource_res"></div>
				  <button class="btn btn-default" id="open_source"><?php echo _('Open source all my code')?></button>
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
          $.post('ajax_opensource.php',{id:'all'},function(msg){
            if(/success/.test(msg))
              $('#opensource_res').removeClass('alert-danger').addClass('alert-success').html('<i class="fa fa-fw fa-check"></i> <?php echo _('Thanks for making a great contribution!')?>').slideDown();
            else
              $('#opensource_res').removeClass('alert-success').addClass('alert-danger').html('<i class="fa fa-fw fa-remove"></i> '+msg).slideDown();
          });
          return false;
        });
      });
    </script>
  </body>
</html>
