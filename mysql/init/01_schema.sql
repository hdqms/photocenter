-- Лабораторна робота № 2. Фізичне проєктування БД «Фотоцентр»
-- СУБД: MySQL 8.4. Модель: фінальна ER-діаграма лабораторної № 1.
-- Виконується автоматично в MySQL Docker під час ПЕРШОЇ ініціалізації тому.
-- Ключі/атрибути відповідають ER-діаграмі; NOT NULL, AUTO_INCREMENT,
-- CHECK та правила ON DELETE є рішеннями фізичного проєктування.

CREATE DATABASE IF NOT EXISTS photocenter
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE photocenter;

CREATE TABLE `Role` (
  role_id INT AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(50) NOT NULL,
  description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE Client (
  client_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  birth_date DATE
) ENGINE=InnoDB;

CREATE TABLE StudioHall (
  hall_id INT AUTO_INCREMENT PRIMARY KEY,
  hall_name VARCHAR(50) NOT NULL,
  hourly_rate DECIMAL(8,2) NOT NULL,
  capacity INT NOT NULL,
  CONSTRAINT chk_hall_rate CHECK (hourly_rate >= 0),
  CONSTRAINT chk_hall_capacity CHECK (capacity > 0)
) ENGINE=InnoDB;

CREATE TABLE ServiceCategory (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL,
  description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE PrintFormat (
  format_id INT AUTO_INCREMENT PRIMARY KEY,
  format_name VARCHAR(20) NOT NULL,
  paper_type VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE Employee (
  employee_id INT AUTO_INCREMENT PRIMARY KEY,
  role_id INT NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  CONSTRAINT fk_employee_role FOREIGN KEY (role_id)
    REFERENCES `Role`(role_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE Service (
  service_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  service_name VARCHAR(100) NOT NULL,
  base_price DECIMAL(8,2) NOT NULL,
  CONSTRAINT chk_service_price CHECK (base_price >= 0),
  CONSTRAINT fk_service_category FOREIGN KEY (category_id)
    REFERENCES ServiceCategory(category_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE Booking (
  booking_id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  hall_id INT NOT NULL,
  employee_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  total_cost DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL,
  CONSTRAINT chk_booking_dates CHECK (end_time > start_time),
  CONSTRAINT chk_booking_cost CHECK (total_cost >= 0),
  CONSTRAINT fk_booking_client FOREIGN KEY (client_id)
    REFERENCES Client(client_id) ON DELETE RESTRICT,
  CONSTRAINT fk_booking_hall FOREIGN KEY (hall_id)
    REFERENCES StudioHall(hall_id) ON DELETE RESTRICT,
  CONSTRAINT fk_booking_employee FOREIGN KEY (employee_id)
    REFERENCES Employee(employee_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE `Order` (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  employee_id INT NOT NULL,
  order_date DATETIME NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL,
  CONSTRAINT chk_order_amount CHECK (total_amount >= 0),
  CONSTRAINT fk_order_client FOREIGN KEY (client_id)
    REFERENCES Client(client_id) ON DELETE RESTRICT,
  CONSTRAINT fk_order_employee FOREIGN KEY (employee_id)
    REFERENCES Employee(employee_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE OrderItem (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  service_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(8,2) NOT NULL,
  CONSTRAINT chk_item_quantity CHECK (quantity > 0),
  CONSTRAINT chk_item_price CHECK (unit_price >= 0),
  CONSTRAINT fk_item_order FOREIGN KEY (order_id)
    REFERENCES `Order`(order_id) ON DELETE RESTRICT,
  CONSTRAINT fk_item_service FOREIGN KEY (service_id)
    REFERENCES Service(service_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE PrintFile (
  file_id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  format_id INT NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  copies_count INT NOT NULL,
  crop_type VARCHAR(20) NOT NULL,
  CONSTRAINT chk_file_copies CHECK (copies_count > 0),
  CONSTRAINT fk_file_item FOREIGN KEY (item_id)
    REFERENCES OrderItem(item_id) ON DELETE RESTRICT,
  CONSTRAINT fk_file_format FOREIGN KEY (format_id)
    REFERENCES PrintFormat(format_id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE Payment (
  payment_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  payment_date DATETIME NOT NULL,
  payment_type VARCHAR(30) NOT NULL,
  CONSTRAINT chk_payment_amount CHECK (amount > 0),
  CONSTRAINT fk_payment_order FOREIGN KEY (order_id)
    REFERENCES `Order`(order_id) ON DELETE RESTRICT
) ENGINE=InnoDB;
