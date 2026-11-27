<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/entities.php';
require_once __DIR__ . '/../src/crud.php';

$config = get_entity();

$key = $_GET['entity'];
$table = $config['table'];
$fields = $config['fields'];

$listUrl = 'list.php?entity=' . $key;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (isset($_GET['id']) && (!$id || $id < 1)) {
    http_response_code(400);
    exit('Некоректний ID запису.');
}

$isEdit = $id !== null && $id !== false;

$values = array_fill_keys(array_keys($fields), '');
$errors = [];
$formError = '';

if ($isEdit) {
    $record = find_record($pdo, $table, $id);

    if (!$record) {
        http_response_code(404);
        exit('Запис не знайдено.');
    }

    foreach ($fields as $name => $field) {
        $values[$name] = $record[$name] ?? '';
    }
}

$title = $isEdit
    ? 'Редагування запису'
    : 'Додавання запису';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $data = [];

    foreach ($fields as $name => $field) {

        $raw = $_POST[$name] ?? '';

        if (!is_string($raw)) {
            $errors[$name] = 'Некоректне значення.';
            continue;
        }

        $value = trim($raw);
        $values[$name] = $value;

        if ($value === '') {

            if ($field['required']) {
                $errors[$name] =
                    'Це поле обов’язкове.';
            }

            $data[$name] = null;
            continue;
        }

        if (isset($field['max']) &&
            mb_strlen($value, 'UTF-8') > $field['max']) {

            $errors[$name] =
                'Перевищено допустиму довжину.';
        }

        if ($field['type'] === 'integer') {

            $number = filter_var(
                $value,
                FILTER_VALIDATE_INT,
                [
                    'options' => [
                        'min_range' => 1,
                        'max_range' => 2147483647
                    ]
                ]
            );

            if ($number === false) {
                $errors[$name] =
                    'Введіть ціле число більше нуля.';
            }
        }

        if ($field['type'] === 'decimal') {

            if (!preg_match(
                '/^[0-9]{1,6}(\.[0-9]{1,2})?$/D',
                $value
            )) {
                $errors[$name] =
                    'Введіть число від 0 до 999999.99.';
            }
        }

        $data[$name] = $value;
    }

    if (empty($errors)) {
        try {

            if ($isEdit) {
                update_record($pdo, $table, $id, $data);

                $_SESSION['success'] =
                    'Запис успішно оновлено.';
            } else {
                insert_record($pdo, $table, $data);

                $_SESSION['success'] =
                    'Запис успішно додано.';
            }

            header('Location: ' . $listUrl, true, 303);
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());

            if (($e->errorInfo[1] ?? null) == 1062) {
                $formError = 'Такий запис уже існує.';
            } else {
                $formError = 'Не вдалося зберегти запис.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Фотоцентр — <?= e($title) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>

    <h2><?= e($title) ?></h2>
    <p><?= e($config['title']) ?></p>

    <?php if ($formError !== ''): ?>
        <div class="errors">
            <?= e($formError) ?>
        </div>
    <?php endif; ?>

    <form method="post"
          action="form.php?entity=<?= e($key) ?><?= $isEdit ? '&amp;id=' . e($id) : '' ?>"
          class="client-form">

        <input type="hidden"
               name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <?php foreach ($fields as $name => $field): ?>

            <label for="<?= e($name) ?>">
                <?= e($field['label']) ?>
            </label>

            <?php if ($field['type'] === 'textarea'): ?>

                <textarea
                    id="<?= e($name) ?>"
                    name="<?= e($name) ?>"
                    maxlength="<?= e($field['max']) ?>"
                    rows="4"
                    <?= $field['required'] ? 'required' : '' ?>
                ><?= e($values[$name]) ?></textarea>

            <?php else: ?>

                <input
                    id="<?= e($name) ?>"
                    name="<?= e($name) ?>"

                    type="<?= in_array(
                        $field['type'],
                        ['integer', 'decimal'],
                        true
                    ) ? 'number' : 'text' ?>"

                    value="<?= e($values[$name]) ?>"

                    <?php if (isset($field['max'])): ?>
                        maxlength="<?= e($field['max']) ?>"
                    <?php endif; ?>

                    <?php if ($field['type'] === 'integer'): ?>
                        min="1" max="2147483647" step="1"
                    <?php endif; ?>

                    <?php if ($field['type'] === 'decimal'): ?>
                        min="0" max="999999.99" step="0.01"
                    <?php endif; ?>

                    <?= $field['required'] ? 'required' : '' ?>
                >

            <?php endif; ?>

            <?php if (isset($errors[$name])): ?>
                <span class="field-error">
                    <?= e($errors[$name]) ?>
                </span>
            <?php endif; ?>

        <?php endforeach; ?>

        <div class="form-actions">

            <button type="submit">
                <?= $isEdit
                    ? 'Зберегти зміни'
                    : 'Зберегти' ?>
            </button>

            <a href="<?= e($listUrl) ?>"
               class="cancel-btn">
                Скасувати
            </a>

        </div>
    </form>
</main>

</body>
</html>
