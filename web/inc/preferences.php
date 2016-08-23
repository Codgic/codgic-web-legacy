<?php
class preferences{
	public $hidehotkey;
	public $night;
	public $edrmode;
    public $i18n;
	public $backuptime;

	function __construct()
	{
		$this->hidehotkey='off';
		$this->night='auto';
		$this->sharecode='off';
		$this->edrmode='default';
        $this->i18n='auto';
	}
}