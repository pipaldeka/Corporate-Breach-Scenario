<?php
session_start();

$db_host = 'localhost';
$db_user = 'webapp_user';
$db_pass = 'myPass123';
$db_name = 'corporate_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    error_log("DB Error: " . $conn->connect_error);
    http_response_code(500);
    die("Database unavailable");
}
?>
