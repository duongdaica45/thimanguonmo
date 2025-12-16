<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Session-Id");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

require __DIR__ . '/db.php';

$data = json_decode(file_get_contents("php://input"), true);

$productId = $data['id'] ?? null;
$quantity  = $data['quantity'] ?? 1;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'Missing product id']);
    exit;
}

$quantity = intval($quantity);
if ($quantity < 1) $quantity = 1;

$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? uniqid('sess_', true);

try {
    // (Có thể thêm kiểm tra sản phẩm tồn tại nếu muốn)
    
    // Kiểm tra sản phẩm đã có trong cart chưa
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$sessionId, $productId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE id = ?");
        $stmt->execute([$quantity, $item['id']]);
    } else {
        // Insert mới
        $stmt = $pdo->prepare("INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$sessionId, $productId, $quantity]);
    }

    echo json_encode([
        'success' => true,
        'session_id' => $sessionId
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
}
?>
