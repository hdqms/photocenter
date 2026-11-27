<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/crud.php';

$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 5;

$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM `Role`
     WHERE role_name LIKE :search"
);

$countStmt->execute([
    'search' => '%' . $search . '%'
]);

$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $limit));
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare(
    "SELECT * FROM `Role`
     WHERE role_name LIKE :search
     ORDER BY role_id
     LIMIT :limit OFFSET :offset"
);

$stmt->bindValue(
    ':search', '%' . $search . '%', PDO::PARAM_STR
);

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$roles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Фотоцентр — Посади</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>
    <h2>Список посад</h2>

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

    <form method="get" action="roles.php" class="toolbar">

        <input type="search"
               name="search"
               placeholder="Пошук посади за назвою"
               value="<?= e($search) ?>">

        <button type="submit">Знайти</button>

        <a href="roles.php" class="cancel-btn">
            Скинути
        </a>

        <a href="role_form.php" class="add-btn">
            Додати посаду
        </a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва посади</th>
                <th>Опис</th>
                <th>Дії</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($roles)): ?>
                <tr>
                    <td colspan="4">
                        Посад не знайдено.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?= e($role['role_id']) ?></td>

                    <td><?= e($role['role_name']) ?></td>

                    <td>
                        <?= e($role['description'] ?? '—') ?>
                    </td>

                    <td>
                        <a class="edit-btn"
                           href="role_form.php?id=<?= e($role['role_id']) ?>">
                            Редагувати
                        </a>

                        <form action="role_delete.php"
                              method="post"
                              class="delete-form"
                              onsubmit="return confirm('Видалити посаду?');">

                            <input type="hidden"
                                   name="id"
                                   value="<?= e($role['role_id']) ?>">

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

    <p>Знайдено посад: <?= $total ?></p>

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
