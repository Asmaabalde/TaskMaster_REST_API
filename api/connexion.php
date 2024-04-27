<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'config/Database.php';
include_once 'objects/User.php';
include_once 'config/core.php';
include_once 'libs/JWT.php';
include_once 'objects/UserSession.php';
use \JWTLib\JWT;

// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);

// check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    // Vérifiez d'abord si les données POST sont définies
    if(isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
    }
}
 
// set product property values
$user->email = $email;
$email_exists = $user->checkLogin();


// check if email exists and if password is correct
if($email_exists && password_verify($password, $user->password)){
    $token = array(
       "iat" => $issued_at,
       "exp" => $expiration_time,
       "iss" => $issuer,
       "data" => array(
           "id" => $user->id,
           "username" => $user->username,
           "email" => $user->email
       )
    );
    // set response code
    http_response_code(200);
    // generate jwt
    $jwt = JWT::encode($token, $key);
     // Set the JWT token in a secure HTTP only cookie
     setcookie("jwt", $jwt, $expiration_time, '/', '', true, true);

     // Insert new user session into user_sessions table
     $userSession = new UserSession($db);
     $userSession->user_id = $user->id; // Assuming $user->id holds the ID of the logged-in user
     $userSession->token = $jwt;
     $userSession->expiry_time = date("Y-m-d H:i:s", $expiration_time);
    
     // Try to create the user session
     if($userSession->create()){
         // Return success response along with JWT token
         http_response_code(200);
         echo json_encode(array("message" => "User logged in successfully.", "jwt" => $jwt, "redirect" => "tableauDeBord.php"));
         header("Refresh: 2; URL=tableauDeBord.php");
     } else {
         // Return error response if session creation fails
         http_response_code(500);
         echo json_encode(array("message" => "Unable to create user session."));
     }
}
// login failed
else{
    // set response code
    http_response_code(401);
    // tell the user login failed
    echo json_encode(array("message" => "Login failed.",
        "email_exists" => $email_exists,
        "db_PWD" =>$user->password,
        "data_PWD" => $password));
}
