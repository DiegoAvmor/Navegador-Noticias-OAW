<?php
include_once 'feed_util.php';

$feed = new SimplePie();
$feed->set_cache_location('../cache');

$url = getURL();
_getRSSFrom($url, $feed);
?>