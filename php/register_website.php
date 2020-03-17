<?php

require_once 'db_connection.php';
require_once 'Website.php';

$url = $_GET['url'];
$db_connection = establish_db_connection();

switch(check_db_instance($url, $db_connection)) {
    case 'INEXISTENT':
        insert_parent_website($url, $db_connection);
    break;
    case 'OLD':
        update_content($url, $db_connection);
    break;
}

function insert_parent_website($url, $db_connection) {
    $website = new Website($url);
    $db_connection->insert('website', [
        'url' => $url,
        'title' => $website->get_title(),
        'html' => $website->body(),
        'description' => $website->get_description(),
        'keywords' => $website->get_keywords(),
        'last_modified' => $website->timestamp()
    ]);
}

function update_content($url, $db_connection) {
    $website = new Website($url);
    $db_connection->update('website', [
        'title' => $website->get_title(),
        'html' => $website->clean_html(),
        'description' => $website->get_description(),
        'last_modified' => $website->timestamp()
    ], ['url' => $url]);
}

function check_db_instance($url, $db_connection) {
    if($db_connection->has('website', ['url' => $url])) {
        $curl = curl_init($url);
        $headers = get_headers($url, 1);
        if(isset($headers['Last-Modified'])) {
            $db_timestamp = $db_connection->select('website', 
            ['last_modified'], ['url' => $url])[0]['last_modified'];
            
            if($db_timestamp == strtotime($headers['Last-Modified']))
                return 'IDENTICAL';
            else
                return 'OLD';
        } else {
            return 'OLD';
        }
        curl_close($curl);
    } else {
        return 'INEXISTENT';
    }
}

?>