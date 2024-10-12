-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_voting-panitia.tb_candidates
CREATE TABLE IF NOT EXISTS `tb_candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `vision` text,
  `mission` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_voting-panitia.tb_candidates: ~2 rows (approximately)
DELETE FROM `tb_candidates`;
INSERT INTO `tb_candidates` (`id`, `name`, `photo`, `vision`, `mission`) VALUES
	(18, 'Prof. Dr. Husna, M.Kom, M.Pd', 'images/1728736029_muslim_avatar_islam_people-05-512.webp', 'Meningkatkan Gedung D menjadi 15 Tingkat', 'Menghayal aja dulu -'),
	(19, 'Dr. Irawan, M. Farm', 'images/1728736093_png-transparent-avatar-user-profile-male-logo-profile-icon-hand-monochrome-head.png', 'SPP Gratis untuk Beasiswa', 'Menambah investor');

-- Dumping structure for table db_voting-panitia.tb_participants
CREATE TABLE IF NOT EXISTS `tb_participants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `has_voted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_voting-panitia.tb_participants: ~8 rows (approximately)
DELETE FROM `tb_participants`;
INSERT INTO `tb_participants` (`id`, `name`, `email`, `has_voted`) VALUES
	(1, 'Husna', 'husna@example.com', 0),
	(2, 'Ahmad', 'ahmad@example.com', 0),
	(3, 'Siti', 'siti@example.com', 0),
	(4, 'Rina', 'rina@example.com', 0),
	(5, 'Budi', 'budi@example.com', 0),
	(6, 'Sani', 'sani@example.com', 0),
	(15, 'John Doe', 'john@example.com', 0),
	(16, 'Jane Smith', 'jane@example.com', 0);

-- Dumping structure for table db_voting-panitia.tb_votes
CREATE TABLE IF NOT EXISTS `tb_votes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `participant_id` int DEFAULT NULL,
  `candidate_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `participant_id` (`participant_id`),
  KEY `candidate_id` (`candidate_id`),
  CONSTRAINT `tb_votes_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `tb_participants` (`id`),
  CONSTRAINT `tb_votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `tb_candidates` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_voting-panitia.tb_votes: ~0 rows (approximately)
DELETE FROM `tb_votes`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
