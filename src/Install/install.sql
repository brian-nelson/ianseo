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
-- Table structure for table `ACL`
--

DROP TABLE IF EXISTS `ACL`;
CREATE TABLE `ACL` (
  `AclTournament` int NOT NULL,
  `AclIP` varchar(15) NOT NULL,
  `AclNick` varchar(50) NOT NULL,
  `AclEnabled` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`AclTournament`,`AclIP`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `AccColors`
--

DROP TABLE IF EXISTS `AccColors`;
CREATE TABLE `AccColors` (
  `AcTournament` int unsigned NOT NULL,
  `AcDivClass` varchar(10) NOT NULL,
  `AcColor` varchar(6) NOT NULL,
  `AcIsAthlete` tinyint unsigned NOT NULL,
  `AcTitleReverse` tinyint NOT NULL DEFAULT '0',
  `AcArea0` tinyint NOT NULL DEFAULT '0',
  `AcArea1` tinyint NOT NULL DEFAULT '0',
  `AcArea2` tinyint NOT NULL DEFAULT '0',
  `AcArea3` tinyint NOT NULL DEFAULT '0',
  `AcArea4` tinyint NOT NULL DEFAULT '0',
  `AcArea5` tinyint NOT NULL DEFAULT '0',
  `AcArea6` tinyint NOT NULL DEFAULT '0',
  `AcArea7` tinyint NOT NULL DEFAULT '0',
  `AcAreaStar` tinyint NOT NULL DEFAULT '0',
  `AcTransport` tinyint NOT NULL DEFAULT '0',
  `AcAccomodation` tinyint NOT NULL DEFAULT '0',
  `AcMeal` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`AcTournament`,`AcDivClass`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `AccEntries`
--

DROP TABLE IF EXISTS `AccEntries`;
CREATE TABLE `AccEntries` (
  `AEId` int unsigned NOT NULL DEFAULT '0',
  `AEOperation` int NOT NULL,
  `AETournament` int unsigned NOT NULL DEFAULT '0',
  `AEWhen` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `AEFromIp` int unsigned NOT NULL DEFAULT '0',
  `AERapp` tinyint unsigned NOT NULL DEFAULT '0',
  `AEExtra` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`AEId`,`AEOperation`,`AETournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `AccOperationType`
--

DROP TABLE IF EXISTS `AccOperationType`;
CREATE TABLE `AccOperationType` (
  `AOTId` smallint unsigned NOT NULL AUTO_INCREMENT,
  `AOTDescr` varchar(32) NOT NULL,
  `AOTOrder` tinyint NOT NULL,
  PRIMARY KEY (`AOTId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `AccOperationType`
--

LOCK TABLES `AccOperationType` WRITE;
/*!40000 ALTER TABLE `AccOperationType` DISABLE KEYS */;
INSERT INTO `AccOperationType` VALUES (1,'Accreditation',10),(2,'ControlMaterial',20),(3,'Payments',5);
/*!40000 ALTER TABLE `AccOperationType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AccPrice`
--

DROP TABLE IF EXISTS `AccPrice`;
CREATE TABLE `AccPrice` (
  `APId` int unsigned NOT NULL AUTO_INCREMENT,
  `APTournament` int unsigned NOT NULL DEFAULT '0',
  `APDivClass` varchar(10) NOT NULL,
  `APPrice` float(15,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`APId`),
  UNIQUE KEY `APTournament` (`APTournament`,`APDivClass`)
) ENGINE=MyISAM AUTO_INCREMENT=1268 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `AclDetails`
--

DROP TABLE IF EXISTS `AclDetails`;
CREATE TABLE `AclDetails` (
  `AclDtTournament` int NOT NULL,
  `AclDtIP` varchar(15) NOT NULL,
  `AclDtFeature` tinyint NOT NULL DEFAULT '0',
  `AclDtLevel` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`AclDtTournament`,`AclDtIP`,`AclDtFeature`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `AclFeatures`
--

DROP TABLE IF EXISTS `AclFeatures`;
CREATE TABLE `AclFeatures` (
  `AclFeId` tinyint NOT NULL,
  `AclFeName` varchar(50) NOT NULL,
  UNIQUE KEY `AclFeId` (`AclFeId`),
  UNIQUE KEY `AclFeName` (`AclFeName`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `AclFeatures`
--

LOCK TABLES `AclFeatures` WRITE;
/*!40000 ALTER TABLE `AclFeatures` DISABLE KEYS */;
INSERT INTO `AclFeatures` VALUES (3,'MenuLM_Accreditation'),(4,'MenuLM_Athletes Sync.'),(1,'MenuLM_Competition'),(6,'MenuLM_Eliminations'),(7,'MenuLM_Individual Finals'),(0,'MenuLM_Modules'),(10,'MenuLM_Output'),(2,'MenuLM_Participants'),(5,'MenuLM_Qualification'),(9,'MenuLM_Speaker'),(8,'MenuLM_Team Finals');
/*!40000 ALTER TABLE `AclFeatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AvailableTarget`
--

DROP TABLE IF EXISTS `AvailableTarget`;
CREATE TABLE `AvailableTarget` (
  `AtTournament` int unsigned NOT NULL,
  `AtTargetNo` varchar(5) NOT NULL,
  `AtSession` tinyint unsigned NOT NULL,
  `AtTarget` int NOT NULL,
  `AtLetter` varchar(1) NOT NULL,
  PRIMARY KEY (`AtTournament`,`AtTargetNo`),
  KEY `AtTournament` (`AtTournament`,`AtSession`,`AtTarget`,`AtLetter`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Awarded`
--

DROP TABLE IF EXISTS `Awarded`;
CREATE TABLE `Awarded` (
  `AwEntry` int NOT NULL,
  `AwTournament` int NOT NULL,
  `AwDivision` varchar(4) NOT NULL,
  `AwClass` varchar(6) NOT NULL,
  `AwSubClass` varchar(2) NOT NULL,
  `AwRank` int NOT NULL,
  `AwValue` decimal(12,2) NOT NULL,
  `AwPrinted` varchar(1) NOT NULL,
  `AwMailed` varchar(1) NOT NULL,
  `AwReference` varchar(25) NOT NULL,
  `AwExtra` text NOT NULL,
  PRIMARY KEY (`AwEntry`),
  KEY `AwTournament` (`AwTournament`,`AwDivision`,`AwClass`,`AwSubClass`,`AwEntry`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Awards`
--

DROP TABLE IF EXISTS `Awards`;
CREATE TABLE `Awards` (
  `AwTournament` int unsigned NOT NULL,
  `AwEvent` varchar(15) NOT NULL,
  `AwFinEvent` tinyint NOT NULL,
  `AwTeam` tinyint NOT NULL,
  `AwUnrewarded` tinyint NOT NULL,
  `AwPositions` varchar(16) NOT NULL,
  `AwDescription` text NOT NULL,
  `AwAwarders` text NOT NULL,
  `AwAwarderGrouping` text NOT NULL,
  `AwGroup` tinyint NOT NULL,
  `AwOrder` tinyint NOT NULL,
  `AwEventTrans` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`AwTournament`,`AwEvent`,`AwFinEvent`,`AwTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `BackNumber`
--

DROP TABLE IF EXISTS `BackNumber`;
CREATE TABLE `BackNumber` (
  `BnTournament` int unsigned NOT NULL,
  `BnFinal` tinyint NOT NULL,
  `BnHeight` smallint unsigned NOT NULL,
  `BnWidth` smallint unsigned NOT NULL,
  `BnBackground` mediumblob NOT NULL,
  `BnBgX` smallint NOT NULL,
  `BnBgY` smallint NOT NULL,
  `BnBgW` smallint NOT NULL,
  `BnBgH` smallint NOT NULL,
  `BnTargetNo` tinyint unsigned NOT NULL,
  `BnTnoColor` varchar(6) NOT NULL DEFAULT '000000',
  `BnTnoSize` smallint NOT NULL,
  `BnTnoX` smallint NOT NULL,
  `BnTnoY` smallint NOT NULL,
  `BnTnoW` smallint NOT NULL,
  `BnTnoH` smallint NOT NULL,
  `BnAthlete` tinyint unsigned NOT NULL,
  `BnAthColor` varchar(6) NOT NULL DEFAULT '000000',
  `BnAthSize` smallint NOT NULL,
  `BnAthX` smallint NOT NULL,
  `BnAthY` smallint NOT NULL,
  `BnAthW` smallint NOT NULL,
  `BnAthH` smallint NOT NULL,
  `BnCountry` tinyint unsigned NOT NULL,
  `BnCoColor` varchar(6) NOT NULL DEFAULT '000000',
  `BnCoSize` smallint NOT NULL,
  `BnCoX` smallint NOT NULL,
  `BnCoY` smallint NOT NULL,
  `BnCoW` smallint NOT NULL,
  `BnCoH` smallint NOT NULL,
  `BnOffsetX` smallint NOT NULL,
  `BnOffsetY` smallint NOT NULL,
  `BnCapitalFirstName` varchar(1) NOT NULL,
  `BnGivenNameInitial` varchar(1) NOT NULL,
  `BnCountryCodeOnly` varchar(1) NOT NULL,
  `BnIncludeSession` tinyint NOT NULL,
  PRIMARY KEY (`BnTournament`,`BnFinal`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `BoinxSchedule`
--

DROP TABLE IF EXISTS `BoinxSchedule`;
CREATE TABLE `BoinxSchedule` (
  `BsTournament` int NOT NULL,
  `BsType` varchar(25) NOT NULL,
  `BsExtra` varchar(25) NOT NULL,
  PRIMARY KEY (`BsTournament`,`BsType`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `CasGrid`
--

DROP TABLE IF EXISTS `CasGrid`;
CREATE TABLE `CasGrid` (
  `CGPhase` tinyint unsigned NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
  `CGRound` tinyint unsigned NOT NULL,
  `CGMatchNo1` tinyint unsigned NOT NULL,
  `CGMatchNo2` tinyint unsigned NOT NULL,
  `CGGroup` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CGPhase`,`CGRound`,`CGMatchNo1`,`CGMatchNo2`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `CasGrid`
--

LOCK TABLES `CasGrid` WRITE;
/*!40000 ALTER TABLE `CasGrid` DISABLE KEYS */;
INSERT INTO `CasGrid` VALUES (1,1,1,16,1),(1,1,2,15,2),(1,1,3,14,3),(1,1,4,13,4),(1,1,5,9,4),(1,1,6,10,3),(1,1,7,11,2),(1,1,8,12,1),(1,2,1,12,1),(1,2,2,11,2),(1,2,3,10,3),(1,2,4,9,4),(1,2,5,13,4),(1,2,6,14,3),(1,2,7,15,2),(1,2,8,16,1),(1,3,1,8,1),(1,3,2,7,2),(1,3,3,6,3),(1,3,4,5,4),(1,3,9,13,4),(1,3,10,14,3),(1,3,11,15,2),(1,3,12,16,1),(2,1,1,8,1),(2,1,7,2,2),(2,1,3,6,3),(2,1,4,5,4),(2,1,9,13,4),(2,1,10,14,3),(2,1,15,11,2),(2,1,12,16,1),(2,2,1,16,1),(2,2,7,11,2),(2,2,3,10,3),(2,2,4,9,4),(2,2,5,13,4),(2,2,6,14,3),(2,2,15,2,2),(2,2,12,8,1),(2,3,1,12,1),(2,3,7,15,2),(2,3,3,14,3),(2,3,4,13,4),(2,3,5,9,4),(2,3,6,10,3),(2,3,2,11,2),(2,3,8,16,1),(0,2,7,3,2),(0,3,3,2,2),(0,3,7,6,2),(0,1,2,7,2),(0,1,3,6,2),(0,2,5,1,1),(0,2,6,2,2),(0,1,4,5,1),(0,3,8,5,1),(0,3,4,1,1),(0,2,8,4,1),(0,1,1,8,1);
/*!40000 ALTER TABLE `CasGrid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasGroupMatch`
--

DROP TABLE IF EXISTS `CasGroupMatch`;
CREATE TABLE `CasGroupMatch` (
  `CaGMGroup` tinyint unsigned NOT NULL,
  `CaGRank` tinyint unsigned NOT NULL,
  `CaGMMatchNo` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CaGMGroup`,`CaGMMatchNo`,`CaGRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `CasGroupMatch`
--

LOCK TABLES `CasGroupMatch` WRITE;
/*!40000 ALTER TABLE `CasGroupMatch` DISABLE KEYS */;
INSERT INTO `CasGroupMatch` VALUES (1,1,1),(1,2,2),(1,3,3),(1,4,4),(2,4,5),(2,3,6),(2,1,7),(2,2,8),(3,4,9),(3,3,10),(3,2,11),(3,1,12),(4,4,13),(4,3,14),(4,1,15),(4,2,16);
/*!40000 ALTER TABLE `CasGroupMatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasRankMatch`
--

DROP TABLE IF EXISTS `CasRankMatch`;
CREATE TABLE `CasRankMatch` (
  `CRMEventPhase` tinyint NOT NULL,
  `CRMRank` tinyint NOT NULL,
  `CRMMatchNo` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CRMEventPhase`,`CRMRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `CasRankMatch`
--

LOCK TABLES `CasRankMatch` WRITE;
/*!40000 ALTER TABLE `CasRankMatch` DISABLE KEYS */;
INSERT INTO `CasRankMatch` VALUES (16,1,1),(16,2,2),(16,3,3),(16,4,4),(16,5,5),(16,6,6),(16,7,7),(16,8,8),(16,9,9),(16,10,10),(16,11,11),(16,12,12),(16,13,13),(16,14,14),(16,15,15),(16,16,16);
/*!40000 ALTER TABLE `CasRankMatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasScore`
--

DROP TABLE IF EXISTS `CasScore`;
CREATE TABLE `CasScore` (
  `CaSTournament` int unsigned NOT NULL DEFAULT '0',
  `CaSPhase` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
  `CaSRound` tinyint unsigned NOT NULL,
  `CaSMatchNo` tinyint unsigned NOT NULL,
  `CaSEventCode` varchar(10) NOT NULL,
  `CaSTarget` varchar(3) NOT NULL,
  `CaSSetPoints` varchar(23) NOT NULL DEFAULT '',
  `CaSSetPointsByEnd` varchar(23) NOT NULL,
  `CaSSetScore` tinyint NOT NULL DEFAULT '0',
  `CaSScore` smallint NOT NULL DEFAULT '0',
  `CaSTie` tinyint(1) NOT NULL DEFAULT '0',
  `CaSWinLose` tinyint NOT NULL,
  `CaSArrowString` varchar(36) NOT NULL,
  `CaSArrowPosition` varchar(360) NOT NULL,
  `CaSTiebreak` varchar(9) NOT NULL,
  `CaSTiePosition` varchar(90) NOT NULL,
  `CaSPoints` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`CaSTournament`,`CaSPhase`,`CaSMatchNo`,`CaSEventCode`,`CaSRound`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

--
-- Table structure for table `CasTeam`
--

DROP TABLE IF EXISTS `CasTeam`;
CREATE TABLE `CasTeam` (
  `CaTournament` int unsigned NOT NULL DEFAULT '0',
  `CaPhase` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
  `CaMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `CaEventCode` varchar(10) NOT NULL,
  `CaTeam` int unsigned NOT NULL DEFAULT '0',
  `CaSubTeam` tinyint NOT NULL,
  `CaRank` tinyint unsigned NOT NULL,
  `CaTiebreak` varchar(9) NOT NULL,
  PRIMARY KEY (`CaTournament`,`CaPhase`,`CaMatchNo`,`CaEventCode`,`CaTeam`,`CaSubTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `CasTeamFinal`
--

DROP TABLE IF EXISTS `CasTeamFinal`;
CREATE TABLE `CasTeamFinal` (
  `CTFEvent` varchar(10) NOT NULL,
  `CTFMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `CTFTournament` int unsigned NOT NULL DEFAULT '0',
  `CTFSetPoints` varchar(23) NOT NULL,
  `CTFSetScore` tinyint NOT NULL DEFAULT '0',
  `CTFScore` smallint NOT NULL DEFAULT '0',
  `CTFTie` tinyint(1) NOT NULL DEFAULT '0',
  `CTFArrowString` varchar(36) NOT NULL,
  `CTFTieBreak` varchar(3) NOT NULL,
  `CTFTiePoins` varchar(5) NOT NULL,
  `CTFTieScore` smallint NOT NULL DEFAULT '0',
  `CTFScore2` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`CTFEvent`,`CTFMatchNo`,`CTFTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `CasTeamTarget`
--

DROP TABLE IF EXISTS `CasTeamTarget`;
CREATE TABLE `CasTeamTarget` (
  `CTTTournament` int unsigned NOT NULL DEFAULT '0',
  `CTTEvent` varchar(10) NOT NULL,
  `CTTMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `CTTTarget` varchar(3) NOT NULL,
  `CTTSchedule` datetime NOT NULL,
  PRIMARY KEY (`CTTTournament`,`CTTEvent`,`CTTMatchNo`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ClassWaEquivalents`
--

DROP TABLE IF EXISTS `ClassWaEquivalents`;
CREATE TABLE `ClassWaEquivalents` (
  `ClWaEqTourRule` varchar(16) NOT NULL,
  `ClWaEqFrom` tinyint unsigned NOT NULL,
  `ClWaEqTo` tinyint unsigned NOT NULL,
  `ClWaEqEvent` varchar(10) NOT NULL,
  `ClWaEqDescription` varchar(60) NOT NULL,
  `ClWaEqGender` tinyint NOT NULL,
  `ClWaEqDivision` varchar(4) NOT NULL,
  `ClWaEqAgeClass` varchar(6) NOT NULL,
  `ClWaEqMain` tinyint NOT NULL,
  `ClWaEqTeam` tinyint NOT NULL,
  `ClWaEqMixedTeam` tinyint unsigned NOT NULL,
  `ClWaEqPara` tinyint unsigned NOT NULL,
  `ClWaEqComponents` tinyint unsigned NOT NULL DEFAULT '1',
  `ClWaEqOrder` int unsigned NOT NULL,
  PRIMARY KEY (`ClWaEqTourRule`,`ClWaEqEvent`,`ClWaEqGender`,`ClWaEqDivision`,`ClWaEqAgeClass`),
  KEY `ClWaEqTourRule` (`ClWaEqTourRule`,`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqOrder`),
  KEY `ClWaEqDivision` (`ClWaEqDivision`,`ClWaEqGender`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`,`ClWaEqTeam`,`ClWaEqTourRule`,`ClWaEqOrder`),
  KEY `ClWaEqGender` (`ClWaEqGender`,`ClWaEqMixedTeam`,`ClWaEqComponents`,`ClWaEqTeam`,`ClWaEqDescription`,`ClWaEqTourRule`,`ClWaEqFrom`,`ClWaEqTo`,`ClWaEqAgeClass`,`ClWaEqOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Classes`
--

DROP TABLE IF EXISTS `Classes`;
CREATE TABLE `Classes` (
  `ClId` varchar(6) NOT NULL,
  `ClTournament` int unsigned NOT NULL DEFAULT '0',
  `ClDescription` varchar(32) NOT NULL,
  `ClViewOrder` tinyint unsigned NOT NULL DEFAULT '0',
  `ClAgeFrom` tinyint NOT NULL,
  `ClAgeTo` tinyint NOT NULL,
  `ClValidClass` varchar(24) NOT NULL DEFAULT '0',
  `ClSex` tinyint NOT NULL DEFAULT '0',
  `ClAthlete` varchar(1) NOT NULL DEFAULT '1',
  `ClDivisionsAllowed` varchar(255) NOT NULL,
  `ClRecClass` varchar(4) NOT NULL,
  `ClWaClass` varchar(4) NOT NULL,
  `ClTourRules` varchar(75) NOT NULL,
  `ClIsPara` tinyint unsigned NOT NULL,
  PRIMARY KEY (`ClId`,`ClTournament`),
  KEY `ClTournament` (`ClTournament`,`ClAthlete`,`ClViewOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ClubTeam`
--

DROP TABLE IF EXISTS `ClubTeam`;
CREATE TABLE `ClubTeam` (
  `CTTournament` int unsigned NOT NULL DEFAULT '0',
  `CTPhase` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
  `CTMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `CTEventCode` varchar(10) NOT NULL,
  `CTPrimary` tinyint unsigned NOT NULL,
  `CTTeam` int unsigned NOT NULL DEFAULT '0',
  `CTSubTeam` tinyint NOT NULL,
  `CTBonus` tinyint unsigned NOT NULL DEFAULT '0',
  `CTRank` tinyint unsigned NOT NULL,
  `CTTiebreak` varchar(9) NOT NULL DEFAULT '',
  `CTSchedule` datetime NOT NULL,
  `CTQualRank` int NOT NULL,
  PRIMARY KEY (`CTTournament`,`CTPhase`,`CTMatchNo`,`CTEventCode`,`CTPrimary`,`CTTeam`,`CTSubTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ClubTeamGrid`
--

DROP TABLE IF EXISTS `ClubTeamGrid`;
CREATE TABLE `ClubTeamGrid` (
  `CTGPhase` tinyint unsigned NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
  `CTGRound` tinyint unsigned NOT NULL,
  `CTGMatchNo1` tinyint unsigned NOT NULL,
  `CTGMatchNo2` tinyint unsigned NOT NULL,
  `CTGGroup` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CTGPhase`,`CTGRound`,`CTGMatchNo1`,`CTGMatchNo2`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ClubTeamGrid`
--

LOCK TABLES `ClubTeamGrid` WRITE;
/*!40000 ALTER TABLE `ClubTeamGrid` DISABLE KEYS */;
INSERT INTO `ClubTeamGrid` VALUES (1,3,1,8,1),(1,3,2,7,2),(1,3,3,6,3),(1,3,4,5,4),(1,3,9,16,1),(1,3,10,15,2),(1,3,11,14,3),(1,3,12,13,4),(1,2,1,9,1),(1,2,2,10,2),(1,2,3,11,3),(1,2,4,12,4),(1,2,5,13,4),(1,2,6,14,3),(1,2,7,15,2),(1,2,8,16,1),(1,1,1,16,1),(1,1,2,15,2),(1,1,3,14,3),(1,1,4,13,4),(1,1,5,12,4),(1,1,6,11,3),(1,1,7,10,2),(1,1,8,9,1),(2,1,1,4,1),(2,1,2,3,1),(2,1,5,8,2),(2,1,6,7,2),(2,1,9,12,3),(2,1,10,11,3),(2,1,13,16,4),(2,1,14,15,4),(3,1,1,2,1),(3,1,3,4,1),(3,1,5,6,2),(3,1,7,8,2),(3,1,9,10,3),(3,1,11,12,3),(3,1,13,14,4),(3,1,15,16,4),(0,1,1,4,1),(0,1,2,3,1),(0,2,1,3,1),(0,2,2,4,1),(0,3,1,2,1),(0,3,3,4,1),(0,1,5,8,2),(0,1,6,7,2),(0,2,5,7,2),(0,2,6,8,2),(0,3,5,6,2),(0,3,7,8,2),(0,1,9,12,3),(0,1,10,11,3),(0,2,9,11,3),(0,2,10,12,3),(0,3,9,10,3),(0,3,11,12,3),(0,1,13,16,4),(0,1,14,15,4),(0,2,13,15,4),(0,2,14,16,4),(0,3,13,14,4),(0,3,15,16,4),(0,1,17,20,5),(0,1,18,19,5),(0,2,17,19,5),(0,2,18,20,5),(0,3,17,18,5),(0,3,19,20,5),(0,1,21,24,6),(0,1,22,23,6),(0,2,21,23,6),(0,2,22,24,6),(0,3,21,22,6),(0,3,23,24,6),(0,1,25,28,7),(0,1,26,27,7),(0,2,25,27,7),(0,2,26,28,7),(0,3,25,26,7),(0,3,27,28,7),(0,1,29,32,8),(0,1,30,31,8),(0,2,29,31,8),(0,2,30,32,8),(0,3,29,30,8),(0,3,31,32,8);
/*!40000 ALTER TABLE `ClubTeamGrid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeamGroupMatch`
--

DROP TABLE IF EXISTS `ClubTeamGroupMatch`;
CREATE TABLE `ClubTeamGroupMatch` (
  `CTGMGroup` tinyint unsigned NOT NULL,
  `CTGMMatchNo` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CTGMGroup`,`CTGMMatchNo`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ClubTeamGroupMatch`
--

LOCK TABLES `ClubTeamGroupMatch` WRITE;
/*!40000 ALTER TABLE `ClubTeamGroupMatch` DISABLE KEYS */;
INSERT INTO `ClubTeamGroupMatch` VALUES (1,1),(1,8),(1,9),(1,16),(2,2),(2,7),(2,10),(2,15),(3,3),(3,6),(3,11),(3,14),(4,4),(4,5),(4,12),(4,13);
/*!40000 ALTER TABLE `ClubTeamGroupMatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeamRankMatch`
--

DROP TABLE IF EXISTS `ClubTeamRankMatch`;
CREATE TABLE `ClubTeamRankMatch` (
  `CTRMEventPhase` tinyint NOT NULL,
  `CTRMRank` tinyint NOT NULL,
  `CTRMMatchNo` tinyint unsigned NOT NULL,
  PRIMARY KEY (`CTRMEventPhase`,`CTRMRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ClubTeamRankMatch`
--

LOCK TABLES `ClubTeamRankMatch` WRITE;
/*!40000 ALTER TABLE `ClubTeamRankMatch` DISABLE KEYS */;
INSERT INTO `ClubTeamRankMatch` VALUES (8,1,1),(8,2,2),(8,3,7),(8,4,8),(8,5,9),(8,6,10),(8,7,15),(8,8,16),(16,1,1),(16,2,2),(16,3,3),(16,4,4),(16,5,5),(16,6,6),(16,7,7),(16,8,8),(16,9,9),(16,10,10),(16,11,11),(16,12,12),(16,13,13),(16,14,14),(16,15,15),(16,16,16);
/*!40000 ALTER TABLE `ClubTeamRankMatch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeamScore`
--

DROP TABLE IF EXISTS `ClubTeamScore`;
CREATE TABLE `ClubTeamScore` (
  `CTSTournament` int unsigned NOT NULL DEFAULT '0',
  `CTSPhase` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '1=fase 1;2=fase2',
  `CTSRound` tinyint unsigned NOT NULL,
  `CTSMatchNo` tinyint unsigned NOT NULL,
  `CTSEventCode` varchar(10) NOT NULL,
  `CTSPrimary` tinyint unsigned NOT NULL,
  `CTSTarget` varchar(2) NOT NULL,
  `CTSScore` smallint NOT NULL DEFAULT '0',
  `CTSTie` tinyint(1) NOT NULL DEFAULT '0',
  `CTSArrowString` varchar(24) NOT NULL,
  `CTSArrowPosition` varchar(240) NOT NULL,
  `CTSTiebreak` varchar(9) NOT NULL,
  `CTSTiePosition` varchar(90) NOT NULL,
  `CTSPoints` tinyint NOT NULL DEFAULT '0',
  `CTSSetPoints` int NOT NULL,
  `CTSSetEnds` varchar(36) DEFAULT NULL,
  `CTSDateTime` datetime NOT NULL,
  `CTSTimeStamp` datetime NOT NULL,
  PRIMARY KEY (`CTSTournament`,`CTSPhase`,`CTSMatchNo`,`CTSEventCode`,`CTSRound`,`CTSPrimary`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci ROW_FORMAT=COMPACT;

--
-- Table structure for table `Countries`
--

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
  `CoId` int unsigned NOT NULL AUTO_INCREMENT,
  `CoOnlineId` int NOT NULL DEFAULT '0',
  `CoTournament` int NOT NULL DEFAULT '0',
  `CoIocCode` varchar(5) NOT NULL,
  `CoCode` varchar(10) NOT NULL,
  `CoName` varchar(30) NOT NULL,
  `CoNameComplete` varchar(80) NOT NULL,
  `CoSubCountry` varchar(10) NOT NULL,
  `CoParent1` int unsigned NOT NULL,
  `CoParent2` int unsigned NOT NULL,
  `CoLevelBitmap` tinyint NOT NULL DEFAULT '4',
  `CoMaCode` varchar(5) NOT NULL,
  `CoCaCode` varchar(5) NOT NULL,
  PRIMARY KEY (`CoId`),
  UNIQUE KEY `CoTournament` (`CoTournament`,`CoCode`)
) ENGINE=MyISAM AUTO_INCREMENT=5869 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `CountryLevels`
--

DROP TABLE IF EXISTS `CountryLevels`;
CREATE TABLE `CountryLevels` (
  `ClBit` tinyint unsigned NOT NULL,
  `ClCountryLevel` varchar(4) NOT NULL,
  `ClRecordLevel` varchar(3) NOT NULL,
  PRIMARY KEY (`ClBit`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `CountryLevels`
--

LOCK TABLES `CountryLevels` WRITE;
/*!40000 ALTER TABLE `CountryLevels` DISABLE KEYS */;
INSERT INTO `CountryLevels` VALUES (1,'Seas','SB'),(2,'Pers','PB'),(4,'Club','CLR'),(8,'',''),(16,'Natl','NR'),(32,'Cont','CR'),(64,'Game','GR'),(127,'Eart','WR');
/*!40000 ALTER TABLE `CountryLevels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DistanceInformation`
--

DROP TABLE IF EXISTS `DistanceInformation`;
CREATE TABLE `DistanceInformation` (
  `DiTournament` int NOT NULL,
  `DiSession` tinyint NOT NULL,
  `DiDistance` tinyint NOT NULL,
  `DiEnds` tinyint NOT NULL,
  `DiArrows` tinyint NOT NULL,
  `DiMaxpoints` int NOT NULL,
  `DiOptions` text NOT NULL,
  `DiType` varchar(1) NOT NULL DEFAULT 'Q',
  `DiDay` date NOT NULL,
  `DiWarmStart` time NOT NULL,
  `DiWarmDuration` int NOT NULL,
  `DiStart` time NOT NULL,
  `DiDuration` int NOT NULL,
  `DiShift` int DEFAULT NULL,
  `DiTargets` text NOT NULL,
  `DiTourRules` varchar(75) NOT NULL,
  PRIMARY KEY (`DiTournament`,`DiSession`,`DiDistance`,`DiType`),
  KEY `DiDay` (`DiDay`,`DiStart`,`DiDuration`),
  KEY `DiDay_2` (`DiDay`,`DiWarmStart`,`DiWarmDuration`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Divisions`
--

DROP TABLE IF EXISTS `Divisions`;
CREATE TABLE `Divisions` (
  `DivId` varchar(4) NOT NULL,
  `DivTournament` int unsigned NOT NULL DEFAULT '0',
  `DivDescription` varchar(32) NOT NULL,
  `DivViewOrder` tinyint unsigned NOT NULL DEFAULT '0',
  `DivAthlete` varchar(1) NOT NULL DEFAULT '1',
  `DivRecDivision` varchar(4) NOT NULL,
  `DivWaDivision` varchar(4) NOT NULL,
  `DivTourRules` varchar(75) NOT NULL,
  `DivIsPara` tinyint unsigned NOT NULL,
  PRIMARY KEY (`DivId`,`DivTournament`),
  KEY `DivTournament` (`DivTournament`,`DivAthlete`,`DivViewOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `DocumentVersions`
--

DROP TABLE IF EXISTS `DocumentVersions`;
CREATE TABLE `DocumentVersions` (
  `DvTournament` int NOT NULL,
  `DvFile` varchar(50) NOT NULL COMMENT 'calling chunk basename or rank object name',
  `DvEvent` varchar(10) NOT NULL COMMENT 'if div+class => DIV|CLASS',
  `DvOrder` int NOT NULL,
  `DvSectors` varchar(50) NOT NULL,
  `DvSector` varchar(1) NOT NULL,
  `DvMajVersion` tinyint NOT NULL,
  `DvMinVersion` tinyint NOT NULL,
  `DvPrintDateTime` datetime NOT NULL,
  `DvIncludedDateTime` datetime NOT NULL,
  `DvNotes` text NOT NULL,
  PRIMARY KEY (`DvTournament`,`DvFile`,`DvEvent`),
  KEY `DvOrder` (`DvOrder`,`DvEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ElabQualifications`
--

DROP TABLE IF EXISTS `ElabQualifications`;
CREATE TABLE `ElabQualifications` (
  `EqId` int unsigned NOT NULL,
  `EqArrowNo` smallint unsigned NOT NULL,
  `EqDistance` tinyint unsigned NOT NULL,
  `EqScore` int NOT NULL,
  `EqHits` int NOT NULL,
  `EqGold` int NOT NULL,
  `EqXnine` int NOT NULL,
  `EqTimestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`EqId`,`EqArrowNo`,`EqDistance`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Eliminations`
--

DROP TABLE IF EXISTS `Eliminations`;
CREATE TABLE `Eliminations` (
  `ElId` int unsigned NOT NULL,
  `ElElimPhase` tinyint NOT NULL DEFAULT '0',
  `ElEventCode` varchar(10) NOT NULL,
  `ElTournament` int unsigned NOT NULL,
  `ElQualRank` smallint NOT NULL,
  `ElSession` tinyint unsigned NOT NULL DEFAULT '0',
  `ElTargetNo` varchar(5) NOT NULL,
  `ElScore` smallint NOT NULL,
  `ElHits` smallint NOT NULL,
  `ElGold` smallint NOT NULL,
  `ElXnine` smallint NOT NULL,
  `ElArrowString` varchar(36) NOT NULL,
  `ElTiebreak` varchar(8) NOT NULL,
  `ElTbClosest` tinyint NOT NULL,
  `ElTbDecoded` varchar(15) NOT NULL,
  `ElConfirm` int NOT NULL,
  `ElRank` tinyint unsigned NOT NULL,
  `ElSO` smallint NOT NULL DEFAULT '0',
  `ElStatus` tinyint unsigned NOT NULL,
  `ElDateTime` datetime NOT NULL,
  `ElBacknoPrinted` datetime NOT NULL,
  `ElIrmType` tinyint NOT NULL,
  PRIMARY KEY (`ElElimPhase`,`ElEventCode`,`ElTournament`,`ElQualRank`),
  KEY `ElAthleteEventTournament` (`ElId`,`ElEventCode`,`ElTournament`),
  KEY `ElDateTime` (`ElTournament`,`ElDateTime`),
  KEY `ElId` (`ElId`,`ElElimPhase`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Emails`
--

DROP TABLE IF EXISTS `Emails`;
CREATE TABLE `Emails` (
  `EmTournament` int NOT NULL,
  `EmKey` int NOT NULL,
  `EmTitle` varchar(50) NOT NULL,
  `EmSubject` varchar(60) NOT NULL,
  `EmBody` text NOT NULL,
  `EmFilter` text NOT NULL,
  `EmSentDate` datetime NOT NULL,
  `EmFrom` varchar(50) NOT NULL,
  `EmCc` varchar(100) NOT NULL,
  `EmBcc` varchar(50) NOT NULL,
  PRIMARY KEY (`EmTournament`,`EmKey`),
  KEY `EmTournament` (`EmTournament`,`EmTitle`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Entries`
--

DROP TABLE IF EXISTS `Entries`;
CREATE TABLE `Entries` (
  `EnId` int unsigned NOT NULL AUTO_INCREMENT,
  `EnOnlineId` int NOT NULL DEFAULT '0',
  `EnTournament` int unsigned NOT NULL DEFAULT '0',
  `EnDivision` varchar(4) NOT NULL,
  `EnClass` varchar(6) NOT NULL,
  `EnSubClass` varchar(2) NOT NULL,
  `EnAgeClass` varchar(6) NOT NULL,
  `EnCountry` int unsigned NOT NULL DEFAULT '0',
  `EnIocCode` varchar(5) NOT NULL DEFAULT '',
  `EnSubTeam` tinyint NOT NULL DEFAULT '0',
  `EnCountry2` int unsigned NOT NULL DEFAULT '0',
  `EnCountry3` int unsigned NOT NULL DEFAULT '0',
  `EnCtrlCode` varchar(16) NOT NULL,
  `EnDob` date NOT NULL,
  `EnCode` varchar(25) NOT NULL,
  `EnName` varchar(30) NOT NULL,
  `EnFirstName` varchar(30) NOT NULL,
  `EnBadgePrinted` datetime DEFAULT NULL,
  `EnAthlete` tinyint unsigned NOT NULL DEFAULT '1',
  `EnSex` tinyint unsigned NOT NULL DEFAULT '0',
  `EnClassified` tinyint unsigned NOT NULL,
  `EnWChair` tinyint unsigned NOT NULL DEFAULT '0',
  `EnSitting` tinyint unsigned NOT NULL DEFAULT '0',
  `EnIndClEvent` tinyint unsigned NOT NULL DEFAULT '1',
  `EnTeamClEvent` tinyint unsigned NOT NULL DEFAULT '1',
  `EnIndFEvent` tinyint unsigned NOT NULL DEFAULT '1',
  `EnTeamFEvent` tinyint unsigned NOT NULL DEFAULT '1',
  `EnTeamMixEvent` tinyint(1) NOT NULL DEFAULT '1',
  `EnDoubleSpace` tinyint(1) NOT NULL DEFAULT '0',
  `EnPays` tinyint unsigned NOT NULL DEFAULT '1',
  `EnStatus` tinyint unsigned NOT NULL DEFAULT '0',
  `EnTargetFace` int NOT NULL,
  `EnLueTimeStamp` datetime NOT NULL,
  `EnLueFieldChanged` smallint NOT NULL,
  `EnTimestamp` timestamp NOT NULL,
  `EnNameOrder` tinyint NOT NULL,
  `EnOdfShortname` varchar(18) NOT NULL,
  `EnTvGivenName` varchar(30) NOT NULL,
  `EnTvFamilyName` varchar(30) NOT NULL,
  `EnTvInitials` varchar(8) NOT NULL,
  PRIMARY KEY (`EnId`),
  KEY `EnDivision` (`EnDivision`),
  KEY `EnClass` (`EnClass`),
  KEY `CalcRank` (`EnTournament`,`EnAthlete`,`EnStatus`),
  KEY `EnTournament` (`EnTournament`),
  KEY `EnCode` (`EnCode`,`EnIocCode`,`EnDivision`,`EnTournament`)
) ENGINE=MyISAM AUTO_INCREMENT=21521 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `EventClass`
--

DROP TABLE IF EXISTS `EventClass`;
CREATE TABLE `EventClass` (
  `EcCode` varchar(10) NOT NULL,
  `EcTeamEvent` tinyint(1) NOT NULL DEFAULT '0',
  `EcTournament` int NOT NULL,
  `EcClass` varchar(6) NOT NULL,
  `EcDivision` varchar(4) NOT NULL,
  `EcSubClass` varchar(2) NOT NULL,
  `EcNumber` tinyint unsigned NOT NULL DEFAULT '1',
  `EcTourRules` varchar(75) NOT NULL,
  PRIMARY KEY (`EcCode`,`EcTeamEvent`,`EcTournament`,`EcClass`,`EcDivision`,`EcSubClass`) USING BTREE,
  KEY `MakeIndividuals` (`EcTeamEvent`,`EcTournament`,`EcClass`,`EcDivision`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
  `EvCode` varchar(10) NOT NULL,
  `EvTeamEvent` tinyint(1) NOT NULL,
  `EvTournament` int NOT NULL,
  `EvEventName` varchar(64) NOT NULL,
  `EvProgr` int NOT NULL,
  `EvShootOff` tinyint unsigned NOT NULL DEFAULT '0',
  `EvE1ShootOff` tinyint unsigned NOT NULL DEFAULT '0',
  `EvE2ShootOff` tinyint unsigned NOT NULL DEFAULT '0',
  `EvSession` int NOT NULL,
  `EvPrint` tinyint(1) NOT NULL,
  `EvQualPrintHead` varchar(64) NOT NULL,
  `EvQualLastUpdate` datetime DEFAULT NULL,
  `EvFinalFirstPhase` tinyint NOT NULL,
  `EvWinnerFinalRank` int NOT NULL DEFAULT '1',
  `EvNumQualified` int NOT NULL,
  `EvFirstQualified` int DEFAULT '1',
  `EvFinalPrintHead` varchar(64) NOT NULL,
  `EvFinalLastUpdate` datetime DEFAULT NULL,
  `EvFinalTargetType` tinyint NOT NULL,
  `EvGolds` varchar(5) NOT NULL,
  `EvXNine` varchar(5) NOT NULL,
  `EvGoldsChars` varchar(16) NOT NULL,
  `EvXNineChars` varchar(16) NOT NULL,
  `EvTargetSize` int NOT NULL DEFAULT '0',
  `EvDistance` varchar(6) NOT NULL,
  `EvFinalAthTarget` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMatchMultipleMatches` tinyint unsigned NOT NULL,
  `EvElimType` tinyint NOT NULL,
  `EvElim1` tinyint unsigned NOT NULL DEFAULT '0',
  `EvE1Ends` tinyint NOT NULL,
  `EvE1Arrows` tinyint NOT NULL,
  `EvE1SO` tinyint NOT NULL,
  `EvElim2` tinyint unsigned NOT NULL DEFAULT '0',
  `EvE2Ends` tinyint NOT NULL,
  `EvE2Arrows` tinyint NOT NULL,
  `EvE2SO` tinyint NOT NULL,
  `EvPartialTeam` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMultiTeam` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMultiTeamNo` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMixedTeam` tinyint unsigned NOT NULL DEFAULT '0',
  `EvTeamCreationMode` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMaxTeamPerson` tinyint NOT NULL DEFAULT '1',
  `EvRunning` tinyint unsigned NOT NULL DEFAULT '0',
  `EvMatchMode` tinyint NOT NULL DEFAULT '0',
  `EvMatchArrowsNo` tinyint unsigned NOT NULL DEFAULT '0',
  `EvElimEnds` tinyint unsigned NOT NULL DEFAULT '0',
  `EvElimArrows` tinyint unsigned NOT NULL DEFAULT '0',
  `EvElimSO` tinyint unsigned NOT NULL DEFAULT '0',
  `EvFinEnds` tinyint unsigned NOT NULL DEFAULT '0',
  `EvFinArrows` tinyint unsigned NOT NULL DEFAULT '0',
  `EvFinSO` tinyint unsigned NOT NULL DEFAULT '0',
  `EvRecCategory` varchar(10) NOT NULL,
  `EvWaCategory` varchar(10) NOT NULL,
  `EvMedals` tinyint NOT NULL DEFAULT '1',
  `EvTourRules` varchar(75) NOT NULL,
  `EvCodeParent` varchar(10) NOT NULL,
  `EvOdfCode` varchar(34) NOT NULL,
  `EvOdfGender` varchar(1) NOT NULL,
  `EvIsPara` tinyint unsigned NOT NULL,
  PRIMARY KEY (`EvCode`,`EvTeamEvent`,`EvTournament`),
  KEY `EvTournament` (`EvTournament`,`EvTeamEvent`,`EvCode`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ExtraData`
--

DROP TABLE IF EXISTS `ExtraData`;
CREATE TABLE `ExtraData` (
  `EdId` int NOT NULL,
  `EdType` varchar(10) NOT NULL,
  `EdEvent` varchar(10) NOT NULL,
  `EdEmail` varchar(100) NOT NULL,
  `EdExtra` text NOT NULL,
  PRIMARY KEY (`EdId`,`EdType`,`EdEvent`),
  KEY `EdId` (`EdId`,`EdType`,`EdEmail`(1),`EdEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `ExtraDataCountries`
--

DROP TABLE IF EXISTS `ExtraDataCountries`;
CREATE TABLE `ExtraDataCountries` (
  `EdcId` int NOT NULL,
  `EdcSubTeam` tinyint NOT NULL,
  `EdcType` varchar(10) NOT NULL,
  `EdcEvent` varchar(10) NOT NULL,
  `EdcEmail` varchar(100) NOT NULL,
  `EdcExtra` text NOT NULL,
  PRIMARY KEY (`EdcId`,`EdcType`,`EdcEvent`,`EdcSubTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `FinOdfTiming`
--

DROP TABLE IF EXISTS `FinOdfTiming`;
CREATE TABLE `FinOdfTiming` (
  `FinOdfTournament` int NOT NULL,
  `FinOdfEvent` varchar(10) NOT NULL,
  `FinOdfTeamEvent` tinyint NOT NULL,
  `FinOdfMatchno` int NOT NULL,
  `FinOdfStartlist` datetime NOT NULL,
  `FinOdfPrepare` datetime NOT NULL,
  `FinOdfBegin` datetime NOT NULL,
  `FinOdfEnd` datetime NOT NULL,
  `FinOdfUnofficial` datetime NOT NULL,
  `FinOdfConfirmed` datetime NOT NULL,
  `FinOdfArrows` text NOT NULL,
  `FinOdfTiming` text NOT NULL,
  PRIMARY KEY (`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
  KEY `FinOdfPrepare` (`FinOdfPrepare`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
  KEY `FinOdfBegin` (`FinOdfBegin`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
  KEY `FinOdfEnd` (`FinOdfEnd`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
  KEY `FinOdfUnofficial` (`FinOdfUnofficial`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`),
  KEY `FinOdfConfirmed` (`FinOdfConfirmed`,`FinOdfTournament`,`FinOdfTeamEvent`,`FinOdfEvent`,`FinOdfMatchno`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `FinSchedule`
--

DROP TABLE IF EXISTS `FinSchedule`;
CREATE TABLE `FinSchedule` (
  `FSEvent` varchar(10) NOT NULL,
  `FSTeamEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `FSMatchNo` tinyint unsigned NOT NULL,
  `FSTournament` int unsigned NOT NULL,
  `FSTarget` varchar(3) NOT NULL,
  `FSGroup` int unsigned NOT NULL DEFAULT '0',
  `FSScheduledDate` date NOT NULL,
  `FSScheduledTime` time DEFAULT NULL,
  `FSScheduledLen` smallint NOT NULL DEFAULT '0',
  `FSLetter` varchar(5) NOT NULL,
  `FsShift` int DEFAULT NULL,
  `FSTimestamp` timestamp NOT NULL,
  `FsOdfMatchName` int NOT NULL,
  `FsLJudge` int NOT NULL,
  `FsTJudge` int NOT NULL,
  PRIMARY KEY (`FSEvent`,`FSTeamEvent`,`FSMatchNo`,`FSTournament`),
  UNIQUE KEY `FSTournament` (`FSTournament`,`FSTeamEvent`,`FSEvent`,`FSMatchNo`,`FSScheduledDate`,`FSScheduledTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `FinWarmup`
--

DROP TABLE IF EXISTS `FinWarmup`;
CREATE TABLE `FinWarmup` (
  `FwTournament` int NOT NULL,
  `FwEvent` varchar(10) NOT NULL,
  `FwTeamEvent` int NOT NULL,
  `FwDay` date NOT NULL,
  `FwTime` time NOT NULL,
  `FwDuration` int NOT NULL,
  `FwMatchTime` time NOT NULL,
  `FwTargets` text NOT NULL,
  `FwOptions` text NOT NULL,
  `FwTimestamp` timestamp NOT NULL,
  PRIMARY KEY (`FwTournament`,`FwEvent`,`FwTeamEvent`,`FwDay`,`FwMatchTime`,`FwTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `FinalReportA`
--

DROP TABLE IF EXISTS `FinalReportA`;
CREATE TABLE `FinalReportA` (
  `FraQuestion` varchar(5) NOT NULL,
  `FraTournament` int unsigned NOT NULL,
  `FraAnswer` text NOT NULL,
  PRIMARY KEY (`FraQuestion`,`FraTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `FinalReportQ`
--

DROP TABLE IF EXISTS `FinalReportQ`;
CREATE TABLE `FinalReportQ` (
  `FrqId` varchar(5) NOT NULL,
  `FrqStatus` tinyint NOT NULL DEFAULT '0',
  `FrqQuestion` tinytext NOT NULL,
  `FrqTip` text NOT NULL,
  `FrqType` tinyint NOT NULL,
  `FrqOptions` varchar(200) NOT NULL,
  PRIMARY KEY (`FrqId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `FinalReportQ`
--

LOCK TABLES `FinalReportQ` WRITE;
/*!40000 ALTER TABLE `FinalReportQ` DISABLE KEYS */;
INSERT INTO `FinalReportQ` VALUES ('A',15,'Organizzazione','',-1,''),('a01',15,'Conforme al calendario','',2,''),('a05',15,'Tutte le classi e divisioni ammesse','',2,''),('a06',15,'in caso contrario, specificare','',1,'70|2'),('a08',3,'Direttore dei tiri','',3,'non iscritto all\'albo|iscritto all\'albo|arbitro|Arbitro e Direttore dei Tiri'),('a10',15,'Commissione di garanzia','Pubblicata ad inizio gara; solo per gare nazionali ed internazionali',2,''),('a11',15,'Field Manager (responsabile di campo)','Responsabile dell\'organizzazione appositamente nominato per la gestione del campo',2,''),('a02',15,'Calendario','',3,'Evento Federale|Internazionale|Nazionale|Interregionale|Giovanile|Sperimentale'),('a12',15,'Squadre Nazionali straniere','Solo rappresentative ufficiali; non applicabile solo per eventi federali',3,'N.A.|No|Si'),('a13',15,'In caso affermativo, specificare','',1,'70|2'),('a14',3,'Segnalazione eventuali Record','',1,'70|10'),('a15',15,'Reclami','',1,'70|7'),('a03',15,'Programma della gara pervenuto alla Giuria di Gara','',3,'No|Pubblicato|Si'),('a07',15,'Tassa ridotta per le classi giovanili','50% o differente se stabilito da apposito regolamento (es: Campionati Italiani)',3,'Come da Regolamento|Non Applicabile|No|Gratuita'),('B',15,'Luogo di gara','',-1,''),('b09',15,'Distanze in tolleranza','Distanze tra linea di tiro e bersagli',3,'Si|Solo dopo intervento GdG|No'),('b10',3,'Separazione interasse conforme al regolamento','160 cm due atleti per turno; 240 cm con turno unico con 3 atleti',2,''),('b11',3,'Linea di tiro','Stabile=ben bloccata a terra\r\nPosizione atleti interasse=Art. 7.1.1.7 - 8.1.1.5',4,'Stabile|Ben definita|Confusa|Con posizione atleti|con interasse|Coperta'),('b12',3,'Numeri sulla linea di Tiro ','tra 1 e 2 metri',3,'Assenti|Sulla linea di tiro|Davanti alla linea di tiro tra 1 e 2 metri'),('b15',3,'Linea dei tre metri','',3,'Presente|Assente'),('b16',3,'Linea Stampa/Media','',2,''),('b17',3,'Linea di attesa conforme al regolamento','',2,''),('b18',3,'Corridoi ','Tra i paglioni ove previsto dal regolamento',3,'Presente|Assente|Non Applicabile'),('b19',7,'Battifreccia','',3,'Usurati|Seminuovi|Nuovi'),('b20',7,'Materiale Battifreccia','',3,'Sintetico|Paglia'),('b21',3,'Supporti battifreccia','cavalletti',3,'Improvvisati|Triangolari|Rettangolari'),('c02',7,'Assicurazione battifreccia con tiranti','Per indoor = tirante tra paglione e cavalletto',3,'No|Si|Si ma con intervento GdG'),('b23',7,'Angolazione Battifreccia conforme al regolamento','per hf intendersi perpendicolarità del bersaglio rispetto alla posizione di tiro',2,''),('b24',7,'Disposizione visuali conforme','',2,''),('b25',1,'Bandiere segnavento','',3,'Assenti|Presenti non ben visibili|Presenti ben visibili'),('b27',3,'Numeratori dei battifreccia','numeri dei paglioni',3,'Assenti|Presenti non conformi|Presenti conformi'),('b26',1,'Maniche a vento','',3,'Non Applicabile|Assente|Si 1|Si 2 o più'),('b28',3,'Impianto semaforico ','Per automatico si intende la temporizzazione e la sincronizzazione delle luci dopo la chiamata tranne che per un eventuale termine anticipato\r\nNon a norma di sicurezza=deferimento automatico',3,'Assenti|Non a norma di sicurezza|Manuale|Automatico'),('b29',3,'Sistemi visivi ausiliari','Bandierine ',2,''),('b30',3,'Indicatori di sequenza ','(AB/CD) Non necessari per turno unico',3,'Turno unico|Assenti|Manuale|Automatico'),('b31',3,'Orologi contasecondi','',2,''),('b32',3,'Indicatori acustici','Automatico se sincronizzato automaticamente con l\'impianto semforico',3,'Manuale|Automatico'),('b33',3,'Indicatori individuali di punteggio per la fase di qualifica','flip board o altri indicatori di punteggio',2,''),('b34',15,'Indicatori di punteggio per le fasi finali','Manuali ed automatici= utilizzati i manuali per le eliminatorie e elettronici solo per le finali a medaglia',3,'Non applicabile|Assenti|Manuali|Manuali ed Automatici|Automatici'),('b36',1,'Sedie e ombrelloni per gli arbitri','',2,''),('b38',3,'Postazione direttore dei tiri rialzata','',3,'No|Si|Si ma non correttamente posizionata'),('b39',1,'Blind per le finali','Protezione per gli arbitri in prossimità del bersaglio',3,'Non Applicabile|Si|No'),('b40',1,'Campo di tiri di allenamento','',3,'Non necessario|Assente|Utilizzate parti del campo di gara|Campo separato con orientamento differente|Campo separato con stesso orientamento '),('b41',15,'Campo per la finale','Campo per le finali singole',3,'Non necessario|Utilizzata parte del campo di gara|Campo apposito'),('b43',15,'Spazio per il pubblico','Per HF e 3D da intendersi solo per le fasi finali',3,'No|Spazio libero|Sedie|Tribune della struttura|Tribune appositamente realizzate'),('b42',12,'Campo tiri di prova','',4,'No|Si|Distante dal punto di raduno|In prossimità del punto di raduno|Sufficiente per tutti gli arcieri|Necessità di turnazione'),('b46',12,'Picchetto dello stop','',3,'Si|Si ma con intervento GdG'),('b49',12,'Percorso alternativo','per raggiungere le piazzole senza attraversare il percorso di gara',2,''),('b45',12,'Distanza campo base / piazzola più lontana','Tempo di percorrenza',3,'Oltre i 15 minuti|Entro i 15 minuti'),('b52',12,'Interferenze','',4,'Al volo delle frecce|Alla visione del bersaglio|All\'arco|No'),('b53',4,'Distanze conosciute','Rispetto delle distanze e del numero dei bersagli per tipo indicati nel regolamento',2,''),('b54',12,'Distanze sconosciute','',3,'Inferiori alla media|nella media|Superiori alla media'),('b48',12,'Visibilità dei picchetti di tiro','dallo stop',3,'Si|Si ma con intervento GdG'),('b55',12,'Pendenze medie','indicativo',3,'zero|Meno di 15°|Più di 15°'),('C',15,'Sicurezza e assistenza sanitaria','',-1,''),('c03',15,'Dietro la linea delle visuali','',3,'Non Presidiata|Presidiata|Assoluta'),('c01',15,'Accesso al campo','libero=nessun controllo; regolato=con pass nel rispetto del regolamento',3,'Libero|Regolato'),('c06',12,'Problemi generali di sicurezza del percorso','',4,'Tiri incrociati|Direzioni di tiro verso altre piazzole|Direzione di tiro verso il percorso di trasferimento|No'),('a17',15,'Gestione della gara','Escluso il cambio delle targhe',3,'Non adeguata|Continui e necessari interventi gdg|Con sporadici interventi gdg|Autonoma'),('a21',15,'Assegnazione piazzole','',3,'Assegnazione manuale|In ordine di rank|Sorteggio manuale|Sorteggio automatico'),('a22',15,'Abbigliamento  Personale organizzazione','',3,'Non riconoscibile|Divisa di società|Pettorine|Divisa evento '),('a23',7,'Visuale di riserva','',3,'Insufficienti|Appena Sufficienti|Abbondanti'),('a24',15,'Battifreccia o sagome di riserva','',2,''),('a25',3,'Cavalletti di riserva','',2,''),('a26',15,'Numeri di gara','Pettorali\r\nPersonalizzati (con nome dell\'atleta)',3,'Assenti|Della Società|Dell\'evento|Dell\'evento personalizzati'),('a27',15,'Ristoro','Alimenti',3,'Assente|Presente a pagamento|Gratuito'),('a28',15,'Bevande sul campo di gara','',3,'Assenti|Presenti a pagamento|Presenti gratuite'),('a30',3,'Assistenza ai disabili','',3,'Non necessaria|No|Si'),('a33',3,'Periodicità di esposizione classifiche','',3,'Mai|A fine gara|A fine distanza|Parziali di distanza|Tempo reale (ogni volèe)'),('a34',15,'Meccanismi di esposizione classifiche','',3,'Assente|Cartaceo|Monitor|Maxischermo|Monitor e Maxischermo'),('a31',3,'Raccolta punteggi parziali','Strumento utilizzato',3,'Assente|Cartaceo|Tastierini elettronici'),('a36',15,'Speaker','',3,'No|Durante la competizione|Durante le Finali|Durante qualifica e finali'),('a37',15,'Musica','',3,'No|Durante le pause|Dal vivo'),('b05',15,'Impianto di amplificazione','Per musica, comunicazioni di servizio, speaker',2,''),('b06',15,'Comunicazione sul campo','',3,'A voce|Telefoni dell\'organizzazione|Radio dell\'organizzazione'),('a32',12,'Raccolta punteggi parziali','',2,''),('a35',12,'Periodicità esposizione classifiche','',3,'Mai|A fine gara|A fine distanza|Parziale una volta|Parziale più volte'),('a29',15,'Rinfresco finale','',2,''),('a38',15,'Connessione ad internet','per pubblico o partecipanti',3,'No|Si Gratuita|Si a pagamento'),('b01',15,'Durata della competizione','',1,'70|1'),('a39',3,'Servizi igienici','',4,'No|Comuni|Divisi per sesso|Lontani dal campo di gara|Facilmente Accessibili|Anche per disabili'),('b07',3,'Barriere architettoniche','',3,'Presenti|Assenti'),('b08',15,'Indicazioni stradali','Non mappe o indicazioni online ma cartellonistica',3,'No|Insufficiente|Sufficiente'),('a40',15,'Recettivita\' alberghiera','',4,'No|Gestita dall\'organizzazione|Oltre i 10 minuti dal campo di gara|Entro i 10 minuti dal campo di gara'),('a41',15,'Trasporto da e per il campo di gara','',3,'Non applicabile|No|Gestiti ma insufficienti|Gestiti e organizzati'),('b44',3,'Sedili','Insufficiente se < al 50% dei partecipanti',3,'Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),('b57',1,'Ombrelloni e/o ombreggiatura','Insufficiente se < al 50% dei partecipanti',3,'Non applicabile|Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),('b58',15,'Sacchetti per la spazzatura','',3,'Sufficiente|Insufficiente'),('b59',1,'Orientamento campo','',3,'Sud|Est o Ovest|Entro 15 gradi|Nord'),('b60',1,'Fondo del campo','',3,'Terriccio|Sintetico|Erboso sconnesso|Erboso raso'),('b61',2,'Temperatura sulla linea di tiro','',3,'Meno di 18°|18° o più'),('b62',2,'Illuminazione artificiale ','',4,'Diffusa|Diretta|Uniforme|Illuminati singolarmente|Non uniforme|Sufficiente|Non Sufficiente'),('b63',2,'Illuminazione naturale ','',4,'Schermata|Filtrante|Omogenea|Difforme'),('b64',12,'Servizi igienici','',3,'No|Solo al punto di ritrovo|Servizi lungo il percorso'),('b50',12,'Indicazioni di percorso','',4,'Con intervento GdG|Scarsamente segnalato|Ben segnalato e visibile|Mappa del percorso distribuita'),('b51',12,'Pulizia del percorso','da intendersi del fondo del sentiero tra le piazzole',2,''),('c08',15,'Ospedale Entro i 10 Km','',2,''),('c09',15,'Medico Presente','',2,''),('c10',15,'Ambulanza','',2,''),('c11',15,'Stanza Attrezzata per le Emergenze','',2,''),('c12',15,'Stanza attrezzata per antidoping','',3,'Non necessaria|No|Si comune|Si separata per sesso'),('D',15,'Premiazioni e Pubblicità','',-1,''),('d05',15,'Podio','',3,'Assente|Realizzato con paglioni o estemporaneamente|Struttura apposita'),('d03',15,'Bandiere','Nazionale, Fitarco, del comitato Regionale, della società',3,'No|Appoggiate a reti o siepi|Appese|Issate su pali'),('d04',15,'Inno Nazionale','',4,'Non Applicabile|No|Si|Inizio gara|Fine gara'),('d06',15,'Tipo di Premi (Scelta multipla)','',4,'Medaglie/Coppe|Diplomi|Premi in Denaro|Premi in Natura'),('d07',15,'Premiazione conforme al Regolamento','Numero di premiati e tipologia dei premi',2,''),('d08',15,'Cerimonia di premiazione conforme al Regolamento','Chiamata nell\'ordine previsto, con posizione, società, punteggio e nome',2,''),('a42',15,'Omaggio di partecipazione','meglio conosciuto come \"premio di partecipazione\"',2,''),('a43',15,'Premi a sorteggio','',2,''),('d09',15,'Conferenza Stampa','Nei giorni precedenti la competizione',2,''),('d10',15,'Presenza sulla carta stampata','Prima della competizione',4,'No|Stampa Locale|Stampa Nazionale|Stampa Estera'),('d11',15,'Testate','',1,'70|5'),('d12',15,'Presenza di televisioni','di emittenti televisive che trasmettono la competizione',4,'Assente|Locali|Web|Nazionali|Diretta'),('d13',15,'Emittenti','',1,'70|5'),('d14',15,'Pubblicazione risultati su internet','',3,'Nessuna|Termine gara|Rilevamenti parziali|Tempo reale (ogni volèe)'),('d15',15,'Indirizzo internet','indicare indirizzo internet sul quale sono stati pubblicati i risultati, con esclusione di quello fitarco',0,''),('Z',15,'Note','',-1,''),('z01',15,'Valutazione della Gara ed eventuali annotazioni ','',1,'70|7'),('b56',4,'Supporti Battifreccia','',4,'Pali|Cavalletti'),('a09',3,'Direttore dei tiri','',4,'Preparato|Non preparato|Attento|Disattento'),('a16',15,'Sintetizzare reclamo ed inviare l\'originale alla Segreteria Ufficio Tecnico','',1,'70|2'),('a18',15,'Commissari di campo ','per il cambio dei bersagli',4,'sufficienti|insufficienti|coordinati|professionali|disattenti'),('a19',15,'Tutte le richieste del Gdg soddisfatte','',2,''),('a20',15,'Se non soddisfatte indicare quali','',1,'70|2'),('b03',2,'Tipologia di impianto','',3,'Capannone|Palestra|Palestra scolastica|Palazzetto|Impianto dedicato'),('b02',1,'Tipologia di impianto','',3,'Campo incolto|Prato|Stadio|Impianto dedicato'),('b04',12,'Tipologia di percorso','',3,'Campagna pianeggiante|Campagna sconnessa|Bosco pianeggiante|Bosco collinare|Bosco montano'),('b031',2,'Capienza piazzole','',3,'fino a 10|da 11 a 15|da 16 a 20|Più di 20'),('b021',1,'Capienza piazzole di tiro','',3,'fino a 15|da 16 a 20|da 21 a 25|da 25 a 30|Più di 30 '),('b13',3,'Linea degli archi','',2,''),('b14',3,'Line del metro per match round','Solo per gare con scontri diretti',3,'Non applicabile|con box per tecnici|con box GdG'),('b35',15,'Cartelli nomi partecipanti agli scontri','',2,''),('b37',2,'Sedie per gli arbitri','',2,''),('b47',8,'Picchetto per l\'immagine della sagoma','',3,'Si|Si ma con intervento GdG'),('c04',15,'Accessi incustoditi','',2,''),('c05',15,'Commissari/Protezione Civile od altro addetti al controllo spettatori','',2,''),('c07',15,' attrezzatura di primo soccorso ','(bende garze ecc.)',2,''),('d01',15,'Tempo tra fine gara e inizio premiazione','',1,'70|1'),('d02',15,'Classifiche finali esposte prima della premiazione','',2,''),('d16',15,'Pubblicazione su sito federale prima dell\'allontanamento dell\'arbitro','',2,''),('M',15,'Atleti','',-1,''),('m01',15,'Fair Play','comportamento corretto dei partecipanti',2,''),('m02',15,'Se la risposta precedente è NO, descrivere nel dettaglio','',1,'70|2'),('m03',15,'Ammonizioni','',2,''),('m04',15,'Comportamento antisportivo: descrizione','',1,'70|2'),('m05',15,'Abbigliamento non conforme: descrizione','',1,'70|2'),('m06',15,'Altri ammonimenti: descrizione','',1,'70|2'),('m07',15,'Sanzioni','',2,''),('m08',15,'Infrazioni registrazione punti: descrizione','',1,'70|2'),('m10',15,'Infrazione esecuzione tiro: descrizione','',1,'70|2'),('m11',15,'Non idonei alla competizione: descrizione','',1,'70|2'),('m12',15,'Tutti gli arcieri idonei al tiro','',4,'No|Si|Visita medica scaduta|Non Tesserato|Tesseramento scaduto|Sotto squalifica|Altro'),('m13',15,'Se la risposta alla domanda precedente è NO indicare i soggetti','',1,'70|2'),('a04',15,'E\' stato rispettato il programma di gara','',2,''),('b22',7,'Visuali omologate','',2,'');
/*!40000 ALTER TABLE `FinalReportQ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Finals`
--

DROP TABLE IF EXISTS `Finals`;
CREATE TABLE `Finals` (
  `FinEvent` varchar(10) NOT NULL,
  `FinMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `FinTournament` int unsigned NOT NULL DEFAULT '0',
  `FinRank` tinyint unsigned NOT NULL DEFAULT '0',
  `FinAthlete` int unsigned NOT NULL DEFAULT '0',
  `FinScore` smallint NOT NULL DEFAULT '0',
  `FinSetScore` tinyint NOT NULL DEFAULT '0',
  `FinSetPoints` varchar(36) NOT NULL,
  `FinSetPointsByEnd` varchar(36) NOT NULL,
  `FinWinnerSet` tinyint NOT NULL DEFAULT '0',
  `FinTie` tinyint(1) NOT NULL DEFAULT '0',
  `FinArrowstring` varchar(60) NOT NULL,
  `FinTiebreak` varchar(10) NOT NULL,
  `FinTbClosest` tinyint NOT NULL,
  `FinTbDecoded` varchar(15) NOT NULL,
  `FinArrowPosition` text NOT NULL,
  `FinTiePosition` text NOT NULL,
  `FinWinLose` tinyint unsigned NOT NULL DEFAULT '0',
  `FinFinalRank` tinyint unsigned NOT NULL DEFAULT '0',
  `FinDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `FinSyncro` datetime NOT NULL,
  `FinLive` tinyint NOT NULL DEFAULT '0',
  `FinStatus` tinyint NOT NULL DEFAULT '0',
  `FinShootFirst` tinyint NOT NULL,
  `FinVxF` tinyint NOT NULL DEFAULT '0',
  `FinTarget` varchar(5) NOT NULL,
  `FinConfirmed` int NOT NULL,
  `FinNotes` varchar(30) NOT NULL,
  `FinRecordBitmap` tinyint unsigned NOT NULL,
  `FinIrmType` tinyint NOT NULL,
  `FinCoach` int unsigned NOT NULL,
  PRIMARY KEY (`FinEvent`,`FinMatchNo`,`FinTournament`),
  KEY `FinAthleteEventTournament` (`FinAthlete`,`FinEvent`,`FinTournament`),
  KEY `FinTournament` (`FinTournament`,`FinEvent`,`FinAthlete`,`FinMatchNo`),
  KEY `FinLive` (`FinLive`,`FinTournament`),
  KEY `FinDateTime` (`FinTournament`,`FinDateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Flags`
--

DROP TABLE IF EXISTS `Flags`;
CREATE TABLE `Flags` (
  `FlTournament` int NOT NULL DEFAULT '0',
  `FlIocCode` varchar(5) NOT NULL,
  `FlCode` varchar(10) NOT NULL,
  `FlSVG` mediumblob NOT NULL,
  `FlJPG` mediumblob NOT NULL,
  `FlEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FlChecked` varchar(1) NOT NULL DEFAULT '',
  `FlContAssoc` varchar(10) NOT NULL,
  PRIMARY KEY (`FlTournament`,`FlIocCode`,`FlCode`),
  KEY `FlEntered` (`FlEntered`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `GateLog`
--

DROP TABLE IF EXISTS `GateLog`;
CREATE TABLE `GateLog` (
  `GLEntry` int NOT NULL,
  `GLDateTime` datetime NOT NULL,
  `GLIP` varchar(15) NOT NULL,
  `GLDirection` tinyint NOT NULL,
  `GLTournament` int NOT NULL,
  `GLStatus` tinyint NOT NULL,
  KEY `GLEntry` (`GLEntry`),
  KEY `GLDateTime` (`GLDateTime`),
  KEY `GLTournament` (`GLTournament`,`GLEntry`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Grids`
--

DROP TABLE IF EXISTS `Grids`;
CREATE TABLE `Grids` (
  `GrMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `GrPosition` smallint NOT NULL DEFAULT '0',
  `GrPosition2` smallint NOT NULL DEFAULT '0',
  `GrPhase` tinyint NOT NULL DEFAULT '0',
  `GrBitPhase` tinyint unsigned NOT NULL,
  PRIMARY KEY (`GrMatchNo`),
  KEY `GrPosition` (`GrPosition`,`GrPhase`),
  KEY `GrPosition2` (`GrPosition2`,`GrPhase`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `Grids`
--

LOCK TABLES `Grids` WRITE;
/*!40000 ALTER TABLE `Grids` DISABLE KEYS */;
INSERT INTO `Grids` VALUES (0,1,1,0,1),(1,2,2,0,1),(2,4,4,1,2),(3,3,3,1,2),(4,1,1,2,4),(5,4,4,2,4),(6,3,3,2,4),(7,2,2,2,4),(8,1,1,4,8),(9,8,8,4,8),(10,5,5,4,8),(11,4,4,4,8),(12,3,3,4,8),(13,6,6,4,8),(14,7,7,4,8),(15,2,2,4,8),(16,1,1,8,16),(17,16,16,8,16),(18,9,9,8,16),(19,8,8,8,16),(20,5,5,8,16),(21,12,12,8,16),(22,13,13,8,16),(23,4,4,8,16),(24,3,3,8,16),(25,14,14,8,16),(26,11,11,8,16),(27,6,6,8,16),(28,7,7,8,16),(29,10,10,8,16),(30,15,15,8,16),(31,2,2,8,16),(32,1,1,16,32),(33,32,0,16,32),(34,17,17,16,32),(35,16,16,16,32),(36,9,9,16,32),(37,24,24,16,32),(38,25,0,16,32),(39,8,8,16,32),(40,5,5,16,32),(41,28,0,16,32),(42,21,21,16,32),(43,12,12,16,32),(44,13,13,16,32),(45,20,20,16,32),(46,29,0,16,32),(47,4,4,16,32),(48,3,3,16,32),(49,30,0,16,32),(50,19,19,16,32),(51,14,14,16,32),(52,11,11,16,32),(53,22,22,16,32),(54,27,0,16,32),(55,6,6,16,32),(56,7,7,16,32),(57,26,0,16,32),(58,23,23,16,32),(59,10,10,16,32),(60,15,15,16,32),(61,18,18,16,32),(62,31,0,16,32),(63,2,2,16,32),(64,1,1,32,64),(65,64,0,32,64),(66,33,33,32,64),(67,32,32,32,64),(68,17,17,32,64),(69,48,48,32,64),(70,49,49,32,64),(71,16,16,32,64),(72,9,9,32,64),(73,56,56,32,64),(74,41,41,32,64),(75,24,24,32,64),(76,25,25,32,64),(77,40,40,32,64),(78,57,0,32,64),(79,8,8,32,64),(80,5,5,32,64),(81,60,0,32,64),(82,37,37,32,64),(83,28,28,32,64),(84,21,21,32,64),(85,44,44,32,64),(86,53,53,32,64),(87,12,12,32,64),(88,13,13,32,64),(89,52,52,32,64),(90,45,45,32,64),(91,20,20,32,64),(92,29,29,32,64),(93,36,36,32,64),(94,61,0,32,64),(95,4,4,32,64),(96,3,3,32,64),(97,62,0,32,64),(98,35,35,32,64),(99,30,30,32,64),(100,19,19,32,64),(101,46,46,32,64),(102,51,51,32,64),(103,14,14,32,64),(104,11,11,32,64),(105,54,54,32,64),(106,43,43,32,64),(107,22,22,32,64),(108,27,27,32,64),(109,38,38,32,64),(110,59,0,32,64),(111,6,6,32,64),(112,7,7,32,64),(113,58,0,32,64),(114,39,39,32,64),(115,26,26,32,64),(116,23,23,32,64),(117,42,42,32,64),(118,55,55,32,64),(119,10,10,32,64),(120,15,15,32,64),(121,50,50,32,64),(122,47,47,32,64),(123,18,18,32,64),(124,31,31,32,64),(125,34,34,32,64),(126,63,0,32,64),(127,2,2,32,64),(128,1,1,64,128),(129,128,0,64,128),(130,65,0,64,128),(131,64,0,64,128),(132,33,33,64,128),(133,96,80,64,128),(134,97,81,64,128),(135,32,32,64,128),(136,17,17,64,128),(137,112,96,64,128),(138,81,65,64,128),(139,48,48,64,128),(140,49,49,64,128),(141,80,64,64,128),(142,113,97,64,128),(143,16,16,64,128),(144,9,9,64,128),(145,120,104,64,128),(146,73,57,64,128),(147,56,56,64,128),(148,41,41,64,128),(149,88,72,64,128),(150,105,89,64,128),(151,24,24,64,128),(152,25,25,64,128),(153,104,88,64,128),(154,89,73,64,128),(155,40,40,64,128),(156,57,0,64,128),(157,72,0,64,128),(158,121,0,64,128),(159,8,8,64,128),(160,5,5,64,128),(161,124,0,64,128),(162,69,0,64,128),(163,60,0,64,128),(164,37,37,64,128),(165,92,76,64,128),(166,101,85,64,128),(167,28,28,64,128),(168,21,21,64,128),(169,108,92,64,128),(170,85,69,64,128),(171,44,44,64,128),(172,53,53,64,128),(173,76,60,64,128),(174,117,101,64,128),(175,12,12,64,128),(176,13,13,64,128),(177,116,100,64,128),(178,77,61,64,128),(179,52,52,64,128),(180,45,45,64,128),(181,84,68,64,128),(182,109,93,64,128),(183,20,20,64,128),(184,29,29,64,128),(185,100,84,64,128),(186,93,77,64,128),(187,36,36,64,128),(188,61,0,64,128),(189,68,0,64,128),(190,125,0,64,128),(191,4,4,64,128),(192,3,3,64,128),(193,126,0,64,128),(194,67,0,64,128),(195,62,0,64,128),(196,35,35,64,128),(197,94,78,64,128),(198,99,83,64,128),(199,30,30,64,128),(200,19,19,64,128),(201,110,94,64,128),(202,83,67,64,128),(203,46,46,64,128),(204,51,51,64,128),(205,78,62,64,128),(206,115,99,64,128),(207,14,14,64,128),(208,11,11,64,128),(209,118,102,64,128),(210,75,59,64,128),(211,54,54,64,128),(212,43,43,64,128),(213,86,70,64,128),(214,107,91,64,128),(215,22,22,64,128),(216,27,27,64,128),(217,102,86,64,128),(218,91,75,64,128),(219,38,38,64,128),(220,59,0,64,128),(221,70,0,64,128),(222,123,0,64,128),(223,6,6,64,128),(224,7,7,64,128),(225,122,0,64,128),(226,71,0,64,128),(227,58,0,64,128),(228,39,39,64,128),(229,90,74,64,128),(230,103,87,64,128),(231,26,26,64,128),(232,23,23,64,128),(233,106,90,64,128),(234,87,71,64,128),(235,42,42,64,128),(236,55,55,64,128),(237,74,58,64,128),(238,119,103,64,128),(239,10,10,64,128),(240,15,15,64,128),(241,114,98,64,128),(242,79,63,64,128),(243,50,50,64,128),(244,47,47,64,128),(245,82,66,64,128),(246,111,95,64,128),(247,18,18,64,128),(248,31,31,64,128),(249,98,82,64,128),(250,95,79,64,128),(251,34,34,64,128),(252,63,0,64,128),(253,66,0,64,128),(254,127,0,64,128),(255,2,2,64,128);
/*!40000 ALTER TABLE `Grids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HTTData`
--

DROP TABLE IF EXISTS `HTTData`;
CREATE TABLE `HTTData` (
  `HtdEnId` int unsigned NOT NULL,
  `HtdMatchNo` tinyint unsigned DEFAULT NULL,
  `HtdEvent` varchar(10) NOT NULL DEFAULT '',
  `HtdTargetNo` varchar(5) NOT NULL,
  `HtdDistance` tinyint NOT NULL,
  `HtdFinScheduling` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `HtdTeamEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `HtdArrowStart` tinyint NOT NULL,
  `HtdArrowEnd` tinyint NOT NULL,
  `HtdArrowString` varchar(6) NOT NULL,
  PRIMARY KEY (`HtdTargetNo`,`HtdDistance`,`HtdArrowStart`,`HtdFinScheduling`,`HtdTeamEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `HeartBeat`
--

DROP TABLE IF EXISTS `HeartBeat`;
CREATE TABLE `HeartBeat` (
  `HbTournament` int NOT NULL,
  `HbEvent` varchar(10) NOT NULL,
  `HbTeamEvent` int NOT NULL,
  `HbMatchNo` int NOT NULL,
  `HbValue` smallint NOT NULL,
  `HbDateTime` datetime NOT NULL,
  PRIMARY KEY (`HbTournament`,`HbEvent`,`HbTeamEvent`,`HbMatchNo`,`HbDateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `HhtData`
--

DROP TABLE IF EXISTS `HhtData`;
CREATE TABLE `HhtData` (
  `HdTournament` int NOT NULL,
  `HdTargetNo` varchar(5) NOT NULL,
  `HdDistance` tinyint NOT NULL,
  `HdFinScheduling` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `HdTeamEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `HdArrowStart` tinyint NOT NULL,
  `HdArrowEnd` tinyint NOT NULL,
  `HdArrowString` varchar(6) NOT NULL,
  `HdEnId` int unsigned NOT NULL,
  `HdMatchNo` tinyint unsigned DEFAULT NULL,
  `HdEvent` varchar(10) NOT NULL DEFAULT '',
  `HdHhtId` int NOT NULL,
  `HdRealTargetNo` varchar(3) NOT NULL,
  `HdLetter` varchar(1) NOT NULL,
  `HdTimeStamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`HdTournament`,`HdTargetNo`,`HdDistance`,`HdArrowStart`,`HdFinScheduling`,`HdTeamEvent`),
  KEY `HdTournament` (`HdTournament`,`HdTeamEvent`,`HdHhtId`,`HdFinScheduling`,`HdTargetNo`),
  KEY `HdTournament_2` (`HdTournament`,`HdTimeStamp`,`HdDistance`,`HdArrowStart`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `HhtEvents`
--

DROP TABLE IF EXISTS `HhtEvents`;
CREATE TABLE `HhtEvents` (
  `HeTournament` int NOT NULL,
  `HeEventCode` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `HeHhtId` int NOT NULL,
  `HeSession` tinyint NOT NULL,
  `HeFinSchedule` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `HeTeamEvent` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`HeTournament`,`HeEventCode`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `HhtSetup`
--

DROP TABLE IF EXISTS `HhtSetup`;
CREATE TABLE `HhtSetup` (
  `HsId` int NOT NULL,
  `HsTournament` int NOT NULL,
  `HsIpAddress` varchar(15) NOT NULL,
  `HsPort` varchar(6) NOT NULL,
  `HsName` varchar(16) NOT NULL,
  `HsMode` tinyint NOT NULL,
  `HsFlags` varchar(16) NOT NULL,
  `HsPhase` varchar(20) NOT NULL,
  `HsSequence` varchar(12) NOT NULL,
  `HsDistance` tinyint NOT NULL,
  PRIMARY KEY (`HsId`,`HsTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `IdCardElements`
--

DROP TABLE IF EXISTS `IdCardElements`;
CREATE TABLE `IdCardElements` (
  `IceTournament` int NOT NULL,
  `IceOrder` int NOT NULL,
  `IceType` varchar(25) NOT NULL,
  `IceContent` longblob NOT NULL,
  `IceMimeType` varchar(25) NOT NULL,
  `IceOptions` text NOT NULL,
  `IceNewOrder` int NOT NULL,
  `IceCardNumber` int NOT NULL,
  `IceCardType` varchar(1) NOT NULL DEFAULT 'A',
  KEY `IceTournament` (`IceTournament`,`IceOrder`),
  KEY `IceTournament_2` (`IceTournament`,`IceCardNumber`,`IceOrder`),
  KEY `IceTournament_3` (`IceTournament`,`IceCardType`,`IceCardNumber`,`IceOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `IdCards`
--

DROP TABLE IF EXISTS `IdCards`;
CREATE TABLE `IdCards` (
  `IcTournament` int NOT NULL,
  `IcBackground` longblob NOT NULL,
  `IcSettings` text NOT NULL,
  `IcNumber` int NOT NULL,
  `IcType` varchar(1) NOT NULL DEFAULT 'A',
  `IcName` varchar(50) NOT NULL DEFAULT 'Accreditation',
  PRIMARY KEY (`IcTournament`,`IcType`,`IcNumber`),
  KEY `IcTournament` (`IcTournament`,`IcNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Images`
--

DROP TABLE IF EXISTS `Images`;
CREATE TABLE `Images` (
  `ImTournament` int NOT NULL,
  `ImIocCode` varchar(5) NOT NULL COMMENT 'If IocCode is empty Ref is on ID and not Code',
  `ImSection` varchar(5) NOT NULL COMMENT 'Section of Ianseo in which it is used',
  `ImReference` varchar(11) NOT NULL COMMENT 'Depending on section, refers to EnCode, position, coCode etc',
  `ImType` varchar(3) NOT NULL COMMENT 'PNG, SVG, JPG, etc',
  `ImContent` mediumblob NOT NULL,
  `ImgLastUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ImChecked` varchar(1) NOT NULL,
  PRIMARY KEY (`ImTournament`,`ImIocCode`,`ImSection`,`ImReference`,`ImType`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `IndOldPositions`
--

DROP TABLE IF EXISTS `IndOldPositions`;
CREATE TABLE `IndOldPositions` (
  `IopId` int unsigned NOT NULL,
  `IopEvent` varchar(10) NOT NULL,
  `IopTournament` int NOT NULL,
  `IopHits` int NOT NULL,
  `IopRank` smallint NOT NULL,
  PRIMARY KEY (`IopId`,`IopEvent`,`IopTournament`,`IopHits`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Individuals`
--

DROP TABLE IF EXISTS `Individuals`;
CREATE TABLE `Individuals` (
  `IndId` int unsigned NOT NULL,
  `IndEvent` varchar(10) NOT NULL,
  `IndTournament` int NOT NULL,
  `IndD1Rank` smallint NOT NULL,
  `IndD2Rank` smallint NOT NULL,
  `IndD3Rank` smallint NOT NULL,
  `IndD4Rank` smallint NOT NULL,
  `IndD5Rank` smallint NOT NULL,
  `IndD6Rank` smallint NOT NULL,
  `IndD7Rank` smallint NOT NULL,
  `IndD8Rank` smallint NOT NULL,
  `IndRank` smallint NOT NULL,
  `IndRankFinal` smallint NOT NULL,
  `IndSO` smallint NOT NULL DEFAULT '0',
  `IndTieBreak` varchar(8) NOT NULL,
  `IndTbClosest` tinyint NOT NULL,
  `IndTbDecoded` varchar(15) NOT NULL,
  `IndTimestamp` datetime DEFAULT NULL,
  `IndTimestampFinal` datetime DEFAULT NULL,
  `IndBacknoPrinted` datetime NOT NULL,
  `IndNotes` varchar(50) NOT NULL,
  `IndRecordBitmap` tinyint unsigned NOT NULL,
  `IndIrmType` tinyint NOT NULL,
  `IndIrmTypeFinal` tinyint NOT NULL,
  PRIMARY KEY (`IndId`,`IndEvent`,`IndTournament`),
  KEY `IndEvent` (`IndEvent`,`IndTournament`,`IndRankFinal`,`IndIrmTypeFinal`,`IndIrmType`,`IndRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `InvolvedType`
--

DROP TABLE IF EXISTS `InvolvedType`;
CREATE TABLE `InvolvedType` (
  `ItId` smallint unsigned NOT NULL AUTO_INCREMENT,
  `ItDescription` varchar(32) NOT NULL,
  `ItJudge` tinyint unsigned NOT NULL DEFAULT '0',
  `ItDoS` tinyint unsigned NOT NULL DEFAULT '0',
  `ItJury` tinyint unsigned NOT NULL,
  `ItOC` tinyint unsigned NOT NULL,
  PRIMARY KEY (`ItId`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `InvolvedType`
--

LOCK TABLES `InvolvedType` WRITE;
/*!40000 ALTER TABLE `InvolvedType` DISABLE KEYS */;
INSERT INTO `InvolvedType` VALUES (1,'Judge',2,0,0,0),(2,'Dos',0,1,0,0),(3,'OrgResponsible',0,0,0,2),(4,'Jury',0,0,2,0),(5,'ChairmanJudge',1,0,0,0),(6,'DosAssistant',0,2,0,0),(7,'ChairmanJury',0,0,1,0),(8,'AlternateJury',0,0,3,0),(9,'FieldResp',0,0,0,3),(10,'ResultResp',0,0,0,7),(11,'LogisticResp',0,0,0,6),(12,'MediaResp',0,0,0,4),(13,'TecDelegate',0,0,0,1),(14,'SportPres',0,0,0,5),(15,'Announcer',0,0,0,8),(16,'ADOfficer',0,0,0,9),(17,'MedOfficer',0,0,0,10),(18,'CompManager',0,0,0,11),(19,'ResVerifier',0,0,0,12);
/*!40000 ALTER TABLE `InvolvedType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IrmTypes`
--

DROP TABLE IF EXISTS `IrmTypes`;
CREATE TABLE `IrmTypes` (
  `IrmId` tinyint NOT NULL,
  `IrmType` varchar(5) NOT NULL,
  `IrmShowRank` tinyint NOT NULL,
  `IrmHideDetails` tinyint NOT NULL,
  PRIMARY KEY (`IrmId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `IrmTypes`
--

LOCK TABLES `IrmTypes` WRITE;
/*!40000 ALTER TABLE `IrmTypes` DISABLE KEYS */;
INSERT INTO `IrmTypes` VALUES (0,'',1,0),(5,'DNF',1,0),(10,'DNS',0,0),(15,'DSQ',0,0),(20,'DQB',0,1),(7,'DNF',0,0);
/*!40000 ALTER TABLE `IrmTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IskData`
--

DROP TABLE IF EXISTS `IskData`;
CREATE TABLE `IskData` (
  `IskDtTournament` int NOT NULL,
  `IskDtMatchNo` int NOT NULL,
  `IskDtEvent` varchar(10) NOT NULL,
  `IskDtTeamInd` int NOT NULL,
  `IskDtType` varchar(2) NOT NULL,
  `IskDtTargetNo` varchar(5) NOT NULL,
  `IskDtDistance` int NOT NULL,
  `IskDtEndNo` int NOT NULL,
  `IskDtArrowstring` varchar(9) NOT NULL,
  `IskDtUpdate` datetime NOT NULL,
  `IskDtDevice` varchar(36) NOT NULL,
  `IskDtSession` tinyint NOT NULL,
  PRIMARY KEY (`IskDtTournament`,`IskDtMatchNo`,`IskDtEvent`,`IskDtTeamInd`,`IskDtType`,`IskDtTargetNo`,`IskDtDistance`,`IskDtEndNo`,`IskDtSession`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `IskDevices`
--

DROP TABLE IF EXISTS `IskDevices`;
CREATE TABLE `IskDevices` (
  `IskDvTournament` int NOT NULL,
  `IskDvDevice` varchar(36) NOT NULL,
  `IskDvGroup` tinyint NOT NULL,
  `IskDvSchedKey` varchar(25) NOT NULL,
  `IskDvVersion` varchar(12) DEFAULT NULL,
  `IskDvAppVersion` tinyint NOT NULL,
  `IskDvCode` varchar(4) NOT NULL,
  `IskDvTarget` varchar(3) NOT NULL,
  `IskDvTargetReq` varchar(3) NOT NULL,
  `IskDvState` tinyint NOT NULL,
  `IskDvBattery` tinyint NOT NULL,
  `IskDvIpAddress` varchar(15) NOT NULL,
  `IskDvLastSeen` datetime NOT NULL,
  `IskDvAuthRequest` tinyint NOT NULL,
  `IskDvProActive` tinyint NOT NULL,
  `IskDvProConnected` tinyint NOT NULL,
  `IskDvSetup` blob NOT NULL,
  `IskDvRunningConf` text NOT NULL,
  `IskDvUrlDownload` tinytext NOT NULL,
  `IskDvGps` text NOT NULL,
  PRIMARY KEY (`IskDvDevice`),
  KEY `IskDvTournament` (`IskDvTournament`),
  KEY `IskDvTournament_2` (`IskDvTournament`,`IskDvGroup`),
  KEY `IskDvTournament_3` (`IskDvTournament`,`IskDvSchedKey`,`IskDvGroup`),
  KEY `IskDvCode` (`IskDvCode`,`IskDvTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Logs`
--

DROP TABLE IF EXISTS `Logs`;
CREATE TABLE `Logs` (
  `LogTournament` int NOT NULL,
  `LogType` varchar(20) NOT NULL,
  `LogTitle` varchar(20) NOT NULL,
  `LogEntry` int NOT NULL,
  `LogMessage` text NOT NULL,
  `LogTimestamp` datetime(3) NOT NULL,
  `LogIP` varchar(15) NOT NULL,
  PRIMARY KEY (`LogTournament`,`LogType`,`LogEntry`,`LogTimestamp`),
  KEY `LogType` (`LogType`,`LogTournament`,`LogTimestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `LookUpEntries`
--

DROP TABLE IF EXISTS `LookUpEntries`;
CREATE TABLE `LookUpEntries` (
  `LueCode` varchar(25) NOT NULL,
  `LueIocCode` varchar(5) NOT NULL DEFAULT '',
  `LueFamilyName` varchar(60) NOT NULL,
  `LueName` varchar(30) NOT NULL,
  `LueSex` tinyint unsigned NOT NULL DEFAULT '0',
  `LueClassified` tinyint unsigned NOT NULL,
  `LueCtrlCode` date DEFAULT NULL,
  `LueCountry` varchar(10) NOT NULL,
  `LueCoDescr` varchar(80) NOT NULL,
  `LueCoShort` varchar(30) NOT NULL,
  `LueCountry2` varchar(10) NOT NULL,
  `LueCoDescr2` varchar(80) NOT NULL,
  `LueCoShort2` varchar(30) NOT NULL,
  `LueCountry3` varchar(10) NOT NULL,
  `LueCoDescr3` varchar(80) NOT NULL,
  `LueCoShort3` varchar(30) NOT NULL,
  `LueDivision` varchar(4) NOT NULL,
  `LueClass` varchar(6) NOT NULL,
  `LueSubClass` varchar(2) NOT NULL,
  `LueStatus` tinyint NOT NULL,
  `LueStatusValidUntil` date NOT NULL DEFAULT '0000-00-00',
  `LueDefault` tinyint NOT NULL DEFAULT '0',
  `LueNameOrder` tinyint NOT NULL,
  PRIMARY KEY (`LueCode`,`LueIocCode`,`LueDivision`,`LueClass`),
  KEY `LueCountry` (`LueCountry`),
  KEY `LueCode` (`LueCode`),
  KEY `LueIocCode` (`LueIocCode`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `LookUpPaths`
--

DROP TABLE IF EXISTS `LookUpPaths`;
CREATE TABLE `LookUpPaths` (
  `LupIocCode` varchar(5) NOT NULL,
  `LupOrigin` varchar(3) NOT NULL,
  `LupPath` varchar(255) NOT NULL,
  `LupPhotoPath` varchar(255) NOT NULL,
  `LupFlagsPath` varchar(255) NOT NULL,
  `LupLastUpdate` datetime DEFAULT NULL,
  `LupRankingPath` varchar(255) NOT NULL,
  `LupClubNamesPath` varchar(255) NOT NULL,
  `LupRecordsPath` varchar(255) NOT NULL,
  PRIMARY KEY (`LupIocCode`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci COMMENT='Defines the LookUp paths for each IOC Country';

--
-- Dumping data for table `LookUpPaths`
--

LOCK TABLES `LookUpPaths` WRITE;
/*!40000 ALTER TABLE `LookUpPaths` DISABLE KEYS */;
INSERT INTO `LookUpPaths` VALUES ('ITA','','http://www.fitarco-italia.org/gare/ianseo/IanseoData.php','http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php','http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php',NULL,'','',''),('FITA','WA','%Modules/LookUpFunctions/LookupFitaId.php','%Modules/LookUpFunctions/LookupFitaPhoto.php','https://extranet.worldarchery.sport/Api/GetFlags.php',NULL,'%Modules/LookUpFunctions/LookupFitaRanking.php','%Modules/LookUpFunctions/LookupFitaClubNames.php',''),('SUI','','http://www.asta-sbv.ch/var/ianseo/IanseoData.php','','',NULL,'','',''),('ITA_e','','http://www.fitarco-italia.org/gare/ianseo/IanseoDataEsordienti.php','http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php','http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php','0000-00-00 00:00:00','','',''),('ITA_p','','http://www.fitarco-italia.org/gare/ianseo/IanseoDataPinocchio.php','http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php','http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php','0000-00-00 00:00:00','','',''),('CAN','','https://can.service.ianseo.net/IanseoData.php','https://can.service.ianseo.net/GetPhoto.php','https://can.service.ianseo.net/GetFlags.php','0000-00-00 00:00:00','','',''),('BALT','','https://baltic.service.ianseo.net/IanseoData.php','https://baltic.service.ianseo.net/GetPhoto.php','https://baltic.service.ianseo.net/GetFlags.php',NULL,'','',''),('ITA_i','','http://www.fitarco-italia.org/gare/ianseo/IanseoDataIndoor.php','http://www.fitarco-italia.org/gare/ianseo/IanseoPhoto.php','http://www.fitarco-italia.org/gare/ianseo/IanseoFlags.php',NULL,'','',''),('FRA','FRA','http://www.ffta-asso.com/Ianseo-FFTA/Licences.json','','',NULL,'','','');
/*!40000 ALTER TABLE `LookUpPaths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ModulesParameters`
--

DROP TABLE IF EXISTS `ModulesParameters`;
CREATE TABLE `ModulesParameters` (
  `MpModule` varchar(50) NOT NULL,
  `MpParameter` varchar(30) NOT NULL,
  `MpTournament` int unsigned NOT NULL,
  `MpValue` text NOT NULL,
  PRIMARY KEY (`MpModule`,`MpParameter`,`MpTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `OdfDocuments`
--

DROP TABLE IF EXISTS `OdfDocuments`;
CREATE TABLE `OdfDocuments` (
  `OdfDocTournament` int NOT NULL,
  `OdfDocCode` varchar(34) NOT NULL,
  `OdfDocSubCode` varchar(34) NOT NULL,
  `OdfDocType` varchar(22) NOT NULL,
  `OdfDocSubType` varchar(34) NOT NULL,
  `OdfDocDataFeed` varchar(1) NOT NULL,
  `OdfDocVersion` int NOT NULL,
  `OdfDocDate` date NOT NULL,
  `OdfDocLogicalDate` date NOT NULL,
  `OdfDocTime` time(3) NOT NULL,
  `OdfDocStatus` varchar(15) NOT NULL,
  `OdfDocSendStatus` tinyint NOT NULL,
  `OdfDocSendRetries` tinyint NOT NULL,
  `OdfDocExtra` text NOT NULL,
  PRIMARY KEY (`OdfDocTournament`,`OdfDocCode`,`OdfDocSubCode`,`OdfDocType`,`OdfDocSubType`,`OdfDocDataFeed`),
  KEY `OdfDocTournament` (`OdfDocTournament`,`OdfDocDate`,`OdfDocTime`),
  KEY `OdfDocTournament_2` (`OdfDocTournament`,`OdfDocSendStatus`,`OdfDocSendRetries`,`OdfDocDate`,`OdfDocTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `OdfMessageStatus`
--

DROP TABLE IF EXISTS `OdfMessageStatus`;
CREATE TABLE `OdfMessageStatus` (
  `OmsTournament` int NOT NULL,
  `OmsType` varchar(5) NOT NULL,
  `OmsKey` varchar(34) NOT NULL,
  `OmsDataFeed` varchar(1) NOT NULL,
  `OmsStatus` varchar(15) NOT NULL,
  `OmsTimestamp` datetime NOT NULL,
  PRIMARY KEY (`OmsTournament`,`OmsType`,`OmsKey`,`OmsDataFeed`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `OdfTranslations`
--

DROP TABLE IF EXISTS `OdfTranslations`;
CREATE TABLE `OdfTranslations` (
  `OdfTrTournament` int NOT NULL,
  `OdfTrInternal` varchar(10) NOT NULL,
  `OdfTrType` varchar(10) NOT NULL,
  `OdfTrOdfCode` varchar(50) NOT NULL,
  `OdfTrIanseo` varchar(34) NOT NULL,
  `OdfTrLanguage` varchar(3) NOT NULL,
  PRIMARY KEY (`OdfTrTournament`,`OdfTrInternal`,`OdfTrType`,`OdfTrIanseo`,`OdfTrLanguage`),
  KEY `OdfTrTournament` (`OdfTrTournament`,`OdfTrLanguage`,`OdfTrInternal`,`OdfTrType`,`OdfTrIanseo`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `OnLineIds`
--

DROP TABLE IF EXISTS `OnLineIds`;
CREATE TABLE `OnLineIds` (
  `OliId` int NOT NULL,
  `OliType` varchar(1) NOT NULL,
  `OliServer` varchar(50) NOT NULL,
  `OliOnlineId` int NOT NULL,
  `OliTournament` int NOT NULL,
  PRIMARY KEY (`OliId`,`OliType`,`OliServer`,`OliTournament`),
  KEY `OliServer` (`OliServer`,`OliTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Parameters`
--

DROP TABLE IF EXISTS `Parameters`;
CREATE TABLE `Parameters` (
  `ParId` varchar(15) NOT NULL,
  `ParValue` text NOT NULL,
  PRIMARY KEY (`ParId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `Parameters`
--

LOCK TABLES `Parameters` WRITE;
/*!40000 ALTER TABLE `Parameters` DISABLE KEYS */;
INSERT INTO `Parameters` VALUES ('LueUpdat','20090908103644'),('ChkUp','2021-12-26 13:49:23'),('ResPath','http://www.fitarco-italia.org/gare/getfiles.php'),('IntEvent','0'),('SwUpdate','2021-08-08'),('HttMode','0'),('HttFlg','NNNNNNNNNNNNNNNN'),('HttSeq','0103011006'),('HttSes','0'),('HttHost','192.168.1.1'),('HttPort','9001'),('DBUpdate','2021-12-25 16:00:05'),('AcceptGP','2021-12-05 13:00:39'),('DEBUG',''),('TourBusy',''),('SpkTimer','30|ffffff;60|ffffff;90|ffffff;120|#'),('IsCode',''),('OnClickMenu',''),('AccActive',''),('AccCompetitions',''),('AccIPs',''),('AcceptGPL','2021-12-26 13:49:04'),('UUID2','Ianseo-61c872d8755908.80054882');
/*!40000 ALTER TABLE `Parameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Phases`
--

DROP TABLE IF EXISTS `Phases`;
CREATE TABLE `Phases` (
  `PhId` tinyint NOT NULL DEFAULT '0',
  `PhDescr` varchar(64) NOT NULL DEFAULT '',
  `PhLevel` tinyint NOT NULL DEFAULT '-1',
  `PhIndTeam` tinyint NOT NULL,
  `PhRuleSets` tinytext NOT NULL,
  PRIMARY KEY (`PhId`),
  KEY `PhId` (`PhId`,`PhRuleSets`(50))
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `Phases`
--

LOCK TABLES `Phases` WRITE;
/*!40000 ALTER TABLE `Phases` DISABLE KEYS */;
INSERT INTO `Phases` VALUES (0,'Final',-1,3,''),(1,'BronzeFinal',0,3,''),(2,'SemiFinal',-1,3,''),(4,'4Final',-1,3,''),(8,'8Final',-1,3,''),(12,'12Final',16,2,''),(16,'16Final',-1,3,''),(24,'24Final',32,1,''),(32,'32Final',-1,1,''),(48,'48Final',64,1,''),(64,'64Final',-1,1,''),(7,'7final',8,1,'FR'),(14,'14final',16,1,'FR');
/*!40000 ALTER TABLE `Phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Photos`
--

DROP TABLE IF EXISTS `Photos`;
CREATE TABLE `Photos` (
  `PhEnId` int unsigned NOT NULL,
  `PhPhoto` longblob NOT NULL,
  `PhPhotoEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PhToRetake` tinyint NOT NULL,
  PRIMARY KEY (`PhEnId`),
  KEY `PhPhotoEntered` (`PhPhotoEntered`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `QualOldPositions`
--

DROP TABLE IF EXISTS `QualOldPositions`;
CREATE TABLE `QualOldPositions` (
  `QopId` int unsigned NOT NULL,
  `QopHits` int NOT NULL,
  `QopClRank` smallint NOT NULL,
  `QopSubClassRank` smallint NOT NULL,
  PRIMARY KEY (`QopId`,`QopHits`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Qualifications`
--

DROP TABLE IF EXISTS `Qualifications`;
CREATE TABLE `Qualifications` (
  `QuId` int unsigned NOT NULL,
  `QuSession` tinyint unsigned NOT NULL DEFAULT '0',
  `QuTarget` int NOT NULL,
  `QuLetter` varchar(1) NOT NULL,
  `QuTargetNo` varchar(5) NOT NULL,
  `QuBacknoPrinted` datetime NOT NULL,
  `QuD1Score` smallint NOT NULL,
  `QuD1Hits` smallint NOT NULL,
  `QuD1Gold` smallint NOT NULL,
  `QuD1Xnine` smallint NOT NULL,
  `QuD1Arrowstring` varchar(255) NOT NULL,
  `QuD1Rank` smallint NOT NULL,
  `QuD1Status` tinyint unsigned NOT NULL,
  `QuD2Score` smallint NOT NULL,
  `QuD2Hits` smallint NOT NULL,
  `QuD2Gold` smallint NOT NULL,
  `QuD2Xnine` smallint NOT NULL,
  `QuD2Arrowstring` varchar(255) NOT NULL,
  `QuD2Rank` smallint NOT NULL,
  `QuD2Status` tinyint unsigned NOT NULL,
  `QuD3Score` smallint NOT NULL,
  `QuD3Hits` smallint NOT NULL,
  `QuD3Gold` smallint NOT NULL,
  `QuD3Xnine` smallint NOT NULL,
  `QuD3Arrowstring` varchar(255) NOT NULL,
  `QuD3Rank` smallint NOT NULL,
  `QuD3Status` tinyint unsigned NOT NULL,
  `QuD4Score` smallint NOT NULL,
  `QuD4Hits` smallint NOT NULL,
  `QuD4Gold` smallint NOT NULL,
  `QuD4Xnine` smallint NOT NULL,
  `QuD4Arrowstring` varchar(255) NOT NULL,
  `QuD4Rank` smallint NOT NULL,
  `QuD4Status` tinyint unsigned NOT NULL,
  `QuD5Score` smallint NOT NULL,
  `QuD5Hits` smallint NOT NULL,
  `QuD5Gold` smallint NOT NULL,
  `QuD5Xnine` smallint NOT NULL,
  `QuD5Arrowstring` varchar(36) NOT NULL,
  `QuD5Rank` smallint NOT NULL,
  `QuD5Status` tinyint unsigned NOT NULL,
  `QuD6Score` smallint NOT NULL,
  `QuD6Hits` smallint NOT NULL,
  `QuD6Gold` smallint NOT NULL,
  `QuD6Xnine` smallint NOT NULL,
  `QuD6Arrowstring` varchar(36) NOT NULL,
  `QuD6Rank` smallint NOT NULL,
  `QuD6Status` tinyint unsigned NOT NULL,
  `QuD7Score` smallint NOT NULL,
  `QuD7Hits` smallint NOT NULL,
  `QuD7Gold` smallint NOT NULL,
  `QuD7Xnine` smallint NOT NULL,
  `QuD7Arrowstring` varchar(36) NOT NULL,
  `QuD7Rank` smallint NOT NULL,
  `QuD7Status` tinyint unsigned NOT NULL,
  `QuD8Score` smallint NOT NULL,
  `QuD8Hits` smallint NOT NULL,
  `QuD8Gold` smallint NOT NULL,
  `QuD8Xnine` smallint NOT NULL,
  `QuD8Arrowstring` varchar(36) NOT NULL,
  `QuD8Rank` smallint NOT NULL,
  `QuD8Status` tinyint unsigned NOT NULL,
  `QuScore` int NOT NULL,
  `QuHits` int NOT NULL,
  `QuGold` int NOT NULL,
  `QuXnine` int NOT NULL,
  `QuArrow` tinyint NOT NULL,
  `QuConfirm` int NOT NULL,
  `QuClRank` smallint NOT NULL,
  `QuSubClassRank` smallint NOT NULL,
  `QuStatus` tinyint unsigned NOT NULL,
  `QuTie` tinyint(1) NOT NULL,
  `QuTieBreak` varchar(16) NOT NULL,
  `QuTimestamp` datetime DEFAULT NULL,
  `QuNotes` varchar(50) NOT NULL,
  `QuIrmType` tinyint NOT NULL,
  PRIMARY KEY (`QuId`),
  KEY `QuSession` (`QuSession`),
  KEY `QuTargetNo` (`QuTargetNo`),
  KEY `QuClRank` (`QuClRank`),
  KEY `QuSubClassRank` (`QuSubClassRank`),
  KEY `QuSession_2` (`QuSession`,`QuTarget`,`QuLetter`),
  KEY `QuIrmType` (`QuIrmType`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Rankings`
--

DROP TABLE IF EXISTS `Rankings`;
CREATE TABLE `Rankings` (
  `RankTournament` int NOT NULL,
  `RankCode` varchar(25) NOT NULL,
  `RankIocCode` varchar(5) NOT NULL,
  `RankTeam` tinyint NOT NULL,
  `RankEvent` varchar(10) NOT NULL,
  `RankRanking` int NOT NULL,
  `RankPersonalBest` int NOT NULL,
  `RankSeasonBest` int NOT NULL,
  PRIMARY KEY (`RankTournament`,`RankCode`,`RankTeam`,`RankEvent`),
  KEY `DvOrder` (`RankTournament`,`RankTeam`,`RankEvent`,`RankRanking`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `RecAreas`
--

DROP TABLE IF EXISTS `RecAreas`;
CREATE TABLE `RecAreas` (
  `ReArCode` varchar(20) NOT NULL,
  `ReArName` varchar(50) NOT NULL,
  `ReArBitLevel` tinyint unsigned NOT NULL,
  `ReArMaCode` varchar(10) NOT NULL,
  `ReArWaMaintenance` tinyint NOT NULL,
  `ReArOdfCode` varchar(3) NOT NULL,
  `ReArOdfHeader` varchar(50) NOT NULL,
  `ReArOdfParaCode` varchar(3) NOT NULL,
  `ReArOdfParaHeader` varchar(50) NOT NULL,
  PRIMARY KEY (`ReArCode`),
  KEY `ReArBitLevel` (`ReArBitLevel`,`ReArName`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `RecBroken`
--

DROP TABLE IF EXISTS `RecBroken`;
CREATE TABLE `RecBroken` (
  `RecBroTournament` int NOT NULL,
  `RecBroAthlete` int NOT NULL,
  `RecBroTeam` int NOT NULL,
  `RecBroSubTeam` int NOT NULL,
  `RecBroRecCode` varchar(25) NOT NULL,
  `RecBroRecCategory` varchar(10) NOT NULL,
  `RecBroRecPara` tinyint unsigned NOT NULL,
  `RecBroRecTeam` tinyint unsigned NOT NULL,
  `RecBroRecPhase` tinyint unsigned NOT NULL,
  `RecBroRecSubPhase` tinyint unsigned NOT NULL,
  `RecBroRecDouble` tinyint unsigned NOT NULL,
  `RecBroRecMeters` tinyint unsigned NOT NULL,
  `RecBroRecEvent` varchar(10) NOT NULL,
  `RecBroRecMatchno` tinyint unsigned NOT NULL,
  `RecBroRecDate` datetime NOT NULL,
  PRIMARY KEY (`RecBroTournament`,`RecBroAthlete`,`RecBroTeam`,`RecBroSubTeam`,`RecBroRecCode`,`RecBroRecCategory`,`RecBroRecPara`,`RecBroRecTeam`,`RecBroRecPhase`,`RecBroRecSubPhase`,`RecBroRecDouble`,`RecBroRecMeters`,`RecBroRecEvent`,`RecBroRecMatchno`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `RecTargetFaces`
--

DROP TABLE IF EXISTS `RecTargetFaces`;
CREATE TABLE `RecTargetFaces` (
  `RtfId` varchar(5) NOT NULL,
  `RtfDescription` varchar(40) NOT NULL,
  `RtfDiameter` tinyint unsigned NOT NULL,
  PRIMARY KEY (`RtfId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `RecTargetFaces`
--

LOCK TABLES `RecTargetFaces` WRITE;
/*!40000 ALTER TABLE `RecTargetFaces` DISABLE KEYS */;
INSERT INTO `RecTargetFaces` VALUES ('40X','40cm Small 10 (Compound)',40),('40','40cm Big 10 (Recurve)',40),('60X','60cm Small 10 (Compound)',60),('60','60cm Big 10 (Recurve)',60),('80','80cm',80),('122','122cm',122),('9753','90m-70m: 122cm; 50m-30m: 80cm',0),('7653','70m-60m: 122cm; 50m-30m: 80cm',0),('6543','60m-50m: 122cm; 40m-30m: 80cm',0),('3333','30m: 60cm; 80cm; 80cm; 122cm',0);
/*!40000 ALTER TABLE `RecTargetFaces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RecTournament`
--

DROP TABLE IF EXISTS `RecTournament`;
CREATE TABLE `RecTournament` (
  `RtTournament` int NOT NULL,
  `RtRecCode` varchar(25) NOT NULL,
  `RtRecDivision` varchar(4) NOT NULL,
  `RtRecTeam` smallint NOT NULL,
  `RtRecPara` varchar(1) NOT NULL,
  `RtRecCategory` varchar(10) NOT NULL,
  `RtRecCategoryName` varchar(50) NOT NULL,
  `RtRecLocalCategory` varchar(10) NOT NULL,
  `RtRecCatEquivalents` varchar(25) NOT NULL,
  `RtRecLocalEquivalents` varchar(25) NOT NULL,
  `RtRecDistance` varchar(50) NOT NULL,
  `RtRecTotal` smallint NOT NULL,
  `RtRecXNine` smallint NOT NULL,
  `RtRecDate` date NOT NULL,
  `RtRecExtra` text NOT NULL,
  `RtRecLastUpdated` datetime NOT NULL,
  `RtRecPhase` tinyint NOT NULL,
  `RtRecSubphase` tinyint NOT NULL,
  `RtRecTargetCode` varchar(5) NOT NULL,
  `RtRecComponents` tinyint unsigned NOT NULL DEFAULT '1',
  `RtRecTarget` varchar(5) NOT NULL,
  `RtRecMeters` tinyint unsigned NOT NULL,
  `RtRecMaxScore` int unsigned NOT NULL,
  `RtRecDouble` tinyint unsigned NOT NULL,
  PRIMARY KEY (`RtTournament`,`RtRecCode`,`RtRecTeam`,`RtRecCategory`,`RtRecPhase`,`RtRecSubphase`,`RtRecDouble`,`RtRecPara`,`RtRecMeters`),
  KEY `RtRecPhase` (`RtTournament`,`RtRecCode`,`RtRecTeam`,`RtRecCategory`,`RtRecPhase`,`RtRecSubphase`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Reviews`
--

DROP TABLE IF EXISTS `Reviews`;
CREATE TABLE `Reviews` (
  `RevEvent` varchar(10) NOT NULL,
  `RevMatchNo` tinyint unsigned NOT NULL,
  `RevTournament` int unsigned NOT NULL,
  `RevTeamEvent` tinyint NOT NULL,
  `RevLanguage1` text NOT NULL,
  `RevLanguage2` text NOT NULL,
  `RevDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `RevSyncro` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`RevEvent`,`RevMatchNo`,`RevTournament`,`RevTeamEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Scheduler`
--

DROP TABLE IF EXISTS `Scheduler`;
CREATE TABLE `Scheduler` (
  `SchTournament` int unsigned NOT NULL,
  `SchOrder` tinyint unsigned NOT NULL DEFAULT '1',
  `SchDateStart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SchDateEnd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `SchSesOrder` tinyint unsigned NOT NULL DEFAULT '0',
  `SchSesType` varchar(1) NOT NULL DEFAULT 'Z' COMMENT 'Q: qual E: elim F: final T: teamfinal Z: freetext',
  `SchDescr` varchar(255) NOT NULL DEFAULT '',
  `SchDay` date NOT NULL,
  `SchStart` time NOT NULL,
  `SchDuration` int NOT NULL,
  `SchTitle` varchar(255) NOT NULL,
  `SchSubTitle` varchar(255) NOT NULL,
  `SchText` varchar(255) NOT NULL,
  `SchShift` int DEFAULT NULL,
  `SchTargets` text NOT NULL,
  `SchLink` varchar(100) NOT NULL,
  `SchTimestamp` timestamp NOT NULL,
  PRIMARY KEY (`SchTournament`,`SchDay`,`SchStart`,`SchOrder`),
  KEY `SchTournament` (`SchTournament`,`SchDay`,`SchStart`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
CREATE TABLE `Session` (
  `SesTournament` int unsigned NOT NULL,
  `SesOrder` tinyint unsigned NOT NULL DEFAULT '0',
  `SesType` varchar(1) NOT NULL DEFAULT 'Q' COMMENT 'Q: qual E: elim F: final T: teamfinal',
  `SesName` varchar(100) NOT NULL DEFAULT '',
  `SesTar4Session` int NOT NULL,
  `SesAth4Target` tinyint unsigned NOT NULL DEFAULT '0',
  `SesFirstTarget` int NOT NULL,
  `SesFollow` tinyint unsigned NOT NULL DEFAULT '0',
  `SesStatus` varchar(15) NOT NULL,
  `SesDtStart` datetime NOT NULL,
  `SesDtEnd` datetime NOT NULL,
  `SesOdfCode` varchar(5) NOT NULL,
  `SesOdfPeriod` varchar(5) NOT NULL,
  `SesOdfVenue` varchar(5) NOT NULL,
  `SesOdfLocation` varchar(5) NOT NULL,
  PRIMARY KEY (`SesTournament`,`SesOrder`,`SesType`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `SubClass`
--

DROP TABLE IF EXISTS `SubClass`;
CREATE TABLE `SubClass` (
  `ScId` varchar(2) NOT NULL,
  `ScTournament` int unsigned NOT NULL DEFAULT '0',
  `ScDescription` varchar(32) NOT NULL,
  `ScViewOrder` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ScId`,`ScTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TVContents`
--

DROP TABLE IF EXISTS `TVContents`;
CREATE TABLE `TVContents` (
  `TVCId` int NOT NULL,
  `TVCTournament` int NOT NULL,
  `TVCName` varchar(50) NOT NULL,
  `TVCContent` mediumblob NOT NULL,
  `TVCMimeType` varchar(50) NOT NULL,
  `TVCTime` tinyint NOT NULL,
  `TVCScroll` tinyint NOT NULL,
  `TVCTimestamp` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TVCId`,`TVCTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `TVContents`
--

LOCK TABLES `TVContents` WRITE;
/*!40000 ALTER TABLE `TVContents` DISABLE KEYS */;
INSERT INTO `TVContents` VALUES (1,-1,'Logo Ianseo',_binary '�PNG\r\n\Z\n\0\0\0\rIHDR\0\0�\0\0-\0\0\0��ю\0\0\0sBIT|d�\0\0\0	pHYs\0\0>\0\0>݆\�~\0\0\0tEXtSoftware\0www.inkscape.org�\�<\Z\0\0 \0IDATx�\�\�y�gc��\�\��\\3���%	\���J)iQJ���\�I�E���_%.k_e\�\�B%\�+���Q����3b\���\�\�>��\�\�}\�\����\��v;�\�\�}�\�e>\�\�}�k��,CDDDD�c(t\"\"\"\"\�.@��Q\0\�@��Q\0\�@��Q\0\�@��Q\0\�@��Q\0\�@����\0�P�\�f.\�\�Y�����>�X\��uf�.CD���t\�\"�%�	]F-fM\��2t\"eD�ܳ+O]Ge�gSW=;t�P\0���\�(��]F\r�\05PDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�ct��t\�4�.]D-��]���(˲\�5���H�\�,\�\�)\0����t�\�\0����t�����H\�(\0����t�����H\�(\0����tL�}\0�so�Ĭ\�[\�]Li\�P�\�*7�.CDDD�-\�6��5yi�\�W5\�\�Pt1\�\�e�H��S\�Y�hx\�\�u\�\�\�\�n�>�:	DD:lx/�\�\�U\�\"\�6�]�Hѩ3�\�\�\�,�\�v[\�\�\�e\�BPDDDB�D˅.���i�@@-\�@��Q\0\�@��Q\0\�@��Q\0\�@��)�\���>\�\"�����0�\�\�\�%������T\0\�\�]\�X\�u#\"Ү�k`x\�\�U\�\"�p_\�Ddp\�(8\�l\�ʗ���C�\�2>\�P�P\�2*z&t	���,]������H�@DDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:�\�60ѩ3&{\�\\K(wfSW�y\�\"D�}ѩ3\�v\r]GM\�ͦ�\�\�E���:cY����s���<��^�\�p	\��:	\�b@P��V\����,�\0�\nn�]D\r�k�.�z,\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"S\�(�\'��u\�=��`�\Z���\�]�H\�K�.��)�,\�B\� \"\"\"\"-\�#`��Q\0\�@��Q\0\�@��Q\0\�R�\0F\�\�\�0C3\�.&�K���l�LщwMa�y(t\�\�.Ϧ���\�U�):u\�7�υ��\�\�\�\�\�X\�\�e�%:\���\rB\�Q�\��\����\�):m�\����\�\�\�\�?\�\�\�F\�����j�%�hR\�\nd�M\Z\ZG\�C�^\�&B4>>&\�\��- \�6t��4\��ݕ�7D�.���6�]\�X�\�CDDD�\�\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�cʝ2{�9L�=�\�Z��B� l���h\�\�\�@vo\�\nZE�1>>i\�l\�DdpDY���ADD��\�\�X�	ٔ\�u\�bƪ�d�3\'t!E�\��0Q�Y\�:j1��\�ڌ\�e�EPDDD�c4PDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c�:\0FQ\�\ZDDDDƛ�\�:\��\�If\�\'>�E\�:�k��5��5�ŀ���iQ\�\�\�\�\�ɻ?�\�\�\�VV\�?\��\�R�d`\�y>\�\�\�\�\�4�i\�FDDD�\n\Z\0�\�ϝ4�\���E_V\�\�\� ;~x(=\";~�/c\��\�ת\rw{;p\�\�4�\�o�?	$X\0���\�#\�8�ރ߼fe?β#�d�\'\�-c\�\��v��M�%\�V\�]�<\���I\�0�\�׆���\��e�\�\�\�\�Cs�Ύ\�{\�F��u+>\n��z\�\���\���i�-t1\"\"\"R^\�\�Xw\�\�\�\�9o��?\�\�8،�}r%\�}\�i��D*\"\"\"��\Z\0\'\�\�\�2.j�\�\�\�\�\�=ߓe\�e \�q\�|\ZX3l5����&\�B#\"\"\"�i-\0F_�\�\�\�+\�M�9c\��E�\�9q��4\�~ƺŀC��\�+vǣG�\�\�\�$~(t1\"\"\"2�\�\�D{\�\�\�_\�dQ\�\�sN�\�\�d�\�\�~�6\��\�\�y\�\��i?\Z���\�6���m�\�#\�\�4\�\��\�g,ƺ�\��Н\���\'p��n\�\�ň��\��Mh��,\�Voa�C4!��6~\�j\�uK\��\�Y���\�X\�\�\�/��\0F\�\�6�\�\�\Z�c8\Zc\�;��P��+>�\�X�A\�bDDD\�%��\0F\�t+�\r�h��n\�2\�M�	L����\�W?\�\�:\�:c\�\���i�����\�\�#`���~��lF�\0\�^�\r���>�[�3\�\� ����\�\�e�\��\r�I\r\�<�\�}ƺ]\�$�\�p\"\"\"2�\� Y\��h\�{�3�9�5\�\0ƺ́\�i~k���o\�p\\�&\�\�},�\�X�0�!�$��\�_Q�\�1m\n\�h��X�ė7\�G_\�W�O��\r�_H�8m��N0\�ML\�:\n��&\�\�E�b�3���\"�\�|\�L�\�\�\�\\1\�M�\�3��\�\�pO�\�\�\�\�\�\�~>�\�l����u{\0\�\��\�m6p->\�]\�\�\�i�\�\�;���@X�\�m\�$�]\�\r\�6ß�\\�?�I���\��8�6�1\�k\�#���r-L���^<<�_�\�\�\�\'\�g?\�\�	\�9\�\�s4֭���M���=\�5\�uM��7\�4�\�	]D��uKk\�s��S>9�\�?\�\�\�l\�L~=�_ww\�7׿�#M\�?\�(c\�2�Z�\�͵�\�\�\�{�\�\�Z(@�\�\�\�\�ݼ~�9��#M\�\�سVO1\��hj0\Zb����l�}�up\���\08\n89M\�g\Zh�c\�\�X\�D�\�\�Y\��iR\�\�\�Xw~\�\�:�\0��z#�V62��\�\���5�6z�\��~��\r�\�\�K��+�\�\�$�\��\�\�\�\')m\n��\�\�u�ߍ\�\�z�\�k\�4����Z\���\�\��\�\�}�y\�?^\\�&\�\�U\�Z\r�\��g,3\�\�\�[�\�\�o<�^z���\�\\�\�\�g�Gjn�>\�H\�4����\�\�\�[\�L\����\�f\�M��\�\�k*\0�\�p5\����ς\�O\�\�\nx?\�>��D]w\\�\���.�W�\�\�w�\�7�\�\�}�\�&�S��\�$�6t1E\�\�_6\��ؘ�X�ض�\��b\��i?��V @��{�\�Pv)DK\�\�fFt~F�\�,\�녺\�)�kp6�]�\�ރM<�_\�\r\�\"Q\�\Z?Z��\Z�|xw�\���-�v\�.�N�xZ\�b�u+\0�\�ߨ\�\�#�~\�\�4�\�]\�X�u\�\0��\0�\\N?��\�i\�\n]\�h�u�\��N��_>�\�\�Gy/\�h��\� \�\�7E\�/��\�<�^\��\�g�\�\�\�\"�\�\�3�jjr:\�\�\�4�	�m\�ߡ)����}\n\�*M\�?\�P�`�2\�\�#���/\�*,\�v��o`>�vG|\�{K\�r͓�i��\�$�3p-/\�G���OnE��7�\�q��ʏ\�\n\�A @�\�OZ\����\�R���a\�\�\�a���\�Tu^�E|\�m\�|�d��\r~\�2֭�?\�n��M=l�&\�_+֣\0ֵ� xq\�\��\�3�U\��q\�\�4�?���\�7�\�Gm\\Π{�pD\�\�/���\�\�*�\�p\���ozZX�\0�bSO[xhҜ=\�_0�a\�y3�K��\�\�\�=\�h�6c\�)�\�IU=LM���\Z\�\�;�(\�A�ש6\�8�a�\�wU�E�?�\�\�Ԃ&c\�\Z��h�Q�\��.b.cݖ�\�9�>�q�\�׵ݱ�n�\�\�\�8upp�\�\�4\�A\�\08R����)O�<!\�\���\� c\�\�S��\�ܙ���\�m\�b���UU>�&\�\�5�\�׌uk�Sm\�뀷�\�oI��L>�&\�u6j�[���\"~�&񁡋0֭M��\�\�%\����_J��\�ߙ�\n\�o�\�\�EM;<M\�7\�p_�~a�\�?��꼅��EU\�K�|�B3\�L��+%�W\0\�/\���I|F�\�VÇ� g~��I�\�P�\�?��3b/\�.�آɹ�ƺw\'�ǽM;\rث\�\��J\�\�\�\��/\�\�\�\��i\�ߥ\��\��~\�LY_2ֽ���$�\��\�ƺ\�Tm\�X�*���\�_q�\�\�\�s�.����\�מ��\�\�\�ke��d�;�-�\�_\�v~e��Rg���\0\Z\�N��}\0?�&\�?�6��&���xYw\�O��g\�zY�]̱�}˸X�h�\�`ߚ��(�\�c�\�t#\�l=\�EǤI\\9��\�>=\�\r\�\�i?^Gc�\�ȳ��\�ў\�g\�muIbc\�ͨ�Ò5�s*\�\�\���=\�f\�T\��\�7\�\�X\���OQk\0\0߫�0	ep��n��\�\�}�����\�P�m=\��ƺ-k\�^\�x�B\�?�Q\���\�\��\"cݮT��\�-��|$d�i++��V\�o��8>�>0\��m�\�3��o^i\�X\�~\�\�5֝\�v\�Ҙ偳\�_Di�{˼����}�x�?B���?���\�6}�J#ƺo{\�R��u��\�4�o�ڐ /� \��\nM\�ޗ&\�m5�\�2�\�7ÿ\�\Zk\�\��55\�]�_�qA�ď\�Z\��I|��\����u\n~k�qu�}ǽ�\��^o0ֽ�\�W\�k{\�\��=[\�SF\�!c\�\�e7\�7\��ވ\��I�iƺ7W}�\0\�H�\�\�/\0ۥI��\Z\��X\�f`{z}=5�ߕ}+\�8c\�o�\�ڟ0�E\�Ϝ\�q\�>�\�(��뾑&\�\��U(\0�_:uh-\0\��Z�o(=�\�7\�\�|��r��\r�\�\�\�)�󫀍u�\��/c\�%M\�\�k,	c\�\�ƺ_\�\�\��ͬz��?��Bc\�όuK�\�\�\�w�\�\�@~\�\�.���\"&\0�\�=\�\�\�h�۠�\�{�m\�p=]\�J\0\�}\�F_R\�\�.��*:έ\�\�ϳ-�\�0߳�J��T�\�g\�X\�Bƺ�\�wso]\�\�`G\�\��]\����`{O\�7�~�4��uX\�^�V2~\�=�\�5�V\�m�\0N\�\�R_R\\�?7�gƺ-\�S���,O\�\�\�/\�\�\0�߹�\�%\�=)M\�\�\�*\�X�p3~\�\�\�u�[�\n�}�N0\�\�{�\�fۺj�I��+\�ޫ\�J\�\�f��|�\�\�\�W\�\rm@\�\�\�;\Z\�*\��/6V�T�c��;;0\�P�\�\�\�\�4/)?F\�Q\�Y#,e�\�1\�\��\�\�W,\�?�I���.�x\��\� Ư\�~��2\�\�S^\�B�EAK\�\�,��\�a\�\0N���5w�ʶLe=\��ޞ��\�\�Zw\�o�5Mw`�{#~nR�^\0�܅��-\�\n�!����\��\�SF\\\�\�8-\�b=x-c\�a�\�ز\�\�6�\�i\�`\�\�kX�9[��>���m\�\����n���A:\0\�(G�\�6�\�\�$~�j���_\�\�\�\��_5֭E\�\0\�$��X\�g��\�\�\�X�p~\�H?���Kh\�us�\�\��j�\�\�\���\�\�ͯM)�=Q\�J��K�\���#p~\��\�4��h�\�\�7�\0\�\�\�G��[�L(\�X7x#��\�\�\�߇\�6\�\�\�\0	7�\�0p5pU~\�\\b\�V\�\�Sn�_oo��W\�\�H�Z�\�\�\0����=�f��`\\P\r\�\�?ie&\�\�+ƺ\�M\n\�\��\�h,�bp�\"�.XOf\�\�/10�I�\���^���	\�>��=�6\\\�J=�������I|OC\�w\�\�\r�=wC�$M\�G\���\��%��\�\���\��h\�\Zc� _`\�������W\�q�W[\�$��\�\�x\�\�\�-�\�+�k=�m>ގ`!��˗q@\�w\"�\�\�\�\�\�󛆀\�\�\�\����Gsp\�>\�#l\0\�\�?�M�xz�:\Z�]_\\c�;�$p(͝\�\�K\0lb�ii��v�n͆\�=�\\�\�7\�~_\�\�7�u\�ORyS݌\0\�ߟ5\�\�H�\'\�ߌ��\�S\�$~�~N�d��\��i.o�>빫�\��\�:7M\�+�t�/��?\��\�m�\�予Q�Ε&\�\�ƺs\�{r;���&\�\�khM�C\�\�\�\��n\�@7��&V\'\r�\�i�^�������a\�\�$�\�X\����\�\�\�|/a��@?\���uL�\�W�T�/\�\�.��\���`\�V\�����fz�lž\�\�\�g�\�7WёҞ`\�l{-c]S�D\�rTW\�\�H�\�[\�4\�� }\�\�^M��<�=\�|4��yx�\�\�m*\0\��M���\�9���۶-\��W\0,�\�\�\�\�U\�\�\��,�4\��I|G�/N��Z�d\�^M\0\�*\\U=.\�op�&\�E�}���b<j\",ܖ&qo<͵Կ�O/�:7�\�~i_\�P\�}+M\�\�\�\ZhZ���\�J\�~F\�\�O��	\�\��C�	���\�S�5~R\�\�\r\�9?eB\�\�_�\�\�/iD\��v�*�y�K+�\�Z�\�%M��\r�9p\�$~\Z��Mۚ���9M\�hwP\\\�@�˔}2ֵw\�Rn��[�LP5\�}\Z�p\��\�)�*Գ�ߤI<\�}�\�л1~\�>�e\�M)\0gPl�PP\�x����y?~�\�g�\�H\�z��[ԕ\r�9Hl�\��d\�7v-\0nU\�ҏ��uS\�����E��i??֋\�-��3\���S\�\�)E�\r�EݓP\�}�\�jnsc\�m��i�\�\�hSJ�\�ܵ\0�\�\��\n��q;\�P\�J\�9������\�\�\rcݯ\�uN¿�\�\�\�_�\�w\�romNc\���\�\�DD:�3\�X�8\�75�+M\�Kv{\�\�v�M����4��\�G&���=\�\�.(:�D\�~d0\�\�`\�[\�׉ƺ;�[�ۀ\��=\�\�y�\�\�qp~U\��z&ߪGD��\�@�<�2#qeG�\�\0v+s\�<�	�+M\�ZGB\�$�\�X�>\�\�8�l\0,�^�[��sM\�ӯ�\�̗�\��\�<��\'\�?+nq�C�hEdA�\0\�)y_\��_��\�\�\��Vi�\�ً4��f�;8��\�~_\�;\�7�3�\��+\�_�渌s񓿗\�\�÷.�\�\�\�ş�z�wl\�[�uׅ.�O,�\0�\0\�L$���c\�k��K\�7\�t`\�4��\�\�XN\�/��\�ӭBj\�};q�xi\�i��=aO�d+\�\�G��uW�I�@\�\�z0�b;�H]\��\�\�?{<\�v^�Qm\�l`�\�ߋ\�NVl�\�&�K|��+\�%��\�12\�;!\�k�;\�X�jؒD$�.�2#��Æ�\�u�\�K\�5\�\�\�]\�\�rU\�����#�M\�$�\���\�R�!``��\��\�\r]����\�X�P\�\\�G$1\��^/I���\�/,M\�\�\�i~�j�{\r�\Z\�|�˓C\�1NM\�4\��iO\' \�3\�U&llS�/\�\�^}�\��U\�R\�*N,U�\�+\0v\�\��\�C1�-m�;�X\�\��\"\�g�\0\�nR(l\�\�^[�/�3\�$u\�\�M%\��]\�~7(�z=\�4��>B�g[w�.6\�-\Z�iVW`\�\�#�>~\�~���}�\��U\�Z\�\�\��򑆢P#��&\�]�ykefI\�\�\\i���i�\�芆�*��&\�*\�_դ\���\�H��u�\��S\0\�4��?\�Z!�YG�.BD��\08��Æ�n1\�%��E�{\�\\�{��&\�\n}nT\�\�\�H��I|$\�A�2�i�\�\��iFW`�33�\�$~��\�\�D�Q��~U\�\�:�	�U�ظ\�\�^�?\'\�$�X��е�s\'\�\�.��>֕\0X\�<\�!c]�-J��f\�4�\�p\�-qO��C�\n\�vi\��d�\�\�\�~\'p0\'l5\�֊��B!\"\�\�J\0|�\�}+xm�\0��\n\�Vf�[?�Y\�(qL\�[+x�m�\"\0��I<\'M\�/�ߧ�~��`;\�&�.BD\�\08�\"�\�#ԹBom�\r�\��*\�\�ۥ\�\�5�\'�&\�i\�\�g�{�4�,�?BND\��\�	�UF\0C�����\�\�\�ŀ�M\��dLi?�&\�\�\�#���#<=hQ\�\�\���zM]@K\�n[\�p��`[�\�6\�]\�\�*@>\�h\�Q\�����I�x�\�\�\�Mڷʯ\�):PWmk���&q�u\"҇:\0\�$~\�X\�o`���|E��GZ�½U}�\�=OQr\�b��\�o;5M\�L\"\0����\�7 _?J�F�q\�\Z�p�:�ؒ�z�\r��zt\"\0\�Q<\0,����ȭ3ֽ(�\�\�U\�n�_$�z�NɾD�K��{+\�9�\�X\�N,���<\�\�\�y�\�\��;[\�D�~�K\�הu�ey\0Eƍ.�;\�[FQd\�m�\�z\0\�\�\�V\�\�R��u�\0_/xۑ\�c)-M\�d\�c �0֭l�_�K7\�]�y\�\"\�g�\0�Z\�X7!�O4��K�?W��\�ה��\���OSlT\�~\�%�\�\�\�\�1\�}�\�-\�\0O\����z=�\0	�KpZ�{�\�O�M��2���n���93\�\�J\�mX\�z��D�\���\�\�4��S�/�.J��a`�|t�}\rt\"\0.�&\�\�\�+2\�ue�?Q\�\0��\���*#�xK��{\�Ⱥ\�\�[c�ue����S\�\�zu;\�Q�tY�\�\�\�TY\�V\���\�\��4�gRb\�\n������Ҫ�T�Y�[��nG\�l��\�\n?�5\��\�l��/j\�H)M-\�\�\�ESG\�n��\�P\�2�2o\�\�Re��\���R��\��\�oK\�\�\n�c\�\\\�*\��\\�\��ƺOgR���\0h�[8�`��I|q�{D$�&\�2?\�XWd\�\�\�\�\�\�w\�x\��\�\�\�eֵ\0���\���8M\�\�T\�\�4c]�_ ƺe�u\'�R�\��@�\�=ϥ4\�-�L*\�\�\��\�E�5�5\�h�\�\��(u;\0\0`IDAT�ԑ\�\�^\�`\�\��\�@�[\�l�ݾg�{7\�O�\�\�e\�t-\0^�\�ɾ\�\����h{���K�u�\�&Wlc\�n�?�ݩ\�B\�\�X7�\�\�\�\�?l�&q�=E�\�\�\r�;\�(\�\���k-\�Oƺ�4܏���\08�\�X\�\�\�\�[ƺM�shf\�m\�\�g]ZL�ĳ�u���\n����\�Z\r�\�R��\�d�|@k��\npZ�\�ý\�l�[\��$P\�7\�m���\Z�Ƈ�\r�=\�&\�?\�&\"/�DC\�vd\���E�<Eeq\�<c\�5�w���\\�&�i\"\0,\\c�K\�;=<\�P?�\�!G\0�\�F\�\�.{c�`\�\�m��c�4�\�4\�\��O�\�\�p2p��\�r�>�w\0w�I|?��\��\�VĿC�0�)͍\�9*i�{5p)\����&q\�T�\�2\�-|h\�ۃ�igƺ\��#\���i~�\�Xwpy�ķ�\�o\�4\0�g�����u�\0g�I|}���\�X�;Ql\��2\0�9~rk\�-\rv0\��&\�\�xݡyuY�y�N3\�=�\�:�\�3�0֝�&\�\�y�C�\�\�`\�K�c���i�\�߭�>�\�1\�M\�?\���n�]�6p1\���\��`�{\0��w�\���I�͎��?\�\�\��\��\�8\�Xw7p>~A\�_\�$���~ke�[��d3\�\��Z\�^�Wi\�6֝	X\�\�\�\�8\�\� i_j��\nxG\�\"\�Vy~`IS�[�ug\�\�<�ߧk}�\�\�%\��+����,@>\'xI�\�\�`Y���\�\�׹8�;�⛽\�e���O\��<l���-�ğ\�>\�zf\�?+0�\\�\��1\�\����.W>;\��u3��n\�\�<�_��I\\e׍u�}\��\���\�\�o ܚ�2\�\���S(\0\�\�g\��1^w\�\�\��E�}򫪫\�>tڇ̗�\�<�{�9(%�\�\�aƘl�&\�ƺ�inJC���\��^n0ֽ�\�C\�\�\'-\��s?F#.F�\�\"�\�io\\\���\r\�N\�\���\��\�_�u\�\�\�v�\�g�K��\������&\�Mƺ+�w���pc\�Yi\�7J�\�\�\��bm�\�vֻn\�b4?�F\�ve��\�O�?`\�$�\n\�\��\�\�O\�W\r\\\��,D�Ӡ�\�1Un\�4ۦ\�J޷pt�\���d\�ݏ�\��D\�Y=�\�\\�\�@Xȧ;\�\�_\�qpA�:\0\�$��r\'�\0|,\�\�q�\�g\�W�\�%\�%d�!iPdk	j=��I��Y`\�fˑ��\�\�\�?�}ǎ���\r��\�+\�{��n�\�^�&\���@�]�r͝\�9��]\�$ֻI��\�\"{��I|pR�\�H�\�\'5�<\��\�<C\r\�c��i_K�\�;^\\�o�0Z�슟D\�ai\�G�\�\�\�b:�u�\�g�ܯ�T\��J\�s~\�R�.	]��h�4�K��;R�`\�@ʯ$z\�|y��I|~%Sȹ1�O����]K}\��\r��&\�\�[\�SD\�sr�ė�)ߢc\'\��)}&Ը=\�MI}��&\�u4\��\0�&\�4�*���N1֍�Yf�Ŀ6\0�T��2f\�\\�\�h�\���\r\��[\�\riYۼ��{�ϔ�9M\�\�C\�\�\�*�`\�$~?�\�еt\�	i��\�:\0s\�\�o8Y֮��c�(M\�{\�F��v	�	�.M\�s\�c�BC}>�?\�w�<\\�\�`�\�$~�Ji��?gI\\�\�\���\r��]�\0�\�٠ /o\�N�\��\Z\�衯\�i\�\�]��\�غ\�l�&\�\����&\�O�o\�ￕt\�`\�4�Ϯ�]iߡe�\�O�\�\�]hG�q!S�5�\�6i\�U����\�F\0si\�H\�@t��\�\�=\�w[�\�;\���9�\�{�#�F�Ŀ\�\�\��?�\�߃�	\��I�V�\�\�L��\�\n\�HX/\0��I��:M��\��\r�����|`aS��?�.g<{?\rc\�&�Ru\�$�Q|\��@�6�u��\��\�]�ķ\�\�\\\��\�V�\Z=\�\�8�Q\�	i\�Z�\�4�3c\�\'�\�\�w�1ny�4\�~\�\�;c\�R�rEj\�I���\��HyOۥI|E��I|��n#\�[�nh\0b��I�\�Xwp$0�����\�~\�&\�\�Mu�\08B�v�\�\�ah4��\Z\�vN���\r4\�%\�\�ƺ��U�W\�\�\�+��\�nw\�\�\�;\�p�����\\c\��[\�\�ۼ��|/��܏3\�\�\�\�X\�:\�r\�\�K�ph�{D��[\���\n��,*�y���\��\�\�9\�/M\�G\�ӓ�\�\�O���ǁ�\�$>�\�\0\�&\�Sƺ\�W\�V9�\�]��ƺ]Jn�p/ͯ\�\�_\n\\�_�\�\�\��\nX�\�7Pme��\�f:�\r\���y:O�\�7[\�\�|X����i_k�{��\��z�K\Z4���O���6:\��|�I�`�/\�x%\�kc\�w�u�	\�ƺ\�X�\�{���\�\�\'�h�0�\�<|\nX+M\�3B͘&\�/\�#F��\�\�<ɀJ�xN�\�ǧI�z`�1r��\�\���ߏx\�4�\�n+��F\0(M\�K�u{\'Rm^C�\�r\�ƺ��I|O-\�)c\�\����7/\�ė�~%\�Q\�\�c\\��o\�\\�\�\�t\�tc\�\���\�\�Y%dmR^�\�\�\�\�>�\�rG|(\�\���x����I��PE(\0�\"Mb�\�\�\�\Z�{+\�wcݾ�\�E\�\�\�;c\�\n�=�\0-\�\�\�\�G�H=��x1pE��o\�\�ϝ�U~\�g�[�\�V@\rJ�m\�	�	ƺE��[\�׆��\'i��\�h_�2\����t)Cpi��\�-s漖�/\��\�\�K]\�9\�	�X��\�]k��88�b\"�\�1���\�\��x\� �\�̟܊_eJ \�\0\�̯�^�n����oe\�\��\�X�~Tp-^�<�	�J�\�\'�<\�	�s\�u+p}?Nk\n\0O��f�ӿkn\�e\�$�A�;\�{\�z\�,\�kƺo?\�7�ƺ!��@\�\�\��\�b`���*����s!>\�tU���\��H¼�\'M⇃Uآ<@ܒ_/�?�YX�<�k���~���\�W�\�\�e�\�\"c��?�_X�Ti\�5y\�\�63\��\�ܳ�Ǒ\�\��U\�\��(\��\�\��u{?��/���#�\�\�$\�\�s3�u�\�OL9\0X��f	|����EDD\����u[\�p��Ê܋�+q~�@\�ON�ryC\����)\�\�\�\�\�\�\�\�yI\"\"\"\�`	ƺ\�\�[4�B\��<�ˁߤI\��J�co3�d\�\�\Z\�\�9\��4�Oj�m�`Iƺ\�\�\�)l\�R��\�\�\�\�\�s\rf��7��\�-�ߟ\��\�u\�\�m\�;\�7?wM��o\r\�#\"\"\"�\0XA>\������\�T;\�3x8�8x䖙\�5�p\�(��\�\��@����\n��0\�m��_�$�\��\�,���\�(\"\"\"\n��1\�M�v]KƟ�\�4�]����x\n�53\�m�6t-�]\�&\�_B\"\"\"\"/b\�ڸ�&\�\��z��\�	\\N{oQ�\�O\ZlP~\�\��O.�iw\�OJ9-?\�SDDD��`�u�\�,�\\\�r\�v#��s�\�pk�`�\�mcv���a\�r��\n8\"M\�_�.DDDD�Q\0\�X�	\�	\�C���\�\�\�4\�<\��4�o\n]������\0��.6\��k��\�܎}\�I|s\�bDDD�:�>c�[x+\�\��z0�\�\0n\0�\\�&\�-\�-\"\"\"-P\0\�sƺ��\r\�s\�\0VͯU\�Ǻ�5܉{/^i?\\�M\0\n�\�X�/�\���Ȉ?\�	�y��\�\�\�t,���H7)\0����t�N\�@��Q\0\�@����\0���Sg�|0t�H�W\�\�\�\n]�t�����hd����(t	\�=z,\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1�(RAt\�\�\�X\�\�KC\�Q��?g��\��\�e��H\�\0E��<kY�Y\�2j�Eυ.ADDڡG�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1Z\"\"\"�\'\�\�#cZ\�2j1{�9�K�\�Q\0���\�\���kdz,\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"eY���A\�iӧ���f��]���i�����H\�\�����H\�(\0����t�����H\�(\0����t�����H\�\�,`�\n��:�I˼?t��\�v_叡\��\�)\0�T1i�EɢC�Q�(�xO\�2DD�yz,\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"�\0(\"\"\"\�1\n�\"\"\"\"��\�D�y�#CQ�lZ\�\nDD�Q�e�k�\�����H\�(\0����t�����H\�(\0����t�����H\�(\0����t�\�� :e\�\�D_���\\�M]e\�\�E��H\�\0E�ZȐ�V\�2\�M]����C��EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c�,\�B\� \"\"\"\"-\�����H\�(\0����t�����H\�(\0����t�����H\�(\0����t̄\���\�\'-ʜ\�\�]G=��dSW94t\"\"\�<@�*\��L�h\�\�e\�#\n]����D��EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c\0EDDD:FPDDD�c�,\�B\� 2�\"�8m�r�\�Ť�糝V~<t\"\"\�<@��\�#`��Q\0\�@��Q\0\�@��Q\0\�@��Q\0\�@��Q\0\�@��Q\0\��\�\�U �\��e\0\0\0\0IEND�B`�','image/png',5,50,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `TVContents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVOut`
--

DROP TABLE IF EXISTS `TVOut`;
CREATE TABLE `TVOut` (
  `TVOId` tinyint unsigned NOT NULL,
  `TVOName` varchar(50) NOT NULL,
  `TVOUrl` text NOT NULL,
  `TVOMessage` text NOT NULL,
  `TVORuleId` int NOT NULL,
  `TVOTourCode` varchar(8) NOT NULL,
  `TVORuleType` tinyint NOT NULL,
  `TVOLastUpdate` datetime NOT NULL,
  `TVOSide` tinyint unsigned NOT NULL,
  `TVOHeight` varchar(15) NOT NULL,
  `TVOFile` varchar(255) NOT NULL,
  PRIMARY KEY (`TVOId`,`TVOSide`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TVParams`
--

DROP TABLE IF EXISTS `TVParams`;
CREATE TABLE `TVParams` (
  `TVPId` int NOT NULL,
  `TVPTournament` int NOT NULL,
  `TVPTimeStop` smallint unsigned NOT NULL,
  `TVPTimeScroll` smallint unsigned NOT NULL,
  `TVPNumRows` smallint unsigned NOT NULL,
  `TVMaxPage` tinyint unsigned NOT NULL,
  `TVPSession` tinyint unsigned NOT NULL,
  `TVPViewNationName` tinyint unsigned NOT NULL,
  `TVPNameComplete` tinyint unsigned NOT NULL,
  `TVPViewTeamComponents` tinyint unsigned NOT NULL,
  `TVPEventInd` varchar(255) NOT NULL,
  `TVPEventTeam` varchar(255) NOT NULL,
  `TVPPhasesInd` varchar(255) NOT NULL,
  `TVPPhasesTeam` varchar(255) NOT NULL,
  `TVPColumns` varchar(255) NOT NULL,
  `TVPPage` varchar(10) NOT NULL,
  `TVPDefault` varchar(1) NOT NULL,
  `TVP_TR_BGColor` varchar(7) NOT NULL,
  `TVP_TRNext_BGColor` varchar(7) NOT NULL,
  `TVP_TR_Color` varchar(7) NOT NULL,
  `TVP_TRNext_Color` varchar(7) NOT NULL,
  `TVP_Content_BGColor` varchar(7) NOT NULL,
  `TVP_Page_BGColor` varchar(7) NOT NULL,
  `TVP_TH_BGColor` varchar(7) NOT NULL,
  `TVP_TH_Color` varchar(7) NOT NULL,
  `TVP_THTitle_BGColor` varchar(7) NOT NULL,
  `TVP_THTitle_Color` varchar(7) NOT NULL,
  `TVP_Carattere` smallint unsigned NOT NULL,
  `TVPViewPartials` varchar(1) NOT NULL DEFAULT '1',
  `TVPViewDetails` varchar(1) NOT NULL DEFAULT '1',
  `TVPViewIdCard` varchar(1) NOT NULL DEFAULT '',
  `TVPSettings` text NOT NULL,
  PRIMARY KEY (`TVPId`,`TVPTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TVRules`
--

DROP TABLE IF EXISTS `TVRules`;
CREATE TABLE `TVRules` (
  `TVRId` int NOT NULL,
  `TVRTournament` int NOT NULL,
  `TVRName` varchar(50) NOT NULL,
  `TV_TR_BGColor` varchar(7) NOT NULL,
  `TV_TRNext_BGColor` varchar(7) NOT NULL,
  `TV_TR_Color` varchar(7) NOT NULL,
  `TV_TRNext_Color` varchar(7) NOT NULL,
  `TV_Content_BGColor` varchar(7) NOT NULL,
  `TV_Page_BGColor` varchar(7) NOT NULL,
  `TV_TH_BGColor` varchar(7) NOT NULL,
  `TV_TH_Color` varchar(7) NOT NULL,
  `TV_THTitle_BGColor` varchar(7) NOT NULL,
  `TV_THTitle_Color` varchar(7) NOT NULL,
  `TV_Carattere` int NOT NULL,
  `TVRSettings` text NOT NULL,
  PRIMARY KEY (`TVRId`,`TVRTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TVSequence`
--

DROP TABLE IF EXISTS `TVSequence`;
CREATE TABLE `TVSequence` (
  `TVSId` int NOT NULL,
  `TVSTournament` int NOT NULL,
  `TVSRule` int NOT NULL,
  `TVSContent` int NOT NULL,
  `TVSCntSameTour` tinyint NOT NULL,
  `TVSTime` tinyint NOT NULL,
  `TVSScroll` tinyint NOT NULL,
  `TVSTable` varchar(5) NOT NULL,
  `TVSOrder` tinyint NOT NULL,
  `TVSFullScreen` varchar(1) NOT NULL,
  PRIMARY KEY (`TVSId`,`TVSTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TargetFaces`
--

DROP TABLE IF EXISTS `TargetFaces`;
CREATE TABLE `TargetFaces` (
  `TfId` int NOT NULL,
  `TfName` varchar(50) NOT NULL,
  `TfTournament` int NOT NULL,
  `TfClasses` varchar(10) NOT NULL,
  `TfRegExp` varchar(255) NOT NULL,
  `TfGolds` varchar(5) NOT NULL,
  `TfXNine` varchar(5) NOT NULL,
  `TfGoldsChars` varchar(16) NOT NULL,
  `TfXNineChars` varchar(16) NOT NULL,
  `TfT1` int NOT NULL,
  `TfW1` int NOT NULL,
  `TfT2` int NOT NULL,
  `TfW2` int NOT NULL,
  `TfT3` int NOT NULL,
  `TfW3` int NOT NULL,
  `TfT4` int NOT NULL,
  `TfW4` int NOT NULL,
  `TfT5` int NOT NULL,
  `TfW5` int NOT NULL,
  `TfT6` int NOT NULL,
  `TfW6` int NOT NULL,
  `TfT7` int NOT NULL,
  `TfW7` int NOT NULL,
  `TfT8` int NOT NULL,
  `TfW8` int NOT NULL,
  `TfDefault` varchar(1) NOT NULL DEFAULT '',
  `TfTourRules` varchar(75) NOT NULL,
  `TfWaTarget` varchar(25) NOT NULL,
  PRIMARY KEY (`TfTournament`,`TfId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci COMMENT='Defines the faces to shoot on';

--
-- Table structure for table `TargetGroups`
--

DROP TABLE IF EXISTS `TargetGroups`;
CREATE TABLE `TargetGroups` (
  `TgTournament` int NOT NULL,
  `TgSession` varchar(1) NOT NULL,
  `TgTargetNo` varchar(4) NOT NULL,
  `TgGroup` varchar(25) NOT NULL,
  `TgSesType` varchar(2) NOT NULL DEFAULT 'Q',
  PRIMARY KEY (`TgTournament`,`TgSession`,`TgTargetNo`,`TgSesType`),
  KEY `TgTournament` (`TgTournament`,`TgGroup`,`TgTargetNo`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Targets`
--

DROP TABLE IF EXISTS `Targets`;
CREATE TABLE `Targets` (
  `TarId` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `TarDescr` varchar(24) NOT NULL,
  `TarArray` varchar(24) NOT NULL DEFAULT '0',
  `TarStars` varchar(10) NOT NULL,
  `TarOrder` int NOT NULL,
  `TarFullSize` int NOT NULL,
  `A_size` int NOT NULL,
  `A_color` varchar(6) NOT NULL,
  `B_size` int NOT NULL,
  `B_color` varchar(6) NOT NULL,
  `C_size` int NOT NULL,
  `C_color` varchar(6) NOT NULL,
  `D_size` int NOT NULL,
  `D_color` varchar(6) NOT NULL,
  `E_size` int NOT NULL,
  `E_color` varchar(6) NOT NULL,
  `F_size` int NOT NULL,
  `F_color` varchar(6) NOT NULL,
  `G_size` int NOT NULL,
  `G_color` varchar(6) NOT NULL,
  `H_size` int NOT NULL,
  `H_color` varchar(6) NOT NULL,
  `I_size` int NOT NULL,
  `I_color` varchar(6) NOT NULL,
  `J_size` int NOT NULL,
  `J_color` varchar(6) NOT NULL,
  `K_size` int NOT NULL,
  `K_color` varchar(6) NOT NULL,
  `L_size` int NOT NULL,
  `L_color` varchar(6) NOT NULL,
  `M_size` int NOT NULL,
  `M_color` varchar(6) NOT NULL,
  `N_size` int NOT NULL,
  `N_color` varchar(6) NOT NULL,
  `O_size` int NOT NULL,
  `O_color` varchar(6) NOT NULL,
  `P_size` int NOT NULL,
  `P_color` varchar(6) NOT NULL,
  `Q_size` int NOT NULL,
  `Q_color` varchar(6) NOT NULL,
  `R_size` int NOT NULL,
  `R_color` varchar(6) NOT NULL,
  `S_size` int NOT NULL,
  `S_color` varchar(6) NOT NULL,
  `T_size` int NOT NULL,
  `T_color` varchar(6) NOT NULL,
  `U_size` int NOT NULL,
  `U_color` varchar(6) NOT NULL,
  `V_size` int NOT NULL,
  `V_color` varchar(6) NOT NULL,
  `W_size` int NOT NULL,
  `W_color` varchar(6) NOT NULL,
  `X_size` int NOT NULL,
  `X_color` varchar(6) NOT NULL,
  `Y_size` int NOT NULL,
  `Y_color` varchar(6) NOT NULL,
  `Z_size` int NOT NULL,
  `Z_color` varchar(6) NOT NULL,
  `TarDummyLine` int NOT NULL,
  `1_size` int NOT NULL DEFAULT '0',
  `1_color` varchar(6) NOT NULL DEFAULT '',
  `2_size` int NOT NULL DEFAULT '0',
  `2_color` varchar(6) NOT NULL DEFAULT '',
  `3_size` int NOT NULL DEFAULT '0',
  `3_color` varchar(6) NOT NULL DEFAULT '',
  `4_size` int NOT NULL DEFAULT '0',
  `4_color` varchar(6) NOT NULL DEFAULT '',
  `5_size` int NOT NULL DEFAULT '0',
  `5_color` varchar(6) NOT NULL DEFAULT '',
  `6_size` int NOT NULL DEFAULT '0',
  `6_color` varchar(6) NOT NULL DEFAULT '',
  `7_size` int NOT NULL DEFAULT '0',
  `7_color` varchar(6) NOT NULL DEFAULT '',
  `8_size` int NOT NULL DEFAULT '0',
  `8_color` varchar(6) NOT NULL DEFAULT '',
  `9_size` int NOT NULL DEFAULT '0',
  `9_color` varchar(6) NOT NULL DEFAULT '',
  PRIMARY KEY (`TarId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `Targets`
--

LOCK TABLES `Targets` WRITE;
/*!40000 ALTER TABLE `Targets` DISABLE KEYS */;
INSERT INTO `Targets` VALUES (1,'TrgIndComplete','TrgIndComplete','a-j',4,100,0,'',100,'FFFFFF',90,'FFFFFF',80,'000000',70,'000000',60,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'F9E11E',10,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(2,'TrgIndSmall','TrgIndSmall','ag-j',5,100,0,'',0,'FFFFFF',0,'FFFFFF',0,'000000',0,'000000',0,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'F9E11E',10,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(4,'TrgCOIndSmall','TrgCOIndSmall','ag-j',7,100,0,'',0,'FFFFFF',0,'FFFFFF',0,'000000',0,'000000',0,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'F9E11E',5,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',10,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(3,'TrgCOIndComplete','TrgCOIndComplete','a-j',6,100,0,'',100,'FFFFFF',90,'FFFFFF',80,'000000',70,'000000',60,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'F9E11E',5,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',10,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(5,'TrgOutdoor','TrgOutdoor','a-j',1,100,0,'',100,'FFFFFF',90,'FFFFFF',80,'000000',70,'000000',60,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',5,'F9E11E',10,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(6,'TrgField','TrgField','a-f',8,50,0,'',50,'000000',40,'000000',30,'000000',20,'000000',10,'F9E11E',5,'F9E11E',0,'ED2939',0,'ED2939',0,'F9E11E',0,'F9E11E',0,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(7,'TrgHMOutComplete','TrgHMOutComplete','a',10,16,16,'ED2939',4,'F9E11E',0,'FFFFFF',0,'000000',0,'000000',0,'00A3D1',0,'00A3D1',0,'ED2939',0,'ED2939',0,'F9E11E',0,'F9E11E',0,'F9E11E',0,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(8,'Trg3DComplete','Trg3DComplete','afil',9,0,0,'',0,'FFFFFF',0,'FFFFFF',0,'000000',0,'000000',60,'00A3D1',0,'00A3D1',0,'ED2939',30,'00A3D1',0,'F9E11E',0,'F9E11E',15,'ED2939',5,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(9,'TrgCOOutdoor','TrgCOOutdoor','af-j',2,100,0,'',0,'',0,'',0,'',0,'',60,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',5,'F9E11E',10,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(10,'TrgCOOutdoorSmall','TrgCOOutdoorSmall','ag-j',3,100,0,'',0,'',0,'',0,'',0,'',0,'',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',5,'F9E11E',10,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(11,'TrgHunterNor','TrgHunterNor','acfhln',11,0,0,'',0,'',30,'00A3D1',0,'',0,'',25,'00A3D1',0,'',20,'ED2939',0,'',0,'',0,'',15,'ED2939',0,'',10,'F9E11E',0,'',0,'',5,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(12,'TrgForestSwe','TrgForestSwe','aflq',12,0,0,'',0,'',0,'',0,'',0,'',30,'00A3D1',0,'',0,'',0,'',0,'',0,'',25,'F9E11E',0,'',0,'',0,'',0,'',20,'ED2939',0,'',0,'',0,'',0,'',15,'f9e11e',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(13,'TrgNfaaInd','TrgNfaaInd','',13,40,0,'',40,'000080',32,'000080',24,'000080',16,'000080',8,'f4f4f4',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',4,'f4f4f4',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(14,'TrgProAMIndNfaa','TrgProAMIndNfaa','a-f',14,40,0,'',40,'000080',32,'000080',24,'000080',16,'000080',8,'f4f4f4',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',4,'f4f4f4',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(15,'TrgProAMIndVegas','TrgProAMIndVegas','a-l',15,100,0,'',100,'FFFFFF',90,'FFFFFF',80,'000000',70,'000000',60,'00A3D1',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'',10,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',5,'F9E11E',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(16,'TrgProAMIndVegasSmall','TrgProAMIndVegasSmall','ag-l',16,100,0,'',0,'FFFFFF',0,'FFFFFF',0,'FFFFFF',0,'FFFFFF',0,'FFFFFF',50,'00A3D1',40,'ED2939',30,'ED2939',20,'F9E11E',0,'',10,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',5,'F9E11E',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(17,'TrgImperial','TrgImperial','abdfh',17,100,0,'',100,'FFFFFF',0,'',80,'000000',0,'',60,'00A3D1',0,'',40,'ED2939',0,'',20,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(18,'TrgNfaa3D','TrgNfaa3D','afil',9,0,0,'',0,'FFFFFF',0,'FFFFFF',0,'000000',0,'000000',60,'00A3D1',0,'00A3D1',0,'ED2939',30,'00A3D1',0,'F9E11E',0,'F9E11E',15,'ED2939',0,'',5,'F9E11E',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(19,'TrgKyudo','TrgKyudo','a',18,36,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',9,'FFFFFF',18,'000000',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(20,'TrgNfaaHunt5','TrgNfaaHunt5','adef',20,50,0,'',0,'',0,'',50,'000000',30,'000000',10,'FFFFFF',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',5,'FFFFFF',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(21,'TrgNfaaHunt6','TrgNfaaHunt6','adef',21,50,0,'',0,'',0,'',50,'000000',30,'000000',10,'FFFFFF',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',5,'FFFFFF',0,'',0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,''),(22,'TrgNfaaAnimal','TrgNfaaAnimal','alnstvw',22,0,0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',0,'',50,'888888',0,'',30,'888888',0,'',0,'',0,'',0,'',0,'',0,'',0,'',30,'888888',0,'',0,'',0,'',0,'',0,10,'888888',50,'888888',30,'888888',10,'888888',50,'888888',10,'888888',0,'',0,'',0,'');
/*!40000 ALTER TABLE `Targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TeamComponent`
--

DROP TABLE IF EXISTS `TeamComponent`;
CREATE TABLE `TeamComponent` (
  `TcCoId` int NOT NULL,
  `TcSubTeam` tinyint NOT NULL,
  `TcTournament` int NOT NULL,
  `TcEvent` varchar(10) NOT NULL,
  `TcId` int unsigned NOT NULL,
  `TcFinEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `TcOrder` tinyint NOT NULL,
  `TcIrmType` tinyint NOT NULL,
  PRIMARY KEY (`TcCoId`,`TcSubTeam`,`TcTournament`,`TcEvent`,`TcId`,`TcFinEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TeamDavis`
--

DROP TABLE IF EXISTS `TeamDavis`;
CREATE TABLE `TeamDavis` (
  `TeDaTournament` int NOT NULL,
  `TeDaEvent` varchar(10) NOT NULL,
  `TeDaTeam` varchar(10) NOT NULL,
  `TeDaSubTeam` int NOT NULL,
  `TeDaBonusPoints` int NOT NULL,
  `TeDaMainPoints` int NOT NULL,
  `TeDaWinPoints` int NOT NULL,
  `TeDaLoosePoints` int NOT NULL,
  `TeDaDateTime` datetime NOT NULL,
  PRIMARY KEY (`TeDaTournament`,`TeDaEvent`,`TeDaTeam`,`TeDaSubTeam`),
  KEY `TeDaTournament` (`TeDaTournament`,`TeDaEvent`,`TeDaMainPoints`,`TeDaWinPoints`,`TeDaLoosePoints`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TeamFinComponent`
--

DROP TABLE IF EXISTS `TeamFinComponent`;
CREATE TABLE `TeamFinComponent` (
  `TfcCoId` int NOT NULL,
  `TfcSubTeam` tinyint NOT NULL,
  `TfcTournament` int NOT NULL,
  `TfcEvent` varchar(10) NOT NULL,
  `TfcId` int unsigned NOT NULL,
  `TfcOrder` tinyint NOT NULL,
  `TfcIrmType` tinyint NOT NULL,
  `TfcTimeStamp` datetime NOT NULL,
  PRIMARY KEY (`TfcCoId`,`TfcSubTeam`,`TfcTournament`,`TfcEvent`,`TfcId`),
  KEY `TfcTournament` (`TfcTournament`,`TfcEvent`,`TfcCoId`,`TfcSubTeam`,`TfcOrder`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TeamFinals`
--

DROP TABLE IF EXISTS `TeamFinals`;
CREATE TABLE `TeamFinals` (
  `TfEvent` varchar(10) NOT NULL,
  `TfMatchNo` tinyint unsigned NOT NULL DEFAULT '0',
  `TfTournament` int unsigned NOT NULL DEFAULT '0',
  `TfSession` tinyint unsigned NOT NULL DEFAULT '0',
  `TfTarget` varchar(6) NOT NULL,
  `TfScheduledtime` datetime NOT NULL,
  `TfRank` tinyint unsigned NOT NULL DEFAULT '0',
  `TfTeam` int unsigned NOT NULL DEFAULT '0',
  `TfSubTeam` tinyint NOT NULL,
  `TfScore` smallint NOT NULL DEFAULT '0',
  `TfSetScore` tinyint NOT NULL DEFAULT '0',
  `TfSetPoints` varchar(36) NOT NULL,
  `TfSetPointsByEnd` varchar(36) NOT NULL,
  `TfWinnerSet` tinyint NOT NULL DEFAULT '0',
  `TfTie` tinyint(1) NOT NULL DEFAULT '0',
  `TfArrowstring` varchar(90) NOT NULL,
  `TfTiebreak` varchar(30) NOT NULL,
  `TfTbClosest` tinyint NOT NULL,
  `TfTbDecoded` varchar(15) NOT NULL,
  `TfArrowPosition` text NOT NULL,
  `TfTiePosition` text NOT NULL,
  `TfWinLose` tinyint unsigned NOT NULL DEFAULT '0',
  `TfFinalRank` tinyint unsigned NOT NULL DEFAULT '0',
  `TfDateTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `TfSyncro` datetime NOT NULL,
  `TfLive` tinyint NOT NULL DEFAULT '0',
  `TfStatus` tinyint NOT NULL DEFAULT '0',
  `TfShootFirst` tinyint NOT NULL,
  `TfShootingArchers` text NOT NULL,
  `TfVxF` tinyint NOT NULL DEFAULT '0',
  `TfConfirmed` int NOT NULL,
  `TfNotes` varchar(30) NOT NULL,
  `TfRecordBitmap` tinyint unsigned NOT NULL,
  `TfIrmType` tinyint NOT NULL,
  `TfCoach` int unsigned NOT NULL,
  PRIMARY KEY (`TfEvent`,`TfMatchNo`,`TfTournament`),
  KEY `TfLive` (`TfLive`,`TfTournament`),
  KEY `TfDateTime` (`TfTournament`,`TfDateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Teams`
--

DROP TABLE IF EXISTS `Teams`;
CREATE TABLE `Teams` (
  `TeCoId` int NOT NULL,
  `TeSubTeam` tinyint NOT NULL,
  `TeEvent` varchar(10) NOT NULL,
  `TeTournament` int NOT NULL,
  `TeFinEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `TeScore` smallint NOT NULL,
  `TeHits` smallint NOT NULL,
  `TeGold` smallint NOT NULL,
  `TeXnine` smallint NOT NULL,
  `TeTie` tinyint(1) NOT NULL,
  `TeTieBreak` varchar(15) NOT NULL,
  `TeTbClosest` tinyint NOT NULL,
  `TeTbDecoded` varchar(15) NOT NULL,
  `TeRank` smallint NOT NULL,
  `TeHitsCalcOld` smallint NOT NULL,
  `TeRankFinal` smallint NOT NULL,
  `TeSO` smallint NOT NULL DEFAULT '0',
  `TeTimeStamp` timestamp NULL DEFAULT NULL,
  `TeTimeStampFinal` datetime DEFAULT NULL,
  `TeFinal` tinyint unsigned NOT NULL DEFAULT '0',
  `TeBacknoPrinted` datetime NOT NULL,
  `TeNotes` varchar(30) NOT NULL,
  `TeRecordBitmap` tinyint unsigned NOT NULL,
  `TeIrmType` tinyint NOT NULL,
  `TeIrmTypeFinal` tinyint NOT NULL,
  PRIMARY KEY (`TeCoId`,`TeSubTeam`,`TeEvent`,`TeTournament`,`TeFinEvent`),
  KEY `TeTournament` (`TeTournament`,`TeFinEvent`,`TeEvent`,`TeScore`),
  KEY `TeTournament_2` (`TeTournament`,`TeSO`,`TeEvent`),
  KEY `TeTournament_3` (`TeTournament`,`TeEvent`,`TeFinEvent`,`TeScore`),
  KEY `TeTournament_4` (`TeTournament`,`TeFinEvent`,`TeEvent`,`TeSO`),
  KEY `TeEvent` (`TeEvent`,`TeTournament`,`TeRankFinal`,`TeIrmTypeFinal`,`TeIrmType`,`TeRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TourRecords`
--

DROP TABLE IF EXISTS `TourRecords`;
CREATE TABLE `TourRecords` (
  `TrTournament` int NOT NULL,
  `TrRecCode` varchar(25) NOT NULL,
  `TrRecTeam` smallint NOT NULL,
  `TrRecPara` varchar(1) NOT NULL,
  `TrColor` varchar(6) NOT NULL DEFAULT '000000',
  `TrFlags` set('bar','gap') NOT NULL,
  `TrHeaderCode` varchar(2) NOT NULL,
  `TrHeader` varchar(25) NOT NULL,
  `TrFontFile` varchar(50) NOT NULL,
  `TrDownload` datetime NOT NULL,
  `TrUpdated` datetime NOT NULL,
  PRIMARY KEY (`TrTournament`,`TrRecCode`,`TrRecTeam`,`TrRecPara`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TourTypes`
--

DROP TABLE IF EXISTS `TourTypes`;
CREATE TABLE `TourTypes` (
  `TtId` int NOT NULL,
  `TtType` varchar(35) NOT NULL,
  `TtDistance` int NOT NULL,
  `TtOrderBy` int NOT NULL,
  `TtWaEquivalent` int NOT NULL,
  PRIMARY KEY (`TtId`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `TourTypes`
--

LOCK TABLES `TourTypes` WRITE;
/*!40000 ALTER TABLE `TourTypes` DISABLE KEYS */;
INSERT INTO `TourTypes` VALUES (1,'Type_FITA',4,1,2),(2,'Type_2xFITA',8,2,3),(4,'Type_FITA 72',4,3,4),(18,'Type_FITA+50',0,4,0),(3,'Type_70m Round',2,5,1),(6,'Type_Indoor 18',2,6,10),(7,'Type_Indoor 25',2,7,11),(8,'Type_Indoor 25+18',4,8,12),(14,'Type_Las Vegas',4,9,0),(9,'Type_HF 12+12',1,10,0),(12,'Type_HF 12+12',2,11,0),(10,'Type_HF 24+24',2,12,15),(17,'Type_NorField',0,13,0),(11,'3D',1,14,0),(13,'3D',2,15,17),(5,'Type_900 Round',3,16,5),(15,'Type_GiochiGioventu',2,17,0),(16,'Type_GiochiGioventuW',2,18,0),(19,'Type_GiochiStudentes',1,19,0),(20,'Type_SweForestRound',0,20,0),(21,'Type_Face2Face',0,21,0),(22,'Type_Indoor 18',1,22,10),(23,'Type_Bel_25m_Out',2,23,0),(24,'Type_Bel_50-30_Out',2,24,0),(25,'Type_Bel_50_Out',2,25,0),(26,'Type_Bel_B10_Out',2,26,0),(27,'Type_Bel_B15_Out',2,27,0),(28,'Type_Bel_B25_Out',2,28,0),(29,'Type_Bel_B50-30_Out',2,29,0),(30,'Type_Bel_BFITA_Out',4,30,0),(31,'Type_ITA_Sperimental',2,31,0),(32,'type_NFAA_Indoor',2,32,0),(33,'type_ITA_TrofeoCONI',1,33,0),(34,'Type_NZ_FITA+72',6,34,0),(35,'Type_NZ_Clout',1,35,0),(36,'type_NFAA_1stDakotaBank',3,36,0),(37,'Type_2x70mRound',4,36,25),(38,'Type_ProAMIndoor',3,37,0),(39,'Type_36Arr70mRound',1,39,0),(40,'Type_LocalUK',4,40,0),(41,'Type_NL_YouthFita',3,41,0),(42,'Type_NL_25p1',1,42,0),(43,'Type_NL_Hout',1,43,0),(44,'Type_CH_Federal',2,44,0),(45,'Type_FR_Kyudo',1,45,0),(46,'Type_NFAA_Target',6,46,0),(47,'Type_NFAA_Field',3,47,0);
/*!40000 ALTER TABLE `TourTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tournament`
--

DROP TABLE IF EXISTS `Tournament`;
CREATE TABLE `Tournament` (
  `ToId` int unsigned NOT NULL AUTO_INCREMENT,
  `ToOnlineId` int NOT NULL DEFAULT '0',
  `ToType` smallint unsigned NOT NULL,
  `ToCode` varchar(8) NOT NULL,
  `ToIocCode` varchar(5) NOT NULL DEFAULT '',
  `ToTimeZone` varchar(50) NOT NULL DEFAULT '',
  `ToName` tinytext NOT NULL,
  `ToNameShort` varchar(60) NOT NULL,
  `ToCommitee` varchar(10) NOT NULL,
  `ToComDescr` tinytext NOT NULL,
  `ToWhere` tinytext NOT NULL,
  `ToVenue` tinytext NOT NULL,
  `ToCountry` varchar(3) NOT NULL,
  `ToWhenFrom` date NOT NULL,
  `ToWhenTo` date NOT NULL,
  `ToIntEvent` tinyint unsigned NOT NULL DEFAULT '0',
  `ToCurrency` varchar(8) DEFAULT NULL,
  `ToPrintLang` varchar(5) NOT NULL,
  `ToPrintChars` tinyint unsigned NOT NULL DEFAULT '0',
  `ToPrintPaper` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0: ansi A4, 1: Letter',
  `ToImpFin` tinyint unsigned NOT NULL DEFAULT '0',
  `ToImgL` mediumblob NOT NULL,
  `ToImgR` mediumblob NOT NULL,
  `ToImgB` mediumblob NOT NULL,
  `ToImgB2` blob NOT NULL,
  `ToNumSession` tinyint unsigned NOT NULL DEFAULT '0',
  `ToIndFinVxA` tinyint unsigned NOT NULL DEFAULT '0',
  `ToTeamFinVxA` tinyint unsigned NOT NULL DEFAULT '0',
  `ToDbVersion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ToBlock` int unsigned NOT NULL,
  `ToUseHHT` tinyint NOT NULL DEFAULT '0',
  `ToLocRule` varchar(16) NOT NULL DEFAULT '',
  `ToTypeName` varchar(25) NOT NULL,
  `ToTypeSubRule` varchar(25) NOT NULL,
  `ToNumDist` tinyint unsigned NOT NULL,
  `ToNumEnds` tinyint unsigned NOT NULL,
  `ToMaxDistScore` mediumint unsigned NOT NULL,
  `ToMaxFinIndScore` mediumint unsigned NOT NULL,
  `ToMaxFinTeamScore` mediumint unsigned NOT NULL,
  `ToCategory` tinyint NOT NULL DEFAULT '0',
  `ToElabTeam` tinyint NOT NULL DEFAULT '0',
  `ToElimination` tinyint NOT NULL DEFAULT '0',
  `ToGolds` varchar(5) NOT NULL,
  `ToXNine` varchar(5) NOT NULL,
  `ToGoldsChars` varchar(16) NOT NULL DEFAULT '',
  `ToXNineChars` varchar(16) NOT NULL DEFAULT '',
  `ToDouble` tinyint NOT NULL DEFAULT '0',
  `ToCollation` varchar(15) NOT NULL,
  `ToIsORIS` varchar(1) NOT NULL DEFAULT '',
  `ToOptions` text NOT NULL,
  `ToRecCode` varchar(25) NOT NULL,
  PRIMARY KEY (`ToId`),
  UNIQUE KEY `ToCode` (`ToCode`),
  KEY `ToDbVersion` (`ToDbVersion`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TournamentDistances`
--

DROP TABLE IF EXISTS `TournamentDistances`;
CREATE TABLE `TournamentDistances` (
  `TdClasses` varchar(10) NOT NULL,
  `TdType` smallint unsigned NOT NULL,
  `TdTournament` int NOT NULL,
  `Td1` varchar(10) NOT NULL,
  `Td2` varchar(10) NOT NULL,
  `Td3` varchar(10) NOT NULL,
  `Td4` varchar(10) NOT NULL,
  `Td5` varchar(10) NOT NULL,
  `Td6` varchar(10) NOT NULL,
  `Td7` varchar(10) NOT NULL,
  `Td8` varchar(10) NOT NULL,
  `TdTourRules` varchar(75) NOT NULL,
  `TdDist1` tinyint unsigned NOT NULL,
  `TdDist2` tinyint unsigned NOT NULL,
  `TdDist3` tinyint unsigned NOT NULL,
  `TdDist4` tinyint unsigned NOT NULL,
  `TdDist5` tinyint unsigned NOT NULL,
  `TdDist6` tinyint unsigned NOT NULL,
  `TdDist7` tinyint unsigned NOT NULL,
  `TdDist8` tinyint unsigned NOT NULL,
  PRIMARY KEY (`TdTournament`,`TdType`,`TdClasses`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `TournamentInvolved`
--

DROP TABLE IF EXISTS `TournamentInvolved`;
CREATE TABLE `TournamentInvolved` (
  `TiId` int unsigned NOT NULL AUTO_INCREMENT,
  `TiTournament` int unsigned NOT NULL,
  `TiType` smallint unsigned NOT NULL,
  `TiCode` varchar(9) NOT NULL,
  `TiName` varchar(64) NOT NULL,
  `TiGivenName` varchar(64) NOT NULL,
  `TiCountry` int unsigned NOT NULL,
  `TiGender` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`TiId`)
) ENGINE=MyISAM AUTO_INCREMENT=297 DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `Vegas`
--

DROP TABLE IF EXISTS `Vegas`;
CREATE TABLE `Vegas` (
  `VeId` int unsigned NOT NULL,
  `VeArrowstring` varchar(90) NOT NULL,
  `VeScore` smallint NOT NULL,
  `VeX` smallint NOT NULL,
  `VeRank` smallint NOT NULL,
  `VeSubClass` varchar(2) NOT NULL,
  `VeTimestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`VeId`),
  KEY `VeScore` (`VeScore`,`VeX`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

--
-- Table structure for table `VegasAwards`
--

DROP TABLE IF EXISTS `VegasAwards`;
CREATE TABLE `VegasAwards` (
  `VaTournament` int NOT NULL,
  `VaDivision` varchar(4) NOT NULL,
  `VaClass` varchar(6) NOT NULL,
  `VaSubClass` varchar(2) NOT NULL,
  `VaRank` int NOT NULL,
  `VaAward` float(15,2) NOT NULL DEFAULT '0.00',
  `VaToDelete` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`VaTournament`,`VaDivision`,`VaClass`,`VaSubClass`,`VaRank`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_general_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

