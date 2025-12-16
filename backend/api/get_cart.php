<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Session-Id");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/db.php';

$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? null;

if (!$sessionId) {
    echo json_encode(['success' => false, 'message' => 'Missing session id']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT ci.id, ci.product_id, ci.quantity, p.name, p.price
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.session_id = ?
");
$stmt->execute([$sessionId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'items' => $items]);
