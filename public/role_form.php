<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';
require_once __DIR__ . '/../src/crud.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (isset($_GET['id']) && (!$id || $id < 1)) {
    http_response_code(400);
    exit('Некоректний ID посади.');
}

$isEdit = $id !== null && $id !== false;

$role_name = '';
$description = '';
$errors = [];

if ($isEdit) {
    $role = find_record($pdo, 'Role', $id);

    if (!$role) {
        http_response_code(404);
        exit('Посаду не знайдено.');
    }

    $role_name = $role['role_name'];
    $description = $role['description'] ?? '';
}

$title = $isEdit
    ? 'Редагування посади'
    : 'Додати посаду';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    $role_name = trim($_POST['role_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($role_name === '' ||
        mb_strlen($role_name, 'UTF-8') > 50) {
        $errors[] = 'Введіть коректну назву посади.';
    }

    if (mb_strlen($description, 'UTF-8') > 255) {
        $errors[] = 'Опис занадто довгий.';
    }

    if (empty($errors)) {
        $data = [
            'role_name' => $role_name,
            'description' => $description === ''
                ? null : $description
        ];

        try {
            if ($isEdit) {
                update_record($pdo, 'Role', $id, $data);

                $_SESSION['success'] =
                    'Посаду успішно оновлено.';
            } else {
                insert_record($pdo, 'Role', $data);

                $_SESSION['success'] =
                    'Посаду успішно додано.';
            }

            header('Location: roles.php', true, 303);
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = 'Не вдалося зберегти посаду.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Фотоцентр — <?= e($title) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>
    <h2><?= e($title) ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?= e($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post"
          action="role_form.php<?= $isEdit ? '?id=' . e($id) : '' ?>"
          class="client-form">

        <input type="hidden"
               name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <label for="role_name">Назва посади</label>

        <input type="text"
               id="role_name"
               name="role_name"
               maxlength="50"
               value="<?= e($role_name) ?>"
               required>

        <label for="description">Опис посади</label>

        <textarea id="description"
                  name="description"
                  maxlength="255"
                  rows="4"><?= e($description) ?></textarea>

        <div class="form-actions">

            <button type="submit">
                <?= $isEdit ? 'Зберегти зміни' : 'Зберегти' ?>
            </button>

            <a href="roles.php" class="cancel-btn">
                Скасувати
            </a>

        </div>
    </form>
</main>

</body>
</html>
