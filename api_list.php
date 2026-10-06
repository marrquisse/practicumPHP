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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');

    sendJson([
        'success' => false,
        'error' => 'Метод не підтримується.'
    ], 405);
}

try {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/classes/Cookbook.php';

    $query = trim((string) ($_GET['q'] ?? ''));

    $cookbook = new Cookbook($pdo);

    if ($query !== '') {
        $rows = $cookbook->search($query);
    } else {
        $rows = $cookbook->getAll();
    }

    sendJson($rows);

} catch (Throwable $e) {
    error_log('api_list.php error: ' . $e->getMessage());

    sendJson([
        'success' => false,
        'error' => 'Внутрішня помилка сервера.'
    ], 500);
}