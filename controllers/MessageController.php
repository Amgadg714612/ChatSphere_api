<?php

require_once 'services/MessageService.php';
require_once 'models/Message.php';

class MessageController {
    private $messageService;

    public function __construct() {
        $pdo = require 'config/config.php'; // Assuming this returns a PDO instance
        $messageModel = new Message($pdo);
        $this->messageService = new MessageService($messageModel);
    }

    /**
     * Handle the request to send a new message.
     
     */

     public function handleRequest($method, $params = []) {
        switch ($method) {
            case 'POST':
                $this->sendMessage();
                break;
            case 'GET':
                if (isset($params['conversationId'])) {
                    $this->getMessages($params['conversationId']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Conversation ID is required']);
                }
                break;
            case 'PUT':
                if (isset($params['messageId'])) {
                    $this->updateMessage($params['messageId']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Message ID is required']);
                }
                break;
            case 'DELETE':
                if (isset($params['messageId'])) {
                    $this->deleteMessage($params['messageId']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Message ID is required']);
                }
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }
    
    public function sendMessage() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate request data
        if (empty($data['conversationId']) || empty($data['senderId']) || empty($data['message'])) {
            $this->sendResponse(400, ['error' => 'Conversation ID, sender ID, and message content are required']);
            return;
        }

        
        if (empty($data['message'])) {
            $this->sendResponse(400, ['error' => 'Message content is required']);
            return;
        }

        try {
            $messageId = $this->messageService->sendMessage($data['conversationId'], $data['senderId'], $data['message']);
            $this->sendResponse(201, ['messageId' => $messageId, 'status' => 'Message sent successfully']);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the request to get messages from a conversation.
     */
    public function getMessages($conversationId) {
        if (empty($conversationId)) {
            $this->sendResponse(400, ['error' => 'Conversation ID is required']);
            return;
        }

        try {
            $messages = $this->messageService->getMessages($conversationId);
            $this->sendResponse(200, ['messages' => $messages]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the request to delete a message.
     */
    public function deleteMessage($messageId) {
        if (empty($messageId)) {
            $this->sendResponse(400, ['error' => 'Message ID is required']);
            return;
        }

        try {
            $success = $this->messageService->deleteMessage($messageId);
            if ($success) {
                $this->sendResponse(200, ['status' => 'Message deleted successfully']);
            } else {
                $this->sendResponse(404, ['error' => 'Message not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the request to update a message.
     */
    public function updateMessage($messageId) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($messageId)) {
            $this->sendResponse(400, ['error' => 'Message ID is required']);
            return;
        }

        if (empty($data['message'])) {
            $this->sendResponse(400, ['error' => 'Message content is required']);
            return;
        }

        try {
            $success = $this->messageService->updateMessage($messageId, $data['message']);
            if ($success) {
                $this->sendResponse(200, ['status' => 'Message updated successfully']);
            } else {
                $this->sendResponse(404, ['error' => 'Message not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send a JSON response with the given status code and data.
     */
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
?>
