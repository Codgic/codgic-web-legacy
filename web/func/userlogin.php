<?php
function login($user, $is_cookie, $pwd=''){
    require __DIR__.'/../conf/database.php';
    if(!function_exists('my_rsa'))
        require __DIR__.'/checkpwd.php';
    $user=mysqli_real_escape_string($con,$user);
    $res=mysqli_query($con,"select password,user_id,language,defunct,email,privilege from users where user_id='$user' or email='$user' limit 1");
    $r=mysqli_fetch_row($res);
    if(!$r)
        return _('There\'s no such user...');
    if($r[3]!='N')
        return _('Your account is still being reviewed...');

    if(!$is_cookie && !password_right($user, $pwd))
        return _('Wrong Username/Password...');
    
    //Clear guest session.
    session_unset();
    if(!function_exists('clear_cookie'))
        require __DIR__.'/cookie.php';
    clear_cookie('SID');

    //Create new session.
    $_SESSION['user']=$r[1];
    $_SESSION['lang']=$r[2];
    $_SESSION['email']=$r[4];
    $_SESSION['priv']=$r[5];
    
    //Initialize user preference.
    if(!class_exists('preferences')) 
        require __DIR__.'/preferences.php';
    global $pref;
    $pref = new preferences();
    $res=mysqli_query($con,"select property,value from preferences where user_id='$user'");
    while($r=mysqli_fetch_row($res)){
        $property=$r[0];
        $pref->$property=$r[1];
    }
    $_SESSION['pref']=serialize($pref);

    require __DIR__.'/userinfo.php';
        $ip=mysqli_escape_string($con,get_ip());
    mysqli_query($con,"update users set accesstime=NOW(),ip='$ip' where user_id='$user'");

    return TRUE;
}