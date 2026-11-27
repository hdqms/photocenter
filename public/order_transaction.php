<?php
session_start();

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/functions.php';

$clients = $pdo->query(
    "SELECT client_id, full_name
     FROM Client ORDER BY full_name"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$employees = $pdo->query(
    "SELECT employee_id, full_name
     FROM Employee ORDER BY full_name"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$services = $pdo->query(
    "SELECT service_id,
            CONCAT(service_name, ' — ', base_price, ' грн')
     FROM Service ORDER BY service_name"
)->fetchAll(PDO::FETCH_KEY_PAIR);

$lists = [
    'client_id' => [
        'label' => 'Клієнт',
        'options' => $clients
    ],
    'employee_id' => [
        'label' => 'Працівник',
        'options' => $employees
    ],
    'service_id' => [
        'label' => 'Послуга',
        'options' => $services
    ]
];

$values = [
    'client_id' => '',
    'employee_id' => '',
    'service_id' => '',
    'quantity' => '1'
];

$errors = [];
$formError = '';
$simulateError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    check_csrf();

    foreach ($values as $name => $default) {
        $raw = $_POST[$name] ?? '';
        $values[$name] = is_string($raw)
            ? trim($raw)
            : '';
    }

    $simulateError =
        ($_POST['simulate_error'] ?? '') === '1';

    foreach ($lists as $name => $list) {

        if (!isset($list['options'][$values[$name]])) {

            $errors[$name] =
                'Оберіть наявний запис зі списку.';
        }
    }

    $quantity = filter_var(
        $values['quantity'],
        FILTER_VALIDATE_INT,
        [
            'options' => [
                'min_range' => 1,
                'max_range' => 2147483647
            ]
        ]
    );

    if ($quantity === false) {
        $errors['quantity'] =
            'Введіть ціле число більше нуля.';
    }

    if (empty($errors)) {

        $simulatedFailure = false;

        try {

            // Початок транзакції
            $pdo->beginTransaction();

            // Отримання актуальної ціни послуги
            $stmt = $pdo->prepare(
                "SELECT base_price
                 FROM Service
                 WHERE service_id = :id
                 FOR SHARE"
            );

            $stmt->execute([
                'id' => $values['service_id']
            ]);

            $price = $stmt->fetchColumn();
            $stmt->closeCursor();

            if ($price === false) {
                throw new RuntimeException(
                    'Послугу не знайдено.'
                );
            }

            // Перевірка максимальної суми замовлення
            $priceCents = (int) str_replace(
                '.', '', (string) $price
            );

            if ($priceCents > 0 &&
                $quantity > intdiv(9999999999, $priceCents)) {

                $errors['quantity'] =
                    'Перевищено максимальну суму замовлення.';

                $pdo->rollBack();

            } else {

                // Створення замовлення
                $stmt = $pdo->prepare(
                    "INSERT INTO `Order`
                     (client_id, employee_id, order_date,
                      total_amount, status)
                     VALUES
                     (:client_id, :employee_id, NOW(),
                      0, 'Нове')"
                );

                $stmt->execute([
                    'client_id' => $values['client_id'],
                    'employee_id' => $values['employee_id']
                ]);

                $orderId = (int) $pdo->lastInsertId();

                // Додавання позиції замовлення
                $stmt = $pdo->prepare(
                    "INSERT INTO OrderItem
                     (order_id, service_id,
                      quantity, unit_price)
                     VALUES
                     (:order_id, :service_id,
                      :quantity, :unit_price)"
                );

                $stmt->execute([
                    'order_id' => $orderId,
                    'service_id' => $values['service_id'],
                    'quantity' => $quantity,
                    'unit_price' => $price
                ]);

                // Розрахунок загальної суми
                $stmt = $pdo->prepare(
                    "UPDATE `Order`
                     SET total_amount = (
                         SELECT SUM(quantity * unit_price)
                         FROM OrderItem
                         WHERE order_id = :item_order_id
                     )
                     WHERE order_id = :order_id"
                );

                $stmt->execute([
                    'item_order_id' => $orderId,
                    'order_id' => $orderId
                ]);

                // Імітація помилки для демонстрації ROLLBACK
                if ($simulateError) {

                    $simulatedFailure = true;

                    throw new RuntimeException(
                        'Тестова помилка транзакції.'
                    );
                }

                // Підтвердження транзакції
                $pdo->commit();

                $_SESSION['success'] =
                    "Транзакцію успішно завершено (COMMIT). "
                    . "Замовлення №$orderId створено.";

                header(
                    'Location: list.php?entity=orders',
                    true,
                    303
                );

                exit;
            }

        } catch (Throwable $exception) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($simulatedFailure) {

                $_SESSION['error'] =
                    "Виконано ROLLBACK. "
                    . "Замовлення №$orderId та його позицію "
                    . "не збережено.";

                header(
                    'Location: list.php?entity=orders',
                    true,
                    303
                );

                exit;
            }

            error_log($exception->getMessage());

            $formError =
                'Не вдалося створити замовлення. '
                . 'Незавершені зміни скасовано.';
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
    <title>Фотоцентр — Оформлення замовлення</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require __DIR__ . '/../src/nav.php'; ?>

<main>

    <h2>Оформлення замовлення</h2>

    <p>
        Створення замовлення та його позиції
        в межах однієї транзакції.
    </p>

    <?php if ($formError !== ''): ?>
        <div class="errors">
            <?= e($formError) ?>
        </div>
    <?php endif; ?>

    <form method="post"
          action="order_transaction.php"
          class="client-form">

        <input type="hidden"
               name="csrf_token"
               value="<?= e(csrf_token()) ?>">

        <?php foreach ($lists as $name => $list): ?>

            <label for="<?= e($name) ?>">
                <?= e($list['label']) ?>
            </label>

            <select id="<?= e($name) ?>"
                    name="<?= e($name) ?>"
                    required>

                <option value="">
                    Оберіть значення
                </option>

                <?php foreach ($list['options'] as $id => $label): ?>

                    <option value="<?= e($id) ?>"
                        <?= (string) $values[$name] === (string) $id
                            ? 'selected' : '' ?>>

                        <?= e($label) ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <?php if (isset($errors[$name])): ?>
                <span class="field-error">
                    <?= e($errors[$name]) ?>
                </span>
            <?php endif; ?>

        <?php endforeach; ?>

        <label for="quantity">Кількість</label>

        <input type="number"
               id="quantity"
               name="quantity"
               min="1"
               max="2147483647"
               step="1"
               value="<?= e($values['quantity']) ?>"
               required>

        <?php if (isset($errors['quantity'])): ?>
            <span class="field-error">
                <?= e($errors['quantity']) ?>
            </span>
        <?php endif; ?>

        <p>
            Загальна сума розраховується автоматично.
        </p>

        <label class="transaction-test">
            <input type="checkbox"
                   name="simulate_error"
                   value="1"
                   <?= $simulateError ? 'checked' : '' ?>>

            Імітувати помилку (перевірка ROLLBACK)
        </label>

        <div class="form-actions">

            <button type="submit">
                Оформити замовлення
            </button>

            <a href="list.php?entity=orders"
               class="cancel-btn">
                Скасувати
            </a>

        </div>

    </form>
</main>

</body>
</html>
