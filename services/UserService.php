<?php

require_once 'models/User.php'; // Import the User model
require_once 'utils/ResponseFormatter.php';

class UserService {

    private $userModel;
    public function __construct() {
        $this->userModel = new User(); // Initialize the User model
    }
////////////////////////////////////////////////////////////////login user /////////////////////////////////////
    // Authenticate a user by username and password
    public function authenticateUser($username, $password) {
        try {
            $user = $this->userModel->getUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Authentication successful
            } else {
                return false; // Invalid credentials
            }
        } catch (Exception $e) {
            throw new Exception('Error authenticating user: ' . $e->getMessage());
        }
    }
 
    ////////////////////////////////////////////////////////// SignUp//////////////////////////////////////////////
    // Register a new user
    public function registerUser($username, $password, $email) {
        try {
            // Check if the username or email already exists
            if ($this->userModel->getUserByUsername($username) || $this->userModel->getUserByEmail($email)) {
              echo  ResponseFormatter::success($username,'Username or email already exists.');
            }

            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            // Create the user
            $userId = $this->userModel->createUser($username, $hashedPassword, $email);
            return $userId; // Return the ID of the newly created user
        } catch (Exception $e) {
            echo  ResponseFormatter::success($e->getMessage(),'Error registering user: ');
        }
    }
//////////////////////////////////////////////////////////////////////////Update user details////////////////////////////
    // Update user details
    public function updateUser($userId, $username, $email, $password = null) {
        try {
            // If password is provided, hash it
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $this->userModel->updateUser($userId, $username, $email, $hashedPassword);
            } else {
                $this->userModel->updateUser($userId, $username, $email);
            }
            return true; // Return true if update is successful
        } catch (Exception $e) {
            throw new Exception('Error updating user: ' . $e->getMessage());
        }
    }
/////////////////////////////////////////////////////////    // Delete a user////////////////////////////
    // Delete a user
    public function deleteUser($userId) {
        try {
            $this->userModel->deleteUser($userId);
            return true; // Return true if deletion is successful
        } catch (Exception $e) {
            throw new Exception('Error deleting user: ' . $e->getMessage());
        }
    }

    ///////////////////////////////////
    public function createUser($data) {
        // Perform additional business logic or 
       
        $errors = [];
        // Validate the username
        if (empty($data['username']) || !Validator::validateUsername($data['username'])) {
            $errors['username'] = 'Invalid username';
        }
        // Validate the email
        if (empty($data['email']) || !Validator::validateEmail($data['email'])) {
            $errors['email'] = 'Invalid email';
        } else {
            // Check if the email is already registered
            if ($this->userModel->emailExists($data['email'])) {
                $errors['email'] = 'Email is already registered';
            }
        }
        // Validate the password
        // if (empty($data['password']) || !Validator::validatePassword($data['password'])) {
        //     $errors['password'] = 'Invalid password';
        // }
        // Check if there are any validation errors
        if (!empty($errors)) {
            // Log validation errors
            Logger::warning('User creation failed due to validation errors: ' . json_encode($errors));
            return false;
        }
        // Additional business logic can be added here
        // Delegate to the User model to perform the actual creation
        try {
         
            $userId = $this->userModel->createUser($data['username'],$data['email'],$data['password']);
            // Log successful user creation
            Logger::info('User created successfully with ID: ' . $userId);
            return $userId;
        } catch (Exception $e) {
            // Log the exception
            Logger::error('Exception occurred while creating user: ' . $e->getMessage());
            return false;
        }
    }
    // Get user details by ID
    public function getUserById($userId) {
        try {
            return $this->userModel->getUserById($userId);
        } catch (Exception $e) {
            throw new Exception('Error fetching user details: ' . $e->getMessage());
        }
    }
    public function getUserByEmail($email) {
        try {
            return $this->userModel->getUserByEmail($email);
        } catch (Exception $e) {
            throw new Exception('Error fetching user details: ' . $e->getMessage());
        }
    }
    public function getUserByUserName($username) {
        try {
            return $this->userModel->getUserByUserName($username);
        } catch (Exception $e) {
        // ResponseFormatter::error('Error fetching user details:s', 1054);
            throw new Exception('Error fetching user details: ' . $e->getMessage());
        }
    }
    public function getUserIdByUserEmail($email) {
        try {
            return $this->userModel-> getUserIdByEmail($email);
        } catch (Exception $e) {
        // ResponseFormatter::error('Error fetching user details:s', 1054);
            ResponseFormatter::error('Error fetching user details: +.'. $e->getMessage()+' ',1054);
        }
    }
}
