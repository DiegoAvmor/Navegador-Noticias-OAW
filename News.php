<?php

class News{
    public $Tittle;
    public $TittleURL;
    public $Author;
    public $Date;
    public $Description;

    function __construct($Tittle,$TittleURL,$Author,$Date,$Description) {
        $this->Tittle = $Tittle;
        $this->TittleURL = $TittleURL;
        $this->Author = $Author;
        $this->Date = $Date;
        $this->Description = $Description;
    }

    function _getJSON(){
        return json_encode($this);
    }


}


?>