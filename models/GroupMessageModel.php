<?php

class GroupMessageModel {
    private $pdo;

    public function __construct() {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }

    // إرسال رسالة جديدة إلى المجموعة
    public function sendMessage($senderId, $groupId, $message, $replyTo = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO group_messages (sender_id, group_id, message, reply_to)
                VALUES (:sender_id, :group_id, :message, :reply_to)
            ");
            $stmt->execute([
                'sender_id' => $senderId,
                'group_id' => $groupId,
                'message' => $message,
                'reply_to' => $replyTo
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Error sending message: ' . $e->getMessage());
        }
    }

    // استرجاع الرسائل الخاصة بالمجموعة
    public function getMessagesByGroupId($groupId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM group_messages WHERE group_id = :group_id ORDER BY created_at ASC
            ");
            $stmt->execute(['group_id' => $groupId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching messages: ' . $e->getMessage());
        }
    }

    // استرجاع رسالة معينة بناءً على ID
    public function getMessageById($messageId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM group_messages WHERE id = :id
            ");
            $stmt->execute(['id' => $messageId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching message: ' . $e->getMessage());
        }
    }
}
?>
