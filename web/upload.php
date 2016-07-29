<?php 
require 'inc/ojsettings.php';
require 'inc/privilege.php';
session_start();
if(!check_priv(PRIV_PROBLEM))
	include '403.php';
else{
if(isset($_FILES['file'])){
	try{
		if(!isset($_FILES['file']['name']) || !preg_match('/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i',$_FILES['file']['name']))
			throw new Exception('无效的图片格式');
		if($_FILES["file"]["error"] > 0)
			throw new Exception('上传错误: '.$_FILES["file"]["error"]);
			
		$filename=isset($_POST['savename']) ? $_POST['savename'] : date('YmdHis_').mt_rand(10000,99999);
		if(!strlen($filename) || preg_match('/[^-)(\w]/',$filename))
			throw new Exception("无效的文件名");
			
		$tmp = explode('.', $filename);
        $file_extension = end($tmp);

		if(file_exists("../images/$filename"))
			throw new Exception("文件 '$filename' 已经存在,<br>换一个文件名再试试。");
		if(!is_dir("../images"))
			if(!mkdir("../images",0770))
				throw new Exception("images目录无法访问, 请联系管理员!");
				
		if(move_uploaded_file($_FILES["file"]["tmp_name"],"../images/$filename")){
			$imgtag="img src=\"../images/$filename\"";
		}else
			throw new Exception("上传失败");
	}catch(Exception $e){
		$info=$e->getMessage();
	}
}else{
	$filename=date('YmdHis_').mt_rand(10000,99999);
	if(isset($_GET['id']))
		$filename=intval($_GET['id']);
}
$inTitle='上传图片';
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
	<?php require 'head.php'; ?>
	<body style="margin:0">
	<div class="container">
	  <div class="row">
		<div class="col-xs-12">
		<?php if(isset($imgtag)){ ?>
			<h2 style="margin:10px auto">上传成功</h2>
			<hr>
			<div style="white-space:nowrap">
				<div>HTML引用标签:</div> 
				<div style="margin-top:15px;margin-buttom:15px;height:30px"><span class="alert alert-info" id="html_tag">&lt;<?php echo $imgtag ?>&gt;</span></div>
			</div>
			<p style="margin-top:15px;margin-buttom:15px">
				<a href="#" class="btn btn-primary copy" id="btn_copy">复制文本</a>
				<a href="#" class="btn btn-danger" onclick="return window.close(),false;">关闭</a>
			</p>
		<?php }else if(isset($info)){ ?>
			<h2 style="margin:10px auto">错误</h2>
			<hr>
			<div class="alert alert-danger"><i class="fa fa-fw fa-remove"></i> <?php echo $info ?></div>
			<a href="#" class="btn btn-primary" onclick="return history.back(),false;">&laquo;返回</a>
		<?php }else{ ?>
			<h2 style="margin:10px auto">上传图片</h2>
			<hr>
			<form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return check_upload();">
			  <div class="form-group">
				<label>选择图片:</label>
				<input type="file" name="file" id="file">
			  </div>
			  <div class="form-group">
				<label>文件名(不含后缀):</label>
				<input class="form-control" type="text" name="savename" value="<?php echo htmlspecialchars($filename);?>">  
			  </div>
			  <div class="form-group">
				<div class="alert alert-danger collapse" id="info"></div>
				<input class="btn btn-primary" type="submit" value="上传"> 
			  </div>
			</form>
		<?php } ?>
		</div>
	  </div>
	</div>
	</body>
	<script src="/assets/js/clipboard.min.js"></script>
	<script type="text/javascript">
		function check_upload(){
			if(/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i.test(document.getElementById('file').value)){
				return true;
			}else{
				$('#info').show();
				document.getElementById('info').innerHTML="<i class="fa fa-fw fa-remove"></i> 不受支持的图片格式...";
			}
			return false;
		}
		var clipboard = new Clipboard('#btn_copy', {
        text: function() {
            return '<<?php echo $imgtag?>>';
        }
    });
    clipboard.on('error', function(e) {
        console.log(e);
    });
	</script>
</html>
<?php }?>
