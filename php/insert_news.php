<?php
include_once 'feed_util.php';
include_once 'db.php';

/*
Sequence order:
select from db
filter by name
ignore repeated news
insert remaining into db
*/

$connection = establishConnectionDB();
$feed = new SimplePie();
$feed->set_cache_location('../cache');
$url = $_POST['url'];

//First we get the titles of the db
$titles =  $connection->select("news", "title");

//Then we get the contents of the rss
$feedNews = _getRSSFrom($url, $feed);
$jsonArray = json_decode($feedNews);

//then we compare the titles with the ones in the db
$count = sizeof($jsonArray);
for ($i=0; $i < $count; $i++) { 
    $jsonElement = json_decode($jsonArray[i]);
    foreach ($titles as $title) {
        if(strcmp($jsonElement->Title, $title) != 0){
            unset($jsonArray[i]);
        }
    }
}
//Finally we insert them into the db
foreach ($jsonArray as $jsonElement) {
    $new = json_decode($jsonElement);
    $connection->insert(
        "news",
        [
            'title'=> $new->Title,
            'link'=> $new->TitleURL,
            'author'=> $new->Author,
            'pub_date'=> $new->Date,
            'description'=> $new->Description,
        ]
    );
}

?>