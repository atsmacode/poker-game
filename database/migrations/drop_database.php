<?php

require('../../config/db.php');

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $sql = "DROP DATABASE IF EXISTS `read-right-hands-vanilla`";
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "<p>" . "Database dropped successfully" . "</p>";
} catch(PDOException $e) {
    /*
     * Do nothing as DB may not exist yet.
     * Handle this elegantly like Laravel for fresh install
     */
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;
