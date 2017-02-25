<?php
require __DIR__.'/../inc/init.php';
require __DIR__.'/../func/privilege.php';

if (!check_priv(PRIV_SYSTEM)) {
    echo _('Permission Denied...');
    exit();
}
if (!isset($_SESSION['admin_tfa']) || !$_SESSION['admin_tfa']) {
    echo _('Privilege not authorized...');
    exit();
}
if (!isset($_POST['op'])) {
    echo _('Invalid Argument...');
    exit();
}

$op=$_POST['op'];

require __DIR__.'/..../src/database.php';
require __DIR__.'/../lib/problem_flags.php';

$level_max = (PROB_LEVEL_MASK>>PROB_LEVEL_SHIFT);

/*
* --------------------
*  Home
* --------------------
*
*   op: update_index
*
*   Update the text shown pn index page.
*/

if ($op == 'update_index') {
    $index_text=isset($_POST['text']) ? mysqli_real_escape_string($con,str_replace(PHP_EOL, "<br>", $_POST['text'])) : '';

    if (mysqli_query($con,"insert into news (news_id,content,time) VALUES (0, '$index_text', NOW()) ON DUPLICATE KEY UPDATE content='$index_text', time=NOW()"))
        echo 'success';
    else
        echo _('Something went wrong...');
}

/*
*   op: update_category
*
*   Update HTML code of Problem Categories on index.
*/

else if ($op == 'update_category') {
    $category=isset($_POST['content']) ? mysqli_real_escape_string($con,trim($_POST['content'])) : '';

    if (mysqli_query($con,"insert into user_notes (id,problem_id,tags,user_id,content,edit_time) VALUES (0, 0,  '', 'root', '$category', NOW()) ON DUPLICATE KEY UPDATE content='$category', edit_time=NOW()")) 
        echo 'success';
    else
        echo _('Something went wrong...');
}

/*
* --------------------
*  News
* --------------------
*
*   op: list_news
*
*   Return HTML code of News List.
*/

else if ($op == "list_news") {
    $res = mysqli_query($con,"select news_id,time,title,importance from news where news_id>0 order by importance desc, news_id desc");
    if (mysqli_num_rows($res) == 0) {
        echo '<div class="alert alert-info">',_('There\'s nothing here...'), '</div>';
    }
?>
    <table class="table table-condensed table-striped">
        <caption></caption>
        <thead>
            <tr>
                <th style="width:6%">ID</th>
                <th style="width:20%"><?php echo _('Date')?></th>
                <th style="width:62%"><?php echo _('Title')?></th>
                <th style="width:12%"><?php echo _('Operations')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                while($row = mysqli_fetch_row($res)) {
                    $addt1='';
                    $addt2='';
                    if ($row[3] =='1') {
                        $row[2] = _('[Sticky] ').$row[2];
                        $addt1='<b>';
                        $addt2='</b>';
                        }
                    echo '<tr><td>',$row[0], '</td><td>',$row[1], '</td><td style="text-align:left">',$addt1.htmlspecialchars($row[2]).$addt2, '</td><td><a href="#"><i class="fa fa-pencil"></i></a></td></tr>';
                }
            ?>
        </tbody>
    </table>
<?php }

/*
*   op: get_news
*
*   Get news info and return them in JSON.
*/

else if ($op == 'get_news') {
    if (!isset($_POST['news_id']))
        die('error');

    $newsid=intval($_POST['news_id']);

    $res = mysqli_query($con,"select title,content,time,importance,privilege from news where news_id=$newsid");
    $row = mysqli_fetch_row($res);

    $arr=array('title'=>$row[0], 'content' => $row[1], 'time' => $row[2], 'importance' => $row[3], 'priv' => $row[4]);
    echo json_encode($arr);
}

/*
*   op: add_news
*
*   Add news to database.
*/

