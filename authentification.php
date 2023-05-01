<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epoka - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php
    session_start();
    if (isset ($_SESSION ["error"]) && ($_SESSION ["error"]!=""))
    echo ("<br/><div style=\"background-color: #f44; padding: 6px;\">" . ($_SESSION ["error"]) . "</div>");
    $_SESSION ["error"]="";

    if (isset ($_SESSION ["info"]) && ($_SESSION ["info"]!=""))
    echo ("<br/><div style=\"background-color: #4f4; padding: 6px;\">" . ($_SESSION ["info"]) . "</div>");
    $_SESSION ["info"]="";

    try {
        
        // Déconnexion si l'utilisateur ne s'est pas authentifié
        if (isset($_POST["deconnexion"])) {
            session_destroy();
            header("location: authentification.php");
            $_SESSION = [];
        }


        // Action de l'authentification
        if (isset($_POST["id"]) && isset($_POST["motDePasse"])) {
            $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
            $id = $_POST["id"];
            $motDePasse = $_POST["motDePasse"];
            $sql = "SELECT * FROM employe WHERE idEmp = :idEmp AND motDePasse = SHA1(:motDePasse)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":idEmp", $id, PDO::PARAM_STR);
            $stmt->bindParam(":motDePasse", $motDePasse, PDO::PARAM_STR);
            $stmt->execute();
            if ($row = $stmt->fetch()) {
                
                $nom = $row["nomEmp"];
                $prenom = $row["prenomEmp"];
                $responsable = $row["estResponsable"];
                $comptable = $row["estComptable"];

                $_SESSION["idEmp"] = $id;
                $_SESSION["estResponsable"] = $responsable;
                $_SESSION["estComptable"] = $comptable;            
            }
            
            
            else {echo("Identifiant ou mot de passe incorrect");}
            
        }
                
    }  

    catch(Exception $e){
        die("LES PROBLEMES :" . $e->getMessage());
    }
    ?>
    <!-- BARRE DE NAVIGATION DONT LES OBJETS SONT ACTIVÉS OU NON EN FONCTION DE L'UTILISATEUR AUTHENTIFIÉ -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <?php if (isset($_SESSION["estResponsable"]) && $_SESSION["estResponsable"]) { ?>
                <li class="nav-item"><a class="nav-link" href="validationMission.php">Valider une mission</a></li>
            <?php } else { ?>
                <li class="nav-item"><a class="nav-link disabled" href="#">Valider une mission</a></li>
            <?php } ?>
            
            <?php if (isset($_SESSION["estComptable"]) && $_SESSION["estComptable"]) { ?>
                <li class="nav-item"><a class="nav-link" href="parametrage.php">Paramétrer la mission</a></li>
                <li class="nav-item"><a class="nav-link" href="paiementFrais.php">Payer la mission</a></li>
            <?php } else { ?>
                <li class="nav-item"><a class="nav-link disabled" href="#">Paramétrer la mission</a></li>
                <li class="nav-item"><a class="nav-link disabled" href="#">Payer la mission</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>
<br><br>
    
    <?php
    // Bouton de déconnexion
    if (isset($_SESSION["idEmp"])) { ?>
        <form method="post">
        <input type="submit" name="deconnexion" value="Se déconnecter">
        </form>
        <?php
        ?>
    <?php
    }
    // Bouton de connexion
    else { ?>
        <form name="epoka_authentification" method="post">
        Identifiant : <input type="number" name="id" min="1" required/>
        Mot de passe : <input type="password" name="motDePasse" required/>
        <p><input type="submit" value="Se connecter"></p>
        </form>
    <?php 
    }
    ?>
</body>
</html>