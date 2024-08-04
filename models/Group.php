<?php

require_once 'config/config.php'; // Import configuration and database connection settings

class Group {

    private $pdo;

    public function __construct() {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }

    // Retrieve all groups
    public function getAllGroups() {
        try {
            $stmt = $this->pdo->query("SELECT id, name, description, created_at FROM groups");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching groups: ' . $e->getMessage());
        }
    }

    // Retrieve a group by ID
    public function getGroupById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, description, created_at FROM groups WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching group: ' . $e->getMessage());
        }
    }

    // Create a new group
    public function createGroup($name, $description) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO groups (name, description) VALUES (:name, :description)");
            $stmt->execute([
                'name' => $name,
                'description' => $description
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created group
        } catch (PDOException $e) {
            throw new Exception('Error creating group: ' . $e->getMessage());
        }
    }

    // Update an existing group
    public function updateGroup($id, $name, $description) {
        try {
            $stmt = $this->pdo->prepare("UPDATE groups SET name = :name, description = :description WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'id' => $id
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error updating group: ' . $e->getMessage());
        }
    }

    // Delete a group
    public function deleteGroup($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM groups WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error deleting group: ' . $e->getMessage());
        }
    }
}
