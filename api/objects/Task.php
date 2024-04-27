<?php
class Task {
    // Connexion à la base de données et nom de la table
    private $conn;
    private $table_name = "tasks";

    // Propriétés de la tâche
    public $id;
    public $title;
    public $description;
    public $status;
    public $assigned_to;
    public $created_by;
    public $created_at;
    public $updated_at;

    // Constructeur avec $db comme base de données
    public function __construct($db) {
        $this->conn = $db;
    }

   // Méthode pour créer une nouvelle tâche
   function create() {
    // Requête d'insertion
    $query = "INSERT INTO " . $this->table_name . "
            SET
                title = :title,
                description = :description,
                status = :status,
                assigned_to = :assigned_to,
                created_by = :created_by,
                created_at = NOW(),
                updated_at = NOW()";

    // Préparation de la requête
    $stmt = $this->conn->prepare($query);

    // Nettoyer les données
    $this->title = htmlspecialchars(strip_tags($this->title));
    $this->description = htmlspecialchars(strip_tags($this->description));
    $this->status = htmlspecialchars(strip_tags($this->status));
    $this->assigned_to = intval($this->assigned_to);
    $this->created_by = intval($this->created_by);

    // Liaison des valeurs
    $stmt->bindParam(":title", $this->title);
    $stmt->bindParam(":description", $this->description);
    $stmt->bindParam(":status", $this->status);
    $stmt->bindParam(":assigned_to", $this->assigned_to);
    $stmt->bindParam(":created_by", $this->created_by);

    // Exécution de la requête
    if ($stmt->execute()) {
        return true;
    }

    // En cas d'échec, retourner false
    return false;
}
    // Lire toutes les tâches
    function read() {
        // Requête pour lire toutes les tâches
        $query = "SELECT
                    id, title, description, status, assigned_to, created_by, created_at, updated_at
                FROM
                    " . $this->table_name . "
                ORDER BY
                    created_at DESC";

        // Préparation de la requête
        $stmt = $this->conn->prepare($query);

        // Exécution de la requête
        $stmt->execute();

        return $stmt;
    }

    // Lire une seule tâche par ID
    function readOne() {
        // Requête pour lire une tâche
        $query = "SELECT
                    id, title, description, status, assigned_to, created_by, created_at, updated_at
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";

        // Préparation de la requête
        $stmt = $this->conn->prepare($query);

        // Liaison de l'ID de la tâche à lire
        $stmt->bindParam(1, $this->id);

        // Exécution de la requête
        $stmt->execute();

        // Récupération de la ligne
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Affectation des valeurs aux propriétés de l'objet
        $this->title = $row['title'];
        $this->description = $row['description'];
        $this->status = $row['status'];
        $this->assigned_to = $row['assigned_to'];
        $this->created_by = $row['created_by'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
    }

    // Mettre à jour une tâche
    public function updateStatus($task_id, $new_status) {
        // Requête SQL pour mettre à jour le statut de la tâche
        $query = "UPDATE tasks SET status = :new_status WHERE id = :task_id";
    
        // Préparation de la requête
        $stmt = $this->conn->prepare($query);
    
        // Liaison des valeurs
        $stmt->bindParam(":new_status", $new_status);
        $stmt->bindParam(":task_id", $task_id);
    
        // Exécution de la requête
        if ($stmt->execute()) {
            return true; // Mise à jour réussie
        } else {
            // En cas d'erreur, afficher le message d'erreur
            $error_info = $stmt->errorInfo();
            error_log("Erreur lors de la mise à jour du statut de la tâche : " . $error_info[2]);
            return false; // Échec de la mise à jour
        }
    }
    

    // Supprimer une tâche
    function delete() {
        // Requête de suppression
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // Préparation de la requête
        $stmt = $this->conn->prepare($query);

        // Nettoyer l'ID de la tâche à supprimer
        $this->id = intval($this->id);

        // Liaison de l'ID de la tâche à supprimer
        $stmt->bindParam(1, $this->id);

        // Exécution de la requête
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getTasksByUserId($user_id) {
        // Requête SQL pour sélectionner les tâches de l'utilisateur avec le nom d'utilisateur associé
        $query = "SELECT tasks.*, users.username 
                  FROM tasks 
                  INNER JOIN users ON tasks.assigned_to = users.id
                  WHERE tasks.created_by = :user_id";
    
        // Préparation de la requête
        $stmt = $this->conn->prepare($query);
    
        // Liaison des valeurs
        $stmt->bindParam(":user_id", $user_id);
    
        // Exécution de la requête
        $stmt->execute();
    
        // Récupération des résultats
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Retourner les tâches
        return $tasks;
    }
    
    
}
