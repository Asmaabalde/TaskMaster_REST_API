<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des tâches</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <!-- Navbar Bootstrap -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">TaskMaster</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="tableauDeBord.php">Tableau de bord</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="create_task_Form.php">New task</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion.html">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="deconnexion.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h2>Liste des tâches</h2>
        <?php
        // Récupérer les tâches de l'utilisateur connecté
        include_once 'config/Database.php';
        include_once 'objects/Task.php';
        include_once 'objects/UserSession.php';
        include_once 'objects/User.php';
        include_once 'libs/JWT.php';
        include_once 'config/core.php';

        use \JWTLib\JWT;

        // Vérifier si le JWT est présent dans les cookies
        if(isset($_COOKIE['jwt'])) 
        {
            try 
            {
                $database = new Database();
                $db = $database->getConnection();
                // Décoder le JWT pour vérifier son authenticité
                $decoded = JWT::decode($_COOKIE['jwt'], $key, array('HS256'));
                // Le JWT est valide, vous pouvez accéder aux informations de l'utilisateur
                $user_id = $decoded->data->id; // ID de l'utilisateur extrait du JWT
                $task = new Task($db);
                $tasks = $task->getTasksByUserId($user_id);
              
                // Vérifier s'il y a des tâches à afficher
                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        echo "<div class='task-container'>";
                        echo "<h4>"  . $task['title'] . "</h4>";
                        echo "<p>Description : " . $task['description'] . "</p>";
                        echo "<p>Statut actuel : " . $task['status'] . "</p>";
                        echo "<p>Assigné à : " . $task['username'] . "</p>";
                        // Ajouter un formulaire pour modifier le statut de la tâche
                        echo "<form method='POST' action='update_task_status.php'>";
                        echo "<input type='hidden' name='task_id' value='" . $task['id'] . "'>";
                        echo "<label for='new_status'>Modifier l'état d'avancement : </label>";
                        echo "<select name='new_status' id='new_status'>";
                        echo "<option value='pending'>En attente</option>";
                        echo "<option value='in_progress'>En cours</option>";
                        echo "<option value='completed'>Terminée</option>";
                        echo "</select>";
                        echo "<button type='submit' class= 'sub'>Modifier le statut</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Aucune tâche n'a été trouvée pour cet utilisateur.</p>";
                }
                
            } 
            catch (Exception $e) {
                // Le JWT est invalide ou a expiré
                // Afficher un message pour le débogage
                echo "Le JWT est invalide ou a expiré.";
            
                // Rediriger l'utilisateur vers la page de connexion
                header("Location: connexion.html");
                exit;
            }
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
