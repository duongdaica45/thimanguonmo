<?php
require __DIR__ . '/db.php';

$email = 'admin@gmail.com';
$password = password_hash('123456', PASSWORD_BCRYPT);

$sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':email' => $email,
    ':password' => $password
]);

echo "✅ Tạo admin user thành công";
