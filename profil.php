<?php
session_start();

//print_r($_SESSION);
//print_r($_POST);

$_SESSION["error"] = NULL;

if (!isset($_SESSION["logged_user"])) {
    header('Location: index.php');
    exit();
} elseif (isset($_POST["modif"]))
    check_modification();
else
    get_info();



function get_info()
{
    include "connexion-tools.php";
    $conn = new mysqli($server, $user, $password, $database);

    //print_r($_POST);

    if ($conn->connect_errno) {
        //echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
        $_SESSION["error"] = "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
        return "Modification echoué";
    }

    $sql = "SELECT login, nom, prenom, password 
            FROM utilisateurs 
            WHERE login='" . $_SESSION["logged_user"] . "';";
    $result = sql_exec($sql, $conn);
    $data = $result->fetch_assoc();
    if (gettype($data) == "array")
        foreach ($data as $key => $value)
            $_SESSION[$key] = $value;

    $conn->close();
}

function check_login($conn): string
{
    if (!isset($_POST["login"])) {
        $_SESSION["error"] = "Veuillez choisir un login";
        return "Veuillez choisir un login";
    } else {
        preg_match('/^[a-zA-Z0-9]{5,}$/', $_POST["login"], $matches);
        //echo "MATCH<br />";
        //print_r($matches);
        if (!$matches) {
            $_SESSION["error"] = "Veuillez choisir un login de 5 characteres ou plus, formé de lettre et chiffres uniquement";
            return "Veuillez choisir un login de 5 characteres ou plus, formé de lettre et chiffres uniquement";
        }
        if ($_POST["login"] === $_SESSION["logged_user"]) // si on ne change pas de login
            return "ok";

        $sql = "SELECT * FROM utilisateurs WHERE login='" . htmlentities($_POST["login"]) . "';";

        $result = sql_exec($sql, $conn);
        if ($result->fetch_assoc()) {
            $_SESSION["error"] = "Login deja existant";
            return "Login deja existant";
        }
        return "ok";
    }
}

function check_password(): string
{
    if (!(isset($_POST["password"]) and isset($_POST["password-repeat"]))) {
        $_SESSION["error"] = "Assurez-vous de bien renseigner le password et bien le confirmer.";
        return "Assurez-vous de bien renseigner le password et bien le confirmer.";
    } elseif ($_POST["password"] !== $_POST["password-repeat"]) {
        $_SESSION["error"] = "Password et sa confirmation ne sont par identinques. Veuillez les retaper!";
        return "Password et sa confirmation ne sont par identinques. Veuillez les retaper!";
    } elseif ($_POST["password"] === "") // Si on ne veut pas changer le password
        return "ok";
    else {
        preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-+=()[\]{}]).{8,}$/", $_POST["password"], $matches);
        //echo "MATCH<br />";
        //print_r($matches);
        if ($matches) {
            return "ok";
        } else {
            $_SESSION["error"] = "Veuillez choisir un password de minimum 8 characteres, contenant au moins une majuscule, une minuscule, une chiffre et un caractere special";
            return "Veuillez choisir un password de minimum 8 characteres, contenant au moins une majuscule, une minuscule, une chiffre et un caractere special";
        }
    }
}


function security_checks()
{
    $_POST["login"] = htmlspecialchars(trim($_POST["login"]), ENT_QUOTES, "UTF-8");
    $_POST["nom"] = htmlspecialchars(trim($_POST["nom"]), ENT_QUOTES, "UTF-8");
    $_POST["prenom"] = htmlspecialchars(trim($_POST["prenom"]), ENT_QUOTES, "UTF-8");
}

function check_modification(): string //CAN ONLY BE CALLED ONCE DUE TO INCLUDE ERROR
{

    if (count($_POST) > 0) {
        //if ("connexion-tools.php") 
        //print_r(get_included_files());
        include_once "connexion-tools.php";
        $conn = new mysqli($server, $user, $password, $database);

        //print_r($_POST);

        if ($conn->connect_errno) {
            //echo "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
            $_SESSION["error"] = "Echec de connexion à la DB. Veuillez essayer ulterieurement: " . $mysqli->connect_error;
            return "Modification echoué";
        }


        $ok = check_login($conn);

        if ($ok !== "ok")
            return $ok;

        //echo "??";
        $ok = check_password();

        //echo "password $ok";

        security_checks();

        $sql = "UPDATE utilisateurs SET login='" . $_POST["login"] . "',
            nom='" . $_POST["nom"] . "',
            prenom='" . $_POST["prenom"] . "' ";

        if ($ok !== "ok")
            return $ok;
        elseif ($_POST["password"] !== "")  // on update password seulement si on tapedans password
            $sql .= ",password='" . password_hash($_POST["password"], PASSWORD_DEFAULT) . "' ";


        $sql .= "WHERE login='" . $_SESSION["logged_user"] . "';";
        //echo $sql;
        sql_exec($sql, $conn);

        $_SESSION["logged_user"] = $_POST["login"];
        $conn->close();

        $_SESSION["message"] = "Profil mis à jour avec succes!";
        return "Profil mis à jour avec succes!";
    }

    $_SESSION["error"] = "Formulaire incomplet";
    return "Formulaire incomplet";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?php echo $_SESSION["logged_user"] ?></title>
    <link rel="stylesheet" href="assets/css/main_page.css">
</head>

<body>
    <header>
        <img src="assets/media/squirrel.webp">
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] === "admin")
                echo "<a href=\"admin.php\"><img src=\"assets/media/admin.svg\">Page administrative</a>";
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"index.php\"><img src=\"assets/media/home-svgrepo-com.svg\">Page d'accueil</a> <form action=\"index.php\" method=\"post\"><img src=\"assets/media/disconnect.svg\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\"></form>";
            ?>
        </nav>
    </header>
    <main>
        <h1>PROFIL DE <?php echo $_SESSION["logged_user"]; ?></h1>
        <form action="profil.php" method="post">
            <label for="login">Login:</label>
            <input type="text" name="login" id="login" <?php echo "value=\"" . $_SESSION["login"] . "\"" ?>>
            <label for="nom">Nom:</label>
            <input type="text" name="nom" id="nom" <?php echo "value=\"" . $_SESSION["nom"] . "\"" ?>>
            <label for="prenom">Prenom:</label>
            <input type="text" name="prenom" id="prenom" <?php echo "value=\"" . $_SESSION["prenom"] . "\"" ?>>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <label for="password-repeat">Répetez le password:</label>
            <input type="password" name="password-repeat" id="password-repeat">
            <?php if (isset($_SESSION["error"])) echo "<p class=\"input-error\">" . $_SESSION["error"] . "</p>"; ?>
            <?php if (isset($_SESSION["message"])) echo "<p class=\"input-message\">" . $_SESSION["message"] . "</p>";
            $_SESSION["message"] = NULL; ?>
            <input type="submit" value="Mettre à jour" name="modif" class="main-form">
        </form>
    </main>
    <footer>
        <p>2025 - Cosmin Bilga</p>
    </footer>
</body>

</html>