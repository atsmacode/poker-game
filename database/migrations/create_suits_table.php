<?php

require('../../config/db.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $sql = "CREATE TABLE suits (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL,
        abbreviation VARCHAR(30) NOT NULL
    )";
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "<p>" . "Suits table created successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;
