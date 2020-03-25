<?php

require 'Website.php';
include_once 'db_connection.php';

// Create connection
$dbconnection= establish_db_connection();

$search=$_GET['word'];
$sql= "SELECT * FROM website WHERE MATCH ( title, description, body, keywords ) AGAINST " . "('". $search . "')";
$result= $dbconnection->query($sql)->fetchAll();
$array= array();

foreach ($result as $row ) {
	$rowArray = array(
		"title" => $row["title"],
		"url" => $row["url"],
		"description" => $row["description"],
		"keywords" => isset($row["keywords"])?$row["keywords"]:"No keywords found",
		"last_modified" => date("Y-m-d H:i:s", $row["last_modified"])
	);
	array_push($array,json_encode($rowArray));
}

echo json_encode($array);
?>