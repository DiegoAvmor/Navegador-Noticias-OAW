<?php

class Website {
    private $url;
    private $title;
    private $body;
    private $description;
    private $last_modified;

    function __construct($url, $title, $body, $description, $last_modified) {
        $this->url = $url;
        $this->title = $title;
        $this->body = $body;
        $this->description = $description;
        $this->last_modified = $last_modified;
    }

    function to_json() {
        return json_encode(
            array(
            'url'=> $this->get_url(),
            'title'=> $this->get_title(),
            'body'=> $this->get_body(),
            'description'=>  $this->get_description(),
            'lastModified'=> $this->get_last_modified()
            )
        );
    }
    
    public function get_url() {
        return $this->url;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_body() {
        return $this->body;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_last_modified() {
        return $this->last_modified;
    }
}

?>