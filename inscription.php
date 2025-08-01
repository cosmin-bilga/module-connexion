<?php
session_start();

echo check_inscription();

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
    echo "hhh" . (!isset($_POST["password"]) or !isset($_POST["password-repeat"])) and ($_POST["password"] !== $_POST["password-repeat"]);
    if ((!isset($_POST["password"]) or !isset($_POST["password-repeat"])) and ($_POST["password"] !== $_POST["password-repeat"]))
        return "Assurez-vous de bien renseigner le password et bien le confirmer.";
    else {
        preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/", $_POST["password"], $matches);
        //echo "MATCH<br />";
        //print_r($matches);
        if ($matches) {
            return "ok";
        } else
            return "Veuillez choisir un password de minimul 8 characteres, contenant au moins une majuscule, une minuscule, une chiffre et un caractere special";
    }
}


function security_checks()
{


    $_POST["login"] = htmlspecialchars(trim($_POST["login"]), ENT_QUOTES, "UTF-8");
    $_POST["nom"] = htmlspecialchars(trim($_POST["nom"]), ENT_QUOTES, "UTF-8");
    $_POST["prenom"] = htmlspecialchars(trim($_POST["prenom"]), ENT_QUOTES, "UTF-8");

    // TODO HASH PASSWORD
    //$_POST["password"] = 
}

function check_inscription(): string
{

    if (count($_POST) > 0) {
        include "connexion-tools.php";
        $conn = new mysqli($server, $user, $password, $database);

        print_r($_POST);

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
    }

    return "Inscription reussie!";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>

<body>
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
        <input type="submit" value="Inscription">
    </form>

</body>

</html>