<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "read-right-hands-vanilla";

try {

    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM ranks");
    $stmt->execute();

    $rows = $stmt->fetchAll();

    return $rows;

} catch(PDOException $e) {
    echo "<p>" . $sql . "<br>" . $e->getMessage() . "</p>";

}
$conn = null;


