<?php

use Medoo\Medoo; // Specify namespace of class
require_once '../lib/Medoo.php'; // Include file

// Returns a Medoo instance, used for querying the specified database
function establish_db_connection() {
    $config = json_decode(file_get_contents('../resources/db_config.json'), true);
    return new Medoo([
        'database_type' => $config['database_type'],
        'database_name' => $config['database_name'],
        'server' => $config['server'],
        'username' => $config['username'],
        'password' => $config['password'],
    ]);
}

?>