else if ($op == 'add_news') {
    if (!isset($_POST['title'])||!isset($_POST['content']))
        die('error');

    if (!isset($_POST['importance']))
        $importance = 0;
    else 
        $importance = 1;

    $title = mysqli_real_escape_string($con,trim($_POST['title']));
    $content = isset($_POST['content']) ? mysqli_real_escape_string($con, $_POST['content']) : '';
    $userid = mysqli_real_escape_string($con, $_SESSION['user']);

    $row = mysqli_fetch_row(mysqli_query($con,"select max(news_id) from news"));

    $id = 1;
    if ($row[0])
        $id = $row[0]+1;

    $priv=0;
    if (!isset($_POST['g'])) {
        for($i = 0; $i < 4; $i++)
            if (isset($_POST["$i"]))
                $priv += pow(2, $i);
        $priv += PRIV_SYSTEM; //System always has the privilege.
    }

    if (mysqli_query($con,"insert into news(news_id,author,time,title,content,privilege,importance) values ($id, '$userid', NOW(), '$title', '$content',$priv,$importance)"))
        echo 'success';
    else
        echo 'error';
}

/*
*   op: edit_news
*
*   Edit news.
*/

else if ($op == 'edit_news') {
    if (!isset($_POST['news_id'])||!isset($_POST['title']))
        die('error');
        
    if (!isset($_POST['importance']))
        $importance = 0;
    else
        $importance = 1;
    
    $news_id = intval($_POST['news_id']);
    $title = mysqli_real_escape_string($con,trim($_POST['title']));
    $content = isset($_POST['content']) ? mysqli_real_escape_string($con, $_POST['content']): '';

    $priv=0;
    if (!isset($_POST['g'])) {
        for($i = 0; $i < 4; $i++)
            if (isset($_POST["$i"]))
                $priv += pow(2, $i);
        $priv += PRIV_SYSTEM; //System always has the privilege.
    }

    if (mysqli_query($con,"update news set title = '$title', content = '$content', importance = $importance, privilege = $priv where news_id = $news_id"))
        echo 'success';
    else
        echo 'error';
}

/*
*   op: del_news
*
*   Delete specified news.
*/

else if ($op == 'del_news') {
    isset($_POST['news_id']) ? $news_id = intval($_POST['news_id']) : die('');

    if (mysqli_query($con,"delete from news where $news_id > 0 and news_id = $news_id")) {
        if (mysqli_query($con,"update news set news_id = news_id-1 where news_id > $news_id order by news_id"))
            echo 'success';
    }
    else
        echo _('Something went wrong...');
}

/*
* --------------------
*  Experience
* --------------------
*
*   op: list_experience_title
*
*   Return HTML code of title list.
*/

else if ($op == 'list_experience_title') {
    $res = mysqli_query($con,"select title,experience from experience_titles order by experience");
?>
    <table class="table table-striped">
        <caption><?php echo _('Titles')?></caption>
        <thead>
            <tr>
                <th><?php echo _('Experience')?>&nbsp;&ge;</th>
                <th><?php echo _('Title')?></th>
                <th><?php echo _('Operations')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                while($row = mysqli_fetch_row($res)) {
                    $t=htmlspecialchars($row[0]);
                    echo'
                        <tr>
                            <td>'.$row[1].'</td>
                            <td>'.$t.'</td>
                            <td><a href="#"><i data-id="'.$row[1].'" class="fa fa-remove"></i></a></td>
                        </tr>';
                }
            ?>
        </tbody>
    </table>
<?php }

/*
*   op: list_level_experience
*
*   Return HTML code of experience list.
*/

else if ($op == 'list_level_experience') {
    $res = mysqli_query($con,"select level,experience from level_experience order by level");
    $le = array_fill(0, $level_max+1,0);
    while($row = mysqli_fetch_row($res)) {
        $le[$row[0]] = $row[1];
    }
?>
    <table class="table table-striped">
        <caption><?php echo _('Experience')?></caption>
        <thead>
            <th><?php echo _('Level')?></th>
            <th><?php echo _('Experience')?></th>
        </thead>
        <tbody>
            <?php
                foreach ($le as $key => $value)
                    echo "<tr><td>$key</td><td><input type=\"number\" name=\"experience[]\" class=\"form-control input-sm center-block\" style=\"width:100px\" value=\"$value\"></td></tr>";
            ?>
        </tbody>
    </table>
<?php }

