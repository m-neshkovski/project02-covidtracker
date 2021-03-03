<?php

// require_once('');
 
// /* Attempt to connect to MySQL database */
$db_type = DB_TYPE;
$servername=DB_SERVER;
$db_name=DB_NAME;
$username=DB_USERNAME;
$password=DB_PASSWORD;
$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // po default e PDO::FETCH_BOTH
];

try { // dsn Data Source name
    $pdo = new PDO("$db_type:host=$servername;dbname=$db_name", $username, $password, $options); // proba dali bazata e konektirana
} catch (PDOException $e) {
    echo $e->getMessage();
    die;
}
?>