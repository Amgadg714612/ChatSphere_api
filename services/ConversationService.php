<?php

require_once 'models/Conversation.php'; // Import the Conversation model

class ConversationService {

    private $conversationModel;

    public function __construct() {
        $this->conversationModel = new Conversation(); // Initialize the Conversation model
    }

    // Create a new conversation
    public function createConversation($title, $participants) {
        try {
            // Create the conversation
            $conversationId = $this->conversationModel->createConversation($title, $participants);
            return $conversationId; // Return the ID of the newly created conversation
        } catch (Exception $e) {
            throw new Exception('Error creating conversation: ' . $e->getMessage());
        }
    }

    // Get details of a conversation by its ID
    public function getConversationById($conversationId) {
        try {
            $conversation = $this->conversationModel->getConversationById($conversationId);
            if ($conversation) {
                return $conversation;
            } else {
                throw new Exception('Conversation not found.');
            }
        } catch (Exception $e) {
            throw new Exception('Error fetching conversation details: ' . $e->getMessage());
        }
    }

    // Update conversation details
    public function updateConversation($conversationId, $title) {
        try {
            $this->conversationModel->updateConversation($conversationId, $title);
            return true; // Return true if update is successful
        } catch (Exception $e) {
            throw new Exception('Error updating conversation: ' . $e->getMessage());
        }
    }

    // Delete a conversation
    public function deleteConversation($conversationId) {
        try {
            $this->conversationModel->deleteConversation($conversationId);
            return true; // Return true if deletion is successful
        } catch (Exception $e) {
            throw new Exception('Error deleting conversation: ' . $e->getMessage());
        }
    }

    // Add a participant to a conversation
    public function addParticipant($conversationId, $userId) {
        try {
            $this->conversationModel->addParticipant($conversationId, $userId);
            return true; // Return true if the participant was added successfully
        } catch (Exception $e) {
            throw new Exception('Error adding participant to conversation: ' . $e->getMessage());
        }
    }

    // Remove a participant from a conversation
    public function removeParticipant($conversationId, $userId) {
        try {
            $this->conversationModel->removeParticipant($conversationId, $userId);
            return true; // Return true if the participant was removed successfully
        } catch (Exception $e) {
            throw new Exception('Error removing participant from conversation: ' . $e->getMessage());
        }
    }

    // Get the list of participants in a conversation
    public function getParticipants($conversationId) {
        try {
            return $this->conversationModel->getParticipants($conversationId);
        } catch (Exception $e) {
            throw new Exception('Error fetching conversation participants: ' . $e->getMessage());
        }
    }
    public function getAllConversations() {
        return $this->conversationModel->fetchAll(); // fetchAll() should be defined in the Conversation model
    }
   
}
