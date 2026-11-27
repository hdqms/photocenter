<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Недозволений метод запиту.');
}

check_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Некоректний ID клієнта.');
}

try {
    $stmt = $pdo->prepare(
        "DELETE FROM Client WHERE client_id = :id"
    );

    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Клієнта успішно видалено.';
    } else {
        $_SESSION['error'] = 'Клієнта не знайдено.';
    }

} catch (PDOException $e) {
    error_log($e->getMessage());

    if (($e->errorInfo[1] ?? null) == 1451) {
        $_SESSION['error'] =
            'Неможливо видалити клієнта, оскільки з ним пов’язані інші записи.';
    } else {
        $_SESSION['error'] = 'Не вдалося видалити клієнта.';
    }
}

header('Location: index.php', true, 303);
exit;
