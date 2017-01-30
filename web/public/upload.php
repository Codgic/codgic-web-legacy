<?php
require __DIR__.'/inc/init.php';
require __DIR__.'/func/privilege.php';

if(!check_priv(PRIV_PROBLEM))
    include __DIR__.'/inc/403.php';
else{
    if(isset($_FILES['file'])){
        try{
            if(!isset($_FILES['file']['name']) || !preg_match('/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i',$_FILES['file']['name']))
                echo _('Unsupported File type...');
            if($_FILES["file"]["error"] > 0)
                echo _('Error: '.$_FILES["file"]["error"]);
                
            $filename=isset($_POST['savename']) ? $_POST['savename'] : date('YmdHis_').mt_rand(10000,99999);
            if(!strlen($filename) || preg_match('/[^-)(\w]/',$filename))
                echo _("Invalid File name...");
                
            $tmp = explode('.', $filename);
            $file_extension = end($tmp);

            if(file_exists(OJDIR."/images/$filename"))
                echo _('File '),"'$filename'",_(' exists already, try another name...');
            if(!is_dir(OJDIR."/images"))
                if(!mkdir(OJDIR."/images",0770))
                    echo _('Can\'t access upload directory...');
                    
            if(move_uploaded_file($_FILES["file"]["tmp_name"],OJDIR."/images/$filename")){
                $imgtag="img src=\"/images/$filename\"";
            }else
                echo _('Upload Failed!');
        }catch(Exception $e){
            $info=$e->getMessage();
        }
    }else{
        $filename=date('YmdHis_').mt_rand(10000,99999);
        if(isset($_GET['id']))
            $filename=intval($_GET['id']);
    }
    
$inTitle=_('Upload');
$Title=$inTitle .' - '. $oj_name;
?>
<!DOCTYPE html>
<html>
    <?php require __DIR__.'/inc/head.php'; ?>
    
    <body style="margin:0">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <?php if(isset($imgtag)){?>
                        <h2 style="margin:10px auto">
                            <?php echo _('Uploaded Successfully!')?>
                        </h2>
                        <hr>
                        <div style="white-space:nowrap">
                            <div>
                                <?php echo _('HTML Code')?>
                            </div> 
                            <div style="margin-top:15px;margin-buttom:15px;height:30px">
                                <span class="alert alert-info" id="html_tag">
                                    &lt;<?php echo $imgtag ?>&gt;
                                </span>
                            </div>
                        </div>
                        <p style="margin-top:15px;margin-buttom:15px">
                            <a href="#" class="btn btn-primary copy" id="btn_copy"><?php echo _('Copy')?></a>
                            <a href="#" class="btn btn-danger" onclick="return window.close(),false;"><?php echo _('Close')?></a>
                        </p>
                    <?php }else if(isset($info)){?>
                        <h2 style="margin:10px auto">
                            <?php echo _('Error!')?>
                        </h2>
                        <hr>
                        <div class="alert alert-danger"><i class="fa fa-fw fa-remove"></i> <?php echo $info ?></div>
                            <a href="#" class="btn btn-primary" onclick="return history.back(),false;"><i class="fa fa-fw fa-angle-left"></i> <?php echo _('Back')?></a>
                    <?php }else{?>
                        <h2 style="margin:10px auto">
                            <?php echo _('Upload Image')?>
                        </h2>
                        <hr>
                        <form action="upload.php" method="post" enctype="multipart/form-data" onsubmit="return check_upload();">
                            <div class="form-group">
                                <label>
                                    <?php echo _('Select Image')?>
                                </label>
                                <input type="file" name="file" id="file">
                            </div>
                            <div class="form-group">
                                <label>
                                    <?php echo _('Image Name (Without extension)')?>
                                </label>
                                <input class="form-control" type="text" name="savename" value="<?php echo htmlspecialchars($filename);?>">  
                            </div>
                            <div class="form-group">
                                <div class="alert alert-danger collapse" id="info"></div>
                                <input class="btn btn-primary" type="submit" value="<?php echo _('Upload')?>"> 
                            </div>
                        </form>
                    <?php }?>
                </div>
            </div>
        </div>

        <script src="/assets/js/clipboard.min.js"></script>
        <script type="text/javascript">
            function check_upload(){
                if(/\.(jpg|jpeg|png|gif|bmp|tif|tiff|ico|wmf)$/i.test(document.getElementById('file').value))
                    return true;
                else{
                    $('#info').show();
                    document.getElementById('info').innerHTML="<i class="fa fa-fw fa-remove"></i> <?php echo _('Unsupported File type...')?>";
                }
                return false;
            }
            var clipboard = new Clipboard('#btn_copy',{
                text: function(){
                    return '<<?php if(isset($imgtag)) echo $imgtag?>>';
                }
            });
            clipboard.on('error',function(e){
                console.log(e);
            });
        </script>
    </body>
</html>
<?php }?>