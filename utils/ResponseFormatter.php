<?php
class ResponseFormatter {
    
    /**
     * Format a success response.
     * 
     * @param mixed $data The data to include in the response.
     * @param string $message Optional message to include in the response.
     * @return string JSON encoded success response.
     */
    public static function success($data, $message = 'Request was successful') {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
        return json_encode($response);
    }

    /**
     * Format an error response.
     * 
     * @param string $message The error message to include in the response.
     * @param int $statusCode Optional HTTP status code.
     * @return string JSON encoded error response.
     */
    public static function error($message, $statusCode = 400) {
        http_response_code($statusCode);
        $response = [
            'status' => 'error',
            'message' => $message
        ];
        return json_encode($response);
    }

    /**
     * Format a validation error response.
     * 
     * @param array $errors Array of validation errors.
     * @return string JSON encoded validation error response.
     */
    public static function validationError($errors) {
        http_response_code(422);
        $response = [
            'status' => 'validation_error',
            'errors' => $errors
        ];
        return json_encode($response);
    }
}
?>
