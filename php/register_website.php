<?php
require_once 'db_connection.php';
require_once 'Website.php';

$url = $_GET['url'];
$db_connection = establish_db_connection();

$check_results = [
    'IDENTICAL' => 'do_nothing',
    'INEXISTENT' => 'insert_parent_website',
    'OLD' => 'update_content'
];

call_user_func(check_db_instance($url, $db_connection, $check_results), $url, $db_connection);

function do_nothing() {}

function insert_parent_website($url, $db_connection) {
    $website = new Website($url);
    $db_connection->insert('website', [
        'url' => $url,
        'title' => $website->get_title(),
        'html' => $website->get_html(),
        'description' => $website->get_description(),
        'last_modified' => $website->timestamp()
    ]);
}

function update_content($url, $db_connection) {
    $website = new Website($url);
    $db_connection->update('website', [
        'title' => $website->get_title(),
        'html' => $website->get_html(),
        'description' => $website->get_description(),
        'last_modified' => $website->timestamp()
    ], ['url' => $url]);
}

function check_db_instance($url, $db_connection, $check_results) {
    if($db_connection->has('website', ['url' => $url])) {
        $curl = curl_init($url);
        $headers = get_headers($url, 1);
        if(isset($headers['Last-Modified'])) {
            $db_timestamp = $db_connection->select('website', 
            ['last_modified'], ['url' => $url])[0]['last_modified'];
            
            if($db_timestamp == strtotime($headers['Last-Modified']))
                return $check_results['IDENTICAL'];
            else
                return $check_results['OLD'];
        } else {
            return $check_results['OLD'];
        }
        curl_close($curl);
    } else {
        return $check_results['INEXISTENT'];
    }
}

?>