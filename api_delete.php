<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function sendJson(array $data, int $statusCode = 200): never
{
    http_response_code($statusCode);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');

    sendJson([
        'success' => false,
        'error' => 'Метод не підтримується.'
    ], 405);
}

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/classes/Cookbook.php';

    $input = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($input)) {
        sendJson([
            'success' => false,
            'error' => 'Некоректне JSON-тіло запиту.'
        ], 400);
    }

    $id = filter_var(
        $input['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($id === false || $id <= 0) {
        sendJson([
            'success' => false,
            'error' => 'Некоректний ID рецепта.'
        ], 400);
    }

    $cookbook = new Cookbook($pdo);

    if ($cookbook->getById($id) === null) {
        sendJson([
            'success' => false,
            'error' => 'Рецепт не знайдено.'
        ], 404);
    }

    $cookbook->deleteRecipe($id);

    sendJson([
        'success' => true,
        'message' => 'Рецепт успішно видалено.'
    ]);

} catch (Throwable $e) {
    error_log('api_delete.php error: ' . $e->getMessage());

    sendJson([
        'success' => false,
        'error' => 'Внутрішня помилка сервера.'
    ], 500);
}