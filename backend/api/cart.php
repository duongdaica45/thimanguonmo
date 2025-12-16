<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require 'db.php';

$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? '';

if (!$sessionId) {
    echo json_encode(['success' => false, 'message' => 'Missing session ID']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, ci.quantity 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.session_id = ?
");
$stmt->execute([$sessionId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'items' => $items
]);
