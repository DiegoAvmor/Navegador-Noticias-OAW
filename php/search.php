<?php

require 'Website.php';
include_once 'db.php';

// Create connection
$dbconnection= establishConnectionDB();

$search=$_GET['word'];
$sql= " SELECT * FROM `website` WHERE MATCH (description, title, body) AGAINST ('".$search."')";

$result= $dbconnection->query($sql)->fetchAll();
$array= array();

foreach ($result as $row ) {
	$newsObject = new Website($row["title"],$row["url"],$row["body"],$row["last_modified"],$row["description"]);
		array_push($array,$newsObject->to_json());
}

echo json_encode($array);
?>