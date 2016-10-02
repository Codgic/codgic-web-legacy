<?php 
require __DIR__.'/conf/ojsettings.php';
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';

function check_permission($prob_id,$opened,$user){
	if(!isset($_SESSION['user']))
		return _('Please login first');
	if(strcmp($user,$_SESSION['user'])==0 || check_priv(PRIV_SOURCE))
		return TRUE;
	require __DIR__.'/conf/database.php';
	require __DIR__.'/lib/problem_flags.php';
	if($opened){
		$row = mysqli_fetch_row(mysqli_query($con,"select has_tex from problem where problem_id=$prob_id"));
		if(!$row)
			return _('There\'s no such problem');
		$prob_flag = $row[0];
		if(($prob_flag & PROB_IS_HIDE) && !check_priv(PRIV_INSIDER))
			return _('Looks like you can\'t access this page');
		if($prob_flag & PROB_DISABLE_OPENSOURCE)
			return _('This solution is not open-source');
		else if($prob_flag & PROB_SOLVED_OPENSOURCE){
			$query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$prob_id group by problem_id";
			$user_status=mysqli_query($con,$query);
			$row=mysqli_fetch_row($user_status);
			if($row && $row[0]==0)
				return TRUE;
			return _('You can\'t see me before solving it');
		}
		return TRUE;
	}
	return _('Looks like you can\'t access this page');
}

require __DIR__.'/lib/result_type.php';
require __DIR__.'/lib/lang.php';

$inTitle=_('Sourcecode');

if(!isset($_GET['solution_id']))
	$info=_('Please specify the solution id');
else{
	$sol_id=intval($_GET['solution_id']);
	require __DIR__.'/func/checklogin.php';
	require __DIR__.'/conf/database.php';
	$result=mysqli_query($con,"select user_id,time,memory,result,language,code_length,problem_id,public_code from solution where solution_id=$sol_id");
	$row=mysqli_fetch_row($result);
	if(!$row)
		die('No such solution.');
	$ret = check_permission($row[6], $row[7], $row[0]);
	if($ret === TRUE)
		$allowed = TRUE;
	else{
		$allowed = FALSE;
		$info=$ret;
	}

	if($allowed){
		$result=mysqli_query($con,"select source from source_code where solution_id=$sol_id");
		if($tmp=mysqli_fetch_row($result))
			$source=$tmp[0];
		else
			$info = _('Sourcecode not available');
	}
	$inTitle.=" #$sol_id";
}

$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php 
		require __DIR__.'/inc/head.php'; 
		if($t_night=='on') 
			echo '<link rel="stylesheet" href="/assets/css/codemirror.midnight.css">';
		else
			echo'<link rel="stylesheet" href="/assets/css/codemirror.eclipse.css">';
	?>
	<link rel="stylesheet" href="/assets/css/codemirror.css"> 
	<link rel="stylesheet" href="/assets/css/codemirror.fullscreen.css">
	<body>
		<?php require __DIR__.'/inc/navbar.php'; ?>  
          
		<div class="container">
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
				<div class="row text-center">
					<?php echo _('User: '),$row[0];?>
				</div>
				<div class="row text-center">
					<?php echo _('Problem: '),$row[6];?>&nbsp;&nbsp;
					<?php echo _('Result: '),$RESULT_TYPE[$row[3]];?>
				</div>
				<div class="row text-center">
					<?php echo _('Size: '),$row[5];?>&nbsp;&nbsp;
					<?php echo _('Language: '),$LANG_NAME[$row[4]];?>
				</div>
				<div class="row text-center">
					<?php echo _('Time: '),$row[1];?>&nbsp;ms&nbsp;
					<?php echo _('Memory: '),$row[2];?>&nbsp;KB
				</div>
				<div class="row">
					<div class="col-xs-12">
						<a href="javascript:history.back(-1);" class="btn btn-primary"><i class="fa fa-angle-left"></i> <?php echo _('Go Back...')?></a>
						<div class="btn-group">
							<button class="btn btn-default" onclick="toggle_fullscreen(editor)">
								<i class="fa fa-fw fa-expand"></i> <?php echo _('Fullscreen')?> (Ctrl+F11)
							</button>
							<button class="btn btn-default" data-clipboard-action="copy" data-toggle="tooltip" data-trigger="manual" id="btn_copy">
								<i class="fa fa-fw fa-clipboard"></i> <?php echo _('Copy')?>
							</button>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-12" id="div_code">
						<textarea id="text_code"><?php echo htmlspecialchars($source);?></textarea>
					</div>
				</div>
			<?php } 
			require __DIR__.'/inc/footer.php';?>
		</div>
		
		<script src="/assets/js/codemirror.js"></script>
		<script src="/assets/js/codemirror.fullscreen.js"></script>
		<script src="/assets/js/codemirror.clike.js"></script>
		<script src="/assets/js/codemirror.pascal.js"></script>
		<script src="/assets/js/clipboard.min.js"></script>
		<script src="/assets/js/common.js?v=<?php echo $web_ver?>"></script>
		<script type="text/javascript"> 
			var editor = CodeMirror.fromTextArea(document.getElementById('text_code'), {
				theme: "<?php 
				if($t_night=='on') 
					echo 'midnight'; 
				else
					echo 'eclipse'
				?>",
				mode: "<?php 
				if($LANG_NAME[$row[4]]=='GCC')
					echo 'text/x-csrc';
				if($LANG_NAME[$row[4]]=='Pascal')
					echo 'text/x-pascal';
				else 
					echo 'text/x-c++src'
				?>",
				lineNumbers: true,
				readOnly: 'nocursor',
				viewportMargin: Infinity
			});
			function toggle_fullscreen(cm){
				if(cm.getOption("fullScreen")){
					$('.navbar').css("z-index",1030);  
					cm.setOption("fullScreen", false);
				}else{
					$('.navbar').css("z-index",0);   
					cm.setOption("fullScreen", !cm.getOption("fullScreen"));
				}  
			};
			var clipboard = new Clipboard('#btn_copy', {
				text: function(){
					return editor.getValue();
				}
			});
			clipboard.on('success', function(e){
				$('#btn_copy').attr('title','<?php echo _('Copied!')?>');
				$('#btn_copy').tooltip('show');
				setTimeout("$('#btn_copy').tooltip('destroy')",800);
			});
			clipboard.on('error', function(e){
				$('#btn_copy').attr('title','<?php echo _('Failed...')?>');
				$('#btn_copy').tooltip('show');
				setTimeout("$('#btn_copy').tooltip('destroy')",800);
			});
			var solution_id=<?php echo $sol_id?>;
			$(document).ready(function(){
				$(document).keydown(function(e){
					if(e.ctrlKey&&e.which==122)
						toggle_fullscreen(editor);
				});
			});
			function doajax(fun){
				$.ajax({type:"GET",url:("sourcecode.php?raw=1&solution_id="+solution_id),success:fun});
			}
			function show_raw(){
				return true; 
				doajax(function(msg){
					$('#div_code').html('<pre>'+htmlEncode(msg)+'</pre>');
				});
				return false;
			}
		</script>
	</body>
</html>