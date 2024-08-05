<?php

require_once 'models/Group.php'; // Import the Group model

class GroupService {

    private $groupModel;

    public function __construct() {
        $this->groupModel = new Group(); // Initialize the Group model
    }

    // Create a new group
    public function createGroup($name, $description) {
        try {
            // Create the group and return its ID
            $groupId = $this->groupModel->createGroup($name, $description);
            return $groupId;
        } catch (Exception $e) {
            throw new Exception('Error creating group: ' . $e->getMessage());
        }
    }


    // Retrieve group details by ID
    public function getGroupById($groupId) {
        try {
            return $this->groupModel->getGroupById($groupId);
        } catch (Exception $e) {
            throw new Exception('Error fetching group details: ' . $e->getMessage());
        }
    }

    // Retrieve all groups
    public function getAllGroups() {
        try {
            return $this->groupModel->getAllGroups();
        } catch (Exception $e) {
            throw new Exception('Error fetching groups: ' . $e->getMessage());
        }
    }

    // Update group details
    public function updateGroup($groupId, $name, $description) {
        try {
            $this->groupModel->updateGroup($groupId, $name, $description);
            return true; // Return true if update is successful
        } catch (Exception $e) {
            throw new Exception('Error updating group: ' . $e->getMessage());
        }
    }

    // Delete a group
    public function deleteGroup($groupId) {
        try {
            $this->groupModel->deleteGroup($groupId);
            return true; // Return true if deletion is successful
        } catch (Exception $e) {
            throw new Exception('Error deleting group: ' . $e->getMessage());
        }
    }
    public function getGroup($groupId) {
        try {
            return $this->groupModel->getGroupById($groupId);
        } catch (Exception $e) {
            // Handle or log the exception
            throw new Exception('Error retrieving group: ' . $e->getMessage());
        }
    }
    public function addMemberToGroup($groupId, $userId) {
        return $this->groupModel->addMemberToGroup($groupId, $userId);
    }
}
