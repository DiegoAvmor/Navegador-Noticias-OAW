<?php
require_once './simplepie-1.5/autoloader.php';
include_once 'News.php';

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
        $description = $item->get_description();
        $newsObject = new News($title,$link,$author,$date,$description);
        array_push($list,$newsObject->_getJSON());
    }
    echo json_encode($list);
}

function getURL(){
    if(file_exists('../resources/savedURL.txt')){
        $fileContent  = file_get_contents("../resources/savedURL.txt");
        return $fileContent;
    }
}

function setURL($url){
    file_put_contents('../resources/savedURL.txt',$url);
}
?>