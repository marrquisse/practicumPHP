<?php
require_once 'db.php';
header('Content-Type: application/json'); 

$query = trim($_GET['q'] ?? '');

try {
    if ($query !== '') {
        $stmt = $pdo->prepare('SELECT * FROM recipes WHERE LOWER(ingredients) LIKE LOWER(:q) OR LOWER(title) LIKE LOWER(:q) ORDER BY id DESC');
        $stmt->execute([':q' => '%' . $query . '%']);
        $rows = $stmt->fetchAll();
    } else {
        $rows = $pdo->query('SELECT * FROM recipes ORDER BY id DESC')->fetchAll();
    }
    echo json_encode($rows);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>