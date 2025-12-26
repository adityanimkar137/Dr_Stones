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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-26 12:23:05
