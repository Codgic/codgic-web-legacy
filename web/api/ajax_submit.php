<?php
require_once __DIR__.'/../inc/init.php';
require_once __DIR__.'/../lib/lang.php';
require_once __DIR__.'/../func/checklogin.php';

if(!isset($_SESSION['user']) || strlen($_SESSION['user'])==0){
    echo _('Please login first...');
    exit();
}
if(!isset($_POST['op'],$_POST['problem'])){
    echo _('Invalid Argument...');
    exit();
}

$prob=intval($_POST['problem']);

require_once __DIR__.'/../conf/database.php';
require_once __DIR__.'/../func/daemonrpc.php';

$res=mysqli_query($con,"select defunct from problem where problem_id=$prob");

if(!($row=mysqli_fetch_row($res))){
    echo _('No such problem...');
    exit();
}

if($_POST['op']=='judge'){
    if(!isset($_POST['language'])){
        echo _('Invalid Argument...');
        exit();
    }
    $lang=intval($_POST['language']);
    if(!array_key_exists($lang,$LANG_NAME)){
        echo _('Unsupported language...');
        exit();
    }
    if(!isset($_POST['source'])){
        echo _('Code too short...');
        exit();
    }
    $code=$_POST['source'];
    if(strlen($code)>29990){
        echo _('Code too long...');
        exit();
    }
    
    require __DIR__.'/../lib/problem_flags.php';
    require __DIR__.'/../func/privilege.php';

    $forbidden=false;
    if($row[0] == 0 && !check_priv(PRIV_PROBLEM))
        $forbidden=true;

    if($forbidden){
        echo _('Permission Denied...');
        exit();
    }

    $_SESSION['lang']=$lang;
    mysqli_query($con,"update users set language=$lang where user_id='".$_SESSION['user']."'");
    mysqli_query($con,"update problem set in_date=NOW() where problem_id=$prob");
    
    $share_code=(isset($_POST['public']) ? 1 : 0);

    $stmt = $con->prepare("INSERT INTO `solution` (`problem_id`, `user_id`, `time`, `memory`, `in_date`, `result`, `score`, `language`, `code_length`, `public_code`, `malicious`) 
        VALUES (?, ?, 0, 0, NOW(), 8, 0, ?, ?, ?, 0)");
    $stmt->bind_param('dsddd', $prob, $_SESSION['user'], $lang, strlen($code), $share_code);
    $stmt->execute();
    $solution_id = $stmt->insert_id;
    $stmt->close();

    $data=array('solution' => $solution_id);
    ignore_user_abort(TRUE);
    $rpcclient = new DaemonRpcClient();
    $rpcresult = $rpcclient->call($data);
    echo $rpcresult["suck_my_dick"];
}else if($_POST['op']=='rejudge'){
    echo "Rejudge is currently under development.";
}
