<?php
require_once 'config/config.php';
include_once 'utils/ResponseFormatter.php '; // Import configuration and database connection settings
class User
{
    private $pdo;
    public function __construct()
    {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }


    // Retrieve all users
    public function getAllUsers()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, username, email, created_at FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseFormatter::error('Error fetching users: ', 409);
        }
    }

    // Retrieve a user by ID
    public function getUserById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseFormatter::error('Error fetching users: ', 409);
        }
    }
    public function getUserRoles($id,$Rolesid)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT role_id FROM users WHERE id = :id and role_id = :role_id");
            $stmt->execute([
                'id' => $id,
                'role_id'=> $Rolesid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['role_id'] : false;
        } catch (PDOException $e) {
            ResponseFormatter::error('Error fetching user not  roles : ', 409);
        }
    }
    // Retrieve a user by username
    public function getUserByUsername($username)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ResponseFormatter::error('Error fetching user by username:: ', 409);
        }
    }
    // Retrieve a user by email
    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, password FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
           echo ResponseFormatter::error('Error fetching user by email:  ', 409);
        }
    }
    public function getUserIdByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id  FROM users WHERE email = :email");
            $stmt->execute( ['email' => $email]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : false;
        } catch (PDOException $e) {
            ResponseFormatter::error('Error fetching user by email: ' + $e->getMessage(), 1024);
        }
    }
    public function getRoleIdByName($roleName)
    {
        $query = "SELECT id FROM roles WHERE role_name = :role_name";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['role_name' => $roleName]);
        $result=0;
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result === 0){
            echo ResponseFormatter::error(" ROLES EMPTY    ");
            exit;
        }
        $idi=$result['id'];
        return  $idi;
    }
    ///
    ////
    ////
    ////

    public function getRoleIdByUserId($RoleUserId)
    {
        $query = "SELECT role_id FROM users WHERE id = :idUser";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['idUser' => $RoleUserId]);
        $result=0;
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result === 0){
            echo ResponseFormatter::error(" no is  getRoleIdByUserId");
            exit;
        }
        $nameRoleUser=self::getRoleIdByid($result['id']);   
        return  $$nameRoleUser;
    }
    public function getRoleIdByid($idRow)
    {
        $query = "SELECT role_name FROM roles WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $idRow]);
        $result=0;
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result === 0){
            echo ResponseFormatter::error(" ROLES EMPTY    ");
            exit;
        }

        $nameRole=$result['role_name'];
        return  $nameRole;
    }
    // Create a new user
    public function createUser($username, $email, $password, $idRole)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password ,role_id) VALUES (:username, :email,:password,:role_id)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role_id'=>  $idRole
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created user
        } catch (PDOException $e) {
            ResponseFormatter::error('Error creating user: ',405);
        }
    }
    public function createUserDev($username, $email, $password)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password ,role_id) VALUES (:username, :email,:password,:role_id)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role_id'=>  1
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created user
        } catch (PDOException $e) {
            ResponseFormatter::error('Error creating user: ',405);
        }
    }

    // Update an existing user
    public function updateUser($id, $username, $email)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $id
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            ResponseFormatter::error('Error updating user: ',405);
        }
    }

    // Delete a user
    public function deleteUser($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            ResponseFormatter::error('Error deleting user: ',405);
        }
    }
    public function emailExists($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Log the error message (assuming Logger class exists)
            Logger::error('Error checking email existence: ' . $e->getMessage());
            ResponseFormatter::error('Error checking email existence: ',405);
        }
    }
}
