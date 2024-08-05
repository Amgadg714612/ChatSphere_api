<?php

require_once 'services/UserService.php';
require_once 'services/TokenService.php'; 
require_once 'utils/ResponseFormatter.php';
require_once 'utils/Validator.php';

class LoginController {
    private $userService;
    private $tokenService;

    public function __construct() {
        $this->userService = new UserService(new User());
        $this->tokenService = new TokenService(); 
    }

    /**
     * Handle login request.
     */
    public function handleLogin() {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            http_response_code(400);
            echo ResponseFormatter::error('Username and password are required');
            exit;
        }

        $user = $this->userService->authenticateUser($username, $password);

        if ($user) {
            $token = $this->tokenService->createToken($user['id']);
            echo ResponseFormatter::success(['token' => $token], 'Login successful');
        } else {
            http_response_code(401);
            echo ResponseFormatter::error('Invalid username or password');
        }
    }
}
?>
