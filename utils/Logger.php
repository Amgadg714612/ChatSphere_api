<?php

class Logger {
    private static $logFilePath;
    /**
     * Set the path for the log file.
     * 
     * @param string $filePath The path to the log file.
     */
    public static function setLogFilePath($filePath) {
        self::$logFilePath = $filePath;
    }

    /**
     * Write an informational message to the log.
     * 
     * @param string $message The message to log.
     */
    public static function info($message) {
        self::log('INFO', $message);
    }

    /**
     * Write a warning message to the log.
     * 
     * @param string $message The message to log.
     */
    public static function warning($message) {
        self::log('WARNING', $message);
    }

    /**
     * Write an error message to the log.
     * 
     * @param string $message The message to log.
     */
    public static function error($message) {
        self::log('ERROR', $message);
    }

    /**
     * Write a log entry to the log file.
     * 
     * @param string $level The log level (e.g., INFO, WARNING, ERROR).
     * @param string $message The message to log.
     */
    private static function log($level, $message) {
        if (!self::$logFilePath) {
            throw new Exception('Log file path is not set.');
        }

        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;

        file_put_contents(self::$logFilePath, $formattedMessage, FILE_APPEND);
        // Ensure the directory exists
        $logDirectory = dirname(self::$logFilePath);
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory, 0777, true);
        }

        // Write the log message to the file
        if (file_put_contents(self::$logFilePath, $formattedMessage, FILE_APPEND) === false) {
            throw new Exception('Failed to write to log file.');
        }
    }
}
?>
