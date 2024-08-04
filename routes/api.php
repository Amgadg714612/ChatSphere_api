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
    switch ($method) {
        case 'GET':
            (new UserController())->getUsers();
            break;
        case 'POST':
            (new UserController())->createUser();
            break;
        case 'PUT':
            (new UserController())->updateUser();
            break;
        case 'DELETE':
            (new UserController())->deleteUser();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

// Handle group requests
function handleGroupRequest($method) {
    switch ($method) {
        case 'GET':
            (new GroupController())->getGroups();
            break;
        case 'POST':
            (new GroupController())->createGroup();
            break;
        case 'DELETE':
            (new GroupController())->deleteGroup();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

// Handle message requests
function handleMessageRequest($method) {
    switch ($method) {
        case 'POST':
            (new MessageController())->sendMessage();
            break;
        case 'GET':
            (new MessageController())->getMessages();
            break;
        case 'PUT':
            (new MessageController())->updateMessage();
            break;
        case 'DELETE':
            (new MessageController())->deleteMessage();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

// Handle conversation requests
function handleConversationRequest($method) {
    switch ($method) {
        case 'POST':
            (new ConversationController())->createConversation();
            break;
        case 'GET':
            (new ConversationController())->getConversations();
            break;
        case 'DELETE':
            (new ConversationController())->deleteConversation();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}
