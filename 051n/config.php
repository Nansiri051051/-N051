<?php

$host = "localhost";
$dbname = "db6646_051";
$username = "root"; 
$password = "";
$charset = 'utf8mb4';
$tablename = "tb_664230051";

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
PDO::ATTR_ERRMODE=> PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

try {
$conn = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
die("Connection failed: " . $e->getMessage());
}
?>