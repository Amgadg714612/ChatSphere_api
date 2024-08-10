<?php

require_once 'config/config.php'; // Import configuration and database connection settings

class Group
{

    private $pdo;

    public function __construct()
    {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }

    // Retrieve all groups
    public function getAllGroups()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, name, description, created_at FROM groups");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching groups: ' . $e->getMessage());
        }
    }

    // Retrieve a group by ID
    public function getGroupById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, description, created_at FROM groups WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching group: ' . $e->getMessage());
        }
    }

    // Create a new group
    public function createGroup($name, $description, $id_admin)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO groups (name, description,id_userAdmin) VALUES (:name, :description,:id_userAdmin)");
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'id_userAdmin' => $id_admin
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created group
        } catch (PDOException $e) {
            throw new Exception('Error creating group: ' . $e->getMessage());
        }
    }

    // Update an existing group
    public function updateGroup($id, $name, $description)
    {
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
    public function deleteGroup($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM groups WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error deleting group: ' . $e->getMessage());
        }
    }

    // Check if a user is the admin of a group
    public function isGroupAdmin($userId, $groupId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM group_admins WHERE group_id = :groupId AND user_id = :userId");
            $stmt->execute([
                'groupId' => $groupId,
                'userId' => $userId
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception('Error checking group admin status: ' . $e->getMessage());
        }
    }

    // Check if a user has permission to add members to a group
    public function hasAddMemberPermission($userId, $groupId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM group_permissions WHERE group_id = :groupId AND user_id = :userId AND can_add_members = 1");
            $stmt->execute([
                'groupId' => $groupId,
                'userId' => $userId
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception('Error checking add member permission: ' . $e->getMessage());
        }
    }

    // Add a member to a group
    public function addMemberToGroup($groupId, $userId, $permissions, $idUserMember)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO user_groups (group_id, user_id,permissions,idUserMember) VALUES (:groupId, :userId ,:permissions,:idUserMember)");
            $stmt->execute([
                'groupId' => $groupId,
                'userId' => $userId,
                'permissions' => $permissions,
                'idUserMember'=>$idUserMember
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error adding member to group: ' . $e->getMessage());
        }
    }
}
