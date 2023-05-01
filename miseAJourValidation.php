<?php
    session_start();
    $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');

    // Action de modification des données de la mission (paiement)
    if (isset($_POST["submit"])) {
        $idMission = $_POST["idMission"];
        $estValidee = $_POST["validee"];

        $sql = "UPDATE mission SET estValidee = :estValidee WHERE idMission = :idMission";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':estValidee' => $estValidee, ':idMission' => $idMission));

        if ($stmt->rowCount() > 0) {
            $_SESSION["info"] = "Validation effectuée"; 
        }

        else {
            $_SESSION["error"] = "Problème lors de la validation";
        }
        header("location: validationMission.php");
        exit();
    }   