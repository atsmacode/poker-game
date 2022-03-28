<?php

require('../../config/db.php');

$ranks = require('../../config/ranks.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO ranks (name, abbreviation, ranking) VALUES (:name, :abbreviation, :ranking)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':abbreviation', $abbreviation);
    $stmt->bindParam(':ranking', $ranking);

    foreach($ranks as $rank) {
        $name = $rank['name'];
        $abbreviation = $rank['abbreviation'];
        $ranking = $rank['ranking'];
        $stmt->execute();
    }
    echo "<p>" . "Suits seeded successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;


