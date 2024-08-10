<?php

require_once 'utils/Logger.php';

// $servername =  "https://alwahash.online" ;//"https://alwahash.online
// $username = "u940853030_adam11";
// $password = "_4263AdamDB_";
// بيانات الاتصال بقاعدة البيانات
$host = '127.0.0.1'; // أو عنوان الخادم الخاص بقاعدة البيانات
$user = 'root';      // اسم المستخدم لقاعدة البيانات
$pass = '';    // كلمة مرور قاعدة البيانات
//////////////////////////////////////////////////////
$db   = 'chatAPI';      // اسم قاعدة البيانات
$charset = 'utf8mb4'; // ترميز الأحرف
$dsn = "mysql:host=$host";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // تفعيل وضع الأخطاء للتعامل مع الاستثناءات
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // وضع الافتراضي لجلب البيانات كـ associative array
    PDO::ATTR_EMULATE_PREPARES   => false, // عدم محاكاة البيانات المعدة
];

Logger::setLogFilePath(__DIR__ . '/logs/app.log');
try {
    // إنشاء كائن PDO للاتصال بالخادم فقط
    $pdo = new PDO($dsn, $user, $pass, $options);
    // إنشاء قاعدة البيانات إذا لم تكن موجودة
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    // استخدام قاعدة البيانات المنشأة
    $pdo->exec("USE `$db`");

    // إنشاء الجداول إذا لم تكن موجودة
    $pdo->exec("
          CREATE TABLE IF NOT EXISTS users (
              id INT AUTO_INCREMENT PRIMARY KEY,
              username VARCHAR(255) NOT NULL UNIQUE,
              email VARCHAR(255) NOT NULL UNIQUE,
              password VARCHAR(255) NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          )
      ");



    $pdo->exec("
          CREATE TABLE IF NOT EXISTS conversations (
              id INT AUTO_INCREMENT PRIMARY KEY,
              type ENUM('group', 'private') NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
          )
      ");

    $pdo->exec("
          CREATE TABLE IF NOT EXISTS groups (
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(255) NOT NULL,
              description TEXT,
              id_userAdmin INT NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             FOREIGN KEY (id_userAdmin) REFERENCES users(id)
          )
      ");


    $pdo->exec("
  CREATE TABLE IF NOT EXISTS personal_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
)
");

    $pdo->exec("
   CREATE TABLE IF NOT EXISTS group_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    group_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES groups(id)
     

)
");


    $pdo->exec("
    CREATE TABLE IF NOT EXISTS tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       expires_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )
");
    $pdo->exec("
CREATE TABLE IF NOT EXISTS user_groups (
    user_id INT NOT NULL,
    group_id INT NOT NULL,
    permissions SET('read', 'write', 'admin') DEFAULT 'read',
    idUserMember INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idUserMember, group_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (idUserMember) REFERENCES users(id)
)
");
} catch (\PDOException $e) {
    // التعامل مع الأخطاء في حالة فشل الاتصال

    $response['status'] = 'error';
    $response['message'] = 'Error: ' . $e->getMessage();
    // إعادة الرسالة بصيغة JSON
    echo json_encode($response);
}