/*
*   op: add_experience_title
*
*   Add a title.
*/

else if ($op == 'add_experience_title') {
    if (!isset($_POST['experience'], $_POST['title']))
        die('');
    
    $e=intval($_POST['experience']);
    $t=mysqli_real_escape_string($con,$_POST['title']);
    mysqli_query($con,"INSERT INTO experience_titles VALUES ($e, '$t')");
}

/*
*   op: del_title
*
*   Delete specified title.
*/

else if ($op == 'del_title') {
    if (!isset($_POST['id']))
        die('');

    $experience = intval($_POST['id']);
    mysqli_query($con,"DELETE FROM experience_titles where experience = $experience");
}

/*
*   op: update_level_experience
*
*   Update the text of specified experience level.
*/

else if ($op == 'update_level_experience') {
    if (!isset($_POST['experience']))
        die('');

    $arr = $_POST['experience'];

    if (count($arr) != $level_max + 1)
        die('');
    
    foreach ($arr as $key => $value) {
        $key = intval($key);
        $value = intval($value);

        mysqli_query($con,"INSERT INTO level_experience VALUES ($key,$value) ON DUPLICATE KEY UPDATE experience=$value");
    }
}

/*
* --------------------
*  Users
* --------------------
*
*   op: list_usr
*
*   Return HTML Code of User List.
*/

else if ($op == "list_usr") {
    if (isset($_POST['page_id'])) { 
        $page_id=(intval($_POST['page_id']) - 1) * 20;
        if ($page_id < 0)
            $page_id = 0;
    }else
        $page_id = 0;

    if (isset($_POST['q'])) {
        $keyword = mysqli_real_escape_string($con,trim($_POST['q']));
    }else
        $keyword = '';

    $res = mysqli_query($con, "select user_id,accesstime,solved,submit,accesstime,email,privilege,nick from users where (user_id like '%$keyword%' or nick like '%$keyword%') order by privilege desc,user_id limit $page_id,20");
    if (mysqli_num_rows($res) == 0) {
        echo '<div class="text-center none-text none-center"><p><i class="fa fa-meh-o fa-4x"></i></p><p><b>Whoops</b><br>',_('Looks like there\'s nothing here'), '</p></div>';
        exit();
    }
?>
    <table class="table table-condensed table-striped">
        <caption></caption>
        <thead>
            <tr>
                <th colspan="2"><?php echo _('User')?></th>
                <th style="width:30%"><?php echo _('Privilege')?></th>
                <th style="width:15%"><?php echo _('Last Seen')?></th>
                <th style="width:10%"><?php echo _('AC/Submit')?></th>
                <th style="width:20%"><?php echo _('Operations')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                require __DIR__.'/../../src/userinfo.php';
                while($row = mysqli_fetch_row($res)) {
                    echo '<tr><td><img class="img-circle" src="'.get_gravatar($row[5],30).'" width="30" height="30"></td>';
                    echo '<td style="text-align:left"><strong><a href="#linkU">'.$row[0].'</a></strong>';
                    if (!($row[6] & PRIV_USER)) {
                      if (is_null($row[1])) echo '<span style="color:red"> ',_('To Be Reviewed'), '</span>';
                      else echo '<span style="color:red"> ',_('Disabled'), '</span>';
                    }
                    echo '</td>';
                    echo '<td>'.list_priv($row[6]).'(<span>'.$row[6].'</span>)</td>';
                    echo '<td>',$row[1], '</td>';
                    echo '<td>',$row[2], '/',$row[3], '</td>';
                    echo '<td><div class="btn-group"><a href="#email" class="btn btn-default">',_('Email'), '</a><a href="#priv" class="btn btn-default">',_('Privilege'), '</a>';
                    echo '</div></td></tr>';
                }
            ?>
        </tbody>
    </table>
<?php }

