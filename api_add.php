<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/lib/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'error' => 'Метод не підтримується.'
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/classes/Cookbook.php';

    $rawBody = file_get_contents('php://input');

    $input = json_decode(
        $rawBody ?: '',
        true
    );

    if (!is_array($input)) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'error' => 'Некоректне JSON-тіло запиту.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $csrf = getRequestCsrfToken($input);

    if (!isValidCsrfToken($csrf)) {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'error' => 'Недійсний CSRF-токен.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $id = filter_var(
        $input['id'] ?? 0,
        FILTER_VALIDATE_INT
    );

    $title = trim(
        (string) ($input['title'] ?? '')
    );

    $ingredients = trim(
        (string) ($input['ingredients'] ?? '')
    );

    $cookTime = filter_var(
        $input['cookTimeMin'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($title === '') {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'error' => 'Назва рецепта є обов’язковою.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if (mb_strlen($title) > 255) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'error' => 'Назва рецепта занадто довга.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if ($ingredients === '') {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'error' => 'Інгредієнти є обов’язковими.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    if ($cookTime === false || $cookTime <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'error' => 'Час приготування повинен бути додатним цілим числом.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $cookbook = new Cookbook($pdo);

    if ($id !== false && $id > 0) {

        $updated = $cookbook->updateRecipe(
            $id,
            $title,
            $ingredients,
            $cookTime
        );

        if (!$updated && $cookbook->getById($id) === null) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'error' => 'Рецепт не знайдено.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Рецепт успішно оновлено.'
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    $newId = $cookbook->addRecipe(
        $title,
        $ingredients,
        $cookTime
    );

    http_response_code(201);

    echo json_encode([
        'success' => true,
        'message' => 'Рецепт успішно додано.',
        'data' => $cookbook->getById($newId)
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    error_log(
        'api_add.php error: '
        . $e->getMessage()
    );

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'error' => 'Внутрішня помилка сервера.'
    ], JSON_UNESCAPED_UNICODE);
}