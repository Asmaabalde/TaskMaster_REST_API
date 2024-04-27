<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'config/Database.php';
include_once 'objects/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Vérifiez d'abord si les données POST sont définies
    if(isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Vérifiez si les mots de passe correspondent
        if ($password !== $confirm_password) {
            http_response_code(400);
            echo json_encode(array("message" => "Passwords do not match."));
            exit;
        }

        // Hash du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Si des données sont manquantes, renvoyez un message d'erreur
        http_response_code(400);
        echo json_encode(array("message" => "Missing POST data."));
        exit;
    }
}

// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 
// set user property values
$user->username = $username;
$user->email = $email;
$user->password = $hashed_password; // Utilisation du mot de passe hashé
 
// create the user
if(
    !empty($user->username) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
){
    // set response code
    http_response_code(200);
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
// message if unable to create user
else{
    // set response code
    http_response_code(400);
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>
