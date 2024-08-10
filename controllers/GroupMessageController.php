<?php

require_once 'services/GroupMessageService.php';
require_once 'utils/ResponseFormatter.php';
require_once 'middlewares/AuthMiddleware.php';
class GroupMessageController
{
    private $groupMessageService;
    public function __construct(GroupMessageService $groupMessageService)
    {
        $this->groupMessageService = $groupMessageService;
    }

    
    public function handleRequest($method, $params = [], $userId)
    {
        switch ($method) {
            case 'GET':
                if (isset($params['groupId'])) {
                    $this->getGroupMessages($params['groupId'], $userId);
                } else {
                    // إذا لم يتم توفير معرف المجموعة، يمكن إعادة قائمة بجميع المجموعات (أو التعامل مع هذا بشكل مختلف).
                    echo ResponseFormatter::error('Group ID is required', 400);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['action']) && $data['action'] === "reply") {
                    $this->replyToMessage($userId, $data);
                } else {
                    $this->sendMessage($userId, $data);
                }
                break;

            case 'DELETE':
                if (isset($params['messageId'])) {
                    $this->deleteMessage($params['messageId'], $userId);
                } else {
                    echo ResponseFormatter::error('Message ID is required', 400);
                }
                break;

            default:
                echo ResponseFormatter::error('Method Not Allowed', 405);
        }
    }

    /**
     * إرسال رسالة إلى المجموعة
     */
    public function sendMessage($userId, $data)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        $groupId = $data['groupId'] ?? null;
        $message = $data['message'] ?? null;

        if (is_null($groupId) || is_null($message)) {
            echo ResponseFormatter::error('Group ID and message are required', 400);
            return;
        }

        try {
            $messageId = $this->groupMessageService->sendMessageToGroup($userId, $groupId, $message);
            echo ResponseFormatter::success(['messageId' => $messageId], 'Message sent successfully');
        } catch (Exception $e) {
            echo ResponseFormatter::error('Failed to send message: ' . $e->getMessage(), 500);
        }
    }

    /**
     * الرد على رسالة في المجموعة
     */
    public function replyToMessage($userId, $data)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        $groupId = $data['groupId'] ?? null;
        $message = $data['message'] ?? null;
        $replyTo = $data['replyTo'] ?? null;

        if (is_null($groupId) || is_null($message) || is_null($replyTo)) {
            echo ResponseFormatter::error('Group ID, message, and replyTo are required', 400);
            return;
        }

        try {
            $messageId = $this->groupMessageService->sendMessageToGroup($userId, $groupId, $message, $replyTo);
            echo ResponseFormatter::success(['messageId' => $messageId], 'Reply sent successfully');
        } catch (Exception $e) {
            echo ResponseFormatter::error('Failed to send reply: ' . $e->getMessage(), 500);
        }
    }

    /**
     * استرجاع رسائل المجموعة
     */
    public function getGroupMessages($groupId, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        try {
            $messages = $this->groupMessageService->getGroupMessages($groupId, $userId);
            echo ResponseFormatter::success(['messages' => $messages], 'Messages retrieved successfully');
        } catch (Exception $e) {
            echo ResponseFormatter::error('Failed to retrieve messages: ' . $e->getMessage(), 500);
        }
    }

    /**
     * حذف رسالة من المجموعة
     */
    public function deleteMessage($messageId, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        try {
            $success = $this->groupMessageService->deleteMessage($messageId, $userId);
            if ($success) {
                echo ResponseFormatter::success([], 'Message deleted successfully');
            } else {
                echo ResponseFormatter::error('Failed to delete message', 500);
            }
        } catch (Exception $e) {
            echo ResponseFormatter::error('Failed to delete message: ' . $e->getMessage(), 500);
        }
    }
}
?>
