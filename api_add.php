<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function sendJson(array $data, int $statusCode = 200): void
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

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (!is_array($input)) {
        sendJson([
            'success' => false,
            'error' => 'Некоректне JSON-тіло запиту.'
        ], 400);
    }

    $id = isset($input['id'])
        ? filter_var($input['id'], FILTER_VALIDATE_INT)
        : 0;

    $title = trim((string) ($input['title'] ?? ''));
    $ingredients = trim((string) ($input['ingredients'] ?? ''));

    $cookTime = filter_var(
        $input['cookTimeMin'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($title === '') {
        sendJson([
            'success' => false,
            'error' => 'Поле "Назва страви" є обов\'язковим.'
        ], 400);
    }

    if ($ingredients === '') {
        sendJson([
            'success' => false,
            'error' => 'Поле "Інгредієнти" є обов\'язковим.'
        ], 400);
    }

    if ($cookTime === false || $cookTime <= 0) {
        sendJson([
            'success' => false,
            'error' => 'Час приготування повинен бути цілим числом більше 0.'
        ], 400);
    }

    $cookbook = new Cookbook($pdo);

    if ($id !== false && $id > 0) {
        $existingRecipe = $cookbook->getById($id);

        if ($existingRecipe === null) {
            sendJson([
                'success' => false,
                'error' => 'Рецепт не знайдено.'
            ], 404);
        }

        $cookbook->updateRecipe(
            $id,
            $title,
            $ingredients,
            $cookTime
        );

        sendJson([
            'success' => true,
            'message' => 'Рецепт успішно оновлено!'
        ]);
    }

    $newId = $cookbook->addRecipe(
        $title,
        $ingredients,
        $cookTime
    );

    sendJson([
        'success' => true,
        'message' => 'Рецепт успішно додано!',
        'data' => [
            'id' => $newId,
            'title' => $title,
            'ingredients' => $ingredients,
            'cook_time_min' => $cookTime
        ]
    ], 201);

} catch (Throwable $e) {
    error_log('api_add.php error: ' . $e->getMessage());

    sendJson([
        'success' => false,
        'error' => 'Внутрішня помилка сервера.'
    ], 500);
}