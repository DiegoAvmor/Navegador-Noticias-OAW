<?php
class Website {
    private $title;
    private $url;
    private $raw;
    private $last_modified;
    private $keywords;

    function __construct($title, $url, $raw, $last_modified, $keywords) {
        $this->title = $title;
        $this->url = $url;
        $this->raw = $raw;
        $this->last_modified = $last_modified;
        $this->keywords = $keywords;
    }

    function _getJSON() {
        return json_encode(
            array(
            'title'=> $this->get_title(),
            'url'=> $this->get_url(),
            'raw'=> $this->get_raw(),
            'lastModified'=> $this->get_last_modified(),
            'keywords'=>  $this->get_keywords()
            )
        );
    }

    public function get_title() {
        return $this->title;
    }

    public function get_url() {
        return $this->url;
    }

    public function get_raw() {
        return $this->raw;
    }

    public function get_last_modified() {
        return $this->last_modified;
    }

    public function get_keywords() {
        return $this->keywords;
    }
}
?>