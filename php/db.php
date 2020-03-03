<?php
use Medoo\Medoo;
require '../lib/Medoo.php'; //https://medoo.in/doc


function establishConnectionDB(){
    $jsonStr = file_get_contents("../resources/dbconfig.json");
    $config = json_decode($jsonStr);
    $connection = new Medoo(
        [
        'database_type' => $config->database->type,
        'database_name' => $config->database->dbname,
        'server' => $config->database->server,
        'username' => $config->database->username,
        'password' => $config->database->password
        ]
    );
    return $connection;
}

function establishConnectionsqli(){
    $jsonStr = file_get_contents("../resources/dbconfig.json");
    $config = json_decode($jsonStr);
    $servername = $config->database->server;
    $username = $config->database->username;
    $password = $config->database->password;
    $database= $config->database->dbname;

    $conn= new mysqli($servername, $username, $password, $database);
    return $conn;
}




//Example
$database = establishConnectionDB();
$database->insert(
    "news_table",
    [
        'title'=> "some title",
        'url'=> "some url",
        'author'=> "some author",
        'date'=> "2020-02-21",
        'description'=> "some description"
    ]
);





?>