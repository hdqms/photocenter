<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/crud.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Недозволений метод запиту.');
}

check_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Некоректний ID посади.');
}

try {
    if (delete_record($pdo, 'Role', $id)) {
        $_SESSION['success'] = 'Посаду успішно видалено.';
    } else {
        $_SESSION['error'] = 'Посаду не знайдено.';
    }

} catch (PDOException $e) {
    error_log($e->getMessage());

    if (($e->errorInfo[1] ?? null) == 1451) {
        $_SESSION['error'] =
            'Неможливо видалити посаду, до якої прив’язані працівники.';
    } else {
        $_SESSION['error'] = 'Не вдалося видалити посаду.';
    }
}

header('Location: roles.php', true, 303);
exit;
