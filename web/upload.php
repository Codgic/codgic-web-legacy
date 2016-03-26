<?php 
require 'inc/ojsettings.php';
session_start();
if(!isset($_SESSION['administrator']))
	die('You are not administrator');

if(isset($_FILES['file'])){
	try{
		if(!isset($_FILES['file']['name']) || !preg_match('/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i',$_FILES['file']['name']))
			throw new Exception('无效的图片格式');
		if($_FILES["file"]["error"] > 0)
			throw new Exception('上传错误: '.$_FILES["file"]["error"]);
			
		$filename=isset($_POST['savename']) ? $_POST['savename'] : date('YmdHis_').mt_rand(10000,99999);
		if(!strlen($filename) || preg_match('/[^-)(\w]/',$filename))
			throw new Exception("无效的文件名");
			
		$filename.='.'.end(explode('.',$_FILES['file']['name']));

		if(file_exists("../images/$filename"))
			throw new Exception("文件 '$filename' 已经存在,<br>换一个文件名再试试。");
		if(!is_dir("../images"))
			if(!mkdir("../images",0770))
				throw new Exception("无法创建images目录! 请联系管理员!");
				
		if(move_uploaded_file($_FILES["file"]["tmp_name"],"../images/$filename")){
			$imgtag="&lt;img src=\"../images/$filename\"&gt;";
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
<html manifest="appcache.manifest">
	<meta charset="utf-8">
	<style type="text/css">
		body{
			background: #FFFFFF;
			font-size: 16px;
			overflow: hidden;
		}
		h3,h4{
			text-align: center;
		}
		a{
			color: #08C;
		}
	</style>
	<script type="text/javascript">
		function check_upload(){
			if(/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i.test(document.getElementById('file').value)){
				return true;
			}else{
				document.getElementById('info').innerHTML="Invalid image format";
			}
			return false;
		}
	</script>
	<body>
		<?php if(isset($imgtag)){ ?>
			<h3 style="margin:10px auto">上传成功！</h3>
			<div style="overflow:auto;white-space:nowrap">HTML引用标签: <br /><span style="color:red"><?php echo $imgtag ?></span></div>
			<p style="text-align:center"><a href="#" onclick="return window.close(),false;">关闭</a></p>
		<?php }else if(isset($info)){ ?>
			<h4 style="margin:10px auto"><?php echo $info ?></h4>
			<a href="#" onclick="return history.back(),false;">&laquo;返回</a>
		<?php }else{ ?>
			<form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return check_upload();">
				<div><p><span>选择图片:<br></span><input type="file" name="file" id="file"></p></div>
				<div><p><span>文件名:<br></span><input type="text" name="savename" value="<?php echo htmlspecialchars($filename);?>" style="width:233px;"></p></div>
				<div style="text-align:center">
					<div id="info"> </div>
					<input type="submit" value="上传"> 
				</div>
			</form>
		<?php } ?>
	</body>
</html>
