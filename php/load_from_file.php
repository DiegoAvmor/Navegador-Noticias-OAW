<?php
include_once 'feed_util.php';

$feed = new SimplePie();
$feed->set_cache_location('../cache');

$url = getURL();
$response = _getRSSFrom($url, $feed);
echo $response;
?>