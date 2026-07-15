
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
INSERT INTO `contact_messages` VALUES (1,'rawad choubassi','rawadchou2@gmail.com','hii','2025-12-01 14:52:49'),(2,'test rawad','test@rawad','test test','2025-12-01 16:15:43'),(3,'hi','rawad@gmail.com','hi','2025-12-02 17:08:11'),(4,'ayham','ayhamtaha10@gmail.com','aaa','2025-12-03 18:50:22'),(5,'FitLife Test','contact@example.test','Transaction-only contact test','2026-07-14 16:09:28');
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exercises` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `muscle_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `difficulty` enum('Beginner','Intermediate','Advanced') NOT NULL,
  `main_muscle` varchar(100) NOT NULL,
  `equipment` varchar(100) NOT NULL,
  `image` varchar(150) NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_exercises_muscle` (`muscle_id`),
  CONSTRAINT `fk_exercises_muscle` FOREIGN KEY (`muscle_id`) REFERENCES `muscles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,1,'Push-up','Beginner','Chest','None','pushup.jpg','https://www.youtube.com/embed/_l3ySVKYVJ8','Bodyweight chest exercise that also works shoulders and triceps. Great starter movement that can be progressed by elevating feet or adding weight.'),(2,1,'Barbell Bench Press','Intermediate','Chest','Barbell + flat bench','Barbell-Bench-Press.webp','https://www.youtube.com/embed/gRVjAtPip0Y','Classic compound press targeting chest, front delts and triceps. Keep feet planted, back tight and bar path controlled.'),(3,1,'Incline Dumbbell Press','Intermediate','Upper chest','Dumbbells + incline bench','incline-db-press.webp','https://www.youtube.com/embed/8iPEnn-ltC8','Pressing movement on an incline bench to emphasize the upper chest. Use a controlled tempo and avoid locking out too hard at the top.'),(4,1,'Dumbbell Chest Fly','Intermediate','Chest','Dumbbells + flat bench','chest-fly.webp','https://www.youtube.com/embed/eozdVDA78K0','Isolation exercise that stretches the chest through a wide range of motion. Keep a slight bend in the elbows and don’t go too heavy.'),(5,1,'Cable Crossover','Advanced','Chest','Cable machine','cablecrossover.jpg','https://www.youtube.com/embed/taI4XduLpTk','Cable fly variation keeping constant tension on the chest. Step slightly forward, lean in a bit and squeeze hard at the middle.'),(6,1,'Machine Chest Press','Advanced','Chest','Chest press machine','machinechestpress.jpg','https://www.youtube.com/embed/TKFSv-0Uyyc','Guided pressing pattern that allows safe heavy loading for chest with less stabilisation required.'),(7,2,'Lat Pulldown','Beginner','Back (lats)','Lat pulldown machine','latpulldown.png','https://www.youtube.com/embed/CAwf7n6Luuc','Vertical pulling exercise focusing on the lats. Pull the bar towards the upper chest and avoid swinging.'),(8,2,'Pull-up','Intermediate','Back','Pull-up bar','pullup.webp','https://www.youtube.com/embed/eGo4IYlbE5g','Bodyweight pull-up building back width and grip strength. Start with band assistance if needed and progress over time.'),(9,2,'Seated Cable Row','Intermediate','Mid-back','Cable row machine','seatedcablerow.webp','https://www.youtube.com/embed/GZbfZ033f74','Horizontal pulling movement focusing on mid-back thickness. Keep chest up and pull handles towards the lower ribs.'),(10,2,'One-Arm Dumbbell Row','Intermediate','Lats & mid-back','Dumbbell + bench','onearmdbrow.jpg','https://www.youtube.com/embed/pYcpY20QaE8','Single-arm row to correct imbalances and build lats. Keep back flat and drive the elbow towards the hip.'),(11,2,'Barbell Row','Advanced','Back','Barbell','barbelrow.jpg','https://www.youtube.com/embed/vT2GjY_Umpw','Heavy bent-over row for overall back development. Hinge at the hips, brace the core and row the bar towards the lower chest.'),(12,2,'Face Pull','Advanced','Upper back & rear delts','Cable machine + rope','Facepull.webp','https://www.youtube.com/embed/rep-qVOkqgk','Great for shoulder health and rear delt development. Pull the rope towards the face while keeping elbows high.'),(13,3,'Overhead Barbell Press','Intermediate','Shoulders','Barbell','overheadbarbelpress.jpg','https://www.youtube.com/embed/2yjwXTZQDDI','Standing press for overall shoulder mass and strength. Squeeze glutes and core to keep the body stable.'),(14,3,'Dumbbell Lateral Raise','Beginner','Side delts','Dumbbells','dumbbelllateralraise.webp','https://www.youtube.com/embed/3VcKaXpzqRo','Isolation exercise for the side delts. Raise dumbbells to shoulder height with a slight bend in the elbows.'),(15,3,'Dumbbell Front Raise','Beginner','Front delts','Dumbbells','dbfrontraise.jpg','https://www.youtube.com/embed/-t7fuZ0KhDA','Targets the front part of the shoulders. Raise one or both dumbbells to eye level with control.'),(16,3,'Rear Delt Fly','Intermediate','Rear delts','Dumbbells or machine','reardeltfly.jpg','https://www.youtube.com/embed/ttvfGg9d76c','Isolation movement for the rear delts and upper back. Helps balance shoulder development.'),(17,4,'Back Squat','Intermediate','Quads, glutes & core','Barbell + rack','backsquat.jpg','https://www.youtube.com/embed/1xMaFs0L3ao','Heavy compound movement for overall lower body strength. Maintain a neutral spine and full-foot contact.'),(18,4,'Leg Press','Intermediate','Quads & glutes','Leg press machine','legpress.jpg','https://www.youtube.com/embed/IZxyjW7MPJQ','Machine-based compound to load the legs safely. Keep hips on the pad and avoid locking out the knees.'),(19,4,'Romanian Deadlift','Intermediate','Hamstrings & glutes','Barbell or dumbbells','romdeadlift.jpg','https://www.youtube.com/embed/2SHsk9AzdjA','Hip-hinge movement emphasizing the hamstrings. Push hips back, keep bar close to the legs and maintain a flat back.'),(20,4,'Leg Extension','Beginner','Quadriceps','Leg extension machine','legext.webp','https://www.youtube.com/embed/YyvSfVjQeL0','Isolation exercise for the quads. Control both the lifting and lowering phase.'),(21,4,'Leg Curl','Beginner','Hamstrings','Leg curl machine','legcurl.webp','https://www.youtube.com/embed/1Tq3QdYUuHs','Isolation movement for the hamstrings. Keep hips down and squeeze at the top of each rep.'),(22,4,'Standing Calf Raise','Beginner','Calves','Bodyweight or calf machine','standingcalfraise.jpg','https://www.youtube.com/embed/-M4-G8p8fmc','Focuses on the calves. Use a full stretch and strong squeeze at the top.'),(23,5,'Barbell Curl','Beginner','Biceps','Barbell','barbellcurl.jpg','https://www.youtube.com/embed/kwG2ipFRgfo','Basic mass-builder for the biceps. Keep elbows close to the body and avoid swinging the weight.'),(24,5,'Hammer Curl','Beginner','Biceps & forearms','Dumbbells','hammercurl.jpg','https://www.youtube.com/embed/zC3nLlEvin4','Neutral-grip curl hitting the brachialis and forearms for thicker arms.'),(25,5,'Triceps Pushdown','Intermediate','Triceps','Cable machine','tricepspushdown.jpg','https://www.youtube.com/embed/2-LAMcpzODU','Cable isolation for the triceps. Keep elbows pinned to the sides and fully extend at the bottom.'),(26,5,'Overhead Triceps Extension','Intermediate','Triceps (long head)','Dumbbell or cable','overheadtricepsext.webp','https://www.youtube.com/embed/2z8JmcrW-As','Stretches the long head of the triceps. Keep elbows pointing up and avoid arching the back.'),(27,6,'Plank','Beginner','Core','None','plank.jpg','https://www.youtube.com/embed/pSHjTRCQxIw','Isometric core exercise focusing on stability. Keep body in a straight line from head to heels.'),(28,6,'Crunch','Beginner','Upper abs','Mat','crunch.jpg','https://www.youtube.com/embed/Xyd_fa5zoEU','Classic ab movement. Focus on curling the ribcage towards the pelvis rather than pulling on the neck.'),(29,6,'Hanging Leg Raise','Intermediate','Lower abs','Pull-up bar','hanginglegraise.jpg','https://www.youtube.com/embed/CFMaEVf9aO8','Targets the lower portion of the abs and hip flexors. Avoid swinging and lift legs with control.'),(30,6,'Russian Twist','Intermediate','Obliques','Bodyweight or light plate','russiantwist.jpg','https://www.youtube.com/embed/wkD8rjkodUI','Rotational exercise focusing on the obliques. Keep core tight and rotate through the torso, not just the arms.');
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `muscles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `muscles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `muscles` WRITE;
/*!40000 ALTER TABLE `muscles` DISABLE KEYS */;
INSERT INTO `muscles` VALUES (1,'Chest'),(2,'Back'),(3,'Shoulders'),(4,'Legs'),(5,'Arms'),(6,'Abs');
/*!40000 ALTER TABLE `muscles` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `program_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_days` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_id` int(10) unsigned NOT NULL,
  `day_title` varchar(255) NOT NULL,
  `day_order` tinyint(3) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_program_days_program` (`program_id`),
  CONSTRAINT `fk_program_days_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `program_days` WRITE;
/*!40000 ALTER TABLE `program_days` DISABLE KEYS */;
INSERT INTO `program_days` VALUES (47,1,'Day 1 – Full Body A',1,''),(48,1,'Day 2 – Full Body B',2,''),(49,1,'Day 3 – Full Body C',3,''),(50,2,'Day 1 – Upper A',1,''),(51,2,'Day 2 – Lower A',2,''),(52,2,'Day 3 – Upper B',3,''),(53,2,'Day 4 – Lower B',4,''),(54,3,'Day 1 – Squat Focus',1,''),(55,3,'Day 2 – Bench Focus',2,''),(56,3,'Day 3 – Deadlift Focus',3,''),(57,4,'Day 1 – Upper',1,''),(58,4,'Day 2 – Lower',2,''),(59,4,'Day 3 – Upper 2',3,''),(60,4,'Day 4 – Lower 2',4,''),(61,5,'Day 1 – Push Focus',1,''),(62,5,'Day 2 – Legs + Core',2,''),(63,5,'Day 3 – Pull + Full Body',3,''),(64,6,'Day 1 – Push 1',1,''),(65,6,'Day 2 – Pull 1',2,''),(66,6,'Day 3 – Legs 1',3,''),(67,6,'Day 4 – Push 2',4,''),(68,6,'Day 5 – Pull 2',5,''),(69,6,'Day 6 – Legs 2',6,'');
/*!40000 ALTER TABLE `program_days` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `program_exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `program_exercises` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `program_day_id` int(10) unsigned NOT NULL,
  `exercise_id` int(10) unsigned DEFAULT NULL,
  `display_text` varchar(255) NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_program_exercises_day` (`program_day_id`),
  KEY `fk_program_exercises_ex` (`exercise_id`),
  CONSTRAINT `fk_program_exercises_day` FOREIGN KEY (`program_day_id`) REFERENCES `program_days` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_program_exercises_ex` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `program_exercises` WRITE;
/*!40000 ALTER TABLE `program_exercises` DISABLE KEYS */;
INSERT INTO `program_exercises` VALUES (1,47,17,'Back Squat – 3×8–10',1),(2,47,1,'Push-up – 3×AMRAP',2),(3,47,7,'Lat Pulldown – 3×10–12',3),(4,47,13,'Dumbbell Shoulder Press – 3×10',4),(5,47,27,'Plank – 3×30–45 sec',5),(6,48,19,'Romanian Deadlift – 3×8–10',1),(7,48,3,'Incline Dumbbell Press – 3×8–10',2),(8,48,9,'Seated Cable Row – 3×10–12',3),(9,48,14,'Dumbbell Lateral Raise – 3×12–15',4),(10,48,28,'Crunch – 3×12–15',5),(11,49,18,'Leg Press – 3×10–12',1),(12,49,2,'Barbell Bench Press – 3×6–8',2),(13,49,10,'One-Arm Dumbbell Row – 3×10–12',3),(14,49,5,'Cable Crossover – 3×12–15',4),(15,49,30,'Russian Twist – 3×20 reps',5),(16,50,2,'Barbell Bench Press – 4×6–8',1),(17,50,7,'Lat Pulldown – 4×8–10',2),(18,50,13,'Overhead Barbell Press – 3×8–10',3),(19,50,23,'Barbell Curl – 3×10–12',4),(20,50,25,'Triceps Pushdown – 3×10–12',5),(21,51,17,'Back Squat – 4×6–8',1),(22,51,19,'Romanian Deadlift – 3×8–10',2),(23,51,18,'Leg Press – 3×10–12',3),(24,51,21,'Leg Curl – 3×12–15',4),(25,51,22,'Standing Calf Raise – 3×12–15',5),(26,52,3,'Incline Dumbbell Press – 4×8–10',1),(27,52,9,'Seated Cable Row – 4×8–10',2),(28,52,14,'Lateral Raise – 3×12–15',3),(29,52,24,'Hammer Curl – 3×10–12',4),(30,52,26,'Overhead Triceps Extension – 3×10–12',5),(31,53,18,'Leg Press – 4×8–10',1),(32,53,19,'Romanian Deadlift – 3×8–10',2),(33,53,17,'Walking Lunges – 3×10–12 each leg',3),(34,53,20,'Leg Extension – 3×12–15',4),(35,53,22,'Calf Raise – 3×15–20',5),(36,54,17,'Back Squat – 5×5',1),(37,54,18,'Leg Press – 3×8',2),(38,54,21,'Leg Curl – 3×10–12',3),(39,54,22,'Standing Calf Raise – 3×12–15',4),(40,55,2,'Barbell Bench Press – 5×5',1),(41,55,3,'Incline Dumbbell Press – 3×8–10',2),(42,55,9,'Seated Cable Row – 3×8–10',3),(43,55,14,'Dumbbell Lateral Raise – 3×12–15',4),(44,56,19,'Romanian Deadlift – 5×5',1),(45,56,8,'Pull-ups or Lat Pulldown – 3×8–10',2),(46,56,11,'Barbell Row – 3×8–10',3),(47,56,27,'Plank – 3×45–60 sec',4),(48,57,1,'Push-up or Bench Press – 3×10–12',1),(49,57,7,'Lat Pulldown – 3×10–12',2),(50,57,13,'Dumbbell Shoulder Press – 3×12',3),(51,57,25,'Triceps Pushdown – 3×12–15',4),(52,57,NULL,'10–15 min brisk walking or easy bike',5),(53,58,18,'Leg Press or Squat – 3×10–12',1),(54,58,19,'Romanian Deadlift – 3×10–12',2),(55,58,17,'Lunges – 3×10 each leg',3),(56,58,27,'Plank – 3×30–45 sec',4),(57,58,NULL,'10–15 min incline treadmill walk',5),(58,59,3,'Incline Dumbbell Press – 3×10–12',1),(59,59,9,'Seated Cable Row – 3×10–12',2),(60,59,14,'Lateral Raise – 3×15',3),(61,59,24,'Hammer Curl – 3×12–15',4),(62,59,NULL,'10–15 min step machine or bike',5),(63,60,18,'Leg Press – 3×12–15',1),(64,60,21,'Leg Curl – 3×12–15',2),(65,60,22,'Calf Raise – 3×15–20',3),(66,60,30,'Russian Twist – 3×20',4),(67,60,NULL,'10–15 min easy cardio of choice',5),(68,61,1,'Push-ups – 4×AMRAP',1),(69,61,NULL,'Chair Dips – 3×10–15',2),(70,61,NULL,'Pike Push-ups – 3×8–10',3),(71,61,27,'Plank – 3×30–45 sec',4),(72,62,NULL,'Bodyweight Squats – 4×15–20',1),(73,62,NULL,'Reverse Lunges – 3×12 each leg',2),(74,62,NULL,'Glute Bridge – 3×15',3),(75,62,28,'Crunches – 3×20',4),(76,63,NULL,'Inverted Rows – 4×AMRAP',1),(77,63,NULL,'Superman Holds – 3×15–20 sec',2),(78,63,NULL,'Mountain Climbers – 3×30 sec',3),(79,63,NULL,'Side Plank – 3×20–30 sec each side',4),(80,64,2,'Barbell Bench Press – 4×6–8',1),(81,64,3,'Incline Dumbbell Press – 4×8–10',2),(82,64,13,'Overhead Press – 3×8–10',3),(83,64,14,'Lateral Raise – 3×15',4),(84,64,25,'Triceps Pushdown – 3×12–15',5),(85,65,8,'Pull-ups – 4×AMRAP',1),(86,65,11,'Barbell Row – 4×8–10',2),(87,65,9,'Seated Cable Row – 3×10–12',3),(88,65,12,'Face Pull – 3×15',4),(89,65,24,'Hammer Curl – 3×10–12',5),(90,66,17,'Back Squat – 4×6–8',1),(91,66,19,'Romanian Deadlift – 4×8–10',2),(92,66,18,'Leg Press – 3×10–12',3),(93,66,21,'Leg Curl – 3×12–15',4),(94,66,22,'Calf Raise – 4×12–15',5),(95,67,3,'Incline Bench Press – 4×8–10',1),(96,67,6,'Machine Chest Press – 3×10–12',2),(97,67,13,'Dumbbell Shoulder Press – 3×10–12',3),(98,67,5,'Cable Crossover – 3×12–15',4),(99,67,26,'Overhead Triceps Extension – 3×10–12',5),(100,68,7,'Lat Pulldown – 4×8–10',1),(101,68,10,'One-Arm Dumbbell Row – 3×10–12',2),(102,68,12,'Face Pull – 3×15',3),(103,68,23,'Barbell Curl – 3×10–12',4),(104,68,23,'Cable Curl – 3×12–15',5),(105,69,18,'Leg Press or Front Squat – 4×8–10',1),(106,69,19,'Romanian Deadlift – 3×8–10',2),(107,69,17,'Walking Lunges – 3×12 each leg',3),(108,69,20,'Leg Extension – 3×12–15',4),(109,69,22,'Calf Raise – 4×15–20',5);
/*!40000 ALTER TABLE `program_exercises` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `level` enum('Beginner','Intermediate','Advanced') NOT NULL,
  `goal` enum('Muscle gain','Fat loss','Strength','General fitness') NOT NULL,
  `days_per_week` tinyint(3) unsigned NOT NULL,
  `duration_weeks` tinyint(3) unsigned NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'Beginner Full-Body (3 days) ','Beginner','Muscle gain',3,8,'Simple three-day full-body routine focused on learning technique and building a base.'),(2,'Upper / Lower Split (4 days)','Intermediate','Muscle gain',4,10,'Two upper-body and two lower-body sessions with moderate volume and progressive overload.'),(3,'Strength Focus (3 days)','Intermediate','Strength',3,8,'Priority on heavy squat, bench and deadlift with accessory volume.'),(4,'Fat Loss + Conditioning (4 days)','Beginner','Fat loss',4,6,'Resistance training plus light conditioning to burn fat while keeping muscle.'),(5,'Home Bodyweight (3 days)','Beginner','General fitness',3,6,'No-equipment routine you can do at home for strength, mobility and conditioning.'),(6,'Push / Pull / Legs (6 days)','Advanced','Muscle gain',6,8,'High-volume split for advanced lifters with good recovery and nutrition.');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `goal` varchar(50) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `protein_g` float DEFAULT NULL,
  `carbs_g` float DEFAULT NULL,
  `fats_g` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_results` WRITE;
/*!40000 ALTER TABLE `user_results` DISABLE KEYS */;
INSERT INTO `user_results` VALUES (1,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:08:06'),(2,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:10:59'),(3,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:11:20'),(4,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:13:45'),(5,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:13:53'),(6,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:17:42'),(7,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:17:47'),(8,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 12:17:55'),(9,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:24:53'),(10,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:25:51'),(11,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:27:36'),(12,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:27:41'),(13,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:29:38'),(14,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:29:43'),(15,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:31:24'),(16,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:35:56'),(17,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:38:00'),(18,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:41:32'),(19,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:41:49'),(20,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:43:37'),(21,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:43:51'),(22,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:44:57'),(23,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:50:16'),(24,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 12:50:34'),(25,NULL,70,170,19,'Male','maintain',2885,126,400.4,86.6,'2025-12-01 13:17:29'),(26,NULL,70,170,19,'Male','maintain',2592,126,347,77.8,'2025-12-01 13:17:38'),(27,NULL,60,160,19,'Female','cut',1548,108,182.3,43,'2025-12-01 13:18:51'),(28,NULL,60,160,19,'Female','cut',1548,108,182.3,43,'2025-12-01 13:25:48'),(29,NULL,60,160,19,'Female','cut',1548,108,182.3,43,'2025-12-01 13:28:03'),(30,NULL,60,160,19,'Female','cut',1548,108,182.3,43,'2025-12-01 13:29:18'),(31,NULL,60,160,19,'Female','cut',1548,108,182.3,43,'2025-12-01 13:32:29'),(32,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 13:40:25'),(33,NULL,64,160,23,'Female','maintain',2114,115.2,270.7,63.4,'2025-12-01 13:46:33'),(34,NULL,67,160,43,'Female','bulk',2306,120.6,300.1,69.2,'2025-12-01 13:48:27'),(35,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-01 13:54:29'),(36,NULL,65,170,19,'Male','cut',2499,117,351.6,69.4,'2025-12-01 13:57:24'),(37,NULL,65,170,19,'Male','cut',2499,117,351.6,69.4,'2025-12-01 13:59:36'),(38,NULL,65,170,19,'Male','cut',2499,117,351.6,69.4,'2025-12-01 14:00:01'),(39,NULL,65,170,19,'Male','cut',2499,117,351.6,69.4,'2025-12-01 14:00:14'),(40,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-01 16:07:53'),(41,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-02 16:08:41'),(42,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-02 16:28:21'),(43,NULL,70,170,20,'Male','bulk',3176,126,453.7,95.3,'2025-12-02 16:31:13'),(44,NULL,39.5,149.6,17,'Male','maintain',1938,71.1,282.6,58.1,'2025-12-02 16:47:15'),(45,NULL,70,160,17,'Male','maintain',2511,126,332.3,75.3,'2025-12-02 17:04:51'),(46,NULL,83,183,20,'Male','bulk',3541,149.4,496.9,106.2,'2025-12-03 10:01:40'),(47,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-03 18:54:40'),(48,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:21:24'),(49,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:23:17'),(50,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:23:38'),(51,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:23:49'),(52,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:24:17'),(53,NULL,65,180,19,'Male','bulk',3207,117,468.2,96.2,'2025-12-03 19:24:43'),(54,NULL,70,190,20,'Male','maintain',3092,126,438.2,92.8,'2025-12-03 21:15:22'),(55,NULL,70,170,19,'Male','bulk',3185,126,455.2,95.6,'2025-12-04 08:34:53'),(56,NULL,65,170,19,'Male','maintain',2799,117,393.7,84,'2025-12-04 09:15:07'),(57,NULL,80,100,80,'Male','maintain',1236,144,81.5,37.1,'2025-12-05 07:34:08'),(58,NULL,93,182,26,'Male','cut',3051,167.4,404.7,84.7,'2025-12-05 12:40:42'),(59,NULL,53,157,19,'Male','cut',1903,95.4,261.3,52.9,'2025-12-05 12:55:39'),(60,NULL,65,170,19,'Male','bulk',3099,117,448.5,93,'2025-12-08 17:25:19'),(61,NULL,62,173,19,'Male','bulk',2797,111.6,399,83.9,'2026-07-14 14:26:45'),(62,NULL,62,170,20,'Male','bulk',2761,111.6,392.3,82.8,'2026-07-14 14:28:02');
/*!40000 ALTER TABLE `user_results` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ayham','ayham10@gmail.com','$2y$10$9lkS9tSD.2g2YkCvhxMYzO9v6hqoKArymuzOG1OF6zzecchNlJHQS','2025-11-26 17:50:14'),(2,'ayham1','ayham1@gmail.com','$2y$10$K108UgmN4X47dWvygtjFhOBTjXgz5L0tpIYQ8s6ilgNoGLUy1Dqr.','2025-11-26 17:51:23'),(3,'ayham3','ayham3@gmail.com','$2y$10$AlhJoGHt5b2cciuUp45bUOnTM1qMSaLoDJUY983STd8c1vrnyI22m','2025-11-26 17:53:38'),(4,'ayham','ayhamtaha10@gmail.com','$2y$10$sYanT8ETS9jfFv.YA1Vjseor5QyTfoW8dNFyUD/ePh1I1uUHWXxRu','2025-12-05 07:32:26'),(5,'ayham','ayham101@gmail.com','$2y$10$GeUP5Vk5Iy2BzyjY5/wOteJligthc4yg5ma9gtpiSBneVRGrOSdV.','2025-12-05 09:29:20'),(6,'Dr mostafa chehatly','mostafa@gmail.com','$2y$10$Yca3rAG9bsHoqYYCHS/9guNcSUJgci6djsqxfPGS8aalNlTBUaJCO','2025-12-05 12:39:17'),(8,'ayham','ayhhm@gmail.com','$2y$10$6wx/p7T1nW3e8dJ6Ml0Zmu7nFflLwKmCx5Zt.Lor7byCmTdZOk4/a','2025-12-08 17:23:31'),(9,'ayham','taha@gmail.com','$2y$10$3SdCRmU0/upEUO4wYp1el.ZJvCTxafAVyrDPEp/B2r3vAWo3IXuDu','2025-12-08 17:30:20'),(11,'Ayham Taha','ayhamtaha100@gmail.com','$2y$10$Ii/WtW2K7bHztxYtJxUYe.qkYCBil98GAZ6A8nZaxnxR2IDxBd.Fy','2026-07-14 14:25:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `membership_plans`;
DROP TABLE IF EXISTS `members`;
DROP TABLE IF EXISTS `gym_staff`;
DROP TABLE IF EXISTS `gyms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gyms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gyms_status` (`status`),
  KEY `idx_gyms_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `gym_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('owner','manager','receptionist','trainer') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gym_staff_user` (`user_id`),
  UNIQUE KEY `uq_gym_staff_gym_user` (`gym_id`,`user_id`),
  KEY `idx_gym_staff_gym_active` (`gym_id`,`is_active`),
  KEY `idx_gym_staff_role` (`role`),
  CONSTRAINT `fk_gym_staff_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_gym_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `member_number` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_to_say') DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(30) DEFAULT NULL,
  `join_date` date NOT NULL,
  `status` enum('active','inactive','archived') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_members_gym_number` (`gym_id`,`member_number`),
  KEY `idx_members_gym_status_name` (`gym_id`,`status`,`last_name`,`first_name`),
  KEY `idx_members_gym_phone` (`gym_id`,`phone`),
  KEY `idx_members_gym_email` (`gym_id`,`email`),
  KEY `idx_members_gym_join_date` (`gym_id`,`join_date`),
  KEY `idx_members_created_by` (`created_by`),
  KEY `idx_members_archived_by` (`archived_by`),
  CONSTRAINT `fk_members_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_members_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_members_archived_by` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `membership_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gym_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_value` int(11) NOT NULL,
  `duration_unit` enum('day','week','month','year') NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `currency` enum('USD','LBP') NOT NULL,
  `freeze_days_allowed` int(11) NOT NULL DEFAULT 0,
  `visit_limit` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_membership_plans_gym_name` (`gym_id`,`name`),
  KEY `idx_membership_plans_gym_active_name` (`gym_id`,`is_active`,`name`),
  KEY `idx_membership_plans_created_by` (`created_by`),
  KEY `idx_membership_plans_updated_by` (`updated_by`),
  CONSTRAINT `fk_membership_plans_gym` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_membership_plans_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_membership_plans_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `chk_membership_plans_duration_value` CHECK (`duration_value` > 0),
  CONSTRAINT `chk_membership_plans_price` CHECK (`price` >= 0),
  CONSTRAINT `chk_membership_plans_freeze_days` CHECK (`freeze_days_allowed` >= 0),
  CONSTRAINT `chk_membership_plans_visit_limit` CHECK (`visit_limit` IS NULL OR `visit_limit` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
