<?php
require_once 'db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare('DELETE FROM recipes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Помилка видалення']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Невірний ID']);
}
?>