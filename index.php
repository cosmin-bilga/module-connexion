<?php
session_start();

if (isset($_POST["Deconnexion"])) {
    print_r($_POST);
    session_unset();
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page accueil</title>
    <link rel="stylesheet" href="assets/css/main_page.css">
</head>

<body>
    <header>
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] === "admin")
                echo "<a href=\"admin.php\">Page administrative</a>";
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"profil.php\">Modifier Profil</a> <form action=\"index.php\" method=\"post\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\"></form>";
            if (!isset($_SESSION["logged_user"])) echo "<a href=\"connexion.php\">Connexion</a> <a href=\"inscription.php\">Inscription</a>"; ?>
        </nav>
    </header>
    <main>
        <h1>Contenu de la page principale</h1>
    </main>
</body>

</html>