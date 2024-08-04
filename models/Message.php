<?php

require_once 'config/config.php'; // Import configuration and database connection settings

class Message {

    private $pdo;

    public function __construct() {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }

    // Retrieve all messages for a specific conversation
    public function getMessagesByConversationId($conversationId) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, conversation_id, user_id, message, created_at FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
            $stmt->execute(['conversation_id' => $conversationId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching messages: ' . $e->getMessage());
        }
    }

    // Retrieve a specific message by ID
    public function getMessageById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, conversation_id, user_id, message, created_at FROM messages WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching message: ' . $e->getMessage());
        }
    }

    // Create a new message
    public function createMessage($conversationId, $userId, $message) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO messages (conversation_id, user_id, message) VALUES (:conversation_id, :user_id, :message)");
            $stmt->execute([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'message' => $message
            ]);
            return $this->pdo->lastInsertId(); // Return the ID of the newly created message
        } catch (PDOException $e) {
            throw new Exception('Error creating message: ' . $e->getMessage());
        }
    }

    // Update a specific message
    public function updateMessage($id, $message) {
        try {
            $stmt = $this->pdo->prepare("UPDATE messages SET message = :message WHERE id = :id");
            $stmt->execute([
                'message' => $message,
                'id' => $id
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error updating message: ' . $e->getMessage());
        }
    }

    // Delete a specific message
    public function deleteMessage($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM messages WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error deleting message: ' . $e->getMessage());
        }
    }

    // Update message status
    public function updateMessageStatus($messageId, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE messages SET status = :status WHERE id = :id");
            $stmt->execute([
                'status' => $status,
                'id' => $messageId
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error updating message status: ' . $e->getMessage());
        }
    }

    // Add a reaction to a message
    public function addReaction($messageId, $userId, $reactionType) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO message_reactions (message_id, user_id, reaction_type) VALUES (:message_id, :user_id, :reaction_type)");
            $stmt->execute([
                'message_id' => $messageId,
                'user_id' => $userId,
                'reaction_type' => $reactionType
            ]);
        } catch (PDOException $e) {
            throw new Exception('Error adding reaction: ' . $e->getMessage());
        }
    }
}
