<?php
if (isset($_COOKIE['jwt'])) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Créer une nouvelle tâche</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <h2 class="mt-5">Créer une nouvelle tâche</h2>
            <form id="taskForm" method="POST" action="create_task.php">
                <div class="form-group">
                    <label for="title">Titre de la tâche:</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Statut:</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="">Sélectionner un statut</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminée</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="assigned_to">Utilisateur attribué:</label>
                    <select class="form-control" id="assigned_to" name="assigned_to" required>
                        <option value="">Sélectionner un utilisateur</option>
                        <?php
                        // Connexion à la base de données
                        $connexion = new mysqli("localhost", "root", "", "rest_api");
                        if ($connexion->connect_error) {
                            die("La connexion a échoué : " . $connexion->connect_error);
                        }
                        
                        // Récupérer les utilisateurs depuis la base de données
                        $sql = "SELECT id, username FROM users";
                        $result = $connexion->query($sql);
                        
                        // Afficher chaque utilisateur dans la liste déroulante
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='".$row['id']."'>".$row['username']."</option>";
                        }
                        
                        // Fermer la connexion à la base de données
                        $connexion->close();
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Créer la tâche</button>
            </form>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
<?php
} else {
    header("Location: connexion.html");
}
?>
