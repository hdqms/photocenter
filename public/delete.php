<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/entities.php';
require_once __DIR__ . '/../src/crud.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Недозволений метод запиту.');
}

check_csrf();

$config = get_entity();

$key = $_GET['entity'];
$table = $config['table'];

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Некоректний ID запису.');
}

try {

    if (delete_record($pdo, $table, $id)) {

        $_SESSION['success'] =
            'Запис успішно видалено.';

    } else {

        $_SESSION['error'] =
            'Запис не знайдено.';
    }

} catch (PDOException $e) {

    error_log($e->getMessage());

    if (($e->errorInfo[1] ?? null) == 1451) {

        $_SESSION['error'] =
            'Неможливо видалити запис, оскільки з ним пов’язані інші записи.';

    } else {

        $_SESSION['error'] =
            'Не вдалося видалити запис.';
    }
}

header('Location: list.php?entity=' . $key, true, 303);
exit;
