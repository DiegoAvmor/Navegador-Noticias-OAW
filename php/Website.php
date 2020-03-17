<?php

class Website {
    private $url;
    private $title;
    private $dom_document;
    private $description;
    private $keywords;
    private $headers;

    public function __construct($url) {
        $this->url = $url;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $this->dom_document = new DomDocument;
        @$this->dom_document->loadHTML($this->minimize(curl_exec($curl)));

        curl_close($curl);

        $this->headers = get_headers($url, 1);
        $this->title = $this->dom_document->getElementsByTagName('title')[0]->nodeValue;

        $meta_tags = get_meta_tags($this->url);

        if(isset($meta_tags['description']))
            $this->description = $meta_tags['description'];
        else if(isset($meta_tags['og:description']))
            $this->description = $meta_tags['og:description'];
        
        if(isset($meta_tags['keywords']))
            $this->keywords = $meta_tags['keywords'];
    }

    public function timestamp() {
        if(isset($this->headers['Last-Modified']))
            return strtotime($this->headers['Last-Modified']);
        return time();
    }

    public function to_json() {
        return json_encode(
            array(
            'url'=> $this->get_url(),
            'title'=> $this->get_title(),
            'html'=> $this->body(),
            'description'=>  $this->get_description(),
            'keywords' => $this->get_keywords(),
            'timestamp'=> $this->timestamp()
            )
        );
    }

    public function extract_links() {
        $anchors = $this->dom_document->getElementsByTagName('a');
        foreach($anchors as $anchor)
            if(preg_match('/^https?:[\/]{2}/', $anchor->getAttribute('href')))  
                $links[] = $anchor->getAttribute('href');
        return $links;
    }

    public function extract_body() {
        return $this->dom_document->getElementsByTagName('body')[0]->nodeValue;
    }

    private function minimize($html) {
        // Remove new lines, tabs and more than two consecutive spaces
        return preg_replace('/[\n\r\t]|\s{2,}/s', '', $html);
    }
    
    public function get_url() {
        return $this->url;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_raw_html() {
        return $this->raw_html;
    }

    public function get_description() {
        return $this->description;
    }

    public function get_keywords() {
        return $this->keywords;
    }
}

?>