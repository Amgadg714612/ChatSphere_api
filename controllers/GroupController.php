<?php

require_once 'services/GroupService.php';
require_once 'utils/ResponseFormatter.php';
require_once 'services/UserService.php';

class GroupController
{
    private $groupService;

    private $userServices;
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
        $this->userServices = new UserService();
    }

    public function handleRequest($method, $params = [], $userId)
    {
        switch ($method) {

            case 'GET':
                if (isset($params['id'])) {
                    $this->getGroup($userId);
                } else {
                    // If no ID is provided, you might want to return a list of groups
                    $this->getAllGroups();
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if ($data['action'] === "addMember") {
                    $userIdadmin = $userId;
                    $this->addMemberToGroup($userIdadmin, $data);
                } 
                
                else
                    $this->createGroup($userId, $data);

                break;
            case 'PUT':
                if (isset($params['id'])) {
                    $this->updateGroup($params['id']);
                } else {
                    // Bad Request
                    echo  ResponseFormatter::error('Group ID is required', 400);
                }
                break;
            case 'DELETE':
                if (isset($params['id'])) {
                    $this->deleteGroup($params['id']);
                } else {
                    // Bad Request
                    echo ResponseFormatter::error('Group ID is required', 400);
                }
                break;
            default:
                // Method Not Allowed
                echo ResponseFormatter::error('Method Not Allowed', 405);
        }
    }

    /**
     * Handle request to create a new group.
     */


    public function getAllGroups()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405); // Method Not Allowed
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        try {
            $groups = $this->groupService->getAllGroups();
            http_response_code(200); // OK
            echo json_encode(['groups' => $groups]);
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function createGroup($iduser, $data)
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Method Not Allowed
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }
        // Get the input data
        // Validate input data
        if (empty($data['name']) || empty($data['description'])) {
            // Bad Request
            echo ResponseFormatter::error('Name and description are required', 400);
            return;
        }
        // Create the group
        try {
            $id_admin = $iduser;
            $groupId = $this->groupService->createGroup($data['name'], $data['description'], $id_admin);
            if ($groupId) {
                // Created
                $this->groupService->addMemberToGroup($groupId, $iduser, 'admin', $iduser);
                echo ResponseFormatter::success(['groupId' => $groupId], 'Group created successfully');
            } else {
                // Internal Server Error
                echo ResponseFormatter::error('Failed to create group', 500);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Handle request to get group details.
     * 
     * @param int $groupId The ID of the group to retrieve.
     */
    public function getGroup($groupId, $userId)
    {
        // Check if the request method is GET
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }
        // Retrieve the group details
        try {
            $group = $this->groupService->getGroup($groupId, $userId);
            if ($group) {
                echo ResponseFormatter::success(['group' => $group],'this all   Group ');
            } else {
                echo ResponseFormatter::error('Group not found',404);
            // Not Found
      
            }
        } catch (Exception $e) {
           // Internal Server Error
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Handle request to update group details.
     * 
     * @param int $groupId The ID of the group to update.
     */
    public function updateGroup($groupId)
    {
        // Check if the request method is PUT
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405); // Method Not Allowed
            echo ResponseFormatter::error('Method not allowed', 405);
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
            $success = $this->groupService->updateGroup($groupId, $data['name'], $data['description']);
            if ($success) {
                http_response_code(200); // OK
                echo json_encode(['success' => 'Group updated successfully']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to update group']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    /**
     * Handle request to delete a group.
     * 
     * @param int $groupId The ID of the group to delete.
     */
    public function deleteGroup($groupId)
    {
        // Check if the request method is DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405); // Method Not Allowed
            echo ResponseFormatter::error('Method not allowed', 405);
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
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }
    public function addMemberToGroup($userIdadmin, $data)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo ResponseFormatter::error('Method not allowed', 405);
            return;
        }

        $data = $data;
        if (empty($data['groupId']) || empty($data['memberEmail'])) {
            // Bad Request

            echo  ResponseFormatter::error('Group ID and User ID are required', 400);
            return;
        }


        try {

            $EmailUserMember = $data['memberEmail'];
            $idUserMember = $this->userServices->getUserIdByUserEmail($EmailUserMember);
            $groupId = $data['groupId'];
            if (empty($idUserMember)) {
                echo  ResponseFormatter::error('idUseremail not fielnd required', 410);
                return;
            }
            $success = $this->groupService->addMemberToGroup($data['groupId'], $userIdadmin, 'read', $idUserMember);
            if ($success) {
                http_response_code(200); // OK
                echo json_encode(['success' => 'Member added to group successfully']);
            } else {
                http_response_code(500); // Internal Server Error
                echo json_encode(['error' => 'Failed to add member to group']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Internal Server Error
            echo ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
