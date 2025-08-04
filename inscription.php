<?php
session_start();

if (isset($_SESSION["logged_user"])) {
    header('Location: index.php');
    exit();
}

// Si on a bien crée un nouveau user, on redirige vers la page de connexion
$return = check_inscription();
$_SESSION["message"] = $return;

if (count($_POST) === 0)
    $_SESSION["message"] = NULL;


//echo $return;
if ($return === "Inscription reussie!") {
    //echo $return;
    $_SESSION["message"] = $return;
    header('Location: connexion.php');
    exit();
}

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

function check_password(): string
{
    if (!(isset($_POST["password"]) and isset($_POST["password-repeat"])))
        return "Assurez-vous de bien renseigner le password et bien le confirmer.";
    elseif ($_POST["password"] !== $_POST["password-repeat"])
        return "Password et sa confirmation ne sont par identinques. Veuillez les retaper!";
    else {
        preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $_POST["password"], $matches);
        //echo "MATCH<br />";
        //print_r($matches);
        if ($matches) {
            return "ok";
        } else
            return "Veuillez choisir un password de minimum 8 characteres, contenant au moins une majuscule, une minuscule, une chiffre et un caractere special";
    }
}


function security_checks()
{


    $_POST["login"] = htmlspecialchars(trim($_POST["login"]), ENT_QUOTES, "UTF-8");
    $_POST["nom"] = htmlspecialchars(trim($_POST["nom"]), ENT_QUOTES, "UTF-8");
    $_POST["prenom"] = htmlspecialchars(trim($_POST["prenom"]), ENT_QUOTES, "UTF-8");

    // TODO HASH PASSWORD
    $_POST["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
}

function check_inscription(): string //CAN ONLY BE CALLED ONCE DUE TO INCLUDE ERROR
{

    if (count($_POST) > 0) {
        //if ("connexion-tools.php") 
        //print_r(get_included_files());
        include "connexion-tools.php";
        $conn = new mysqli($server, $user, $password, $database);

        //print_r($_POST);

        if ($conn->connect_errno) {
            echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
            return "Inscriptioon echoué";
        }

        $ok = check_login($conn);

        if ($ok !== "ok")
            return $ok;

        //echo "??";
        $ok = check_password();

        if ($ok !== "ok")
            return $ok;
        //echo "??!";


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
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] === "admin")
                echo "<a href=\"admin.php\">Page administrative</a>";
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"index.php\">Page d'accueil</a> <a href=\"profil.php\">Modifier Profil</a> <form action=\"index.php\" method=\"post\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\"></form>";
            if (!isset($_SESSION["logged_user"])) echo "<a href=\"index.php\">Page d'accueil</a> <a href=\"connexion.php\">Connexion</a>"; ?>
        </nav>
    </header>
    <main>
        <form action="inscription.php" method="post">
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
            <input type="submit" value="Inscription">
        </form>
    </main>
</body>

</html>