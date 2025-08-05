<?php
session_start();

// DECONNEXION
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
        <img src="assets/media/squirrel.webp">
        <nav>
            <?php
            if (isset($_SESSION["logged_user"]) and $_SESSION["logged_user"] === "admin")
                echo "<a href=\"admin.php\"><img src=\"assets/media/admin.svg\">Page administrative</a>";
            if (isset($_SESSION["logged_user"]))
                echo " <a href=\"profil.php\"><img src=\"assets/media/profile.svg\">Modifier Profil</a> <form action=\"index.php\" method=\"post\"><img src=\"assets/media/disconnect.svg\"><input type=\"submit\" name=\"Deconnexion\" value=\"Deconnexion\" id=\"deconnexion\"></form>";
            if (!isset($_SESSION["logged_user"])) echo "<a href=\"connexion.php\"><img src=\"assets/media/login.svg\">Connexion</a> <a href=\"inscription.php\"><img src=\"assets/media/register.svg\">Inscription</a>"; ?>
        </nav>
        <?php if (isset($_SESSION['message'])) echo "<p class=\"index-message\">" . $_SESSION['message'] . "</p>";
        $_SESSION['message'] = null; ?>
    </header>
    <main>
        <h1>Les amis des écureuils!</h1>
        <div class="article">
            <h2>DIY : Fabriquer une mangeoire pour écureuils et attirer ces petits acrobates chez vous</h2>
            <p>Les écureuils fascinent par leur agilité, leur vivacité et leur curiosité. Si vous aimez les observer dans votre jardin ou votre parc local, pourquoi ne pas leur offrir un petit coin gourmand avec une mangeoire spécialement conçue pour eux ? Dans cet article, je vous explique comment fabriquer facilement une mangeoire à écureuils, respectueuse de leur mode de vie.</p>
            <h3>Pourquoi une mangeoire pour écureuils ?</h3>
            <p>Contrairement aux mangeoires pour oiseaux, les écureuils ont besoin d’un espace robuste et stable, car ils sont plus lourds et plus actifs. Une mangeoire bien conçue permet de les attirer, surtout en hiver quand la nourriture se fait rare, tout en évitant qu’ils ne volent la nourriture des oiseaux.</p>
            <h3>Matériel nécessaire</h3>
            <ul>
                <li>Planche en bois (idéalement non traité)</li>
                <li>Vis et clous</li>
                <li>Corde ou fil résistant</li>
                <li>Une petite assiette ou un bac en plastique</li>
                <li>Perceuse, marteau, scie</li>
                <li>Papier abrasif</li>
            </ul>
            <h3>Étapes de fabrication</h3>
            <ol>
                <li>
                    <h4>Découper la base</h4>
                    <p>Prenez la planche en bois et découpez un rectangle de 30 cm sur 20 cm. C’est la base de votre mangeoire.</p>
                </li>
                <li>
                    <h4>Poncer les bords</h4>
                    <p>Utilisez le papier abrasif pour lisser les bords afin d’éviter que les écureuils ne se blessent.</p>
                </li>
                <li>
                    <h4>Fixer l’assiette</h4>
                    <p>Fixez l’assiette ou le bac en plastique au centre de la planche avec des vis. Cela servira de récipient pour la nourriture.</p>
                </li>
                <li>
                    <h4>Créer un support et une suspension</h4>
                    <p>Fixez un petit cadre sur la base (pour empêcher la nourriture de tomber) et percez deux trous aux extrémités du haut pour passer la corde.</p>
                </li>
                <li>
                    <h4>Installer la mangeoire</h4>
                    <p>Accrochez votre mangeoire à une branche solide à environ 2 mètres de hauteur, dans un endroit calme et à l’abri du vent.</p>
                </li>
            </ol>
            <h3>Que mettre dans la mangeoire ?</h3>
            <p>Les écureuils adorent les noix (noisettes, noix de cajou, noix classiques), les graines de tournesol, les pommes, et même quelques morceaux de carottes. Évitez les aliments salés ou transformés.</p>
            <h3>Conseils pour une cohabitation réussie</h3>
            <ul>
                <li>Changez régulièrement la nourriture pour éviter la moisissure.</li>
                <li>Nettoyez la mangeoire toutes les semaines.</li>
                <li>Ne donnez pas trop de nourriture pour ne pas rendre les écureuils dépendants.</li>
            </ul>


        </div>
    </main>
    <footer>
        <p>2025 - Cosmin Bilga</p>
    </footer>
</body>

</html>