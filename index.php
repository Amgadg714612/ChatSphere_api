
<?php
require_once 'config/config.php';
require_once 'controllers/UserController.php';
require_once 'controllers/GroupController.php';
require_once 'controllers/MessageController.php';
require_once 'controllers/ConversationController.php';
require_once 'controllers/GroupMessageController.php';
// # OF ERROR 403  // Unauthorized 
//  # OF  ERROR 401 Token is required 
// # OF ERROR  405  Method Not Allowed 
// # of error  404 objects not found 
// # of error 500 Internal Server Error
// Handle the incoming request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestEndpoint = $_SERVER['REQUEST_URI'];
// Remove any query parameters from the endpoint
//  $requestEndpoint = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
 $requestEndpoint = strtok($requestEndpoint, '?');
// Dispatch the request to the appropriate controller
switch ($requestEndpoint) {
case '/ChatSphere/ChatSphere_api/chat-api/login':
            handleLoginRequest($requestMethod);
            break;
     case '/ChatSphere/ChatSphere_api/chat-api/signup': // New endpoint for signup
                handleSignupRequest($requestMethod);
                break;

    case '/ChatSphere/ChatSphere_api/chat-api/users':
        // token  ----------->->->>
        handleUserRequest($requestMethod);
        break;
    case '/ChatSphere/ChatSphere_api/chat-api/groups':
                // token  ----------->->->>

        handleGroupRequest($requestMethod);
        break;
        case '/ChatSphere/ChatSphere_api/chat-api/groupsMessage':
            // token  ----------->->->>

    handleMessageRequesttogroup($requestMethod)
;    break;
    case '/ChatSphere/ChatSphere_api/chat-api/messages':
                // token  ----------->->->>
        handleMessageRequest($requestMethod);
        break;
    case '/ChatSphere/ChatSphere_api/chat-api/conversations':
                // token  ----------->->->>
        handleConversationRequest($requestMethod);
        break;
    
    default:
        // Handle invalid or unsupported endpoint
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found main ']);
        break;
}

// Handle user requests
function handleUserRequest($method) {
    $controller = new UserController();
   $controller->handleRequest($method);
}

// Handle group requests
function handleGroupRequest($method) {
      // الحصول على التوكن من الهيدر Authorization
      $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
      if (!$authHeader) {
        echo ResponseFormatter::error('Token is required',401);
          exit;
      }
      // تحقق من التوكن واستخرج userId
      $tokenService = new TokenService();
      $userId = $tokenService->getUserIdFromToken($authHeader);
      if ($userId === null) {
        echo ResponseFormatter::error('Invalid token NOT END',403);
          exit;
      }
  
      // إعداد المعاملات
      $params = [];
      if (isset($_GET['id'])) {
          $params['id'] = (int)$_GET['id']; // تأكد من أن ID هو عدد صحيح
      }
      // تهيئة خدمات المجموعات
      $groupService = new GroupService(new Group()); // تأكد من تهيئة الخدمة بشكل صحيح
      $groupController = new GroupController($groupService);
      $data = json_decode(file_get_contents('php://input'), true);
      $groupController->handleRequest($method, $params, $userId);
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
    $messageService = new MessageService(new Message());
    $messageController = new MessageController($messageService);
    $messageController->handleRequest($method, $params);
}

// Handle message requests
function handleMessageRequesttogroup($method) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
    if (!$authHeader) {
        echo ResponseFormatter::error('Token is required', 401);
        exit;
    }

    // تحقق من التوكن واستخرج userId
    $tokenService = new TokenService();
    $userId = $tokenService->getUserIdFromToken($authHeader);
    if ($userId === null) {
        echo ResponseFormatter::error('Invalid token', 403);
        exit;
    }

    // إعداد المعاملات
    $params = [];
    if (isset($_GET['messageId'])) {
        $params['messageId'] = (int)$_GET['messageId']; // تأكد من أن messageId هو عدد صحيح
    }
    $pdo = require 'config/config.php'; 
    $messageModel = new GroupMessageModel($pdo);
    $messageService = new GroupMessageService($messageModel);
    $messageController = new GroupMessageController($messageService);
    // معالجة الطلبات
    $messageController->handleRequest($method, $params, $userId);
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
function handleLoginRequest($method) {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['email']) && isset($data['password'])) {
            $email = $data['email'];
            $password = $data['password'];
            $userController = new UserController();
            $response = $userController->login($email, $password);
            echo $response;
        } else {
            echo ResponseFormatter::error('Username  or email , password required ',400);
             
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }
}
// Handle signup requests
function handleSignupRequest($method) {
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['username']) && isset($data['email']) && isset($data['password'])) {
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];
            $userController = new UserController();
            $response = $userController->signup($username, $email, $password);
            echo $response;
        } else {
        
            echo ResponseFormatter::error('Username, email, and password required', 400);
        }
    } else {
      ;
        echo ResponseFormatter::error('Method Not Allowed', 405);
    }
}
