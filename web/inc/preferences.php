<?php
class preferences{
	public $hidehotkey;
	public $autonight;
	public $night;
	public $sharecode;
	public $backuptime;

	function __construct()
	{
     if(!isset($_SESSION['user'])){
		$this->hidehotkey='off';
		$this->night='off';
		$this->autonight='on';
		$this->sharecode='off';
    }else{
    require('inc/database.php');
    $user=$_SESSION['user'];
    $sql=mysqli_query($con, "select value,property from preferences where user_id='$user'");
    while($r=mysqli_fetch_row($sql)){
     if($r[1]=='hidehotkey') $this->hidehotkey=$r[0];
		else if($r[1]=='night') $this->night=$r[0];
		else if($r[1]=='autonight') $this->autonight=$r[0];
		else if($r[1]=='sharecode') $this->sharecode=$r[0];
     }
    }
	}
}