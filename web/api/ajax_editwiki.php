<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user'])){
    echo json_encode(array('success' => false, 'message' => _('Permission Denied...')));
	exit();
}else if(!isset($_POST['op'])){
	echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
    exit();
}

require __DIR__.'/../conf/database.php';

if($_POST['op']=='del'){
    if(!check_priv(PRIV_PROBLEM)){
        echo json_encode(array('success' => false, 'message' => _('Permission Denied...')));
        exit();
    }
    if(!isset($_POST['wiki_id'])){
        echo json_encode(array('success' => false, 'message' => _('No such wiki...')));
        exit();
    }
    $id=intval($_POST['wiki_id']);
    if(mysqli_query($con,"update wiki set defunct=(!defunct) where wiki_id=$id"))
        echo json_encode(array('success' => true));
    else
        echo json_encode(array('success' => false, 'message' => _('Database operation failed...')));
}else{
    if(isset($_POST['title'])&&!empty($_POST['title'])){
        $title=mysqli_real_escape_string($con,$_POST['title']);
    }else{
        echo json_encode(array('success' => false, 'message' => _('Please enter Title...')));
        exit();
    }
    $content=isset($_POST['content']) ? mysqli_real_escape_string($con,$_POST['content']) : '';
    $tags=isset($_POST['tags']) ? mysqli_real_escape_string($con,$_POST['tags']) : '';
    $author=$_SESSION['user'];

    $priv=0;
    if(isset($_POST['hide_cont'])){
        $priv=1; //This is temporary! Unfinished!
    }

    if($_POST['op']=='edit'){
        if(!isset($_POST['wiki_id'])){
            echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
            exit();
        }
        $id=intval($_POST['wiki_id']);
        //Get current newest revision.
        $row=mysqli_fetch_row(mysqli_query($con,"select revision from wiki where wiki_id=$id and is_max=1"));
        //revision++.
        if(isset($row[0])){
            $revision=$row[0]+1;
            //Mark the current revision as not the newest.
            $result=mysqli_query($con,"update wiki set is_max=0 where wiki_id=$id and is_max=1");
            if($result)
                //Create a new revision and mark it as the newest.
                $result=mysqli_query($con,"insert into wiki (wiki_id,title,content,tags,author,revision,is_max,in_date,privilege,defunct) values ($id,'$title','$content','$tags','$author','$revision',1,NOW(),$priv,0)");
        }else
            //If getting current newest revision failed.
            $result=false;
    }else if($_POST['op']=='add'){
        $id=1;
        $result=mysqli_query($con,'select max(wiki_id) from wiki');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $id=intval($row[0])+1;
        //Create a new revision and mark it as the newest.
        $result=mysqli_query($con,"insert into wiki (wiki_id,title,content,tags,author,revision,is_max,in_date,privilege,defunct) values ($id,'$title','$content','$tags','$author',0,1,NOW(),$priv,0)");
    }else{
        echo json_encode(array('success' => false, 'message' => _('Invalid Argument...')));
        exit();
    }

    if($result)
        echo json_encode(array('success' => true, 'wikiID' => $id));
    else
        echo json_encode(array('success' => false, 'message' => _('Unknown Error...')));
}