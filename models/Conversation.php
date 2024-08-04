<?php

require_once 'config/config.php'; // Import configuration and database connection settings

class Conversation {

    private $pdo;

    public function __construct() {
        global $pdo; // Use the PDO instance from the config file
        $this->pdo = $pdo;
    }
    public function fetchAll() {
        $stmt = $this->pdo->query("SELECT * FROM conversations");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Retrieve all conversations for a specific user
    public function getConversationsByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT c.id, c.type, c.created_at, 
                       GROUP_CONCAT(DISTINCT u.id) AS participant_ids,
                       GROUP_CONCAT(DISTINCT u.username) AS participant_usernames
                FROM conversations c
                JOIN conversation_participants cp ON c.id = cp.conversation_id
                JOIN users u ON cp.user_id = u.id
                WHERE cp.user_id = :user_id
                GROUP BY c.id
            ");
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching conversations: ' . $e->getMessage());
        }
    }

    // Retrieve a specific conversation by ID
    public function getConversationById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, type, created_at FROM conversations WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching conversation: ' . $e->getMessage());
        }
    }

    // Create a new conversation
    public function createConversation($type, $participants) {
        try {
            // Begin a transaction
            $this->pdo->beginTransaction();

            // Insert the conversation
            $stmt = $this->pdo->prepare("INSERT INTO conversations (type) VALUES (:type)");
            $stmt->execute(['type' => $type]);
            $conversationId = $this->pdo->lastInsertId();

            // Insert the participants
            $stmt = $this->pdo->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:conversation_id, :user_id)");
            foreach ($participants as $userId) {
                $stmt->execute([
                    'conversation_id' => $conversationId,
                    'user_id' => $userId
                ]);
            }

            // Commit the transaction
            $this->pdo->commit();

            return $conversationId; // Return the ID of the newly created conversation
        } catch (PDOException $e) {
            // Rollback the transaction in case of error
            $this->pdo->rollBack();
            throw new Exception('Error creating conversation: ' . $e->getMessage());
        }
    }

    // Update a specific conversation
    public function updateConversation($id, $type) {
        try {
            $stmt = $this->pdo->prepare("UPDATE conversations SET type = :type WHERE id = :id");
            $stmt->execute([
                'type' => $type,
                'id' => $id
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error updating conversation: ' . $e->getMessage());
        }
    }

    // Delete a specific conversation
    public function deleteConversation($id) {
        try {
            // Begin a transaction
            $this->pdo->beginTransaction();
            // Delete the conversation participants
            $stmt = $this->pdo->prepare("DELETE FROM conversation_participants WHERE conversation_id = :id");
            $stmt->execute(['id' => $id]);

            // Delete the conversation
            $stmt = $this->pdo->prepare("DELETE FROM conversations WHERE id = :id");
            $stmt->execute(['id' => $id]);

            // Commit the transaction
            $this->pdo->commit();

            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            // Rollback the transaction in case of error
            $this->pdo->rollBack();
            throw new Exception('Error deleting conversation: ' . $e->getMessage());
        }
    }


    // Add a participant to a conversation
    public function addParticipant($conversationId, $participantId) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO conversation_participants (conversation_id, user_id) VALUES (:conversation_id, :user_id)");
            $stmt->execute([
                'conversation_id' => $conversationId,
                'user_id' => $participantId
            ]);
        } catch (PDOException $e) {
            throw new Exception('Error adding participant to conversation: ' . $e->getMessage());
        }
    }

    // Remove a participant from a conversation
    public function removeParticipant($conversationId, $participantId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM conversation_participants WHERE conversation_id = :conversation_id AND user_id = :user_id");
            $stmt->execute([
                'conversation_id' => $conversationId,
                'user_id' => $participantId
            ]);
            return $stmt->rowCount(); // Return the number of rows affected
        } catch (PDOException $e) {
            throw new Exception('Error removing participant from conversation: ' . $e->getMessage());
        }
    }

    // Get participants of a conversation
    public function getParticipants($conversationId) {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM conversation_participants WHERE conversation_id = :conversation_id");
            $stmt->execute(['conversation_id' => $conversationId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Error fetching participants: ' . $e->getMessage());
        }
    }
}

