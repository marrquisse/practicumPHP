<?php

declare(strict_types=1);


header('Content-Type: application/json; charset=utf-8');

$profileStart = microtime(true);

function sendJson(
    bool $success,
    mixed $data = null,
    ?string $error = null,
    int $statusCode = 200
): void {
    http_response_code($statusCode);

    $elapsedMs = (microtime(true) - $GLOBALS['profileStart']) * 1000;
    $peakMemoryMb = memory_get_peak_usage(true) / 1024 / 1024;

    error_log(sprintf(
        'API profile: time=%.2f ms, memory=%.2f MB',
        $elapsedMs,
        $peakMemoryMb
    ));

    $response = [
        'success' => $success
    ];

    if ($success) {
        $response['data'] = $data;
    } else {
        $response['error'] = $error;
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

function getJsonBody(): array
{
    $rawBody = file_get_contents('php://input');

    if ($rawBody === false || trim($rawBody) === '') {
        return [];
    }

    $data = json_decode($rawBody, true);

    if (!is_array($data)) {
        sendJson(
            false,
            null,
            'Некоректне JSON-тіло запиту.',
            400
        );
    }

    return $data;
}

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/classes/Cookbook.php';

    $cookbook = new Cookbook($pdo);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $resource = trim((string) ($_GET['resource'] ?? ''));
    $action = trim((string) ($_GET['action'] ?? ''));
    $mode = trim((string) ($_GET['mode'] ?? ''));

    if ($resource === '') {
        sendJson(
            false,
            null,
            'Не вказано параметр resource.',
            400
        );
    }

    if ($resource !== 'recipes') {
        sendJson(
            false,
            null,
            'Ресурс не знайдено.',
            404
        );
    }


if ($method === 'GET') {

    if ($action === 'shortest') {
        $recipe = $cookbook->shortestCookTimeCached(60);

        sendJson(
            true,
            $recipe,
            null,
            200
        );
    }

    if ($action !== '') {
        sendJson(
            false,
            null,
            'Параметр action не підтримується для GET-запиту.',
            400
        );
    }

    if (!isset($_GET['id'])) {

        if ($mode === 'nplus1') {

            $recipes =
                $cookbook->getAllWithIngredientCountNPlusOne();

        } elseif ($mode === 'optimized') {

            $recipes =
                $cookbook->getAllWithIngredientCountOptimized();

        } else {

            $recipes = $cookbook->getAll();
        }

        sendJson(
            true,
            $recipes,
            null,
            200
        );
    }

    $id = filter_var(
        $_GET['id'],
        FILTER_VALIDATE_INT
    );

    if ($id === false || $id <= 0) {
        sendJson(
            false,
            null,
            'Параметр id повинен бути додатним цілим числом.',
            400
        );
    }

    $recipe = $cookbook->getById($id);

    if ($recipe === null) {
        sendJson(
            false,
            null,
            'Рецепт не знайдено.',
            404
        );
    }

    sendJson(
        true,
        $recipe,
        null,
        200
    );
}

    if ($method === 'POST') {
        $input = getJsonBody();



        if ($action === 'search') {
            $maxCookTime = filter_var(
                $input['max_cook_time'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($maxCookTime === false || $maxCookTime <= 0) {
                sendJson(
                    false,
                    null,
                    'Поле max_cook_time повинно бути додатним цілим числом.',
                    400
                );
            }

            $recipes = $cookbook->findByMaxCookTime(
                $maxCookTime
            );

            sendJson(
                true,
                $recipes,
                null,
                200
            );
        }

    
        if ($action !== '') {
            sendJson(
                false,
                null,
                'Невідома дія.',
                404
            );
        }

        $title = trim(
            (string) ($input['title'] ?? '')
        );

        $ingredients = trim(
            (string) ($input['ingredients'] ?? '')
        );

        $cookTime = filter_var(
            $input['cook_time_min'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($title === '') {
            sendJson(
                false,
                null,
                'Поле title є обов\'язковим.',
                400
            );
        }

        if (mb_strlen($title) > 255) {
            sendJson(
                false,
                null,
                'Поле title не може містити більше 255 символів.',
                400
            );
        }

        if ($ingredients === '') {
            sendJson(
                false,
                null,
                'Поле ingredients є обов\'язковим.',
                400
            );
        }

        if ($cookTime === false || $cookTime <= 0) {
            sendJson(
                false,
                null,
                'Поле cook_time_min повинно бути додатним цілим числом.',
                400
            );
        }

        $newId = $cookbook->addRecipe(
            $title,
            $ingredients,
            $cookTime
        );

        $createdRecipe = $cookbook->getById($newId);

        sendJson(
            true,
            $createdRecipe,
            null,
            201
        );
    }


    header('Allow: GET, POST');

    sendJson(
        false,
        null,
        'HTTP-метод не підтримується для ресурсу recipes.',
        405
    );

} catch (Throwable $e) {

    error_log('api.php error: ' . $e->getMessage());

    sendJson(
        false,
        null,
        'Внутрішня помилка сервера.',
        500
    );
}