/*
*   op: update_priv
*
*   Update user privilege.
*/

else if ($op == 'update_priv') {
    isset($_POST['user_id']) ? $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');
    if (empty($uid)) die('');
    
    $priv=0;
    for($i=0;$i<5;$i++)
        if (isset($_POST["$i"])) $priv+=pow(2,$i);
      
    if (mysqli_query($con,"update users set privilege='$priv' where user_id='$uid'"))
        echo 'success';
    else
        echo _('Something went wrong...');
}

/*
*   op: del_user
*
*   Delete specified user. 
*/

else if ($op == 'del_usr') {
    isset($_POST['user_id']) ? $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');

    if (!strcasecmp($uid,$_SESSION['user']))
        die('');

    mysqli_query($con,"delete from users where user_id='$uid' and (accesstime IS NULL)");
}

/*
*   op: toggle_user
*
*   Enable / Disable specified user.
*/

else if ($op == 'toggle_usr') {
    isset($_POST['user_id']) ?  $uid=mysqli_real_escape_string($con,trim($_POST['user_id'])) : die('');

    $row = mysqli_fetch_row(mysqli_query($con,"select defunct,privilege from users where user_id='$uid'"));
    if ($row[1]&PRIV_SYSTEM)
        die('');

    if ($row[0] =='Y')
        mysqli_query($con,"update users set defunct='N' where user_id='$uid'");
    else if ($row[0] =='N')
        mysqli_query($con,"update users set defunct='Y' where user_id='$uid'");
}

/*
*   op: sendemail
*
*   Send email to specified user.
*/

else if ($op == 'sendemail') {
    require __DIR__.'/../../src/mailsettings.php';

    if (isset($_POST['to_user']) && !empty($_POST['to_user'])) 
        $uid=mysqli_real_escape_string($con,trim($_POST['to_user']));
    else {
        echo _('Reciever can\'t be empty...');
        exit();
    }

    if (isset($_POST['title']) && !empty($_POST['title']))
        $title=mysqli_real_escape_string($con,trim($_POST['title']));
    else {
        echo _('Title can\'t be empty...');
        exit();
    }

    require __DIR__.'/../func/text.php';

    if (isset($_POST['content']) && !empty($_POST['content'])) 
        $content=mysqli_real_escape_string($con,trim(encode_space($_POST['content'])));
    else {
        echo _('Content can\'t be empty...');
        exit();
    }

    $row = mysqli_fetch_row(mysqli_query($con,"select email from users where user_id='$uid'"));

    echo postmail($row[0],$title,$content);
}

/*
*   op: sendemail_all
*
*   Send email to all users (DEPRECATED).
*/

else if ($op == 'sendemail_all') {
    require __DIR__.'/../../src/mailsettings.php';
    ignore_user_abort(true); // This is actually quite dirty =.=

    if (isset($_POST['title']) && !empty($_POST['title'])) 
        $title=mysqli_real_escape_string($con,trim($_POST['title']));
    else {
        echo _('Title can\'t be empty...');
        exit();
    }

    require __DIR__.'/../func/text.php';

    if (isset($_POST['content']) && !empty($_POST['content'])) 
        $content=mysqli_real_escape_string($con,trim(encode_space($_POST['content'])));
    else {
        echo _('Content can\'t be empty...');
        exit();
    }

    $res = mysqli_query($con,"select email from users");

    while($row = mysqli_fetch_row($res)) {
      $re = '';
      $r = postmail($row[0],$title,$content);
      if ($r != 'success')
        $re .= $r.'<br>';
    }
    if ($re=='')
        $re='success';
        
    echo $re;
}else
    echo _('Invalid Argument...');
