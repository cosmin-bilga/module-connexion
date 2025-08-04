<?php
session_start();

if (!(isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] == "admin")) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Administrative</title>
    <link rel="stylesheet" href="assets/css/main_page.css">
</head>

<body>

    <?php
    include "connexion-tools.php";
    $conn = new mysqli($server, $user, $password, $database);

    if ($conn->connect_errno) {
        echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
        exit();
    }

    $sql = "SELECT * FROM utilisateurs;";
    $result = sql_exec($sql, $conn);
    if (!isset($result)) {
        echo "Erreur de connexion à la DB";
        exit();
    }

    ?>
    <header>
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"index.php\">Page d'accueil</a> <a href=\"profil.php\">Modifier Profil</a> <form action=\"index.php\" method=\"post\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\"></form>";
            else echo "<a href=\"index.php\">Page d'accueil</a> <a href=\"connexion.php\">Connexion</a> <a href=\"inscription.php\">Inscription</a>"; ?>
        </nav>
    </header>
    <main>
        <h1>Page administrative</h1>
        <h3>Données dans le tableau "utilisateurs"</h3>
        <table>
            <thead>
                <tr>
                    <?php
                    $row = $result->fetch_assoc();
                    foreach ($row as $key => $value)
                        echo "<th>" . ucfirst($key) . "</th>";
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row) {
                    echo "<tr>";
                    foreach ($row as $cell)
                        echo "<td>$cell</td>";
                    echo "</tr>";
                    $row = $result->fetch_assoc();
                    //print_r($row);
                }
                $conn->close();
                ?>
            </tbody>
    </main>
</body>

</html>