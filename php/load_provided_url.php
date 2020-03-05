<?php
include_once 'feed_util.php';

$feed = new SimplePie();
$feed->set_cache_location('../cache');

$url = $_GET['url'];
setURL($url);
$response = _getRSSFrom($url, $feed);
echo $response;
?>