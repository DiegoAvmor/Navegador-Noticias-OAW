<?php

require_once 'db_connection.php';
require_once 'Website.php';

$url = $_GET['url'];
$db_connection = establish_db_connection();

switch(check_db_instance($url, $db_connection)) {
    case 'INEXISTENT':
        register_new_website($url, $db_connection);
    break;
    case 'OLD':
        update_old_website($url, $db_connection);
    break;
}

function register_new_website($url, $db_connection) {
    $website = new Website($url);
    insert_website($website, $db_connection);
    
    $links = $website->extract_links();
    foreach($links as $link)
        insert_referenced_website($website->get_url(), $link, $db_connection);
}

function insert_website($website, $db_connection) {
    $db_connection->insert('website', [
        'url' => $website->get_url(),
        'title' => $website->get_title(),
        'body' => $website->extract_body(),
        'description' => $website->get_description(),
        'keywords' => $website->get_keywords(),
        'last_modified' => $website->timestamp()
    ]);
}

function insert_referenced_website($url_parent, $url_child, $db_connection) {
    insert_website(new Website($url_child), $db_connection);

    $website_id_parent = $db_connection->select('website',['website_id'],['url' => $url_parent]);
    $website_id_child = $db_connection->select('website',['website_id'],['url' => $url_child]);
    
    if(isset($website_id_parent[0]['website_id']) && isset($website_id_child[0]['website_id']))
        $db_connection->insert('reference', [
            'website_id_parent' => $website_id_parent[0]['website_id'],
            'website_id_child' => $website_id_child[0]['website_id']
        ]);
}

function update_old_website($url, $db_connection) {
    $website = new Website($url);
    update_website($website, $db_connection);
    
    $links = $website->extract_links();
    foreach($links as $link)
        switch(check_db_instance($link, $db_connection)) {
            case 'INEXISTENT':
                insert_referenced_website($url, $link, $db_connection);
            break;
            case 'OLD':
                update_website($link, $db_connection);
            break;
        }
}

function update_website($website, $db_connection) {
    $db_connection->update('website', [
        'title' => $website->get_title(),
        'body' => $website->extract_body(),
        'description' => $website->get_description(),
        'last_modified' => $website->timestamp()
    ], ['url' => $website->get_url()]);
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