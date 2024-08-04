<?php

require_once 'services/ConversationService.php';
require_once 'models/Conversation.php';

class ConversationController {
    private $conversationService;

    public function __construct() {
        $pdo = require 'config/config.php'; // Assuming this returns a PDO instance
        $conversationModel = new Conversation($pdo);
        $this->conversationService = new ConversationService($conversationModel);
    }



    /**
     * Handle the request to create a new conversation.
     */

     public function handleRequest($method, $params = []) {
        $conversationId = $params['id'] ?? null;

        switch ($method) {
            case 'POST':
                $this->createConversation();
                break;
            case 'GET':
                if ($conversationId) {
                    $this->getConversation($conversationId);
                } else {
                    $this->getConversations();
                }
                break;
            case 'DELETE':
                if ($conversationId) {
                    $this->deleteConversation($conversationId);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Conversation ID is required']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }



    public function createConversation() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate request data
        if (empty($data['type']) || empty($data['participants'])) {
            $this->sendResponse(400, ['error' => 'Conversation type and participants are required']);
            return;
        }

        try {
            $conversationId = $this->conversationService->createConversation($data['type'], $data['participants']);
            $this->sendResponse(201, ['conversationId' => $conversationId, 'status' => 'Conversation created successfully']);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }



    public function getConversations() {
        // Implementation for getting all conversations
        try {
            $conversations = $this->conversationService->getAllConversations();
            $this->sendResponse(200, ['conversations' => $conversations]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Handle the request to get details of a specific conversation.
     */
    public function getConversation($conversationId) {
        if (empty($conversationId)) {
            $this->sendResponse(400, ['error' => 'Conversation ID is required']);
            return;
        }

        try {
            $conversation = $this->conversationService->getConversationById($conversationId);
            $this->sendResponse(200, ['conversation' => $conversation]);
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the request to delete a conversation.
     */
    public function deleteConversation($conversationId) {
        if (empty($conversationId)) {
            $this->sendResponse(400, ['error' => 'Conversation ID is required']);
            return;
        }

        try {
            $success = $this->conversationService->deleteConversation($conversationId);
            if ($success) {
                $this->sendResponse(200, ['status' => 'Conversation deleted successfully']);
            } else {
                $this->sendResponse(404, ['error' => 'Conversation not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle the request to update a conversation.
     */
    public function updateConversation($conversationId) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($conversationId)) {
            $this->sendResponse(400, ['error' => 'Conversation ID is required']);
            return;
        }

        try {
            $success = $this->conversationService->updateConversation($conversationId, $data);
            if ($success) {
                $this->sendResponse(200, ['status' => 'Conversation updated successfully']);
            } else {
                $this->sendResponse(404, ['error' => 'Conversation not found']);
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
