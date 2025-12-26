-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: stones
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text,
  `price` int NOT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `era` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'Ancient Obsidian','Forged in Dragon’s Fire','Born from molten depths of primordial volcanoes.',299,'2.4 lbs','Mediterranean Depths','3000 BCE','assets/obsidian.png'),(2,'Sacred Jade','Emperor’s Treasure','Imperial relic of forgotten dynasties.',450,'1.8 lbs','Ancient China','2000 BCE','assets/jade.png'),(3,'Mystic Amethyst','Seer’s Vision Stone','Used by ancient oracles.',350,'3.2 lbs','Egypt','1500 BCE','assets/amethyst.png'),(4,'Eternal Marble','Temple Guardian','Carved from the sacred quarries that built the Parthenon, this marble echoes with hymns of ancient gods.',275,'5.6 lbs','Greek Acropolis','500 BCE','assets/marble.png'),(5,'Crimson Jasper','Warrior\'s Amulet','Carried by legionnaires into battle, this jasper is said to grant courage and protection to its bearer. Worn by kings in the past.',320,'2.1 lbs','Roman Battlefields','100 CE','assets/crimson.png'),(6,'Golden Topaz','Pharaoh\'s Crown Jewel','Once adorning a pharaoh\'s death mask, this topaz shimmers with the golden light of Ra himself.',550,'1.5 lbs','Valley of Kings','1200 BCE','assets/gold.png'),(7,'Red Giant',NULL,'lorem',600,'5.5','Myanmar','1000','uploads/stone_694d3747e11160.50846839.png');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('PENDING','COMPLETED','CANCELLED') DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,1,3,'2025-12-23 15:36:23','PENDING'),(2,4,3,'2025-12-23 15:37:30','PENDING'),(3,1,2,'2025-12-23 15:57:37','PENDING'),(4,1,3,'2025-12-23 15:57:37','PENDING'),(5,1,1,'2025-12-23 15:57:37','PENDING'),(6,1,3,'2025-12-23 16:06:19','PENDING'),(7,1,2,'2025-12-23 16:17:13','PENDING');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proposals`
--

DROP TABLE IF EXISTS `proposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proposals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stone_name` varchar(255) NOT NULL,
  `stone_subtitle` varchar(255) DEFAULT NULL,
  `stone_description` text,
  `price` decimal(10,2) NOT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  `era` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `vendor_name` varchar(100) DEFAULT NULL,
  `vendor_email` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text,
  `rejected_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proposals`
--

LOCK TABLES `proposals` WRITE;
/*!40000 ALTER TABLE `proposals` DISABLE KEYS */;
INSERT INTO `proposals` VALUES (1,'Red Giant',NULL,'lorem',600.00,'5.5','Myanmar','1000','uploads/stone_694d3747e11160.50846839.png','picasa','acac@gmail.com','APPROVED','2025-12-25 13:08:23','2025-12-25 13:45:48',NULL,NULL);
/*!40000 ALTER TABLE `proposals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('USER','ADMIN') DEFAULT 'USER',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'picasa','pablo','pablopicaso@gmail.com','$2y$12$7XeGYqJ9f9bDnb1cNUsmgeskhwpLHlra7uYRUSYj5aNS1jg0XGeFW','USER','2025-12-21 11:39:57'),(2,'Admin','Root','admin@stones.com','$2y$12$05HuBP5Iaa0VwxomjZ4.RewGexVXH85eDebQag0UO8RpiCCxgbeEO','ADMIN','2025-12-21 13:22:08'),(4,'adi','adi','aditya@gmail.com','$2y$12$dgM.BTJ3ICDt4roklL1Z3OuZeKC7EWfg3RM5jHE7ds6j5IwX8hp.e','USER','2025-12-21 17:50:30'),(5,'abc','xyz','acac@gmail.com','$2y$12$ErcJXrtbmj/Hi8yNcP0uye4msjWj6wYFlJ2y/xAwnrY8fxI3MF60.','USER','2025-12-23 10:06:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'stones'
--

--
-- Dumping routines for database 'stones'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-26 12:24:54
