<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epoka - Paramétrage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js">
    </script>
</head>
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

        // Redirection vers la page d'authentification si l'utilisateur ne s'est pas authentifié
        if (!isset($_SESSION["idEmp"]) && !isset($_SESSION["motDePasse"])) {
            header("location: authentification.php");
        }

        // Action de déconnexion
        if (isset($_POST["deconnexion"])) {
            session_destroy();
            header("location: authentification.php");
            $_SESSION = [];
        }

        // Action de modification du montant
        if (isset($_POST["modifier"])) {
            if (isset($_POST["montantKM"]) && isset($_POST["montantJour"])) {
                $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
                $montantKM = $_POST["montantKM"];
                $montantJour = $_POST["montantJour"];
                $sql = "UPDATE parametres SET mtJour = :montantJour, mtKM = :montantKM";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":montantJour", $montantJour, PDO::PARAM_STR);
                $stmt->bindParam(":montantKM", $montantKM, PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION["info"] = "Montant mis à jour avec succès";
            }
        }

        // Action de validation de la mission
        if (isset($_POST["valider"])) {
            if (isset($_POST["villeDepart"]) && isset($_POST["villeArrivee"]) && isset($_POST["km"])) {
                $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
                $villeDepart = $_POST["villeDepart"];
                $villeArrivee = $_POST["villeArrivee"];
                $km = $_POST["km"];
                $sql = "INSERT INTO trajet (idVilleDep, idVilleArr, km) VALUES (:idVilleDep, :idVilleArr, :km)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":idVilleDep", $villeDepart, PDO::PARAM_INT);
                $stmt->bindParam(":idVilleArr", $villeArrivee, PDO::PARAM_INT);
                $stmt->bindParam(":km", $km, PDO::PARAM_INT);
                try {
                    $stmt->execute();
                }
                catch(Exception $e){
                    die("LES PROBLEMES :" . $e->getMessage());
                }
                
            }
            
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
    <?php
        $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
        $req = "SELECT * FROM parametres";
        $res = $pdo->prepare($req);
        $res->execute();
        $var = $res->fetchAll();
        // print_r($var[0]['mtKM']);

    ?>
    <h1>Paramétrage de l'application<h1>
    <h4>Montant du remboursement au KM</h4>
    
    <!-- FORMULAIRE DE SAISIE DU MONTANT DE REMBOURSEMENT -->
    <form method="post">
        Remboursement au km : <input type="text" value=<?php echo($var[0]['mtKM']); ?> name="montantKM">
        Indemnité d'hébergement : <input type="text" value=<?php echo($var[0]['mtJour']); ?> name="montantJour">
        <button type="submit" name="modifier">Mettre à jour</button>      
    </form>

    <h4>Distance entre les villes</h4>

    <!-- FORMULAIRE DE SÉLÉCTION DES VILLES ET LEUR DISTANCE EN KM -->
    <form method="post">
        De : <select name="villeDepart" id="villeDepart" required>
            <?php
            $reponse = $pdo->query('SELECT * FROM commune');
            while ($donnees = $reponse->fetch()) {
               ?> 
               <option value=<?php echo $donnees['idCommune']; ?>>
                    <?php echo $donnees['comNom']; ?>
                    <?php echo $donnees['comCP']; ?>
               </option>
			   <?php } ?>
            </select>
          
        à : <select name="villeArrivee" id="villeArrivee" required>
        <?php
            
            $reponse = $pdo->query('SELECT * FROM commune');
            while ($donnees = $reponse->fetch()) {
               ?> 
               <option value=<?php echo $donnees['idCommune']; ?>>
                    <?php echo $donnees['comNom']; ?>
                    <?php echo $donnees['comCP']; ?>
                    <?php //echo $donnees['idCommune']; ?>
               </option>
			   <?php } ?>
            </select>
        
        Distance en km : <input type="number" name="km" required>
        <button type="submit" name="valider">Valider</button>

    </form>

    <h4>Distance entre les villes déja saisies</h4>

    <!-- TABLEAU DES TRAJETS -->
    <table>
        <thead>
            <tr>
                <th colspan="1">De</th>
                <th colspan="2">A</th>
                <th colspan="3">Km</th>              
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT v1.comNom AS ville_depart, v2.comNom AS ville_arrivee, t.km FROM trajet t JOIN commune v1 ON t.idVilleDep = v1.idCommune JOIN commune v2 ON t.idVilleArr = v2.idCommune";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch()) {
                $ville_depart = $row['ville_depart'];
                $ville_arrivee = $row['ville_arrivee'];
                $distance_km = $row['km'];
                echo "<td>$ville_depart</td>";
                echo "<td>$ville_arrivee</td>";
                echo "<td>$distance_km</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>Aucun trajet trouvé</td></tr>";
        }
        ?>
        <script>
        // Récupération des éléments de la page HTML
        var villeDepart = document.getElementById("villeDepart");
        var villeArrivee = document.getElementById("villeArrivee");

        // Ajout d'un gestionnaire d'événements sur la sélection de la ville de départ
        villeDepart.addEventListener("change", function() {
            // Désactivation de l'option sélectionnée dans la liste déroulante de la ville d'arrivée
            villeArrivee.querySelector("option[value='" + villeDepart.value + "']").disabled = true;
        });
        </script>
        </tbody>
    </table>
    
</body>
</html>