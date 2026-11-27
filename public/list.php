<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/entities.php';

$config = get_entity();

$key = $_GET['entity'];
$table = $config['table'];
$pk = $config['id'];
$fields = $config['fields'];


$columns = ['t.*'];
$joins = [];
$i = 0;

foreach ($fields as $name => $field) {

    if (isset($field['foreign'])) {

        $foreign = $field['foreign'];

        $foreignTable = $foreign['table'];
        $foreignId = $foreign['id'];
        $foreignLabel = $foreign['label'];

        $alias = 'f' . $i++;

        $labelSql = foreign_label_sql($pdo, $foreign, $alias);

        $columns[] =
            "$labelSql AS `{$name}_label`";

        $joins[] =
            "LEFT JOIN `$foreignTable` $alias
             ON t.`$name` = $alias.`$foreignId`";
    }
}

$sql = "SELECT " . implode(', ', $columns)
     . " FROM `$table` t "
     . implode(' ', $joins)
     . " ORDER BY t.`$pk`";

$stmt = $pdo->query($sql);
$records = $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Фотоцентр — <?= e($config['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>
    <h2><?= e($config['title']) ?></h2>

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

    
<div class="toolbar">

    <a href="form.php?entity=<?= e($key) ?>"
       class="add-btn">
        Додати запис
    </a>

    <?php if ($key === 'orders'): ?>

        <a href="order_transaction.php"
           class="add-btn">
            Оформити замовлення
        </a>

    <?php endif; ?>

</div>


    <table>
        <thead>
            <tr>
                <th>ID</th>

                <?php foreach ($fields as $field): ?>
                    <th><?= e($field['label']) ?></th>
                <?php endforeach; ?>

                <th>Дії</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($records)): ?>
                <tr>
                    <td colspan="<?= count($fields) + 2 ?>">
                        Записів не знайдено.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= e($record[$pk]) ?></td>

                    
            <?php foreach ($fields as $name => $field): ?>
                <td>
                    <?php if (isset($field['foreign'])): ?>

                        <?= e($record[$name . '_label'] ?? '—') ?>

                    <?php else: ?>

                        <?= e($record[$name] ?? '—') ?>

                    <?php endif; ?>
                </td>
            <?php endforeach; ?>


                    <td>
                        <a class="edit-btn"
                           href="form.php?entity=<?= e($key) ?>&amp;id=<?= e($record[$pk]) ?>">
                            Редагувати
                        </a>

                        <form method="post"
                              action="delete.php?entity=<?= e($key) ?>"
                              class="delete-form"
                              onsubmit="return confirm('Видалити запис?');">

                            <input type="hidden"
                                   name="id"
                                   value="<?= e($record[$pk]) ?>">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= e(csrf_token()) ?>">

                            <button type="submit"
                                    class="delete-btn">
                                Видалити
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <p>Усього записів: <?= count($records) ?></p>
</main>

</body>
</html>
