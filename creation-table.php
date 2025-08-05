<?php
// Page PHP qui permet l'initialisation de la base de données si elle n'existe pas et de la remplir


// Fonction pour executer les requetes SQL


include "connection-tools.php";

$conn = new mysqli($server, $user, $password);

// On verifie la connexion
if ($conn->connect_errno) {
    exit("Connexion échoué: " . $conn->connect_error);
}

// Requete SQL pour la création de la DB
$sql = "CREATE DATABASE $database DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
sql_exec($sql, $conn);
$conn->close();

// On se connecte à la DB crée
$conn = new mysqli($server, $user, $password, $database);

// Requete SQL pour la création du table utilisateurs
$sql = "CREATE TABLE utilisateurs(
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(255) NOT NULL COLLATE latin1_bin,
        prenom VARCHAR(255), nom VARCHAR(255),
        password VARCHAR(255) NOT NULL,
        UNIQUE (login)); ";
sql_exec($sql, $conn);

// Ajouter à la BD le compte admin
$sql = "INSERT INTO utilisateurs(login, prenom, nom, password) VALUES ('admin','admin','admin','$2y$10\$Q2B0qo2PsAO7yMukJ.UnzeXls2iCaK3D74zctHlz9hiyHcChdk/By');";
sql_exec($sql, $conn);

$conn->close();
