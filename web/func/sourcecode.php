<?php
//Check if user has privilege to access certain sourcecode.
function sc_check_priv($prob_id,$opened,$user){
    if(!function_exists('check_priv'))
        require __DIR__.'/privilege.php';
    if(isset($_SESSION['user']))
        if(strcmp($user,$_SESSION['user'])==0 || check_priv(PRIV_SOURCE))
            return TRUE;
    require __DIR__.'/../conf/database.php';
    if(!defined('PROB_HAS_TEX'))
        require __DIR__.'/../lib/problem_flags.php';
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
            if(isset($_SESSION['user'])){
                $query='select min(result) from solution where user_id=\''.$_SESSION['user']."' and problem_id=$prob_id group by problem_id";
                $user_status=mysqli_query($con,$query);
                $row=mysqli_fetch_row($user_status);
                if($row && $row[0]==0)
                    return TRUE;
            }
            return _('You can\'t see me before solving it');
        }
        return TRUE;
    }    
    return _('Looks like you can\'t access this page');
}
