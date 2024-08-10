<?php
require_once 'config/config.php'; // Import configuration and database connection settings
class User {
    private $pdo;
    public function __construct() {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }

  
    // Retrieve all users
    public function getAllUsers() {
        try {
            $stmt = $this->pdo->query("SELECT id, username, email, created_at FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching users: ' . $e->getMessage());
        }
    }

    // Retrieve a user by ID
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching user: ' . $e->getMessage());
        }
    }
// Retrieve a user by username
public function getUserByUsername($username) {
    try {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception('Error fetching user by username: ' . $e->getMessage());
    }
}
 // Retrieve a user by email
 public function getUserByEmail($email) {
    try {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception('Error fetching user by email: ' . $e->getMessage());
    }
}
public function getUserIdByEmail($email) {
    try {
        $stmt = $this->pdo->prepare("SELECT id  FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : false; 
    } catch (PDOException $e) {
        ResponseFormatter::error('Error fetching user by email: '+$e->getMessage(),1024);
    }
}
    // Create a new user
    public function createUser($username, $email,$password) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password ) VALUES (:username, :email,:password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created user
        } catch (PDOException $e) {
            throw new Exception('Error creating user: ' . $e->getMessage());
        }
    }

    // Update an existing user
    public function updateUser($id, $username, $email) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $id
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error updating user: ' . $e->getMessage());
        }
    }

    // Delete a user
    public function deleteUser($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error deleting user: ' . $e->getMessage());
        }
    }
    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log the error message (assuming Logger class exists)
            Logger::error('Error checking email existence: ' . $e->getMessage());
            throw new Exception('Error checking email existence: ' . $e->getMessage());
        }
    }
}
