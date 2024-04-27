<?php

class UserSession {
    // database connection and table name
    private $conn;
    private $table_name = "user_sessions";

    // object properties
    public $id;
    public $user_id;
    public $token;
    public $expiry_time;

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        try {
            // query to insert record
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        user_id = :user_id,
                        token = :token,
                        expiry_time = :expiry_time";
    
            // prepare query statement
            $stmt = $this->conn->prepare($query);
    
            // bind values
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":token", $this->token);
            $stmt->bindParam(":expiry_time", $this->expiry_time);
    
            // execute query
            if($stmt->execute()) {
                return true;
            } else {
                // Si l'exécution échoue, afficher l'erreur
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error executing query: " . $errorInfo[2]);
            }
        } catch (Exception $e) {
            // Si une exception est levée, afficher l'erreur
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    // get user session by token
    function getByToken() {
        // query to read single record
        $query = "SELECT
                    id, user_id, token, expiry_time
                FROM
                    " . $this->table_name . "
                WHERE
                    token = ?
                LIMIT
                    0,1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind token value
        $stmt->bindParam(1, $this->token);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set values to object properties
        $this->id = $row['id'];
        $this->user_id = $row['user_id'];
        $this->token = $row['token'];
        $this->expiry_time = $row['expiry_time'];
    }

    // update user session
    function update() {
        // query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    token = :token,
                    expiry_time = :expiry_time
                WHERE
                    id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":expiry_time", $this->expiry_time);
        $stmt->bindParam(":id", $this->id);

        // execute the query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // delete user session
    function delete() {
        // query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));

        // bind id of record to delete
        $stmt->bindParam(1, $this->id);

        // execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }


}
?>
