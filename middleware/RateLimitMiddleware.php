<?php

require_once 'utils/ResponseFormatter.php';
require_once 'utils/Logger.php';

class RateLimitMiddleware {
    private static $rateLimit = 100; // Maximum requests
    private static $timeWindow = 3600; // Time window in seconds (1 hour)

    public static function handle() {
        session_start();
        
        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }

        $currentTime = time();
        $_SESSION['rate_limit'] = array_filter($_SESSION['rate_limit'], function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) < self::$timeWindow;
        });

        if (count($_SESSION['rate_limit']) >= self::$rateLimit) {
            Logger::error('Rate limit exceeded by user ' . ($_SESSION['user_id'] ?? 'guest'));
            echo ResponseFormatter::error('Rate limit exceeded', 429);
            exit();
        }

        $_SESSION['rate_limit'][] = $currentTime;
    }
}
?>
