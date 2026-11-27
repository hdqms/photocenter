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

$options = [];

foreach ($fields as $name => $field) {

    if (isset($field['foreign'])) {

        $foreign = $field['foreign'];

        $foreignTable = $foreign['table'];
        $foreignId = $foreign['id'];
        $foreignLabel = $foreign['label'];

        
        $labelSql = foreign_label_sql($pdo, $foreign, 'f');

        $stmt = $pdo->query(
            "SELECT f.`$foreignId`, $labelSql
            FROM `$foreignTable` f
            ORDER BY 2"
        );


        $options[$name] =
            $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}


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

    if ($field['type'] === 'datetime') {
        $values[$name] = str_replace(
            ' ',
            'T',
            (string) $values[$name]
        );
    }
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

        if ($field['type'] === 'select') {

            if (!isset($options[$name][$value])) {
                $errors[$name] =
                    'Оберіть наявний запис зі списку.';
            }
        }

        if ($field['type'] === 'phone') {

            if (!preg_match('/^\+?[0-9]{7,15}$/D', $value)) {
                $errors[$name] =
                    'Введіть коректний номер телефону.';
            }
        }


        if ($field['type'] === 'datetime') {

            if (strlen($value) === 16) {
                $value .= ':00';
            }

            $validFormat = preg_match(
                '/^[1-9][0-9]{3}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}$/D',
                $value
            );

            $date = $validFormat
                ? DateTimeImmutable::createFromFormat(
                    '!Y-m-d\TH:i:s',
                    $value
                )
                : false;

            if (!$date ||
                $date->format('Y-m-d\TH:i:s') !== $value) {

                $errors[$name] =
                    'Введіть коректну дату та час.';

            } else {

                $value = $date->format('Y-m-d H:i:s');
            }
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

            $digits = $field['digits'] ?? 6;

            $maxValue =
                str_repeat('9', $digits) . '.99';

            $minValue = !empty($field['positive'])
                ? '0.01'
                : '0';

            $pattern = '/^[0-9]{1,' . $digits .
                       '}(\.[0-9]{1,2})?$/D';

            if (!preg_match($pattern, $value)) {

                $errors[$name] =
                    "Введіть число від $minValue до $maxValue.";

            } elseif (
                !empty($field['positive']) &&
                (float) $value <= 0
            ) {

                $errors[$name] =
                    'Сума повинна бути більшою за нуль.';
            }
        }


        $data[$name] = $value;
    }


    if ($table === 'Booking' &&
        !isset($errors['start_time']) &&
        !isset($errors['end_time'])) {

        if ($data['end_time'] <= $data['start_time']) {

            $errors['end_time'] =
                'Час завершення повинен бути пізніше часу початку.';
        }
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

    } elseif (($e->errorInfo[1] ?? null) == 1452) {

        $formError =
            'Пов’язаний запис не існує. Оберіть інше значення.';

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

            
        <?php if ($field['type'] === 'select'): ?>

            <select id="<?= e($name) ?>"
                    name="<?= e($name) ?>"
                    <?= $field['required'] ? 'required' : '' ?>>

                <option value="">Оберіть значення</option>

                <?php foreach ($options[$name] as $optionId => $optionLabel): ?>

                    <option value="<?= e($optionId) ?>"
                        <?= (string) $values[$name] === (string) $optionId
                            ? 'selected' : '' ?>>

                        <?= e($optionLabel) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        <?php elseif ($field['type'] === 'textarea'): ?>


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

                    type="<?= match ($field['type']) {
                        'integer', 'decimal' => 'number',
                        'phone' => 'tel',
                        'datetime' => 'datetime-local',
                        default => 'text'
                    } ?>"

                    <?php if ($field['type'] === 'datetime'): ?>
                        step="1"
                    <?php endif; ?>

                    value="<?= e($values[$name]) ?>"

                    <?php if (isset($field['max'])): ?>
                        maxlength="<?= e($field['max']) ?>"
                    <?php endif; ?>

                    <?php if ($field['type'] === 'integer'): ?>
                        min="1" max="2147483647" step="1"
                    <?php endif; ?>

                    
                    <?php if ($field['type'] === 'decimal'): ?>

                        min="<?= !empty($field['positive'])
                            ? '0.01' : '0' ?>"

                        max="<?= str_repeat(
                            '9',
                            $field['digits'] ?? 6
                        ) . '.99' ?>"

                        step="0.01"

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
