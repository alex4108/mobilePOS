-- MySQL dump 10.13  Distrib 5.6.24, for Linux (x86_64)
--
-- Host: localhost    Database: iTrap
-- ------------------------------------------------------
-- Server version	5.6.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cash`
--

DROP TABLE IF EXISTS `cash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash` (
  `location` text NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash`
--

LOCK TABLES `cash` WRITE;
/*!40000 ALTER TABLE `cash` DISABLE KEYS */;
INSERT INTO `cash` VALUES ('store',628),('pmb',140),('alex',0);
/*!40000 ALTER TABLE `cash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(40) NOT NULL,
  `name` varchar(40) NOT NULL,
  `phone` varchar(40) NOT NULL,
  `notes` longtext NOT NULL,
  `notifications` int(11) NOT NULL,
  `balance` double NOT NULL,
  `credit` double NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (7,'alexis','ALEXIS','9999999999','',0,205,5),(6,'allen','Allen','99999999999','',0,50,0),(30,'Alpha','','','',0,10,1),(29,'ashley','','','alexis friend',0,20,0),(47,'bart','bart','','eat my shorts',0,0,3),(3,'boss','Will\'s Boss','99999999999','',1,50,0),(37,'buck','','','',0,50,4),(12,'caleb','caleb','','',0,60,0),(11,'danny','danny','','',0,315,0),(24,'dillon','','','',0,20,0),(46,'Evan','','','',0,0,7),(10,'gabi','gabi from work','8176732127','',0,260,3),(38,'hunt','','','',0,20,1),(9,'Hunter','Hunter','','',0,300,13),(27,'hunter4392','','','north irving',0,15,0),(16,'ian','ian','','',0,305,0),(13,'jack','jack','','',0,30,3),(22,'jackson','','','',0,30,4),(4,'jacob','Jacob','9999999999','',0,30,0),(23,'jessica','','','',0,115,5),(15,'joanna','joanna','','',0,45,2),(17,'joe','','','',0,180,0),(8,'kenzie','kenzie','9999999999','',0,0,0),(31,'kim','','','',0,30,2),(52,'kristan marriot','kristan','','lives in building 33',0,0,3),(40,'matt 4354','','','',0,90,1),(20,'moff','','','',0,175,5),(51,'Morgan','','','Waffle\'s Sister',0,0,4),(32,'mortal X','rio','14806968645','wils new customer ',0,50,3),(19,'nick','','','',0,230,7),(39,'paul','','','',0,0,0),(25,'rex','','','',0,29.26,1),(26,'ried','','','',0,30,2),(14,'ryan','ryan','','',0,60,1),(33,'ryan j','','','',0,20,1),(36,'Sabrina','','','',0,140,1),(21,'Shayne','','','',0,25,0),(50,'store','','','',0,0,0),(49,'store2','','','',0,0,1),(5,'trent','Trent','8157090508','Edmund',0,120,3),(48,'truston','','','',0,0,2),(28,'VTLR','','','',0,145,1),(41,'walter','','','',0,140,0),(2,'wil','Will','1000000000','',0,0,0),(18,'will','','','',0,107,0);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `amount` double NOT NULL,
  `avgprice` double NOT NULL DEFAULT '0',
  `amt_sold` double NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (1,'WA Durban Poison',0.98,10.869467195014,27.790000000000003,1),(2,'Gummy Bears (Piece)',0,20,3,1),(3,'WA Blue Dream ',0,14.563106796117,1.03,1),(4,'Blondie',7.47,8.701714159625,75.92,1),(5,'Pineapple',2.82,13.952895228088,21.48,1);
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `cash` int(11) NOT NULL,
  `inventory` text NOT NULL,
  `points` int(11) NOT NULL,
  `admin` text NOT NULL,
  `status` int(11) NOT NULL,
  `time_due` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flag` text NOT NULL,
  `data` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  `admin` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1074 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
INSERT INTO `log` VALUES (930,'inventory','N;',1435695524,'alex'),(931,'transac','a:6:{s:9:\"timestamp\";i:1435695524;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:7:\"jackson\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"3.52\";}s:6:\"points\";i:3;}',1435695524,'alex'),(932,'cash','a:8:{s:9:\"timestamp\";i:1435696064;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"mgmt\";s:8:\"location\";s:5:\"store\";s:9:\"direction\";s:3:\"out\";s:8:\"cashOrig\";s:3:\"375\";s:7:\"cashNew\";i:193;s:4:\"note\";s:12:\"payroll_will\";}',1435696064,'alex'),(933,'cash','a:8:{s:9:\"timestamp\";i:1435696073;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"mgmt\";s:8:\"location\";s:5:\"store\";s:9:\"direction\";s:3:\"out\";s:8:\"cashOrig\";s:3:\"193\";s:7:\"cashNew\";i:0;s:4:\"note\";s:12:\"payroll_alex\";}',1435696073,'alex'),(934,'cash','a:9:{s:9:\"timestamp\";i:1435699829;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"50\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:1:\"0\";s:11:\"destCashNew\";i:50;}',1435699829,'alex'),(935,'inventory','N;',1435734605,'alex'),(936,'transac','a:6:{s:9:\"timestamp\";i:1435734606;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"20\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.01\";}s:6:\"points\";i:4;}',1435734606,'alex'),(937,'cash','a:9:{s:9:\"timestamp\";i:1435734615;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"20\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:2:\"50\";s:11:\"destCashNew\";i:70;}',1435734615,'alex'),(938,'inventory','N;',1435734898,'alex'),(939,'transac','a:6:{s:9:\"timestamp\";i:1435734898;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"jack\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"3\";s:6:\"amount\";s:4:\"1.03\";}s:6:\"points\";i:2;}',1435734898,'alex'),(940,'cash','a:9:{s:9:\"timestamp\";i:1435735215;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"15\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:2:\"70\";s:11:\"destCashNew\";i:85;}',1435735215,'alex'),(941,'inventory','N;',1435780871,'alex'),(942,'transac','a:6:{s:9:\"timestamp\";i:1435780871;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"nick\";s:6:\"amount\";s:2:\"60\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"2\";s:6:\"amount\";s:1:\"3\";}s:6:\"points\";i:7;}',1435780871,'alex'),(943,'inventory','a:6:{s:9:\"timestamp\";i:1435780878;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:1;s:13:\"inventory_old\";s:5:\"22.97\";s:13:\"inventory_new\";s:5:\"22.97\";}',1435780878,'alex'),(944,'inventory','a:6:{s:9:\"timestamp\";i:1435780878;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:2;s:13:\"inventory_old\";s:1:\"1\";s:13:\"inventory_new\";s:1:\"0\";}',1435780878,'alex'),(945,'inventory','a:6:{s:9:\"timestamp\";i:1435780878;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:3;s:13:\"inventory_old\";s:4:\"0.47\";s:13:\"inventory_new\";s:4:\"0.47\";}',1435780878,'alex'),(946,'cash','a:9:{s:9:\"timestamp\";i:1435869094;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"60\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:2:\"85\";s:11:\"destCashNew\";i:145;}',1435869094,'alex'),(947,'data','a:5:{s:9:\"timestamp\";i:1435900999;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:4:\"Evan\";s:4:\"name\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:0:\"\";}}',1435900999,'alex'),(948,'inventory','N;',1435901006,'alex'),(949,'transac','a:6:{s:9:\"timestamp\";i:1435901006;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"Evan\";s:6:\"amount\";s:2:\"60\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"3.94\";}s:6:\"points\";i:3;}',1435901006,'alex'),(950,'inventory','N;',1435901077,'alex'),(951,'transac','a:6:{s:9:\"timestamp\";i:1435901077;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.08\";}s:6:\"points\";i:5;}',1435901077,'alex'),(952,'cash','a:9:{s:9:\"timestamp\";i:1435901121;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"75\";s:13:\"sourceCashNew\";i:60;s:12:\"destCashOrig\";s:3:\"145\";s:11:\"destCashNew\";i:160;}',1435901121,'alex'),(953,'inventory','N;',1435950098,'pmb'),(954,'transac','a:6:{s:9:\"timestamp\";i:1435950098;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:6:\"alexis\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:3:\"3.7\";}s:6:\"points\";i:5;}',1435950098,'pmb'),(955,'cash','a:9:{s:9:\"timestamp\";i:1435955362;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"60\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"160\";s:11:\"destCashNew\";i:220;}',1435955362,'alex'),(956,'cash','a:8:{s:9:\"timestamp\";i:1435955376;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"mgmt\";s:8:\"location\";s:5:\"store\";s:9:\"direction\";s:2:\"in\";s:8:\"cashOrig\";s:3:\"220\";s:7:\"cashNew\";i:475;s:4:\"note\";s:3:\"Ian\";}',1435955376,'alex'),(957,'cash','a:9:{s:9:\"timestamp\";i:1435957125;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"will\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"50\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"475\";s:11:\"destCashNew\";i:525;}',1435957125,'alex'),(958,'data','a:5:{s:9:\"timestamp\";i:1435958855;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:4:\"bart\";s:4:\"name\";s:4:\"bart\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:13:\"eat my shorts\";}}',1435958855,'pmb'),(959,'inventory','N;',1435958881,'pmb'),(960,'transac','a:6:{s:9:\"timestamp\";i:1435958881;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:4:\"bart\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:3:\"3.7\";}s:6:\"points\";i:3;}',1435958881,'pmb'),(961,'data','a:5:{s:9:\"timestamp\";i:1435992707;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:7:\"truston\";s:4:\"name\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:0:\"\";}}',1435992707,'pmb'),(962,'inventory','N;',1435992722,'pmb'),(963,'transac','a:6:{s:9:\"timestamp\";i:1435992722;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:7:\"truston\";s:6:\"amount\";s:2:\"30\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"2.26\";}s:6:\"points\";i:2;}',1435992722,'pmb'),(964,'cash','a:9:{s:9:\"timestamp\";i:1435992730;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:1:\"0\";s:13:\"sourceCashNew\";i:-30;s:12:\"destCashOrig\";s:3:\"525\";s:11:\"destCashNew\";i:555;}',1435992730,'pmb'),(965,'cash','a:9:{s:9:\"timestamp\";i:1435992739;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"will\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"80\";s:13:\"sourceCashNew\";i:30;s:12:\"destCashOrig\";s:3:\"555\";s:11:\"destCashNew\";i:605;}',1435992739,'pmb'),(966,'cash','a:9:{s:9:\"timestamp\";i:1435992960;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:5:\"store\";s:4:\"dest\";s:4:\"alex\";s:14:\"sourceCashOrig\";s:3:\"605\";s:13:\"sourceCashNew\";i:575;s:12:\"destCashOrig\";s:3:\"-30\";s:11:\"destCashNew\";i:0;}',1435992960,'alex'),(967,'cash','a:9:{s:9:\"timestamp\";i:1435992974;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"will\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"30\";s:13:\"sourceCashNew\";i:-20;s:12:\"destCashOrig\";s:3:\"575\";s:11:\"destCashNew\";i:625;}',1435992974,'alex'),(968,'cash','a:9:{s:9:\"timestamp\";i:1435992989;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:5:\"store\";s:4:\"dest\";s:4:\"will\";s:14:\"sourceCashOrig\";s:3:\"625\";s:13:\"sourceCashNew\";i:605;s:12:\"destCashOrig\";s:3:\"-20\";s:11:\"destCashNew\";i:0;}',1435992989,'alex'),(969,'inventory','N;',1436137833,'pmb'),(970,'transac','a:6:{s:9:\"timestamp\";i:1436137833;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:7:\"jessica\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:1:\"1\";}s:6:\"points\";i:5;}',1436137833,'pmb'),(971,'cash','a:9:{s:9:\"timestamp\";i:1436137849;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"will\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"15\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"605\";s:11:\"destCashNew\";i:620;}',1436137849,'pmb'),(972,'inventory','a:6:{s:9:\"timestamp\";i:1436137916;s:5:\"admin\";s:3:\"pmb\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:1;s:13:\"inventory_old\";s:4:\"7.29\";s:13:\"inventory_new\";s:4:\"7.29\";}',1436137916,'pmb'),(973,'inventory','a:6:{s:9:\"timestamp\";i:1436137916;s:5:\"admin\";s:3:\"pmb\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:2;s:13:\"inventory_old\";s:1:\"0\";s:13:\"inventory_new\";s:1:\"0\";}',1436137916,'pmb'),(974,'inventory','a:6:{s:9:\"timestamp\";i:1436137916;s:5:\"admin\";s:3:\"pmb\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:3;s:13:\"inventory_old\";s:4:\"0.47\";s:13:\"inventory_new\";s:3:\"0.0\";}',1436137916,'pmb'),(975,'cash','a:8:{s:9:\"timestamp\";i:1436137973;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:4:\"mgmt\";s:8:\"location\";s:5:\"store\";s:9:\"direction\";s:3:\"out\";s:8:\"cashOrig\";s:3:\"620\";s:7:\"cashNew\";i:15;s:4:\"note\";s:4:\"reup\";}',1436137973,'pmb'),(976,'inventory','N;',1436142804,'alex'),(977,'transac','a:6:{s:9:\"timestamp\";i:1436142804;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"hunt\";s:6:\"amount\";s:2:\"10\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:3:\".79\";}s:6:\"points\";s:1:\"1\";}',1436142804,'alex'),(978,'cash','a:9:{s:9:\"timestamp\";i:1436142812;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"10\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:2:\"15\";s:11:\"destCashNew\";i:25;}',1436142812,'alex'),(979,'cash','a:8:{s:9:\"timestamp\";i:1436148922;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"mgmt\";s:8:\"location\";s:5:\"store\";s:9:\"direction\";s:3:\"out\";s:8:\"cashOrig\";s:2:\"25\";s:7:\"cashNew\";i:23;s:4:\"note\";s:12:\"payroll_alex\";}',1436148922,'alex'),(980,'inventory','N;',1436163958,'alex'),(1004,'inventory','N;',1436171048,'alex'),(1005,'transac','a:9:{s:9:\"timestamp\";i:1436171048;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"20\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"1.13\";s:6:\"oldavg\";s:1:\"0\";s:6:\"newavg\";d:17.69911504424779;}s:6:\"points\";i:5;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436187958;s:5:\"admin\";s:4:\"alex\";}}',1436171048,'alex'),(1013,'transac','a:9:{s:9:\"timestamp\";i:1436178435;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"alex\";s:6:\"amount\";s:1:\"5\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:1:\"1\";s:6:\"oldavg\";s:15:\"5.1986607108947\";s:6:\"newavg\";d:5.1910569904775707;}s:6:\"points\";i:11;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436186519;s:5:\"admin\";s:4:\"alex\";}}',1436178435,'alex'),(1014,'transac','a:9:{s:9:\"timestamp\";i:1436178453;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"alex\";s:6:\"amount\";s:1:\"5\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:1:\"1\";s:6:\"oldavg\";s:15:\"5.1910569904776\";s:6:\"newavg\";d:5.1840147816546471;}s:6:\"points\";i:11;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436186426;s:5:\"admin\";s:4:\"alex\";}}',1436178453,'alex'),(1015,'transac','a:9:{s:9:\"timestamp\";i:1436179050;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"alex\";s:6:\"amount\";s:1:\"1\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:1:\"1\";s:6:\"oldavg\";s:15:\"5.1840147816546\";s:6:\"newavg\";d:1.1289235210558219;}s:6:\"points\";i:13;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436186421;s:5:\"admin\";s:4:\"alex\";}}',1436179050,'alex'),(1016,'transac','a:9:{s:9:\"timestamp\";i:1436179060;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"alex\";s:6:\"amount\";s:1:\"9\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:1:\"9\";s:6:\"oldavg\";s:15:\"1.1289235210558\";s:6:\"newavg\";d:1.0229409372293397;}s:6:\"points\";i:14;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:5:{s:9:\"timestamp\";i:1436185574;s:5:\"admin\";s:4:\"alex\";s:11:\"invReturned\";b:1;s:12:\"cashReturned\";b:1;s:11:\"cashDropped\";b:1;}}',1436179060,'alex'),(1023,'transac','a:7:{s:9:\"timestamp\";i:1436187975;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"20\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"1.13\";s:6:\"oldavg\";s:15:\"10.875262054507\";s:6:\"newavg\";d:0.57313952753002373;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436187975,'alex'),(1024,'transac','a:9:{s:9:\"timestamp\";i:1436187993;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:4:\"1.13\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:2:\"20\";s:6:\"oldavg\";s:16:\"0.57313952753002\";s:6:\"newavg\";d:0.07046198605432448;}s:6:\"points\";i:1;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436188004;s:5:\"admin\";s:4:\"alex\";}}',1436187993,'alex'),(1025,'data','a:5:{s:9:\"timestamp\";i:1436189189;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:6:\"store2\";s:4:\"name\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:0:\"\";}}',1436189189,'alex'),(1026,'transac','a:7:{s:9:\"timestamp\";i:1436189204;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"store2\";s:6:\"amount\";s:3:\"255\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:2:\"28\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436189204,'alex'),(1027,'data','a:5:{s:9:\"timestamp\";i:1436204061;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:4:\"edit\";s:4:\"data\";a:4:{s:2:\"id\";N;s:4:\"name\";N;s:5:\"phone\";N;s:5:\"notes\";N;}}',1436204061,'pmb'),(1028,'data','a:5:{s:9:\"timestamp\";i:1436204068;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:4:\"edit\";s:4:\"data\";a:4:{s:2:\"id\";N;s:4:\"name\";N;s:5:\"phone\";N;s:5:\"notes\";N;}}',1436204068,'pmb'),(1029,'data','a:5:{s:9:\"timestamp\";i:1436206393;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:4:\"edit\";s:4:\"data\";a:4:{s:2:\"id\";N;s:4:\"name\";N;s:5:\"phone\";N;s:5:\"notes\";N;}}',1436206393,'alex'),(1030,'data','a:5:{s:9:\"timestamp\";i:1436206423;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:4:\"edit\";s:4:\"data\";a:4:{s:2:\"id\";N;s:4:\"name\";N;s:5:\"phone\";N;s:5:\"notes\";N;}}',1436206423,'alex'),(1031,'data','a:5:{s:9:\"timestamp\";i:1436206474;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:4:\"edit\";s:4:\"data\";a:4:{s:2:\"id\";N;s:4:\"name\";N;s:5:\"phone\";N;s:5:\"notes\";N;}}',1436206474,'alex'),(1032,'transac','a:7:{s:9:\"timestamp\";i:1436210622;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:7:\"jackson\";s:6:\"amount\";s:3:\"140\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:5:\"14.08\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436210622,'alex'),(1033,'cash','a:9:{s:9:\"timestamp\";i:1436210645;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:3:\"140\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:2:\"23\";s:11:\"destCashNew\";i:163;}',1436210645,'alex'),(1034,'transac','a:7:{s:9:\"timestamp\";i:1436243674;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:3:\"wil\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:3:\"1.1\";s:6:\"oldavg\";s:15:\"12.658227848101\";s:6:\"newavg\";d:6.6974750519052906;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436243674,'pmb'),(1035,'transac','a:7:{s:9:\"timestamp\";i:1436247672;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"danny\";s:6:\"amount\";s:2:\"80\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"7.03\";s:6:\"oldavg\";s:15:\"17.699115044248\";s:6:\"newavg\";d:11.732092156117389;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436247672,'alex'),(1036,'redeem','a:5:{s:9:\"timestamp\";i:1436247724;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"danny\";s:6:\"amount\";s:2:\"10\";s:9:\"inventory\";a:2:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"1.03\";}}',1436247724,'alex'),(1037,'cash','a:9:{s:9:\"timestamp\";i:1436247739;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"80\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"163\";s:11:\"destCashNew\";i:243;}',1436247739,'alex'),(1038,'transac','a:7:{s:9:\"timestamp\";i:1436247920;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"evan\";s:6:\"amount\";s:2:\"60\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"4.61\";s:6:\"oldavg\";s:15:\"11.732092156117\";s:6:\"newavg\";d:13.2251359235847;}s:6:\"points\";i:3;s:6:\"closed\";b:1;}',1436247920,'alex'),(1039,'transac','a:7:{s:9:\"timestamp\";i:1436248688;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"Evan\";s:6:\"amount\";s:2:\"20\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.43\";s:6:\"oldavg\";s:15:\"14.349332013855\";s:6:\"newavg\";d:14.649106962624661;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436248688,'alex'),(1040,'transac','a:7:{s:9:\"timestamp\";i:1436298357;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:4:\"buck\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.15\";s:6:\"oldavg\";s:15:\"14.649106962625\";s:6:\"newavg\";d:13.686264876166845;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436298357,'pmb'),(1041,'cash','a:9:{s:9:\"timestamp\";i:1436303368;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"80\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"243\";s:11:\"destCashNew\";i:323;}',1436303368,'alex'),(1042,'transac','a:9:{s:9:\"timestamp\";i:1436303383;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:3:\"wil\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:4:\"0.35\";s:6:\"oldavg\";s:15:\"6.6974750519053\";s:6:\"newavg\";d:2.9899442196005803;}s:6:\"points\";N;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436308886;s:5:\"admin\";s:4:\"alex\";}}',1436303383,'pmb'),(1043,'cash','a:9:{s:9:\"timestamp\";i:1436303544;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:3:\"pmb\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"15\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"323\";s:11:\"destCashNew\";i:338;}',1436303544,'alex'),(1044,'transac','a:7:{s:9:\"timestamp\";i:1436308908;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:3:\"wil\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:6:\" 	0.35\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436308908,'alex'),(1045,'transac','a:7:{s:9:\"timestamp\";i:1436310619;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:4:\"jack\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:4:\"1.16\";s:6:\"oldavg\";s:15:\"8.5964627477785\";s:6:\"newavg\";d:15.459405879164063;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436310619,'alex'),(1046,'cash','a:9:{s:9:\"timestamp\";i:1436315751;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"15\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"338\";s:11:\"destCashNew\";i:353;}',1436315751,'alex'),(1047,'transac','a:9:{s:9:\"timestamp\";i:1436325306;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:3:\"wil\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.05\";s:6:\"oldavg\";s:15:\"13.686264876167\";s:6:\"newavg\";d:0.57408829178552856;}s:6:\"points\";N;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436328471;s:5:\"admin\";s:4:\"alex\";}}',1436325306,'pmb'),(1048,'transac','a:7:{s:9:\"timestamp\";i:1436328483;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:3:\"wil\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.05\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436328483,'alex'),(1049,'transac','a:9:{s:9:\"timestamp\";i:1436328538;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"store2\";s:6:\"amount\";s:3:\"255\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:5:\"13.72\";s:6:\"oldavg\";s:15:\"15.459405879164\";s:6:\"newavg\";d:19.489008510761501;}s:6:\"points\";i:1;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436328640;s:5:\"admin\";s:4:\"alex\";}}',1436328538,'alex'),(1050,'transac','a:9:{s:9:\"timestamp\";i:1436328597;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"store2\";s:6:\"amount\";s:0:\"\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:5:\"13.99\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436328615;s:5:\"admin\";s:4:\"alex\";}}',1436328597,'alex'),(1051,'transac','a:7:{s:9:\"timestamp\";i:1436328626;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"store2\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:5:\"13.99\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:0;}',1436328626,'alex'),(1052,'transac','a:7:{s:9:\"timestamp\";i:1436328662;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"store2\";s:6:\"amount\";s:3:\"255\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:5:\"13.72\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:0;}',1436328662,'alex'),(1053,'data','a:5:{s:9:\"timestamp\";i:1436328753;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:5:\"store\";s:4:\"name\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:0:\"\";}}',1436328753,'alex'),(1054,'transac','a:7:{s:9:\"timestamp\";i:1436328763;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"store\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:3:\".68\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436328763,'alex'),(1055,'cash','a:9:{s:9:\"timestamp\";i:1436365671;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:3:\"255\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"353\";s:11:\"destCashNew\";i:608;}',1436365671,'alex'),(1056,'transac','a:9:{s:9:\"timestamp\";i:1436365892;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:3:\"3.5\";s:6:\"oldavg\";s:15:\"8.6015772445403\";s:6:\"newavg\";d:14.402949903058436;}s:6:\"points\";i:3;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436413059;s:5:\"admin\";s:3:\"pmb\";}}',1436365892,'alex'),(1057,'transac','a:9:{s:9:\"timestamp\";i:1436365902;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:2:\".5\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436413063;s:5:\"admin\";s:3:\"pmb\";}}',1436365902,'alex'),(1058,'data','a:5:{s:9:\"timestamp\";i:1436365934;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:6:\"Morgan\";s:4:\"name\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:16:\"Waffle\'s Sister\";}}',1436365934,'alex'),(1059,'transac','a:9:{s:9:\"timestamp\";i:1436366270;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:6:\"Morgan\";s:6:\"amount\";s:2:\"25\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"2.37\";s:6:\"oldavg\";s:15:\"14.402949903058\";s:6:\"newavg\";d:10.73868626330054;}s:6:\"points\";i:2;s:6:\"closed\";b:1;s:4:\"void\";b:1;s:11:\"void_detail\";a:2:{s:9:\"timestamp\";i:1436413066;s:5:\"admin\";s:3:\"pmb\";}}',1436366270,'alex'),(1060,'inventory','a:6:{s:9:\"timestamp\";i:1436366288;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:1;s:13:\"inventory_old\";s:4:\"1.75\";s:13:\"inventory_new\";s:4:\"4.43\";}',1436366288,'alex'),(1061,'inventory','a:6:{s:9:\"timestamp\";i:1436366288;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:2;s:13:\"inventory_old\";s:1:\"0\";s:13:\"inventory_new\";s:1:\"0\";}',1436366288,'alex'),(1062,'inventory','a:6:{s:9:\"timestamp\";i:1436366288;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:3;s:13:\"inventory_old\";s:1:\"0\";s:13:\"inventory_new\";s:1:\"0\";}',1436366288,'alex'),(1063,'inventory','a:6:{s:9:\"timestamp\";i:1436366288;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:4;s:13:\"inventory_old\";s:4:\"7.96\";s:13:\"inventory_new\";s:4:\"7.65\";}',1436366288,'alex'),(1064,'inventory','a:6:{s:9:\"timestamp\";i:1436366288;s:5:\"admin\";s:4:\"alex\";s:4:\"flag\";s:14:\"post_inventory\";s:12:\"inventory_id\";i:5;s:13:\"inventory_old\";s:5:\"10.14\";s:13:\"inventory_new\";s:3:\"6.5\";}',1436366288,'alex'),(1065,'cash','a:9:{s:9:\"timestamp\";i:1436366328;s:5:\"admin\";s:4:\"alex\";s:4:\"type\";s:4:\"drop\";s:6:\"source\";s:4:\"alex\";s:4:\"dest\";s:5:\"store\";s:14:\"sourceCashOrig\";s:2:\"95\";s:13:\"sourceCashNew\";i:0;s:12:\"destCashOrig\";s:3:\"608\";s:11:\"destCashNew\";i:703;}',1436366328,'alex'),(1066,'transac','a:7:{s:9:\"timestamp\";i:1436366811;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"store\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:3:\".58\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436366811,'alex'),(1067,'transac','a:7:{s:9:\"timestamp\";i:1436394473;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:4:\"ryan\";s:6:\"amount\";s:2:\"15\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"1.12\";s:6:\"oldavg\";s:15:\"8.5378762541806\";s:6:\"newavg\";d:13.728201850405476;}s:6:\"points\";i:1;s:6:\"closed\";b:1;}',1436394473,'pmb'),(1068,'transac','a:7:{s:9:\"timestamp\";i:1436413116;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:6:\"hunter\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:3:\"4.3\";s:6:\"oldavg\";s:15:\"8.7713991992209\";s:6:\"newavg\";d:11.745250109844132;}s:6:\"points\";i:3;s:6:\"closed\";b:1;}',1436413116,'pmb'),(1069,'transac','a:7:{s:9:\"timestamp\";i:1436413147;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:6:\"morgan\";s:6:\"amount\";s:2:\"25\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:4:\"2.37\";s:6:\"oldavg\";s:15:\"8.7713991992209\";s:6:\"newavg\";d:10.869467195013803;}s:6:\"points\";i:2;s:6:\"closed\";b:1;}',1436413147,'pmb'),(1070,'transac','a:7:{s:9:\"timestamp\";i:1436415649;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"store\";s:6:\"amount\";s:2:\"10\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"4\";s:6:\"amount\";s:4:\"1.17\";s:6:\"oldavg\";s:15:\"11.745250109844\";s:6:\"newavg\";d:8.7017141596250376;}s:6:\"points\";N;s:6:\"closed\";b:0;}',1436415649,'alex'),(1071,'data','a:5:{s:9:\"timestamp\";i:1436417044;s:5:\"admin\";s:3:\"pmb\";s:4:\"type\";s:8:\"customer\";s:6:\"action\";s:3:\"new\";s:4:\"data\";a:4:{s:2:\"id\";s:15:\"kristan marriot\";s:4:\"name\";s:7:\"kristan\";s:5:\"phone\";s:0:\"\";s:5:\"notes\";s:20:\"lives in building 33\";}}',1436417044,'pmb'),(1072,'transac','a:7:{s:9:\"timestamp\";i:1436417672;s:5:\"admin\";s:3:\"pmb\";s:8:\"customer\";s:15:\"kristan marriot\";s:6:\"amount\";s:2:\"50\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"5\";s:6:\"amount\";s:4:\"3.68\";s:6:\"oldavg\";s:15:\"7.8603634123807\";s:6:\"newavg\";d:13.952895228088323;}s:6:\"points\";i:3;s:6:\"closed\";b:1;}',1436417672,'pmb'),(1073,'transac','a:7:{s:9:\"timestamp\";i:1436421490;s:5:\"admin\";s:4:\"alex\";s:8:\"customer\";s:5:\"store\";s:6:\"amount\";s:1:\"0\";s:9:\"inventory\";a:4:{s:2:\"id\";s:1:\"1\";s:6:\"amount\";s:3:\".46\";s:6:\"oldavg\";N;s:6:\"newavg\";N;}s:6:\"points\";N;s:6:\"closed\";b:1;}',1436421490,'alex');
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `setting` varchar(40) NOT NULL,
  `value` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('logNumber','9999999999'),('biteSMS',''),('creditTrigger','150'),('creditAmt','15'),('textRedemption','0'),('textTransaction','0'),('textMessage','0'),('textMinor','0'),('textMajor','0'),('wtdStart','1436245200');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `cash` int(11) NOT NULL,
  `inventory` text NOT NULL,
  `points` int(11) NOT NULL,
  `admin` text NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `access` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'alex','891a1a5ba4ade3f95e6fd0e77c407b43',2),(2,'pmb','579dbb491c7910ff361503bf1a1a340c',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-09  1:40:14
