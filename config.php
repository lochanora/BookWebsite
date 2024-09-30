

<?php

// Your existing code starts here
$servername = "localhost";
$username = "root";
$password = "root"; 
$dbname = "priceless_pages";
$socket = '/tmp/mysql.sock';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, null, $socket);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Additional code for your application
?>