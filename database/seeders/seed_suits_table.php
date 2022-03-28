<?php

require('../../config/db.php');

$suits = require('../../config/suits.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO suits (name, abbreviation) VALUES (:name, :abbreviation)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':abbreviation', $abbreviation);

    foreach($suits as $suit) {
        $name = $suit['name'];
        $abbreviation = $suit['abbreviation'];
        $stmt->execute();
    }
    echo "<p>" . "Suits seeded successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;


