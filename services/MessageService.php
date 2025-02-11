<?php

require_once 'models/Message.php'; // Import the Message model

class MessageService {
    private $messageModel;
    public function __construct() {
        $this->messageModel = new Message(); // Initialize the Message model
    }

    // Send a new message
    public function sendMessage($conversationId, $senderId, $messageContent) {
        try {
            // Create the message
            $messageId = $this->messageModel->createMessage($conversationId, $senderId, $messageContent);
            return $messageId; // Return the ID of the newly created message
        } catch (Exception $e) {
            throw new Exception('Error sending message: ' . $e->getMessage());
        }
    }

     // Send a new message
     public function sendMessageoneTOone($receiver_id,$senderId, $messageContent) {
        try {
            // Create the message
            $messageId = $this->messageModel->createMessageoneTone($senderId,$receiver_id, $messageContent);
            return $messageId; // Return the ID of the newly created message
        } catch (Exception $e) {
            echo ResponseFormatter::error( $e->getMessage());
        }
    }

    // Get messages for a specific conversation
    public function getMessages($conversationId) {
        try {
            $messages = $this->messageModel->getMessagesByConversationId($conversationId);
            if ($messages) {
                return $messages;
            } else {
                throw new Exception('No messages found for this conversation.');
            }
        } catch (Exception $e) {
            throw new Exception('Error fetching messages: ' . $e->getMessage());
        }
    }
    // Update a message
    public function updateMessage($messageId, $newContent) {
        try {
            $this->messageModel->updateMessage($messageId, $newContent);
            return true; // Return true if the update is successful
        } catch (Exception $e) {
            throw new Exception('Error updating message: ' . $e->getMessage());
        }
    }

    // Delete a message
    public function deleteMessage($messageId) {
        try {
            $this->messageModel->deleteMessage($messageId);
            return true; // Return true if deletion is successful
        } catch (Exception $e) {
            throw new Exception('Error deleting message: ' . $e->getMessage());
        }
    }

    // Update the read status of a message
    public function updateMessageStatus($messageId, $status) {
        try {
            $this->messageModel->updateMessageStatus($messageId, $status);
            return true; // Return true if the status is updated successfully
        } catch (Exception $e) {
            throw new Exception('Error updating message status: ' . $e->getMessage());
        }
    }

    // Add a reaction to a message
    public function addReaction($messageId, $userId, $reactionType) {
        try {
            $this->messageModel->addReaction($messageId, $userId, $reactionType);
            return true; // Return true if the reaction is added successfully
        } catch (Exception $e) {
            throw new Exception('Error adding reaction to message: ' . $e->getMessage());
        }
    }
}
