<?php

class News{
    private $Title;
    private $TitleURL;
    private $Author;
    private $Date;
    private $Description;

    function __construct($Title,$TitleURL,$Author,$Date,$Description) {
        $this->Title = $Title;
        $this->TitleURL = $TitleURL;
        $this->Author = $Author;
        $this->Date = $Date;
        $this->Description = $Description;
    }

    function _getJSON(){
        return json_encode(
            array(
            'Title'=> $this->getTitle(),
            'TitleURL'=> $this->getTitleURL(),
            'Author'=> $this->getAuthor(),
            'Date'=> $this->getDate(),
            'Description'=>  $this->getDescription()
            )
        );
    }

    public function getTitle(){
        return $this->Title;
    }

    public function getTitleURL(){
        return $this->TitleURL;
    }

    public function getAuthor(){
        return $this->Author;
    }

    public function getDate(){
        return $this->Date;
    }

    public function getDescription(){
        return $this->Description;
    }


}


?>