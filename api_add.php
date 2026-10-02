<?php
require_once 'db.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

$id = isset($input['id']) ? (int)$input['id'] : 0;
$title = trim($input['title'] ?? '');
$ingredients = trim($input['ingredients'] ?? '');
$cookTime = (int)($input['cookTimeMin'] ?? 0);

if ($title !== '' && $ingredients !== '' && $cookTime > 0) {
    try {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE recipes SET title = :title, ingredients = :ingredients, cook_time_min = :time WHERE id = :id');
            $stmt->execute([':id' => $id, ':title' => $title, ':ingredients' => $ingredients, ':time' => $cookTime]);
            echo json_encode(['success' => true, 'message' => 'Рецепт успішно оновлено!']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO recipes (title, ingredients, cook_time_min) VALUES (:title, :ingredients, :time)');
            $stmt->execute([':title' => $title, ':ingredients' => $ingredients, ':time' => $cookTime]);
            
            http_response_code(201); 
            echo json_encode(['success' => true, 'message' => 'Рецепт успішно додано!']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Помилка БД: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Перевірте правильність заповнення полів.']);
}
?>