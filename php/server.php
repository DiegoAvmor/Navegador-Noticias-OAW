<?php
require_once './simplepie-1.5/autoloader.php';
include ('News.php');

$action = $_GET['action'];
$feed = new SimplePie();
$feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/Navegador-Noticias-OAW/cache');

switch ($action) {
    case 'retrieved':
        $url = getURL();
        _getRSSFrom($url,$feed);
        break;
    
    case 'search':
        $url = $_GET['url'];
        setURL($url);
        _getRSSFrom($url,$feed);
        break;
}


function _getRSSFrom($url,$feed){

    $feed->set_feed_url($url);
    $feed->init();

    $list = array();
    $itemQry = $feed->get_item_quantity();
    
    for ($i = 0; $i < $itemQry; $i++) {
   
        $item = $feed->get_item($i);
        $title = $item->get_title();
        $link = $item->get_link();
        $author = ($item->get_author()->get_name()!==null)?$item->get_author()->get_name():"No Author found";
        $date = $item->get_date('Y-m-d');
        $descript = $item->get_description();
        $newsObject = new News($title,$link,$author,$date,$descript);
        array_push($list,$newsObject->_getJSON());
    }
    echo json_encode($list);
}

function getURL(){
    if(file_exists("../resources/savedURL.txt")){
        $fileContent  = file_get_contents("../resources/savedURL.txt");
        return $fileContent;
    }
}

function setURL($url){
    file_put_contents("../resources/savedURL.txt",$url);
}


?>