<?php

require_once 'models/User.php';
require_once 'services/UserService.php';
require_once 'utils/Validator.php';
require_once 'utils/ResponseFormatter.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'middleware/RateLimitMiddleware.php';

class UserController
{
    private $userService;
    private $tokenService;
    public function __construct()
    {
        $this->userService = new UserService(new User());
        $this->tokenService = new TokenService();
    }

    /**
     * Handle the incoming request based on the HTTP method.
     * 
     * @param string $method The HTTP method of the request.
     */
    public function handleRequest($method)
    {
        // Apply rate limiting for all requests
        RateLimitMiddleware::handle();
        // Authenticate the user for all requests
        AuthMiddleware::authenticate();
        switch ($method) {
            case 'GET':
                $this->getUser();
                break;
            case 'POST':
                $this->createUser();
                break;
            case 'PUT':
                $this->updateUser();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
            default:
                http_response_code(405);
                echo ResponseFormatter::error('Method Not Allowed', 405);
        }
    }

    /**
     * Handle GET request to retrieve a user.
     */
    private function getUser()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo ResponseFormatter::error('User ID is required', 400);
            return;
        }

        $user = $this->userService->getUserById($id);
        if ($user) {
            echo ResponseFormatter::success($user);
        } else {
            echo ResponseFormatter::error('User not found', 404);
        }
    }

    /**
     * Handle POST request to create a new user.
     */
    private function createUser()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $errors = [];
        if (!Validator::validateUsername($data['username'] ?? '')) {
            $errors['username'] = 'Invalid username';
        }
        if (!Validator::validateEmail($data['email'] ?? '')) {
            $errors['email'] = 'Invalid email';
        }
        if (!Validator::validatePassword($data['password'] ?? '')) {
            $errors['password'] = 'Invalid password';
        }
        if (!empty($errors)) {
            echo ResponseFormatter::validationError($errors);
            return;
        }
        $user = $this->userService->registerUser($data['username'], $data['password'], $data['email']);
        // $user = $this->userService->createUser($data);
        if ($user) {
            echo ResponseFormatter::success($user, 'User created successfully');
        } else {
            echo ResponseFormatter::error('Failed to create user');
        }
    }

    /**
     * Handle PUT request to update an existing user.
     */
    private function updateUser()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            echo ResponseFormatter::error('User ID is required', 400);
            return;
        }

        $user = $this->userService->getUserById($id);
        if (!$user) {
            echo ResponseFormatter::error('User not found', 404);
            return;
        }

        $errors = [];
        if (isset($data['username']) && !Validator::validateUsername($data['username'])) {
            $errors['username'] = 'Invalid username';
        }
        if (isset($data['email']) && !Validator::validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email';
        }
        if (isset($data['password']) && !Validator::validatePassword($data['password'])) {
            $errors['password'] = 'Invalid password';
        }

        if (!empty($errors)) {
            echo ResponseFormatter::validationError($errors);
            return;
        }

        $updatedUser = $this->userService->updateUser($id, $data['email'], $data['username']);
        if ($updatedUser) {
            echo ResponseFormatter::success($updatedUser, 'User updated successfully');
        } else {
            echo ResponseFormatter::error('Failed to update user');
        }
    }

    /**
     * Handle DELETE request to remove a user.
     */
    private function deleteUser()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo ResponseFormatter::error('User ID is required', 400);
            return;
        }

        $deleted = $this->userService->deleteUser($id);
        if ($deleted) {
            echo ResponseFormatter::success(null, 'User deleted successfully');
        } else {
            echo ResponseFormatter::error('Failed to delete user', 500);
        }
    }
    public function Signup($username, $email, $password)
    {
        // التحقق من صحة البيانات
        $errors = [];
        if (!Validator::validateUsername($username)) {
            $errors['username'] = 'Invalid username';
        }
        if (!Validator::validateEmail($email)) {
            $errors['email'] = 'Invalid email';
        }
        if (!Validator::validatePassword($password)) {
            $errors['password'] = 'Invalid password" Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
        }
        if (!empty($errors)) {
            return ResponseFormatter::validationError($errors);
        }
        // التحقق من وجود المستخدم بالفعل
        $existingUser = $this->userService->getUserByUsername($username);
        if ($existingUser) {
            return ResponseFormatter::error('Username already exists', 400);
        }
        // إنشاء المستخدم
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->userService->createUser([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        if ($userId) {
            // إنشاء التوكن
            $token = $this->tokenService->createToken($userId);
            return ResponseFormatter::success(['user_id' => $userId, 'token' => $token], 'User registered successfully');
        } else {
            return ResponseFormatter::error('Failed to register user', 500);
        }
    }
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            echo ResponseFormatter::error('Email and password are required', 400);
            return;
        }
        $user = $this->userService->getUserByEmail($email);
        if (!$user) {
            echo ResponseFormatter::error('Invalid email or password', 401);
            return;
        }
        if (password_verify($password, $user['password'])) {
            // Create a token for the user
            $token = $this->tokenService->updateToken($user['id']);
            return ResponseFormatter::success(['token' => $token], 'Login successful');
        } else {
            echo ResponseFormatter::error('Invalid email or password', 401);
        }
    }
}
