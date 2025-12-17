
<?php
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $db   = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');

    if (!$host || !$port || !$db || !$user) {
        throw new Exception("Missing database environment variables");
    }

    // ⚠️ sslmode=require là BẮT BUỘC
    $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode=require";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "DB connection failed",
        "message" => $e->getMessage()
    ]);
    exit;
}
