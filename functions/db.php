<?php

$host='localhost';
$db = 'stroyka';
$username = 'postgres';
$password = '';

$connect = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";

try{
    // create a PostgreSQL database connection
    $DB = new PDO($connect);
}catch (PDOException $e){
    // report error message
    echo $e->getMessage();
    exit;
}