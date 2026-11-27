<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

$full_name = '';
$phone = '';
$birth_date = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birth_date = trim($_POST['birth_date'] ?? '');

    if ($full_name === '' ||
        mb_strlen($full_name, 'UTF-8') > 100) {
        $errors[] = 'Введіть коректне ПІБ клієнта.';
    }

    if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $errors[] = 'Введіть коректний номер телефону.';
    }

    if ($birth_date !== '') {
        $date = DateTime::createFromFormat('!Y-m-d', $birth_date);

        if (!$date ||
            $date->format('Y-m-d') !== $birth_date ||
            $birth_date > date('Y-m-d')) {

            $errors[] = 'Введіть коректну дату народження.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO Client
                 (full_name, phone, birth_date)
                 VALUES (:full_name, :phone, :birth_date)"
            );

            $stmt->execute([
                'full_name' => $full_name,
                'phone' => $phone,
                'birth_date' => $birth_date === ''
                    ? null : $birth_date
            ]);

            header('Location: index.php', true, 303);
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'Не вдалося додати клієнта.';
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
    <title>Фотоцентр — Додати клієнта</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>
    <h2>Додати клієнта</h2>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="client_form.php"
          method="post" class="client-form">

        <input type="hidden"
               name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <label for="full_name">ПІБ клієнта</label>
        <input type="text"
               id="full_name"
               name="full_name"
               maxlength="100"
               value="<?= e($full_name) ?>"
               required>

        <label for="phone">Номер телефону</label>
        <input type="tel"
               id="phone"
               name="phone"
               maxlength="20"
               value="<?= e($phone) ?>"
               required>

        <label for="birth_date">Дата народження</label>
        <input type="date"
               id="birth_date"
               name="birth_date"
               max="<?= date('Y-m-d') ?>"
               value="<?= e($birth_date) ?>">

        <div class="form-actions">
            <button type="submit">Зберегти</button>

            <a href="index.php" class="cancel-btn">
                Скасувати
            </a>
        </div>

    </form>
</main>

</body>
</html>
