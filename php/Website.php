<?php

class Website {
    private $url;
    private $title;
    private $html;
    private $description;
    private $headers;

    function __construct($url) {
        $this->url = $url;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $raw_html = curl_exec($curl);
        curl_close($curl);

        $this->headers = get_headers($url, 1);
        $this->title = $this->extract_title($raw_html);
        $this->description = $this->extract_description($raw_html);
        $this->html = $this->clean_html($raw_html);
    }

    function timestamp() {
        if(isset($this->headers['Last-Modified']))
            return strtotime($this->headers['Last-Modified']);
        return time();
    }

    function to_json() {
        return json_encode(
            array(
            'url'=> $this->get_url(),
            'title'=> $this->get_title(),
            'html'=> $this->get_html(),
            'description'=>  $this->get_description(),
            'lastModified'=> $this->get_last_modified()
            )
        );
    }

    private function extract_title($raw_html) {
        preg_match('/<title>(.*?)<\/title>/', $raw_html, $match);
        return $match[1];
    }

    private function extract_description($raw_html) {
        preg_match('/<meta.*?(name|property)="(og:)?description".*?\/>/',
            $raw_html, $meta_tag);
        preg_match('/content="([^"]+)"/', $meta_tag[0], $description);
        return $description[1];
    }

    private function clean_html($html) {
        // Remove scripts and styles
        $html = preg_replace('/<script.*?>.*?<\/script>/s', '', $html);
        $html = preg_replace('/<style.*?>.*?<\/style>/s', '', $html);

        // Remove new lines and tabs
        $html = preg_replace('/[\n\r\t]/s', '', $html);

        return strip_tags($html);
    }
    
    public function get_url() {
        return $this->url;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_html() {
        return $this->html;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_last_modified() {
        return $this->last_modified;
    }
}

?>