<?php

require_once 'services/GroupService.php';
require_once 'utils/ResponseFormatter.php';

class GroupController {
    private $groupService;

    public function __construct(GroupService $groupService) {
        $this->groupService = $groupService;
    }

    public function handleRequest($method, $params = []) {
        switch ($method) {
            case 'GET':
                if (isset($params['id'])) {
                    $this->getGroup($params['id']);
                } else {
                    // If no ID is provided, you might want to return a list of groups
                    $this->getAllGroups();
                }
                break;
            case 'POST':
                $this->createGroup();
                break;
            case 'PUT':
                if (isset($params['id'])) {
                    $this->updateGroup($params['id']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Group ID is required']);
                }
                break;
            case 'DELETE':
                if (isset($params['id'])) {
                    $this->deleteGroup($params['id']);
                } else {
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Group ID is required']);
                }
                break;
            default:
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method Not Allowed']);
        }
    }

    /**
     * Handle request to create a new group.
     */


    public function getAllGroups() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        try {
            $groups = $this->groupService->getAllGroups();
            http_response_code(200); // OK
            echo json_encode(['groups' => $groups]);
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function createGroup() {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get the input data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input data
        if (empty($data['name']) || empty($data['description'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Name and description are required']);
            return;
        }

        // Create the group
        try {
            $groupId = $this->groupService->createGroup($data['name'],$data['description']);
            if ($groupId) {
                http_response_code(201); // Created
                echo json_encode(['success' => 'Group created successfully', 'groupId' => $groupId]);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to create group']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle request to get group details.
     * 
     * @param int $groupId The ID of the group to retrieve.
     */
    public function getGroup($groupId) {
        // Check if the request method is GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Retrieve the group details
        try {
            $group = $this->groupService->getGroup($groupId);
            if ($group) {
                http_response_code(200); // OK
                echo json_encode(['group' => $group]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'Group not found']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle request to update group details.
     * 
     * @param int $groupId The ID of the group to update.
     */
    public function updateGroup($groupId) {
        // Check if the request method is PUT
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get the input data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input data
        if (empty($data['name']) || empty($data['description'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Name and description are required']);
            return;
        }

        // Update the group
        try {
            $success = $this->groupService->updateGroup($groupId,$data['name'],$data['description']);
            if ($success) {
                http_response_code(200); // OK
                echo json_encode(['success' => 'Group updated successfully']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to update group']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle request to delete a group.
     * 
     * @param int $groupId The ID of the group to delete.
     */
    public function deleteGroup($groupId) {
        // Check if the request method is DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Delete the group
        try {
            $success = $this->groupService->deleteGroup($groupId);
            if ($success) {
                http_response_code(200); // OK
                echo json_encode(['success' => 'Group deleted successfully']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to delete group']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>
