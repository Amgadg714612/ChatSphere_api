<?php

require_once 'config/config.php'; // Import configuration and database connection settings
class Token {
    private $pdo;
    public function __construct() {
          global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
        }    /**
     * Save a new token to the database.
     * 
     * @param int $userId
     * @param string $token
     * @param string $expiresAt
     */
    public function saveToken($userId, $token, $expiresAt) {
        try{
        $stmt = $this->pdo->prepare("INSERT INTO tokens (user_id, token, expires_at) VALUES (:userId, :token, :expiresAt)");
            $stmt->execute([
                'userId' => $userId,
                'token' => $token,
                'expiresAt' => $expiresAt
            ]);
        }
        catch(PDOException $e){
            throw new Exception('tooken filed ' . $e->getMessage());


        }
    }

    /**
     * Check if a token is valid.
     * 
     * @param string $token
     * @return bool True if token is valid, false otherwise.
     */


     public function updateToken($userId, $newToken, $newExpiresAt) {
        try {
            $stmt = $this->pdo->prepare("UPDATE tokens SET token = :newToken, expires_at = :newExpiresAt WHERE user_id = :userId");
            $stmt->execute([
                'newToken' => $newToken,
                'newExpiresAt' => $newExpiresAt,
                'userId' => $userId
            ]);

        } catch (PDOException $e) {
            throw new Exception('Token update failed: ' . $e->getMessage());
        }
    }

    
    public function isValid($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        echo "dddddddddd";
        return $stmt->fetch() !== false;
    }
 
    public function getUserIdFromToken($token) {
        $stmt = $this->pdo->prepare('SELECT user_id FROM tokens WHERE token = ? AND expires_at > NOW()');
        // $stmt = $this->pdo->prepare('SELECT user_id FROM tokens WHERE token = ? AND expires_at > NOW()');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ? $row['user_id'] : null;
    }
}
?>
