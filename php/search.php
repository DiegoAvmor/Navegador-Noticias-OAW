<?php

require 'Website.php';
include_once 'db.php';

// Create connection
$dbconnection= establishConnectionDB();

$search=$_GET['word'];
$sql= " SELECT * FROM `web_scrapping` WHERE MATCH (raw) AGAINST ('".$search."')";

$result= $dbconnection->query($sql)->fetchAll();
$array= array();

foreach ($result as $row ) {
	$newsObject = new Website($row["title"],$row["url"],$row["raw"],$row["last_modified"],$row["keywords"]);
		array_push($array,$newsObject->_getJSON());    
}

echo json_encode($array);
?>