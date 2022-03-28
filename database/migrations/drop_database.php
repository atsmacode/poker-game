<?php

require('../../config/db.php');

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "<p class='bg-primary rounded'>" . "Database dropped successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p class='bg-danger rounded'>" . $sql . "<br>" . $e->getMessage() . "</p>";
}
$conn = null;
