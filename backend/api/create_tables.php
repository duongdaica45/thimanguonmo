<?php
require __DIR__ . '/db.php';

$sql = "
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    price INTEGER
);

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);
";

$pdo->exec($sql);
echo "✅ Tạo bảng products & users thành công";
