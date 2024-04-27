<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// files needed to connect to database and manage tasks
include_once 'config/Database.php';
include_once 'objects/Task.php';
include_once 'objects/UserSession.php';
include_once 'config/core.php';
include_once 'libs/JWT.php';

use \JWTLib\JWT;

// check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier d'abord si le jeton est valide
    $database = new Database();
    $db = $database->getConnection();

    // Vérifier si le JWT est présent dans les cookies
    if(isset($_COOKIE['jwt'])) {
        try {
            // Décoder le JWT pour vérifier son authenticité
            $decoded = JWT::decode($_COOKIE['jwt'], $key, array('HS256'));
            // Le JWT est valide, vous pouvez accéder aux informations de l'utilisateur
            $user_id = $decoded->data->id; // ID de l'utilisateur extrait du JWT

            // Vérifiez si les données POST requises sont définies
            if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && isset($_POST['status'])) {
                $title = $_POST['title'];
                $description = $_POST['description'];
                $assigned_to = $_POST['assigned_to'];
                $status = $_POST['status'];
                
                // instanciate task object
                $task = new Task($db);
                
                // set task properties
                $task->title = $title;
                $task->description = $description;
                $task->assigned_to = $assigned_to;
                $task->status = $status;
                $task->created_by = $user_id;
                
                // create the task
                if($task->create()){
                    // set response code
                    http_response_code(200);
                    // display message: task was created
                    echo json_encode(array("message" => "Task was created."));
                    exit; // Assurez-vous de quitter le script après avoir envoyé la réponse JSON
                }
                // message if unable to create task
               else{
                    // set response code
                    http_response_code(400);
                    // display message: unable to create task
                    echo json_encode(array("message" => "Unable to create task.", "error" => $task->error()));
                    exit; // Assurez-vous de quitter le script après avoir envoyé la réponse JSON
                }

            } else {
                // missing required data in POST request
                http_response_code(400);
                echo json_encode(array("message" => "Missing required data."));
                exit; // Assurez-vous de quitter le script après avoir envoyé la réponse JSON
            }
        } catch (Exception $e) {
            // Le JWT est invalide ou a expiré
            // Rediriger l'utilisateur vers la page de connexion
            header("Location: connexion.html");
            exit;
        }
    } else {
        // Le JWT n'est pas présent dans les cookies, renvoyer une erreur d'accès non autorisé
        http_response_code(401); // Unauthorized
        echo json_encode(array("message" => "Unauthorized access."));
        exit; // Assurez-vous de quitter le script après avoir envoyé la réponse JSON
    }
} else {
    // La méthode de requête n'est pas POST
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed."));
    exit; // Assurez-vous de quitter le script après avoir envoyé la réponse JSON
}
?>
