<?php

require 'News.php';
include_once 'db.php';

// Create connection
$dbconnection= establishConnectionDB();

$search=$_GET['word'];
$sql= " SELECT * FROM `news` WHERE MATCH (description) AGAINST ('".$search."')";

$result= $dbconnection->query($sql)->fetchAll();
$array= array();

foreach ($result as $row ) {
	$newsObject = new News($row["title"],$row["link"],$row["author"],$row["pub_date"],$row["description"]);
		array_push($array,$newsObject->_getJSON());    
}

echo json_encode($array);

  ?>