<?php

require_once 'services/MessageService.php';
require_once 'models/Message.php';
require_once 'utils/ResponseFormatter.php';
class MessageController
{
    private $messageService;
    private $responseformate;

    public function __construct()
    {
        $pdo = require 'config/config.php'; // Assuming this returns a PDO instance
        $messageModel = new Message($pdo);
        $this->messageService = new MessageService($messageModel);
        $this->responseformate = new ResponseFormatter();
    }

    /**
     * Handle the request to send a new message.
     */
    public function handleRequest($method, $params = [])
    {
        switch ($method) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (!empty($data['action']) & $data['action'] === 'sendMessageTogroup') {
                    $this->sendMessagetoGroup($data);
                } elseif (!empty($data['action']) & $data['action'] === 'sendmassgeuserB') {
                    $this->sendmassageOnetoOne($data);
                }
                else {
                    echo 'wkse ';
                }
                break;

            case 'GET':

                if (isset($params['conversationId'])) {
                    $this->getMessages($params['conversationId']);
                } else {
                    http_response_code(400); // Bad Request
                    $this->responseformate->error('Conversation ID is required', 400);
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
                echo ResponseFormatter::error('Method not allowed', 405);
        }
    }

    public function sendMessage()
    {

        $data = json_decode(file_get_contents('php://input'), true);
        // Validate request data
        if (empty($data['conversationId']) || empty($data['senderId'])) {
            $this->responseformate->error('Conversation ID, sender ID, and message content are required', 400);
            return;
        }
        if (empty($data['message'])) {
            $this->responseformate->error('Message content is required', 400);
            return;
        }
        try {
            $messageId = $this->messageService->sendMessage($data['conversationId'], $data['senderId'], $data['message']);
           echo ResponseFormatter::success(['messageId' => $messageId, 'status' => 'Message sent successfully']);
        } catch (Exception $e) {
            echo ResponseFormatter::error(['error' => $e->getMessage()],500);
        }
    }

    public function sendMessagetoGroup($data)
    {


        // Validate request data

        if (empty($data['message'])) {
            echo ResponseFormatter::error('Message content is required',400);
            return;
        }
        try {
            $messageId = $this->messageService->sendMessage($data['conversationId'], $data['senderId'], $data['message']);
            echo ResponseFormatter::success( ['messageId' => $messageId, 'status' => 'Message sent successfully'], 'Message sent successfully');
        } catch (Exception $e) {
            echo ResponseFormatter::error($e->getMessage(),500);
        }
    }

    /**
     * Handle the request to get messages from a conversation.
     * Message updated successfully
     * INSERT INTO `personal_messages`(`id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')
     * Message updated successfully
     */
    public function  sendmassageOnetoOne($data)
    {
        // Validate request data
        if (empty($data['receiver_email']) || empty($data['senderId'])) {
            echo ResponseFormatter:: error('receiver_email, sender ID, and message content are required', 400);
            return;
        }
        if (empty($data['message'])) {
          echo ResponseFormatter::error('Message content is required', 400);
            return;
        }
        
        try {
            $UserService = new UserService();
            $receiver = $UserService->getUserByEmail($data['receiver_email']);
            $receiver_id = $receiver['id'];
            echo  $data['senderId'];
            $messageId = $this->messageService->sendMessageoneTOone($receiver_id, $data['senderId'], $data['message']);
            echo ResponseFormatter::success(['messageId' => $messageId, 'status' => 'Message sent successfully'], 'Message sent successfully');
        } catch (Exception $e) {
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function getMessages($conversationId)
    {
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
    public function deleteMessage($messageId)
    {
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
    public function updateMessage($messageId)
    {
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
                echo ResponseFormatter::success('200', "Message updated successfully");
            } else {
                echo ResponseFormatter::error("Message not found", 404);
            }
        } catch (Exception $e) {
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
