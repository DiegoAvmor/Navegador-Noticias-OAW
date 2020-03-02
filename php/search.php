<?php

require 'News.php';
include_once 'db.php';

// Create connection
$dbconnection= establishConnectionsqli();


if ($dbconnection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search=$_GET['word'];
$sql= " SELECT * FROM `news` WHERE MATCH (description) AGAINST ('".$search."')";
$result= $dbconnection->query($sql) or die($dbconnection->error);
$array= array();

while($row = $result->fetch_assoc()) {
		
		$newsObject = new News($row["title"],$row["link"],$row["author"],$row["pub_date"],$row["description"]);
		array_push($array,$newsObject->_getJSON());       
    }

echo json_encode($array);
$dbconnection->close();

  ?>