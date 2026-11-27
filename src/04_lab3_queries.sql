USE photocenter_lab3;
SET NAMES utf8mb4;

-- Запит 1. Перегляд усіх клієнтів фотоцентру
SELECT *
FROM Client
ORDER BY client_id;

-- Запит 2. Перелік послуг та їхньої вартості
SELECT
    service_id,
    service_name,
    base_price
FROM Service
ORDER BY service_id;

-- 3. Послуги вартістю понад 80 грн
SELECT service_id, service_name, base_price
FROM Service
WHERE base_price > 80
ORDER BY base_price DESC;

-- 4. Бронювання із загальною вартістю від 900 грн
SELECT booking_id, client_id, hall_id, total_cost
FROM Booking
WHERE total_cost >= 900
ORDER BY total_cost DESC;

-- 5. П'ять найдорожчих замовлень
SELECT order_id, client_id, total_amount
FROM `Order`
ORDER BY total_amount DESC
LIMIT 5;

-- 6. Замовлення разом з іменами клієнтів (JOIN)
SELECT o.order_id, c.full_name, o.total_amount
FROM `Order` AS o
JOIN Client AS c ON o.client_id = c.client_id
ORDER BY o.order_id;

-- 7. ПІДЗАПИТ IN:
-- Клієнти, які мають хоча б одне замовлення
SELECT client_id, full_name
FROM Client
WHERE client_id IN (
    SELECT client_id
    FROM `Order`
);

-- 8. ПІДЗАПИТ EXISTS:
-- Послуги, які використовувалися хоча б в одному замовленні
SELECT s.service_id, s.service_name
FROM Service AS s
WHERE EXISTS (
    SELECT 1
    FROM OrderItem AS oi
    WHERE oi.service_id = s.service_id
);

-- 9. ПІДЗАПИТ ANY:
-- Замовлення, дорожчі за хоча б одне замовлення клієнта №1
SELECT order_id, client_id, total_amount
FROM `Order`
WHERE total_amount > ANY (
    SELECT total_amount
    FROM `Order`
    WHERE client_id = 1
);

-- 10. ПІДЗАПИТ ALL:
-- Замовлення з максимальною вартістю
SELECT order_id, client_id, total_amount
FROM `Order`
WHERE total_amount >= ALL (
    SELECT total_amount
    FROM `Order`
);