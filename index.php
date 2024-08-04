<?php
require_once 'config/config.php';
require_once 'controllers/UserController.php';
require_once 'controllers/GroupController.php';
require_once 'controllers/MessageController.php';
require_once 'controllers/ConversationController.php';

// Handle the incoming request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestEndpoint = $_SERVER['REQUEST_URI'];

// Remove any query parameters from the endpoint
$requestEndpoint = strtok($requestEndpoint, '?');

// Dispatch the request to the appropriate controller
switch ($requestEndpoint) {
    case '/chat-api/users':
        handleUserRequest($requestMethod);
        break;
    case '/chat-api/groups':
        handleGroupRequest($requestMethod);
        break;
    case '/chat-api/messages':
        handleMessageRequest($requestMethod);
        break;
    case '/chat-api/conversations':
        handleConversationRequest($requestMethod);
        break;
    default:
        // Handle invalid or unsupported endpoint
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Handle user requests
function handleUserRequest($method) {
    $controller = new UserController();
   $controller->handleRequest($method);
}

// Handle group requests
function handleGroupRequest($method) {
    $params = [];
    if (isset($_GET['id'])) {
        $params['id'] = (int)$_GET['id']; // Ensure ID is an integer
    }

    $groupService = new GroupService(new Group()); // Ensure service is instantiated correctly
    $groupController = new GroupController($groupService);
    $groupController->handleRequest($method, $params);
}

// Handle message requests
function handleMessageRequest($method) {
    $params = [];
    if (isset($_GET['conversationId'])) {
        $params['conversationId'] = (int)$_GET['conversationId']; // Ensure ID is an integer
    }
    if (isset($_GET['messageId'])) {
        $params['messageId'] = (int)$_GET['messageId']; // Ensure ID is an integer
    }

    $pdo = require 'config/config.php'; // Assuming this returns a PDO instance
    $messageModel = new Message($pdo);
    $messageService = new MessageService($messageModel);
    $messageController = new MessageController($messageService);
    $messageController->handleRequest($method, $params);
}

// Handle conversation requests
function handleConversationRequest($method) {
    $params = [];
    if (isset($_GET['id'])) {
        $params['id'] = (int)$_GET['id']; // Ensure ID is an integer
    }

    $pdo = require 'config/config.php'; // Assuming this returns a PDO instance
    $conversationModel = new Conversation($pdo);
    $conversationService = new ConversationService($conversationModel);
    $conversationController = new ConversationController($conversationService);

    $conversationController->handleRequest($method, $params);
}
