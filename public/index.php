<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';


$search = trim($_GET['search'] ?? '');

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 5;

$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM Client
     WHERE full_name LIKE :search"
);

$countStmt->execute([
    'search' => '%' . $search . '%'
]);

$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $limit));

$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare(
    "SELECT client_id, full_name, phone, birth_date
     FROM Client
     WHERE full_name LIKE :search
     ORDER BY client_id
     LIMIT :limit OFFSET :offset"
);

$stmt->bindValue(
    ':search', '%' . $search . '%', PDO::PARAM_STR
);

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$clients = $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Фотоцентр — Клієнти</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>
    <h2>Список клієнтів</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
            <?= e($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="errors">
            <?= e($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


    
<form method="get" action="index.php" class="toolbar">

    <input type="search"
           name="search"
           placeholder="Пошук клієнта за ПІБ"
           value="<?= e($search) ?>">

    <button type="submit">Знайти</button>

    <a href="index.php" class="cancel-btn">
        Скинути
    </a>

    <a href="client_form.php" class="add-btn">
    Додати клієнта
    </a>

</form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ПІБ</th>
                <th>Телефон</th>
                <th>Дата народження</th>
                <th>Дії</th>
            </tr>
        </thead>

        <tbody>
            
        <?php if (empty($clients)): ?>
            <tr>
                <td colspan="5">Клієнтів не знайдено.</td>
            </tr>
        <?php endif; ?>

            <?php foreach ($clients as $client): ?>
                <tr>
                    <td>
                        <?= e($client['client_id']) ?>
                    </td>

                    <td>
                        <?= e($client['full_name']) ?>
                    </td>

                    <td>
                        <?= e($client['phone']) ?>
                    </td>

                    <td>
                        <?= e($client['birth_date'] ?? '—') ?>
                    </td>

                    <td>
                       
                    <a href="client_edit.php?id=<?= e($client['client_id']) ?>"
                    class="edit-btn">
                        Редагувати
                    </a>


                        
                        <form action="client_delete.php"
                            method="post"
                            class="delete-form"
                            onsubmit="return confirm('Ви дійсно бажаєте видалити клієнта?');">

                            <input type="hidden"
                                name="id"
                                value="<?= e($client['client_id']) ?>">

                            <input type="hidden"
                                name="csrf_token"
                                value="<?= e(csrf_token()) ?>">

                            <button type="submit" class="delete-btn">
                                Видалити
                            </button>

                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p>Знайдено клієнтів: <?= $total ?></p>

    <div class="pagination">

        <?php if ($page > 1): ?>
            <a class="edit-btn"
            href="?page=<?= $page - 1 ?>&amp;search=<?= e(urlencode($search)) ?>">
                Назад
            </a>
        <?php endif; ?>

        <span>
            Сторінка <?= $page ?> з <?= $totalPages ?>
        </span>

        <?php if ($page < $totalPages): ?>
            <a class="edit-btn"
            href="?page=<?= $page + 1 ?>&amp;search=<?= e(urlencode($search)) ?>">
                Далі
            </a>
        <?php endif; ?>

    </div>
</main>

</body>
</html>
