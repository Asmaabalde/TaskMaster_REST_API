<?php
// 'user' object
class User
{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $username;
    public $email;
    public $password;
 
    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // create new user
    public function create() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // update the user
    public function update() {
        // query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password
                WHERE
                    id = :id";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // bind new values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // get all users
    public function getAllUsers() {
        // query to get all users
        $query = "SELECT * FROM " . $this->table_name;
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // get single user by id
    public function getUserById() {
        // query to get single user
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
    
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // bind id of user to be updated
        $stmt->bindParam(1, $this->id);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    // delete the user
    public function deleteUser() {
        // delete query
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // bind id of record to delete
        $stmt->bindParam(1, $this->id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // check user login
public function checkLogin(){
    // query to retrieve hashed password for the provided email
    $query = "SELECT id, password FROM users WHERE email = :email";
    
    // prepare query
    $stmt = $this->conn->prepare($query);
    
    // bind values
    $stmt->bindParam(":email", $this->email);
    
    // execute query
    $stmt->execute();
    
    // check if email exists
    if($stmt->rowCount() > 0){
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        // assign values to object properties
        $this->id = $row['id'];
        $this->password = $row['password'];
            return true;
    }
    else
    {
         
     // email does not exist or password is incorrect
        return false;
    }
   
}
}


?>
