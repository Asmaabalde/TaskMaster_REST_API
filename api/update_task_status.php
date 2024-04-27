<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les données requises sont présentes dans la requête
    if(isset($_POST['task_id']) && isset($_POST['new_status'])) {
        // Récupérer les données du formulaire
        $task_id = $_POST['task_id'];
        $new_status = $_POST['new_status'];

        // inclure les fichiers requis
        include_once 'config/Database.php';
        include_once 'objects/Task.php';

        // Instancier la connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();

        // Instancier l'objet Task
        $task = new Task($db);

        // Mettre à jour le statut de la tâche dans la base de données
        if($task->updateStatus($task_id, $new_status)) {
            // Statut de la tâche mis à jour avec succès
            http_response_code(200);
            echo json_encode(array("message" => "Le statut de la tâche a été mis à jour avec succès.", "redirect" => "tableauDeBord.php"));
            header("Refresh: 2; URL=tableauDeBord.php");

            
        } else {
            // Erreur lors de la mise à jour du statut de la tâche
            http_response_code(500);
            echo json_encode(array("message" => "Impossible de mettre à jour le statut de la tâche."));
        }
    } else {
        // Données requises manquantes dans la requête
        http_response_code(400);
        echo json_encode(array("message" => "Données requises manquantes."));
    }
} else {
    // Méthode de requête incorrecte
    http_response_code(405);
    echo json_encode(array("message" => "Méthode non autorisée."));
}
?>
