<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/classes/Cookbook.php';

    $cookbook = new Cookbook($pdo);

    $query = trim(
        (string) ($_GET['q'] ?? '')
    );

    if ($query === '') {
        $rows = $cookbook->getAll();
    } else {
        $rows = $cookbook->search($query);
    }

    echo json_encode(
        $rows,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    );

} catch (Throwable $e) {

    error_log(
        'api_list.php error: '
        . $e->getMessage()
    );

    http_response_code(500);

    echo json_encode(
        [
            'success' => false,
            'error' => 'Внутрішня помилка сервера.'
        ],
        JSON_UNESCAPED_UNICODE
    );
}