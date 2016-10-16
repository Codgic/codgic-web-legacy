<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';

if(!isset($_SESSION['user'])){
    echo _('Permission Denied...');
	exit();
}else if(!isset($_POST['op'])){
	echo _('Invalid Argument...');
    exit();
}

require __DIR__.'/../conf/database.php';

if($_POST['op']=='del'){
    if(!check_priv(PRIV_PROBLEM)){
        echo _('Permission Denied...');
        exit();
    }
    if(!isset($_POST['wiki_id'])){
        echo _('No such wiki...');
        exit();
    }
    $id=intval($_POST['wiki_id']);
    $result=mysqli_query($con,"select defunct from wiki where wiki_id=$id and is_max='Y' limit 1");
    if($row=mysqli_fetch_row($result)){
        if($row[0]=='N') 
            $opr='Y';
        else 
            $opr='N';
        if(mysqli_query($con,"update wiki set defunct='$opr' where wiki_id=$id and is_max='Y' limit 1"))
            echo 'success';
        else
            echo _('Something went wrong...');
    }
}else{
    if(isset($_POST['title'])&&!empty($_POST['title'])){
        $title=mysqli_real_escape_string($con,$_POST['title']);
    }else{
        echo _('Please enter title...');
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
            echo _('Invalid Argument...');
            exit();
        }
        $id=intval($_POST['wiki_id']);
        //Get current newest revision.
        $row=mysqli_fetch_row(mysqli_query($con,"select revision from wiki where wiki_id=$id and is_max='Y'"));
        //revision++.
        if(isset($row[0])){
            $revision=$row[0]+1;
            //Mark the current revision as not the newest.
            $result=mysqli_query($con,"update wiki set is_max='N' where wiki_id=$id and is_max='Y'");
            if($result)
                //Create a new revision and mark it as the newest.
                $result=mysqli_query($con,"insert into wiki (wiki_id,title,content,tags,author,revision,is_max,in_date,privilege,defunct) values ($id,'$title','$content','$tags','$author','$revision','Y',NOW(),$priv,'N')");
        }else
            //If getting current newest revision failed.
            $result=false;
    }else if($_POST['op']=='add'){
        $id=1;
        $result=mysqli_query($con,'select max(wiki_id) from wiki');
        if(($row=mysqli_fetch_row($result)) && intval($row[0]))
            $id=intval($row[0])+1;
        //Create a new revision and mark it as the newest.
        $result=mysqli_query($con,"insert into wiki (wiki_id,title,content,tags,author,revision,is_max,in_date,privilege,defunct) values ($id,'$title','$content','$tags','$author',0,'Y',NOW(),$priv,'N')");
    }else{
        echo _('Invalid Argument...');
        exit();
    }

    if($result)
        echo 'success';
    else{
        echo _('Something went wrong...');
        exit();
    }
}