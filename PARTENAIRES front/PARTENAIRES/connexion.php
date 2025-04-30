<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "lets_link"; // ⚠️ sans apostrophe dans le nom de la base

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
