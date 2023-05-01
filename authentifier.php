<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset ($_GET["idEmp"]) && !isset ($_GET["motDePasse"]))  die ("T'as pas oublié un truc par hasard ?");

    $matricule = $_GET["matricule"];
    $motdepasse = $_GET["motdepasse"];

    $req="SELECT Emp_Matricule, Emp_MotDePasse FROM employe WHERE Emp_Matricule = :matricule AND Emp_MotDePasse = :motdepasse";
    $stmt = $pdo->prepare($req);
    $stmt->bindParam(":matricule",$matricule,PDO::PARAM_STR);
    $stmt->bindParam(":motdepasse",$motdepasse,PDO::PARAM_STR);
    $stmt->execute();

    if (empty($stmt->fetchAll())) {
        echo("matricule ou mot de passe incorrect/manquant");
    }

    else {
        echo("Bienvenue");
    }

} catch (Exception $e) {
    die("LES PROBLEMES : ".$e->getMessage());
}
?>