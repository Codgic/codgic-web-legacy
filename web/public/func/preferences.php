<?php
class preferences{
    public $hidehotkey;
    public $night;
    public $edrmode;
    public $backuptime;

    function __construct()
    {
        $this->hidehotkey='off';
        $this->night='auto';
        $this->sharecode='off';
        $this->edrmode='default';
    }
}