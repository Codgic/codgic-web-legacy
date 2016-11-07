<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';

if(!check_priv(PRIV_PROBLEM)){
    echo _('Permission Denied...');
    exit();
}
if(!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']){
    echo _('Privilege not authorized...');
    exit();
}

function get_extension($file){
  return pathinfo($file, PATHINFO_EXTENSION);
}

function get_testcase_dir(){
    $content = file_get_contents("http://127.0.0.1:8881/get_datapath");
    if(FALSE === $content)
        return FALSE;
    return $content.DIRECTORY_SEPARATOR;
}

function upload_file_handler($targetDir){
    @set_time_limit(250);
    umask(0007);

    // Create target dir
    if (!file_exists($targetDir)) {
        @mkdir($targetDir, 0770, true);
    }

    // Get a file name
    if (isset($_REQUEST["name"])) {
        $fileName = $_REQUEST["name"];
    } elseif (!empty($_FILES)) {
        $fileName = $_FILES["file"]["name"];
    } else {
        $fileName = uniqid("file_");
    }
    
    $fileName = basename($fileName);
    
    if($fileName=='' || $fileName=='.' || $fileName=='..' ){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        $info=_('Incorrect File name...');
        echo '{"jsonrpc" : "2.0", "error" : {"code": 104, "message": "'.$info.'"}, "id" : "id"}';
    }
    $ext=get_extension($fileName);
    if($ext!="in" && $ext!="out" && $ext!="cpp"){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        $info=_('Incorrect File type...');
        echo '{"jsonrpc" : "2.0", "error" : {"code": 104, "message": "'.$info.'"}, "id" : "id"}';
    }
    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

    // Chunking might be enabled
    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


    // Open temp file
    if(!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")){
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        $info=_('Failed to launch output stream...');
        echo '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "'.$info.'"}, "id" : "id"}';
    }

    if(!empty($_FILES)){
        if($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])){
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            $info=_('Can\'t move the files to its place...');
            echo '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'.$info.'"}, "id" : "id"}';
        }

        // Read binary input stream and append it to temp file
        if(!$in = @fopen($_FILES["file"]["tmp_name"], "rb")){
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            $info=_('Can\'t launch input stream...');
            echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.$info.'"}, "id" : "id"}';
        }
    }else{    
        if(!$in = @fopen("php://input", "rb")){
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            $info=_('Can\'t launch input stream...');
            echo '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.$info.'"}, "id" : "id"}';
        }
    }

    while ($buff = fread($in, 4096))
        fwrite($out, $buff);

    @fclose($out);
    @fclose($in);

    // Check if file has been uploaded
    if(!$chunks || $chunk == $chunks - 1){
        // Strip the temp .part suffix off 
        rename("{$filePath}.part", $filePath);
    }

    // Return Success JSON-RPC response
    die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

}

if(!isset($_SESSION['testcase_dir'])){
    $testcase_dir = get_testcase_dir();
    if(FALSE === $testcase_dir){
        echo _('Can\'t access data directory...');
        exit();
    }else
      $_SESSION['testcase_dir']=$testcase_dir;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    if(!isset($_REQUEST['problem_id'])){
        $info=_('No problem ID specified...');
        echo '{"jsonrpc" : "2.0", "error" : {"code": 104, "message": "'.$info.'"}, "id" : "id"}';
    }
    $prob_dir = $_SESSION['testcase_dir'].intval($_REQUEST['problem_id']);
    upload_file_handler($prob_dir);
}else{
    if(!isset($_GET['problem_id'],$_GET['op']))
        die('error');
    $prob_dir = $_SESSION['testcase_dir'].intval($_REQUEST['problem_id']);
    $op=$_GET['op'];
    if($op == 'list'){
        $arr = scandir($prob_dir);
        if(FALSE === $arr)
            $arr = array();
        echo '{"files":',json_encode(array_diff($arr,array('.','..'))),'}';
    }else if($op == 'del'){
        if(!isset($_GET['filename']))
            die('error');
        $name = basename($_GET['filename']);
        if($name!='.' && $name!='..' && $name!='' && unlink($prob_dir.DIRECTORY_SEPARATOR.$name))
            echo 'success';
        else
            echo _('Something went wrong...');
    }else
        echo _('Invalid Argument...');
}
