<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "moduleconnexion";

$verbose = true;

function sql_exec(string $sql, object $conn)
{
    // Faire en sorte qu'on recupere l'erreur pour le try/catch
    //mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);

    try {
        $result = $conn->query($sql);
        //echo "Requete $sql crée avec succes!";
    } catch (mysqli_sql_exception $e) {
        echo 'Erreur: ' . $conn->error;
    }
    //echo "<br />";
    return $result;
}
