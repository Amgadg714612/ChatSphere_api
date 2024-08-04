 تم  عمل  مشروع PHP معتمد على بنية MVC ونظام API وإضافة كافة الوظائف المطلوبة وهو API مصممة لدعم تطبيقات الدردشة والمراسلة. توفر هذه الـ API مجموعة من النقاط النهائية لإدارة المستخدمين، المجموعات، الرسائل، والمحادثات.
 يمكن استخدام هذه API في تطوير تطبيقات الدردشة الخاصة بالشركات أو للأغراض الشخصية

هيكلية المشروع

ChatSphere_api/
│
├── config/
│   └── config.php
│
├── controllers/
│   ├── UserController.php
│   ├── GroupController.php
│   ├── MessageController.php
│   └── ConversationController.php
│
├── models/
│   ├── User.php
│   ├── Group.php
│   ├── Message.php
│   └── Conversation.php
│
├── services/
│   ├── UserService.php
│   ├── GroupService.php
│   ├── MessageService.php
│   └── ConversationService.php
│
├── utils/
│   ├── ResponseFormatter.php
│   └── Validator.php
│
├── middleware/
│   ├── AuthMiddleware.php
│   └── RateLimitMiddleware.php
│
├── index.php
└── 


### ملفات المشروع


بالطبع! إليك وصف مختصر لكل ملف في مشروع الـ API:

1. index.php: نقطة الدخول الرئيسية للتطبيق التي تقوم بتوجيه الطلبات إلى وحدة التحكم المناسبة بناءً على نقطة النهاية وطريقة الطلب.

2. config/config.php: ملف تكوين يتضمن إعدادات الاتصال بقاعدة البيانات والبيانات الأساسية الأخرى.

3.controllers/UserController.php: وحدة تحكم مسؤولة عن إدارة عمليات المستخدمين مثل إنشاء، تحديث، حذف، واسترجاع بيانات المستخدمين.

4. controllers/GroupController.php: وحدة تحكم مسؤولة عن إدارة عمليات المجموعات مثل إنشاء، حذف، واسترجاع بيانات المجموعات.

5.controllers/MessageController.php: وحدة تحكم مسؤولة عن إدارة الرسائل في المحادثات مثل إرسال، تحديث، حذف، واسترجاع الرسائل.

6.controllers/ConversationController.php: وحدة تحكم مسؤولة عن إدارة المحادثات مثل إنشاء، حذف، واسترجاع المحادثات.

7.models/User.php: نموذج البيانات المستخدم لتمثيل وتفاعل مع بيانات المستخدمين في قاعدة البيانات.

8. models/Group.php: نموذج البيانات المستخدم لتمثيل وتفاعل مع بيانات المجموعات في قاعدة البيانات.

9.models/Message.php: نموذج البيانات المستخدم لتمثيل وتفاعل مع بيانات الرسائل في قاعدة البيانات.

10.models/Conversation.php: نموذج البيانات المستخدم لتمثيل وتفاعل مع بيانات المحادثات في قاعدة البيانات.

11.services/UserService.php: خدمة تحتوي على المنطق الخاص بإدارة عمليات المستخدمين مثل التفاعل مع نموذج بيانات المستخدمين.

12.services/GroupService.php: خدمة تحتوي على المنطق الخاص بإدارة عمليات المجموعات مثل التفاعل مع نموذج بيانات المجموعات.

13. services/MessageService.php: خدمة تحتوي على المنطق الخاص بإدارة عمليات الرسائل مثل التفاعل مع نموذج بيانات الرسائل.

14. services/ConversationService.php: خدمة تحتوي على المنطق الخاص بإدارة عمليات المحادثات مثل التفاعل مع نموذج بيانات المحادثات.

15.utils/Validator.php: أدوات مساعدة للتحقق من صحة البيانات المدخلة مثل التحقق من صحة اسم المستخدم، البريد الإلكتروني، وكلمة المرور.

16. utils/ResponseFormatter.php: أدوات مساعدة لتنسيق استجابات الـ API بحيث تكون الاستجابات متسقة وسهلة الفهم.

17.middleware/AuthMiddleware.php: وسيط مسؤول عن التحقق من هوية المستخدم للتأكد من أن الطلبات مصرح بها.

18. middleware/RateLimitMiddleware.php: وسيط مسؤول عن تطبيق قيود على معدل الطلبات لتجنب الاستخدام المفرط.



1. config/config.php
<?php

