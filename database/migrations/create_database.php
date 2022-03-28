<?php

require('../../config/db.php');

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $sql = "CREATE DATABASE `read-right-hands-vanilla`";
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "<p>" . "Database created successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;
?>