<?php

require_once 'db_connection.php';
require_once 'Website.php';

// Get provided url and establish DB connection
$url = $_GET['url'];
$db_connection = establish_db_connection();

// Check the validity of the url against DB
switch(check_db_instance($url, $db_connection)) {
    case 'INEXISTENT':
        register_new_website($url, $db_connection);
    break;
    case 'OLD':
        update_old_website($url, $db_connection);
    break;
}

// Add corresponding $url website, along with its children, to `website`
// table. Furthermore, register the children in the `reference` table.
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
    try{
        insert_website(new Website($url_child), $db_connection);

        $website_id_parent = $db_connection->select('website',['website_id'],['url' => $url_parent]);
        $website_id_child = $db_connection->select('website',['website_id'],['url' => $url_child]);
        
        if(isset($website_id_parent[0]['website_id']) && isset($website_id_child[0]['website_id']))
            $db_connection->insert('reference', [
                'website_id_parent' => $website_id_parent[0]['website_id'],
                'website_id_child' => $website_id_child[0]['website_id']
            ]);
    }catch( Exception $e ) {
        //Todo handle exception
    }
}

// Update $url website in DB. Furthermore, if a child url is not registered
// in the DB, add it to `website` table and register it in `reference`; if 
// it already exists and is OLD, only update its contents in `website`.
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

/* A url is categorized in one of three categories:
    - IDENTICAL: The timestamp (`last-modified`) of the website
      in the `website` table is equal to the timestamp generated by
      the Last-Modified header. Simply put, the Last-Modified of both
      the DB and the now fetched resource are the same.
    - INEXISTENT: The url does not exist in the `url` column of the
      `website` table.
    - OLD: The website does not provide a Last-Modified header OR
      the timestamp in the DB entry does not match the timestamp
      provided by the current headers fetch.
    
    Required actions per category:
    - If the url is IDENTICAL, no action is required.
    - If the url is INEXISTENT, insertion is required of parent and
      all children websites.
    - If the url is OLD, update of parent website and insertion/update
     of children websites is required.
*/
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