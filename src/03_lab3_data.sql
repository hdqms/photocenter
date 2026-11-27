USE photocenter_lab3;
SET NAMES utf8mb4;

START TRANSACTION;

-- Додаткові клієнти
INSERT INTO Client (full_name, phone, birth_date)
VALUES
    ('Гриценко Ольга', '+380671234509', '2001-06-14'),
    ('Кравченко Денис', '+380931234510', '1998-11-22');

-- Додаткові працівники
INSERT INTO Employee (role_id, full_name, phone)
VALUES
    (2, 'Сидоренко Марина', '+380501234511'),
    (3, 'Костенко Владислав', '+380671234512'),
    (4, 'Черненко Аліна', '+380931234513');

-- Додаткові послуги
INSERT INTO Service (category_id, service_name, base_price)
VALUES
    (1, 'Друк фото 30x40', 110.00),
    (3, 'Фото на чашці', 180.00);

-- Додаткові замовлення
INSERT INTO `Order`
    (client_id, employee_id, order_date, total_amount, status)
VALUES
    (9, 8, '2026-10-25 10:00:00', 220.00, 'Виконано'),
    (10, 9, '2026-10-26 11:30:00', 180.00, 'Нове');

-- Позиції нових замовлень
INSERT INTO OrderItem
    (order_id, service_id, quantity, unit_price)
VALUES
    (9, 9, 2, 110.00),
    (10, 10, 1, 180.00);

-- Додаткові бронювання фотозалів
INSERT INTO Booking
    (client_id, hall_id, employee_id,
     start_time, end_time, total_cost, status)
VALUES
    (9, 1, 8, '2026-10-25 10:00:00',
     '2026-10-25 12:00:00', 1200.00, 'Підтверджено'),

    (10, 2, 9, '2026-10-26 13:00:00',
     '2026-10-26 14:00:00', 750.00, 'Підтверджено'),

    (9, 3, 8, '2026-10-27 09:00:00',
     '2026-10-27 11:00:00', 900.00, 'Нове'),

    (10, 1, 9, '2026-10-28 15:00:00',
     '2026-10-28 16:00:00', 600.00, 'Нове');

-- Додаткові платежі за замовленнями
INSERT INTO Payment
    (order_id, amount, payment_date, payment_type)
VALUES
    (9, 220.00, '2026-10-25 10:15:00', 'Картка'),
    (10, 100.00, '2026-10-26 11:40:00', 'Готівка'),
    (10, 80.00, '2026-10-26 12:00:00', 'Картка'),
    (4, 180.00, '2026-10-25 16:30:00', 'Готівка');

COMMIT;