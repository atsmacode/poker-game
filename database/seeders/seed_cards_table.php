<?php

require('../../config/db.php');

$ranks = require('../helpers/select_ranks.php');
$suits = require('../helpers/select_suits.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO cards (rank_id, suit_id) VALUES (:rank_id, :suit_id)");
    $stmt->bindParam(':rank_id', $rank_id);
    $stmt->bindParam(':suit_id', $suit_id);

    foreach($suits as $suit){
        foreach($ranks as $rank){
            $rank_id = $rank['id'];
            $suit_id = $suit['id'];
            $stmt->execute();
        }
    }


    echo "<p class='bg-primary rounded'>" . "Cards seeded successfully" . "</p>";
} catch(PDOException $e) {
    echo "<p class='bg-danger rounded'>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;


