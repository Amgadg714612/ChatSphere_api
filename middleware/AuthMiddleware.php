<?php

require_once 'utils/ResponseFormatter.php';
require_once 'utils/Logger.php';

class AuthMiddleware {

    public static function authenticate() {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            Logger::error('Unauthorized access attempt');
            echo ResponseFormatter::error('Unauthorized', 401);
            exit();
        }
    }

    public static function authorize($role) {
        self::authenticate();

        if ($_SESSION['role'] !== $role) {
            Logger::error('Forbidden access attempt by user ' . $_SESSION['user_id']);
            echo ResponseFormatter::error('Forbidden', 403);
            exit();
        }
    }
}
?>
