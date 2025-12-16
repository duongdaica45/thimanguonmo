<?php
require __DIR__ . '/db.php';

$sql = "
INSERT INTO products (name, price) VALUES
('Nước hoa A', 500000),
('Nước hoa B', 700000)
";

$pdo->exec($sql);
echo "✅ Seed products thành công";
