<?php
    session_start();
    $pdo = new PDO('mysql:host=localhost;dbname=epokamission;charset=utf8', 'root', '');

    if (isset($_POST["submit"])) {
        $idMission = $_POST["idMission"];
        $estPayee = $_POST["payee"];

        $sql = "UPDATE mission SET estPayee = :estPayee WHERE idMission = :idMission";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':estPayee' => $estPayee, ':idMission' => $idMission));

        if ($stmt->rowCount() > 0) {
            $_SESSION["info"] = "Paiement effectué"; 
        }

        else {
            $_SESSION["error"] = "Problème lors de la validation du paiement";
        }
        header("location: paiementFrais.php");
        exit();
    }    