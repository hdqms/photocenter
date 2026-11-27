-- MySQL dump 10.13  Distrib 8.4.11, for Linux (x86_64)
--
-- Host: localhost    Database: photocenter
-- ------------------------------------------------------
-- Server version	8.4.11

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Booking`
--

DROP TABLE IF EXISTS `Booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Booking` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `hall_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` varchar(30) NOT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `fk_booking_client` (`client_id`),
  KEY `fk_booking_hall` (`hall_id`),
  KEY `fk_booking_employee` (`employee_id`),
  CONSTRAINT `fk_booking_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_booking_employee` FOREIGN KEY (`employee_id`) REFERENCES `Employee` (`employee_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_booking_hall` FOREIGN KEY (`hall_id`) REFERENCES `StudioHall` (`hall_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_booking_cost` CHECK ((`total_cost` >= 0)),
  CONSTRAINT `chk_booking_dates` CHECK ((`end_time` > `start_time`))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Booking`
--

LOCK TABLES `Booking` WRITE;
/*!40000 ALTER TABLE `Booking` DISABLE KEYS */;
INSERT INTO `Booking` VALUES (1,1,1,6,'2026-10-20 10:00:00','2026-10-20 12:00:00',1200.00,'Підтверджено'),(2,2,2,7,'2026-10-20 13:00:00','2026-10-20 14:00:00',750.00,'Підтверджено'),(3,3,3,6,'2026-10-21 09:00:00','2026-10-21 11:00:00',900.00,'Нове'),(4,4,1,7,'2026-10-22 15:00:00','2026-10-22 16:00:00',600.00,'Нове'),(5,5,2,6,'2026-10-23 12:00:00','2026-10-23 14:00:00',1500.00,'Підтверджено'),(6,6,3,7,'2026-10-24 11:00:00','2026-10-24 12:00:00',450.00,'Нове');
/*!40000 ALTER TABLE `Booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Client`
--

DROP TABLE IF EXISTS `Client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Client` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Client`
--

LOCK TABLES `Client` WRITE;
/*!40000 ALTER TABLE `Client` DISABLE KEYS */;
INSERT INTO `Client` VALUES (1,'Коваленко Анна','+380670000101','2002-03-12'),(2,'Шевченко Максим','+380670000102','1999-11-25'),(3,'Мельник Олена','+380670000103','2001-06-08'),(4,'Бондар Андрій','+380670000104',NULL),(5,'Ткаченко Ірина','+380670000105','1998-09-17'),(6,'Петренко Владислав','+380670000106','2003-02-03'),(7,'Лисенко Марія','+380670000107',NULL),(8,'Савченко Дмитро','+380670000108','2000-12-21');
/*!40000 ALTER TABLE `Client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Employee`
--

DROP TABLE IF EXISTS `Employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Employee` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `fk_employee_role` (`role_id`),
  CONSTRAINT `fk_employee_role` FOREIGN KEY (`role_id`) REFERENCES `Role` (`role_id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Employee`
--

LOCK TABLES `Employee` WRITE;
/*!40000 ALTER TABLE `Employee` DISABLE KEYS */;
INSERT INTO `Employee` VALUES (1,1,'Романенко Наталія','+380670000201'),(2,2,'Гончаренко Ігор','+380670000202'),(3,2,'Дяченко Оксана','+380670000203'),(4,3,'Мороз Олександр','+380670000204'),(5,3,'Кравець Софія','+380670000205'),(6,4,'Поліщук Артем','+380670000206'),(7,4,'Іванчук Катерина','+380670000207');
/*!40000 ALTER TABLE `Employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Order`
--

DROP TABLE IF EXISTS `Order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Order` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `order_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(30) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `fk_order_client` (`client_id`),
  KEY `fk_order_employee` (`employee_id`),
  CONSTRAINT `fk_order_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_order_employee` FOREIGN KEY (`employee_id`) REFERENCES `Employee` (`employee_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_order_amount` CHECK ((`total_amount` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Order`
--

LOCK TABLES `Order` WRITE;
/*!40000 ALTER TABLE `Order` DISABLE KEYS */;
INSERT INTO `Order` VALUES (1,1,2,'2026-10-01 09:15:00',180.00,'Виконано'),(2,2,3,'2026-10-02 10:20:00',195.00,'Виконано'),(3,3,2,'2026-10-03 11:30:00',180.00,'Виконано'),(4,4,3,'2026-10-04 12:10:00',180.00,'У роботі'),(5,5,2,'2026-10-05 14:40:00',215.00,'Виконано'),(6,6,3,'2026-10-06 15:05:00',165.00,'У роботі'),(7,7,2,'2026-10-07 16:45:00',180.00,'Нове'),(8,8,3,'2026-10-08 17:30:00',225.00,'Виконано');
/*!40000 ALTER TABLE `Order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrderItem`
--

DROP TABLE IF EXISTS `OrderItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `OrderItem` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `service_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(8,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `fk_item_order` (`order_id`),
  KEY `fk_item_service` (`service_id`),
  CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `Order` (`order_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_item_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_item_price` CHECK ((`unit_price` >= 0)),
  CONSTRAINT `chk_item_quantity` CHECK ((`quantity` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrderItem`
--

LOCK TABLES `OrderItem` WRITE;
/*!40000 ALTER TABLE `OrderItem` DISABLE KEYS */;
INSERT INTO `OrderItem` VALUES (1,1,1,10,12.00),(2,1,3,1,60.00),(3,2,2,3,25.00),(4,2,4,1,120.00),(5,3,6,1,80.00),(6,3,1,5,12.00),(7,3,5,2,15.00),(8,3,1,1,10.00),(9,4,8,1,90.00),(10,4,7,2,45.00),(11,5,3,2,60.00),(12,5,2,2,25.00),(13,5,7,1,45.00),(14,6,4,1,120.00),(15,6,5,3,15.00),(16,7,1,15,12.00),(17,8,6,2,80.00),(18,8,2,1,25.00),(19,8,7,1,40.00);
/*!40000 ALTER TABLE `OrderItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Payment`
--

DROP TABLE IF EXISTS `Payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_type` varchar(30) NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payment_order` (`order_id`),
  CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `Order` (`order_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_payment_amount` CHECK ((`amount` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Payment`
--

LOCK TABLES `Payment` WRITE;
/*!40000 ALTER TABLE `Payment` DISABLE KEYS */;
INSERT INTO `Payment` VALUES (1,1,180.00,'2026-10-01 09:20:00','Картка'),(2,2,195.00,'2026-10-02 10:30:00','Готівка'),(3,3,100.00,'2026-10-03 11:40:00','Картка'),(4,3,80.00,'2026-10-03 12:00:00','Готівка'),(5,5,215.00,'2026-10-05 14:45:00','Картка'),(6,8,225.00,'2026-10-08 17:40:00','Картка');
/*!40000 ALTER TABLE `Payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PrintFile`
--

DROP TABLE IF EXISTS `PrintFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `PrintFile` (
  `file_id` int NOT NULL AUTO_INCREMENT,
  `item_id` int NOT NULL,
  `format_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `copies_count` int NOT NULL,
  `crop_type` varchar(20) NOT NULL,
  PRIMARY KEY (`file_id`),
  KEY `fk_file_item` (`item_id`),
  KEY `fk_file_format` (`format_id`),
  CONSTRAINT `fk_file_format` FOREIGN KEY (`format_id`) REFERENCES `PrintFormat` (`format_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_file_item` FOREIGN KEY (`item_id`) REFERENCES `OrderItem` (`item_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_file_copies` CHECK ((`copies_count` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PrintFile`
--

LOCK TABLES `PrintFile` WRITE;
/*!40000 ALTER TABLE `PrintFile` DISABLE KEYS */;
INSERT INTO `PrintFile` VALUES (1,1,1,'uploads/order01/photo01.jpg',10,'По центру'),(2,2,4,'uploads/order01/photo02.jpg',1,'Без обрізки'),(3,3,3,'uploads/order02/photo01.jpg',3,'По центру'),(4,4,1,'uploads/order02/document.jpg',1,'Під формат'),(5,5,1,'uploads/order03/magnet.jpg',1,'По центру'),(6,6,2,'uploads/order03/photo01.jpg',5,'Без обрізки'),(7,7,5,'uploads/order03/document.pdf',2,'Під формат'),(8,8,1,'uploads/order03/photo02.jpg',1,'По центру'),(9,9,3,'uploads/order04/retouch.jpg',1,'Без обрізки'),(10,10,3,'uploads/order04/card.jpg',2,'По центру'),(11,11,4,'uploads/order05/photo01.jpg',2,'По центру'),(12,12,3,'uploads/order05/photo02.jpg',2,'Під формат'),(13,13,3,'uploads/order05/card.jpg',1,'По центру'),(14,14,1,'uploads/order06/document.jpg',1,'Під формат'),(15,15,5,'uploads/order06/document.pdf',3,'Без обрізки'),(16,16,2,'uploads/order07/photo01.jpg',15,'По центру'),(17,17,1,'uploads/order08/magnet.jpg',2,'По центру'),(18,18,3,'uploads/order08/photo01.jpg',1,'Без обрізки'),(19,19,3,'uploads/order08/card.jpg',1,'По центру');
/*!40000 ALTER TABLE `PrintFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PrintFormat`
--

DROP TABLE IF EXISTS `PrintFormat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `PrintFormat` (
  `format_id` int NOT NULL AUTO_INCREMENT,
  `format_name` varchar(20) NOT NULL,
  `paper_type` varchar(30) NOT NULL,
  PRIMARY KEY (`format_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PrintFormat`
--

LOCK TABLES `PrintFormat` WRITE;
/*!40000 ALTER TABLE `PrintFormat` DISABLE KEYS */;
INSERT INTO `PrintFormat` VALUES (1,'10x15','Глянцевий'),(2,'10x15','Матовий'),(3,'15x21','Глянцевий'),(4,'20x30','Матовий'),(5,'A4','Глянцевий');
/*!40000 ALTER TABLE `PrintFormat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Role`
--

DROP TABLE IF EXISTS `Role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Role` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Role`
--

LOCK TABLES `Role` WRITE;
/*!40000 ALTER TABLE `Role` DISABLE KEYS */;
INSERT INTO `Role` VALUES (1,'Адміністратор','Керує послугами та розкладом'),(2,'Менеджер','Оформлює замовлення й бронювання'),(3,'Друкар','Виконує друк замовлень'),(4,'Фотограф','Проводить фотосесії');
/*!40000 ALTER TABLE `Role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Service`
--

DROP TABLE IF EXISTS `Service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Service` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `base_price` decimal(8,2) NOT NULL,
  PRIMARY KEY (`service_id`),
  KEY `fk_service_category` (`category_id`),
  CONSTRAINT `fk_service_category` FOREIGN KEY (`category_id`) REFERENCES `ServiceCategory` (`category_id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_service_price` CHECK ((`base_price` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Service`
--

LOCK TABLES `Service` WRITE;
/*!40000 ALTER TABLE `Service` DISABLE KEYS */;
INSERT INTO `Service` VALUES (1,1,'Друк фото 10x15',12.00),(2,1,'Друк фото 15x21',25.00),(3,1,'Друк фото 20x30',60.00),(4,2,'Комплект фото на документи',120.00),(5,2,'Друк документа A4',15.00),(6,3,'Фотомагніт',80.00),(7,3,'Друк на листівці',45.00),(8,1,'Ретуш фотографії',90.00);
/*!40000 ALTER TABLE `Service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ServiceCategory`
--

DROP TABLE IF EXISTS `ServiceCategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ServiceCategory` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ServiceCategory`
--

LOCK TABLES `ServiceCategory` WRITE;
/*!40000 ALTER TABLE `ServiceCategory` DISABLE KEYS */;
INSERT INTO `ServiceCategory` VALUES (1,'Фотодрук','Друк фотографій різних розмірів'),(2,'Документи','Фото та друк для документів'),(3,'Сувенірна продукція','Друк зображень на сувенірах');
/*!40000 ALTER TABLE `ServiceCategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StudioHall`
--

DROP TABLE IF EXISTS `StudioHall`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `StudioHall` (
  `hall_id` int NOT NULL AUTO_INCREMENT,
  `hall_name` varchar(50) NOT NULL,
  `hourly_rate` decimal(8,2) NOT NULL,
  `capacity` int NOT NULL,
  PRIMARY KEY (`hall_id`),
  CONSTRAINT `chk_hall_capacity` CHECK ((`capacity` > 0)),
  CONSTRAINT `chk_hall_rate` CHECK ((`hourly_rate` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StudioHall`
--

LOCK TABLES `StudioHall` WRITE;
/*!40000 ALTER TABLE `StudioHall` DISABLE KEYS */;
INSERT INTO `StudioHall` VALUES (1,'Білий зал',600.00,6),(2,'Лофт',750.00,8),(3,'Предметна студія',450.00,4);
/*!40000 ALTER TABLE `StudioHall` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-15 15:00:30
