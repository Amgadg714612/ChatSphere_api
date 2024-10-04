<?php

require_once 'models/Token.php'; // تأكد من إنشاء نموذج توكن

class TokenService {
    private $tokenModel;

    public function __construct() {
        $this->tokenModel = new Token(); // تأكد من وجود Token
    }

    /**
     * Create a new token for a user.
     * 
     * @param int $userId
     * @return string The generated token.
     */
    public function createToken($userId) {
        $token = bin2hex(random_bytes(32));
        while ($this->tokenModel->isValid($token)) {
            $token = bin2hex(random_bytes(32));
             // Generate a new token if the current one is not unique
        }
// Generate a random token
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // Set token expiry (1 hour from now)
        $this->tokenModel->saveToken($userId, $token, $expiresAt);
        return $token;
    }

    /**
     * Validate a token.
     * 
     * @param string $token
     * @return bool True if token is valid, false otherwise.
     */
    public function validateToken($token) {
        return $this->tokenModel->isValid($token);
    }
    public function getUserIdFromToken($token) {
        return $this->tokenModel->getUserIdFromToken($token);
    }
    public function updateToken( $userId33 ) {
        $newtoken = bin2hex(random_bytes(32));
        while ($this->tokenModel->isValid($newtoken)) {
            $newtoken = bin2hex(random_bytes(32));
             // Generate a new token if the current one is not unique
        } // Generate a random token
        $newExpiresAt = date('Y-m-d H:i:s', strtotime('+40 minutes')); // Set token expiry (40 minutes from now)
        $createdAt = date('Y-m-d H:i:s'); // Get the current creation time
        $this->tokenModel->updateToken($userId33, $newtoken, $newExpiresAt,$createdAt);
        return $newtoken;
    }

    
}
?>
