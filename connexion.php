<?php
session_start();

$_SESSION["error"] = NULL;

if (isset($_SESSION["logged_user"])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST["login"]))
    if (isset($_POST["password"])) {
        $ok = connexion_utilisateur();
        if ($ok === "Login ok") {
            $_SESSION["logged_user"] = $_POST["login"];
            header('Location: index.php');
            exit();
        } else
            $_SESSION["error"] = $ok;
    } else
        $_SESSION["error"] = "Veuillez introduire votre password";
//echo "Veuillez introduire votre password";

function connexion_utilisateur()
{
    include "connexion-tools.php";
    $conn = new mysqli($server, $user, $password, $database);
    if ($conn->connect_errno) {
        echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
        return;
    }

    $sql = "SELECT password FROM utilisateurs WHERE login='" . $_POST["login"] . "';";
    /* $sql = "INSERT INTO utilisateurs(login,nom,prenom,password) VALUES('" . $_POST["login"] . "','" .
        $_POST["nom"] . "','" .
        $_POST["prenom"] . "','" .
        $_POST["password"] . "');"; */
    $result = sql_exec($sql, $conn);
    $pass = $result->fetch_assoc();
    $conn->close();

    //echo " --- " . password_verify($pass["password"], $_POST["password"]);
    if (isset($pass["password"]) and (password_verify($_POST["password"], $pass["password"])))
        return "Login ok";
    return "Password incorrect";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/main_page.css">
</head>

<body>
    <header>
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] === "admin")
                echo "<a href=\"admin.php\">Page administrative</a>";
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"index.php\">Page d'accueil</a> <a href=\"profil.php\">Modifier Profil</a> <form action=\"index.php\" method=\"post\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\"></form>";
            if (!isset($_SESSION["logged_user"])) echo "<a href=\"index.php\">Page d'accueil</a> <a href=\"inscription.php\">Inscription</a>"; ?>
        </nav>
    </header>
    <main>
        <h1>PAGE CONNEXION</h1>
        <form action="connexion.php" method="post">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" <?php if (isset($_POST["login"])) echo "value=\"" . $_POST["login"] . "\"" ?>>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <?php if (isset($_SESSION["error"])) echo "<p class=\"input-error\">" . $_SESSION["error"] . "</p>"; ?>
            <?php if (isset($_SESSION["message"])) echo "<p class=\"input-message\">" . $_SESSION["message"] . "</p>"; ?>
            <input type="submit" value="Connexion">
        </form>
    </main>
</body>

</html>