<?php
session_start();


// Si logged on redirect vers la page d'accueil
if (isset($_SESSION["logged_user"])) {
    header('Location: index.php');
    exit();
}

// Si on a bien crée un nouveau user, on redirige vers la page de connexion
$return = check_inscription();
$_SESSION["message"] = $return;

if (count($_POST) === 0)
    $_SESSION["message"] = NULL;

if ($return === "Inscription reussie!") {
    $_SESSION["message"] = $return;
    header('Location: connexion.php');
    exit();
}


// Fonction qui check qui login donné correspond aux demandes et n'exista pas deja
function check_login($conn): string
{
    if (!isset($_POST["login"]))
        return "Veuillez choisir un login";
    else {
        preg_match('/^[a-zA-Z0-9]{5,}$/', $_POST["login"], $matches);
        //echo "MATCH<br />";
        //print_r($matches);
        if (!$matches)
            return "Veuillez choisir un login de 5 characteres ou plus, formé de lettre et chiffres uniquement";
        $sql = "SELECT * FROM utilisateurs WHERE login='" . htmlentities($_POST["login"]) . "';";

        $result = sql_exec($sql, $conn);
        if ($result->fetch_assoc())
            return "Login deja existant";
        return "ok";
    }
}

// Check que le password est valide et qu'on l'a bien repete
function check_password(): string
{
    if (!(isset($_POST["password"]) and isset($_POST["password-repeat"])))
        return "Assurez-vous de bien renseigner le password et bien le confirmer.";
    elseif ($_POST["password"] !== $_POST["password-repeat"])
        return "Password et sa confirmation ne sont par identinques. Veuillez les retaper!";
    else {
        preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-+=()[\]{}]).{8,}$/", $_POST["password"], $matches);
        if ($matches) {
            return "ok";
        } else
            return "Veuillez choisir un password de minimum 8 characteres, contenant au moins une majuscule, une minuscule, une chiffre et un caractere special";
    }
}


// Protection contre les injections + password hash
function security_checks()
{


    $_POST["login"] = htmlspecialchars(trim($_POST["login"]), ENT_QUOTES, "UTF-8");
    $_POST["nom"] = htmlspecialchars(trim($_POST["nom"]), ENT_QUOTES, "UTF-8");
    $_POST["prenom"] = htmlspecialchars(trim($_POST["prenom"]), ENT_QUOTES, "UTF-8");
    $_POST["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
}

// Fonction qui verifie les donnée passées dans le form et insere les données dans la DB si tout ok
function check_inscription(): string
{

    if (count($_POST) > 0) {
        include "connexion-tools.php";
        $conn = new mysqli($server, $user, $password, $database);

        if ($conn->connect_errno) {
            echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
            return "Inscriptioon echoué";
        }

        $ok = check_login($conn);
        if ($ok !== "ok")
            return $ok;

        $ok = check_password();
        if ($ok !== "ok")
            return $ok;

        security_checks();

        $sql = "INSERT INTO utilisateurs(login,nom,prenom,password) VALUES('" . $_POST["login"] . "','" .
            $_POST["nom"] . "','" .
            $_POST["prenom"] . "','" .
            $_POST["password"] . "');";
        sql_exec($sql, $conn);
        $conn->close();

        return "Inscription reussie!";
    }

    return "Formulaire d'inscription non-rempli";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/main_page.css">
</head>

<body>
    <header>
        <img src="assets/media/squirrel.webp">
        <nav>
            <?php
            if (!isset($_SESSION["logged_user"])) echo "<a href=\"index.php\"><img src=\"assets/media/home-svgrepo-com.svg\">Page d'accueil</a> <a href=\"connexion.php\"><img src=\"assets/media/login.svg\">Connexion</a>"; ?>
        </nav>
    </header>
    <main>
        <form action="inscription.php" method="post">
            <p>Inscription</p>
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" <?php if (isset($_POST["login"])) echo "value=\"" . $_POST["login"] . "\"" ?>>
            <label for="nom">Nom:</label>
            <input type="text" name="nom" id="nom" <?php if (isset($_POST["nom"])) echo "value=\"" . $_POST["nom"] . "\"" ?>>
            <label for="prenom">Prenom:</label>
            <input type="text" name="prenom" id="prenom" <?php if (isset($_POST["prenom"])) echo "value=\"" . $_POST["prenom"] . "\"" ?>>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <label for="password-repeat">Répetez le password:</label>
            <input type="password" name="password-repeat" id="password-repeat">
            <?php if (isset($_SESSION["message"])) echo "<p class=\"input-message\">" . $_SESSION["message"] . "</p>";
            $_SESSION["message"] = NULL; ?>
            <input type="submit" value="Inscription" class="main-form">
        </form>
    </main>
    <footer>
        <p>2025 - Cosmin Bilga</p>
    </footer>
</body>

</html>