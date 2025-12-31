<?php
$host = "localhost";
$dbname = "tache";
$user = "root";
$pass = "";

try {
    //chaine de connection $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    //PDO =clees et $pdo est un objet
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// c est pous qu on nous affiche les erreur
} 
catch (PDOException $e) {// capter l execption
    die("Erreur connexion : " . $e->getMessage());//affiche le message d eurreur et arrete l appli
}
?>
