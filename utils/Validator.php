<?php

class Validator {

    /**
     * Validate email format.
     * 
     * @param string $email The email address to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate username.
     * 
     * @param string $username The username to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validateUsername($username) {
        // Username must be between 3 and 20 characters long and can include letters, numbers, and underscores.
        return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
    }

    /**
     * Validate password.
     * 
     * @param string $password The password to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validatePassword($password) {
        // Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password);
    }

    /**
     * Validate group name.
     * 
     * @param string $name The group name to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validateGroupName($name) {
        // Group name must be between 3 and 50 characters long and can include letters, numbers, and spaces.
        return preg_match('/^[a-zA-Z0-9 ]{3,50}$/', $name);
    }

    /**
     * Validate message content.
     * 
     * @param string $message The message content to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function validateMessage($message) {
        // Message content must be between 1 and 1000 characters long.
        return strlen($message) > 0 && strlen($message) <= 1000;
    }

    /**
     * Validate conversation type.
     * 
     * @param string $type The type of the conversation (e.g., 'group' or 'individual').
     * @return bool True if valid, false otherwise.
     */
    public static function validateConversationType($type) {
        // Conversation type must be either 'group' or 'individual'.
        return in_array($type, ['group', 'individual']);
    }
}
?>
