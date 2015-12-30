<?php
class preferences{
	//public $hidelogo;
	public $hidehotkey;
	public $autonight;
	public $night;
	public $sharecode;
	public $backuptime;

	function __construct()
	{
	//	$this->hidelogo='on';
		$this->hidehotkey='off';
		$this->night='off';
		$this->autonight='on';
		$this->sharecode='off';
	}
}