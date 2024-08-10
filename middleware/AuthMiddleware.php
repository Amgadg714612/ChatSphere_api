<?php

require_once 'utils/ResponseFormatter.php';
require_once 'utils/Logger.php';
require_once 'services/TokenService.php'; // تأكد من استيراد خدمة التوكن

class AuthMiddleware {

    /**
     * Authenticate the user by checking for a valid token.
     */
    public static function authenticate() {
        // تحقق من وجود توكن في الرؤوس
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;
        if ($authHeader) {
            // استخراج التوكن من رأس الطلب
            $token = str_replace('Bearer ', '', $authHeader);
            $tokenService = new TokenService(); // إنشاء كائن لخدمة التوكن
            // تحقق من التوكن في قاعدة البيانات
            if ($tokenService->validateToken($token)) {
                // يمكنك هنا إعداد الجلسة للمستخدم إذا لزم الأمر
                $_SESSION['user_id'] = self::getUserIdFromToken($token); // إعداد معرف المستخدم في الجلسة
                return true; // التوكن صحيح
            }
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            Logger::error('Unauthorized access attempt');
            echo ResponseFormatter::error('Unauthorized', 401);
            exit();
        }
    }

    /**
     * Authorize the user by checking their role.
     * 
     * @param string $role The required role for access.
     */
    public static function authorize($role) {
        self::authenticate();
        if ($_SESSION['role'] !== $role) {
            Logger::error('Forbidden access attempt by user ' . $_SESSION['user_id']);
            echo ResponseFormatter::error('Forbidden', 403);
            exit();
        }
    }

    /**
     * Get user ID from the token.
     * 
     * @param string $token
     * @return int|null The user ID or null if not found.
     */
    private static function getUserIdFromToken($token) {
        $tokenService = new TokenService(); // إنشاء كائن لخدمة التوكن
        return $tokenService->getUserIdFromToken($token);
    }
}
?>
