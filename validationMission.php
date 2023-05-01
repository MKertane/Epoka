<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epoka - Validation des missions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js">
    </script>
</head>
<body>
<body>
    <form method="post">
        <input type="submit" name="deconnexion" value="Se déconnecter">
    </form>
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
        if (!isset($_SESSION["idEmp"]) && !isset($_SESSION["motDePasse"])) {
            header("location: authentification.php");
        }
        
        // Action de déconnexion
        if (isset($_POST["deconnexion"])) {
            session_destroy();
            header("location: authentification.php");
            $_SESSION = [];
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
    <h1>Validation des missions</h1>
    
    <?php

    // Récupération des données de missions
    $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
    $sql = "SELECT employe.nomEmp, employe.prenomEmp, commune.comNom, mission.debut, mission.fin, mission.estValidee, mission.estPayee, mission.idMission
        FROM mission 
        JOIN employe ON mission.idEmp = employe.idEmp 
        JOIN commune ON mission.idCommune = commune.idCommune";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Afficher les informations des missions dans un tableau HTML
    ?>
    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Lieu de la mission</th>
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Payée</th>
            <th>Validée</th>
        </tr>
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["nomEmp"] . "</td>";
            echo "<td>" . $row["prenomEmp"] . "</td>";
            echo "<td>" . $row["comNom"] . "</td>";           
            echo "<td>" . date('d/m/Y', strtotime($row["debut"])) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row["fin"])) . "</td>";
            echo "<td>" . ($row["estPayee"] ? "Oui" : "Non") . "</td>";
            echo "<td>" . ($row["estValidee"] ? "Oui" : "Non") . "</td>";
            if (!$row["estValidee"]) {
                echo "<td>
                        <form method='post' action='miseAJourValidation.php'>
                            <label for='oui'>Oui</label>
                            <input type='radio' id='oui' name='validee' value='1' checked>
                            <input type='hidden' name='idMission' value='" . $row["idMission"] . "'>
                            <input type='submit' name='submit' value='Valider'>
                        </form>
                    </td>";
                $_SESSION ["info"]="Mission Validée avec succès";
            } else {
                echo "<td></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</body>
</html>