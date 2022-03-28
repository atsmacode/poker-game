<?php

require('../../config/db.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $sql = "CREATE TABLE cards (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        rank_id INT(6) UNSIGNED,
        suit_id INT(6) UNSIGNED,
        FOREIGN KEY (rank_id) REFERENCES ranks(id),
        FOREIGN KEY (suit_id) REFERENCES suits(id)
    )";
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "<p class='bg-primary rounded'>" . "Cards table created successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p class='bg-danger rounded'>" . $sql . "<br>" . $e->getMessage() . "</p>";
}
$conn = null;
