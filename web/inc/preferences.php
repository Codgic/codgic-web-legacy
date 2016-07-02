<?php
class preferences{
	public $hidehotkey;
	public $autonight;
	public $night;
	public $sharecode;
	public $backuptime;

	function __construct()
	{
		$this->hidehotkey='off';
		$this->night='off';
		$this->autonight='on';
		$this->sharecode='off';
	}
}