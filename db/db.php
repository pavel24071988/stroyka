<?php
/*
$host='localhost';
$db = 'stroyka';
$username = 'postgres';
$password = '';
*/

$host='pg.sweb.ru';
$db = 'pavel24071';
$username = 'pavel24071';
$password = 'build2016';

$connect = "pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";

try{
    // create a PostgreSQL database connection
    $DB = new PDO($connect);
}catch (PDOException $e){
    // report error message
    echo $e->getMessage();
    exit;
}
