<?php

require 'db.php';
require 'News.php';
$dbconnection= establishConnectionDB();


if ($dbconnection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search=$_GET['word'];
$sql= " SELECT * FROM news WHERE MATCH (description) AGAISNT (' ".$search."' IN BOOLEAN MODE);";
$result= $dbconnection->query($sql);
$array= array();

while($row = $result->fetch_assoc()) {
        array_push($array, new News($row["title"],$row["link"],$row["author"],$row["pub_date"],$row["description"]));
    }

$dbconnection->close();
return $array;
  ?>