// Create a new PDO instance
try {
    $pdo = new PDO('mysql:host=localhost;dbname=chat_db', 'username', 'password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

return $pdo;
?>



 2. controllers/UserController.php

php
<?php

require_once 'services/UserService.php';
require_once 'utils/ResponseFormatter.php';

class UserController {
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function handleRequest($method) {
        switch ($method) {
            case 'GET':
                $this->getUser();
                break;
            case 'POST':
                $this->createUser();
                break;
            case 'PUT':
                $this->updateUser();
                break;
            case 'DELETE':
                $this->deleteUser();
                break;
            default:
                http_response_code(405);
                echo ResponseFormatter::error('Method Not Allowed', 405);
        }
    }

    // Define methods getUser(), createUser(), updateUser(), deleteUser()...
}
?>
```

 3. controllers/GroupController.php


<?php

require_once 'services/GroupService.php';
require_once 'utils/ResponseFormatter.php';

class GroupController {
    private $groupService;

    public function __construct(GroupService $groupService) {
        $this->groupService = $groupService;
    }

    public function handleRequest($method, $params = []) {
        $groupId = $params['id'] ?? null;

        switch ($method) {
            case 'POST':
                $this->createGroup();
                break;
            case 'GET':
                if ($groupId) {
                    $this->getGroup($groupId);
                } else {
                    $this->getGroups(); // Make sure getGroups() is defined
                }
                break;
            case 'DELETE':
                if ($groupId) {
                    $this->deleteGroup($groupId);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Group ID is required']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }

    // Define methods createGroup(), getGroups(), getGroup(), deleteGroup()...
}
?>


 4.controllers/MessageController.php
<?php
require_once 'services/MessageService.php';
require_once 'models/Message.php';
class MessageController {
    private $messageService;
    public function __construct() {
        $pdo = require 'config/config.php';
        $messageModel = new Message($pdo);
        $this->messageService = new MessageService($messageModel);
    }

    public function handleRequest($method) {
        switch ($method) {
            case 'POST':
                $this->sendMessage();
                break;
            case 'GET':
                $this->getMessages();
                break;
            case 'PUT':
                $this->updateMessage();
                break;
            case 'DELETE':
                $this->deleteMessage();
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }

    // Define methods sendMessage(), getMessages(), updateMessage(), deleteMessage()...
}
?>

 5.controllers/ConversationController.php

php
<?php

require_once 'services/ConversationService.php';
require_once 'models/Conversation.php';
class ConversationController {
    private $conversationService;
    public function __construct(ConversationService $conversationService) {
        $this->conversationService = $conversationService;
    }

    public function handleRequest($method, $params = []) {
        $conversationId = $params['id'] ?? null;

        switch ($method) {
            case 'POST':
                $this->createConversation();
                break;
            case 'GET':
                if ($conversationId) {
                    $this->getConversation($conversationId);
                } else {
                    $this->getConversations(); // Make sure getConversations() is defined
                }
                break;
            case 'DELETE':
                if ($conversationId) {
                    $this->deleteConversation($conversationId);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Conversation ID is required']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
                break;
        }
    }

    // Define methods createConversation(), getConversations(), getConversation(), deleteConversation()...
}
?>
```

 6.models/User.php
<?php
class User {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Define methods for CRUD operations
}
?>


 7. models/Group.php
<?php
class Group {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Define methods for CRUD operations
}
?>


 8. models/Message.php
<?php
class Message {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // Define methods for CRUD operations
}
?>
 9. models/Conversation.php

<?php

class Conversation {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Define methods for CRUD operations
}
?>


 10. services/UserService.php


<?php

class UserService {
    private $userModel;

    public function __construct(User $userModel) {
        $this->userModel = $userModel;
    }

    // Define methods for user-related operations
}
?>


 11.services/GroupService.php


<?php

class GroupService {
    private $groupModel;

    public function __construct(Group $groupModel) {
        $this->groupModel = $groupModel;
    }

    // Define methods for group-related operations
}
?>
```

 12. services/MessageService.php

php
<?php

class MessageService {
    private $messageModel;

    public function __construct(Message $messageModel) {
        $this->messageModel = $messageModel;
    }

    // Define methods for message-related operations
}
?>
 13.services/ConversationService.php


<?php

class ConversationService {
    private $conversationModel;

    public function __construct(Conversation $conversationModel) {
        $this->conversationModel = $conversationModel;
    }

    // Define methods for conversation-related operations
}
?>


 14.utils/ResponseFormatter.php
<?php

class ResponseFormatter {
    public static function success($data, $message = '') {
        return json_encode(['status' => 'success', 'message' => $message, 'data' => $data]);
    }

    public static function error($message, $statusCode = 400) {
        http_response_code($statusCode);
        return json_encode(['status' => 'error', 'message' => $message]);
    }

    public static function validationError($errors) {
        http_response_code(400);
        return json_encode(['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors]);
    }
}
?>


 15. utils/Validator.php
class Validator {
    public static function validateUsername($username) {
        // Example validation
        return !empty($username) && strlen($username) >= 3;
    }

    public static function validateEmail($email) {
        // Example validation
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password) {
        // Example validation

        return !empty($
