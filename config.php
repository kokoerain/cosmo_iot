<?php
// PostgreSQL connection settings
$host = "localhost";
$port = "5432";
$dbname = "iotc";
$user = "postgres";
$password = "root";

// Create connection
$conn = pg_connect("host=localhost port=5432 dbname=iotc user=postgres password=root");
if (!$conn) {
    die("Database connection failed.");
}
?>