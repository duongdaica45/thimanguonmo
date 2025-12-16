<?php
// Cho phép mọi nguồn truy cập (thay * bằng domain cụ thể nếu cần)
header("Access-Control-Allow-Origin: *");

// Cho phép các method bạn sẽ dùng
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Cho phép các header mà frontend gửi (thêm X-Session-Id nếu bạn gửi header này)
header("Access-Control-Allow-Headers: Content-Type, X-Session-Id");

// Xử lý preflight request OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");
session_start();

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['email'];
    echo json_encode(['success' => true]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
