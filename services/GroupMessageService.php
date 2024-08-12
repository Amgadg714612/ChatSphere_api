<?php

require_once 'models/GroupMessageModel.php';
require_once 'utils/Logger.php';

class GroupMessageService {
    private $groupMessageModel;

    public function __construct() {
        $this->groupMessageModel = new GroupMessageModel();
    }

    public function deleteMessage($messageId, $userId)
    {
        try {
        } catch (Exception $e) {
            throw new Exception('Failed to delete message: ' . $e->getMessage());
        }
    }
    // إرسال رسالة إلى المجموعة
    public function sendMessageToGroup($senderId, $groupId, $message, $replyTo = null) {
        try {
            $messageId = $this->groupMessageModel->sendMessage($senderId, $groupId, $message, $replyTo);
            return $messageId;
        } catch (Exception $e) {
            Logger::Error('Error sending message to group: ' . $e->getMessage());
            echo ResponseFormatter::error('Error sending message to group', 500);
            exit();
        }
    }

    // استرجاع رسائل المجموعة
    public function getGroupMessages($groupId) {
        try {
            return $this->groupMessageModel->getMessagesByGroupId($groupId);
        } catch (Exception $e) {
            Logger::Error('Error fetching group messages: ' . $e->getMessage());
            echo ResponseFormatter::error('Error fetching group messages', 500);
            exit();
        }
    }
}
?>
