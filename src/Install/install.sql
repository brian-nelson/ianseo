-- MySQL dump 10.11
--
-- Host: localhost    Database: ianseo
-- ------------------------------------------------------

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
-- Table structure for table `AccColors`
--

DROP TABLE IF EXISTS `AccColors`;
CREATE TABLE `AccColors` (
  `AcTournament` int(10) unsigned NOT NULL,
  `AcDivClass` varchar(4) NOT NULL,
  `AcColor` varchar(6) NOT NULL,
  `AcIsAthlete` tinyint(3) unsigned NOT NULL,
  `AcTitleReverse` tinyint(4) NOT NULL default '0',
  `AcArea0` tinyint(4) NOT NULL default '0',
  `AcArea1` tinyint(4) NOT NULL default '0',
  `AcArea2` tinyint(4) NOT NULL default '0',
  `AcArea3` tinyint(4) NOT NULL default '0',
  `AcArea4` tinyint(4) NOT NULL default '0',
  `AcArea5` tinyint(4) NOT NULL default '0',
  `AcArea6` tinyint(4) NOT NULL default '0',
  `AcArea7` tinyint(4) NOT NULL default '0',
  `AcAreaStar` tinyint(4) NOT NULL default '0',
  `AcTransport` tinyint(4) NOT NULL default '0',
  `AcAccomodation` tinyint(4) NOT NULL default '0',
  `AcMeal` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`AcTournament`,`AcDivClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `AccColors`
--

LOCK TABLES `AccColors` WRITE;
/*!40000 ALTER TABLE `AccColors` DISABLE KEYS */;
/*!40000 ALTER TABLE `AccColors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AccEntries`
--

DROP TABLE IF EXISTS `AccEntries`;
CREATE TABLE `AccEntries` (
  `AEId` int(10) unsigned NOT NULL default '0',
  `AEOperation` tinyint(4) NOT NULL default '0',
  `AETournament` int(10) unsigned NOT NULL default '0',
  `AEWhen` datetime NOT NULL default '0000-00-00 00:00:00',
  `AEFromIp` int(10) unsigned NOT NULL default '0',
  `AERapp` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`AEId`,`AEOperation`,`AETournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `AccEntries`
--

LOCK TABLES `AccEntries` WRITE;
/*!40000 ALTER TABLE `AccEntries` DISABLE KEYS */;
/*!40000 ALTER TABLE `AccEntries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AccOperationType`
--

DROP TABLE IF EXISTS `AccOperationType`;
CREATE TABLE `AccOperationType` (
  `AOTId` smallint(5) unsigned NOT NULL auto_increment,
  `AOTDescr` varchar(32) NOT NULL,
  PRIMARY KEY  (`AOTId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `AccOperationType`
--

LOCK TABLES `AccOperationType` WRITE;
/*!40000 ALTER TABLE `AccOperationType` DISABLE KEYS */;
INSERT INTO `AccOperationType` VALUES (1,'Accreditation'),(2,'ControlMaterial');
/*!40000 ALTER TABLE `AccOperationType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AccPrice`
--

DROP TABLE IF EXISTS `AccPrice`;
CREATE TABLE `AccPrice` (
  `APId` int(10) unsigned NOT NULL auto_increment,
  `APTournament` int(10) unsigned NOT NULL default '0',
  `APDivClass` varchar(4) NOT NULL,
  `APPrice` float(5,2) NOT NULL default '0.00',
  PRIMARY KEY  (`APId`),
  UNIQUE KEY `APTournament` (`APTournament`,`APDivClass`)
) ENGINE=MyISAM AUTO_INCREMENT=1268 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `AccPrice`
--

LOCK TABLES `AccPrice` WRITE;
/*!40000 ALTER TABLE `AccPrice` DISABLE KEYS */;
/*!40000 ALTER TABLE `AccPrice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AvailableTarget`
--

DROP TABLE IF EXISTS `AvailableTarget`;
CREATE TABLE `AvailableTarget` (
  `AtTournament` int(10) unsigned NOT NULL,
  `AtTargetNo` varchar(5) NOT NULL,
  PRIMARY KEY  (`AtTournament`,`AtTargetNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `AvailableTarget`
--

LOCK TABLES `AvailableTarget` WRITE;
/*!40000 ALTER TABLE `AvailableTarget` DISABLE KEYS */;
/*!40000 ALTER TABLE `AvailableTarget` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BackNumber`
--

DROP TABLE IF EXISTS `BackNumber`;
CREATE TABLE `BackNumber` (
  `BnTournament` int(10) unsigned NOT NULL,
  `BnFinal` tinyint(4) NOT NULL,
  `BnHeight` smallint(5) unsigned NOT NULL,
  `BnWidth` smallint(5) unsigned NOT NULL,
  `BnBackground` mediumblob NOT NULL,
  `BnBgX` smallint(6) NOT NULL,
  `BnBgY` smallint(6) NOT NULL,
  `BnBgW` smallint(6) NOT NULL,
  `BnBgH` smallint(6) NOT NULL,
  `BnTargetNo` tinyint(3) unsigned NOT NULL,
  `BnTnoColor` varchar(6) NOT NULL default '000000',
  `BnTnoSize` smallint(6) NOT NULL,
  `BnTnoX` smallint(6) NOT NULL,
  `BnTnoY` smallint(6) NOT NULL,
  `BnTnoW` smallint(6) NOT NULL,
  `BnTnoH` smallint(6) NOT NULL,
  `BnAthlete` tinyint(3) unsigned NOT NULL,
  `BnAthColor` varchar(6) NOT NULL default '000000',
  `BnAthSize` smallint(6) NOT NULL,
  `BnAthX` smallint(6) NOT NULL,
  `BnAthY` smallint(6) NOT NULL,
  `BnAthW` smallint(6) NOT NULL,
  `BnAthH` smallint(6) NOT NULL,
  `BnCountry` tinyint(3) unsigned NOT NULL,
  `BnCoColor` varchar(6) NOT NULL default '000000',
  `BnCoSize` smallint(6) NOT NULL,
  `BnCoX` smallint(6) NOT NULL,
  `BnCoY` smallint(6) NOT NULL,
  `BnCoW` smallint(6) NOT NULL,
  `BnCoH` smallint(6) NOT NULL,
  `BnOffsetX` smallint(6) NOT NULL,
  `BnOffsetY` smallint(6) NOT NULL,
  PRIMARY KEY  (`BnTournament`,`BnFinal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `BackNumber`
--

LOCK TABLES `BackNumber` WRITE;
/*!40000 ALTER TABLE `BackNumber` DISABLE KEYS */;
/*!40000 ALTER TABLE `BackNumber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Broadcast`
--

DROP TABLE IF EXISTS `Broadcast`;
CREATE TABLE `Broadcast` (
  `BrTournament` int(10) unsigned NOT NULL,
  `BrEnabled` int(10) unsigned NOT NULL default '0',
  `BrSottoPancia0` tinytext NOT NULL,
  `BrSottoPancia1` tinytext NOT NULL,
  `BrSottoPancia2` tinytext NOT NULL,
  `BrSottoPancia3` tinytext NOT NULL,
  `BrEvent` varchar(4) NOT NULL,
  `BrMatchNo` tinyint(3) unsigned NOT NULL,
  `BrCommentL` tinytext NOT NULL,
  `BrCommentR` tinytext NOT NULL,
  `BrTimeStamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`BrTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Broadcast`
--

LOCK TABLES `Broadcast` WRITE;
/*!40000 ALTER TABLE `Broadcast` DISABLE KEYS */;
/*!40000 ALTER TABLE `Broadcast` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasGrid`
--

DROP TABLE IF EXISTS `CasGrid`;
CREATE TABLE `CasGrid` (
  `CGPhase` tinyint(3) unsigned NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
  `CGRound` tinyint(3) unsigned NOT NULL,
  `CGMatchNo1` tinyint(3) unsigned NOT NULL,
  `CGMatchNo2` tinyint(3) unsigned NOT NULL,
  `CGGroup` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CGPhase`,`CGRound`,`CGMatchNo1`,`CGMatchNo2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `CasGrid`
--

LOCK TABLES `CasGrid` WRITE;
/*!40000 ALTER TABLE `CasGrid` DISABLE KEYS */;
INSERT INTO `CasGrid` VALUES (1,1,1,8,1),(1,1,2,7,2),(1,1,3,6,3),(1,1,4,5,4),(1,1,9,13,4),(1,1,10,14,3),(1,1,11,15,2),(1,1,12,16,1),(1,2,1,12,1),(1,2,2,11,2),(1,2,3,10,3),(1,2,4,9,4),(1,2,5,13,4),(1,2,6,14,3),(1,2,7,15,2),(1,2,8,16,1),(1,3,1,16,1),(1,3,2,15,2),(1,3,3,14,3),(1,3,4,13,4),(1,3,5,9,4),(1,3,6,10,3),(1,3,7,11,2),(1,3,8,12,1),(2,1,1,8,1),(2,1,2,7,2),(2,1,3,6,3),(2,1,4,5,4),(2,1,9,13,4),(2,1,10,14,3),(2,1,11,15,2),(2,1,12,16,1),(2,2,1,12,1),(2,2,2,11,2),(2,2,3,10,3),(2,2,4,9,4),(2,2,5,13,4),(2,2,6,14,3),(2,2,7,15,2),(2,2,8,16,1),(2,3,1,16,1),(2,3,2,15,2),(2,3,3,14,3),(2,3,4,13,4),(2,3,5,9,4),(2,3,6,10,3),(2,3,7,11,2),(2,3,8,12,1);
/*!40000 ALTER TABLE `CasGrid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasGroupMatch`
--

DROP TABLE IF EXISTS `CasGroupMatch`;
CREATE TABLE `CasGroupMatch` (
  `CaGMGroup` tinyint(3) unsigned NOT NULL,
  `CaGRank` tinyint(4) unsigned NOT NULL,
  `CaGMMatchNo` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CaGMGroup`,`CaGMMatchNo`,`CaGRank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `CRMEventPhase` tinyint(4) NOT NULL,
  `CRMRank` tinyint(4) NOT NULL,
  `CRMMatchNo` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CRMEventPhase`,`CRMRank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `CaSTournament` int(10) unsigned NOT NULL default '0',
  `CaSPhase` tinyint(3) unsigned NOT NULL default '0' COMMENT '1=fase 1;2=fase2',
  `CaSRound` tinyint(3) unsigned NOT NULL,
  `CaSMatchNo` tinyint(3) unsigned NOT NULL,
  `CaSEventCode` varchar(4) NOT NULL,
  `CaSTarget` varchar(2) NOT NULL,
  `CaSScore` smallint(6) NOT NULL default '0',
  `CaSTie` tinyint(1) NOT NULL default '0',
  `CaSArrowString` varchar(24) NOT NULL,
  `CaSArrowPosition` varchar(240) NOT NULL,
  `CaSTiebreak` varchar(9) NOT NULL,
  `CaSTiePosition` varchar(90) NOT NULL,
  `CaSPoints` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`CaSTournament`,`CaSPhase`,`CaSMatchNo`,`CaSEventCode`,`CaSRound`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `CasScore`
--

LOCK TABLES `CasScore` WRITE;
/*!40000 ALTER TABLE `CasScore` DISABLE KEYS */;
/*!40000 ALTER TABLE `CasScore` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CasTeam`
--

DROP TABLE IF EXISTS `CasTeam`;
CREATE TABLE `CasTeam` (
  `CaTournament` int(10) unsigned NOT NULL default '0',
  `CaPhase` tinyint(3) unsigned NOT NULL default '0' COMMENT '1=fase 1;2=fase2',
  `CaMatchNo` tinyint(3) unsigned NOT NULL default '0',
  `CaEventCode` varchar(4) NOT NULL,
  `CaTeam` int(11) unsigned NOT NULL default '0',
  `CaSubTeam` varchar(1) NOT NULL,
  `CaRank` tinyint(4) unsigned NOT NULL,
  `CaTiebreak` varchar(9) NOT NULL,
  PRIMARY KEY  (`CaTournament`,`CaPhase`,`CaMatchNo`,`CaEventCode`,`CaTeam`,`CaSubTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `CasTeam`
--

LOCK TABLES `CasTeam` WRITE;
/*!40000 ALTER TABLE `CasTeam` DISABLE KEYS */;
/*!40000 ALTER TABLE `CasTeam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Classes`
--

DROP TABLE IF EXISTS `Classes`;
CREATE TABLE `Classes` (
  `ClId` varchar(2) NOT NULL,
  `ClTournament` int(10) unsigned NOT NULL default '0',
  `ClDescription` varchar(32) NOT NULL,
  `ClViewOrder` tinyint(3) unsigned NOT NULL default '0',
  `ClAgeFrom` tinyint(4) NOT NULL,
  `ClAgeTo` tinyint(4) NOT NULL,
  `ClValidClass` varchar(16) NOT NULL default '0',
  `ClSex` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ClId`,`ClTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Classes`
--

LOCK TABLES `Classes` WRITE;
/*!40000 ALTER TABLE `Classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `Classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeam`
--

DROP TABLE IF EXISTS `ClubTeam`;
CREATE TABLE `ClubTeam` (
  `CTTournament` int(10) unsigned NOT NULL default '0',
  `CTPhase` tinyint(3) unsigned NOT NULL default '0' COMMENT '1=fase 1;2=fase2',
  `CTMatchNo` tinyint(3) unsigned NOT NULL default '0',
  `CTEventCode` varchar(4) NOT NULL,
  `CTPrimary` tinyint(3) unsigned NOT NULL,
  `CTTeam` int(11) unsigned NOT NULL default '0',
  `CTSubTeam` varchar(1) NOT NULL,
  `CTBonus` tinyint(3) unsigned NOT NULL default '0',
  `CTRank` tinyint(4) unsigned NOT NULL,
  `CTTiebreak` varchar(9) NOT NULL default '',
  PRIMARY KEY  (`CTTournament`,`CTPhase`,`CTMatchNo`,`CTEventCode`,`CTPrimary`,`CTTeam`,`CTSubTeam`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ClubTeam`
--

LOCK TABLES `ClubTeam` WRITE;
/*!40000 ALTER TABLE `ClubTeam` DISABLE KEYS */;
/*!40000 ALTER TABLE `ClubTeam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeamGrid`
--

DROP TABLE IF EXISTS `ClubTeamGrid`;
CREATE TABLE `ClubTeamGrid` (
  `CTGPhase` tinyint(3) unsigned NOT NULL COMMENT '1 o 2 a seconda della fase della gara',
  `CTGRound` tinyint(3) unsigned NOT NULL,
  `CTGMatchNo1` tinyint(3) unsigned NOT NULL,
  `CTGMatchNo2` tinyint(3) unsigned NOT NULL,
  `CTGGroup` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CTGPhase`,`CTGRound`,`CTGMatchNo1`,`CTGMatchNo2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ClubTeamGrid`
--

LOCK TABLES `ClubTeamGrid` WRITE;
/*!40000 ALTER TABLE `ClubTeamGrid` DISABLE KEYS */;
INSERT INTO `ClubTeamGrid` VALUES (1,1,1,8,1),(1,1,2,7,2),(1,1,3,6,3),(1,1,4,5,4),(1,1,9,16,1),(1,1,10,15,2),(1,1,11,14,3),(1,1,12,13,4),(1,2,1,9,1),(1,2,2,10,2),(1,2,3,11,3),(1,2,4,12,4),(1,2,5,13,4),(1,2,6,14,3),(1,2,7,15,2),(1,2,8,16,1),(1,3,1,16,1),(1,3,2,15,2),(1,3,3,14,3),(1,3,4,13,4),(1,3,5,12,4),(1,3,6,11,3),(1,3,7,10,2),(1,3,8,9,1),(2,1,1,4,1),(2,1,2,3,1),(2,1,5,8,2),(2,1,6,7,2),(2,1,9,12,3),(2,1,10,11,3),(2,1,13,16,4),(2,1,14,15,4),(3,1,1,2,1),(3,1,3,4,1),(3,1,5,6,2),(3,1,7,8,2),(3,1,9,10,3),(3,1,11,12,3),(3,1,13,14,4),(3,1,15,16,4);
/*!40000 ALTER TABLE `ClubTeamGrid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ClubTeamGroupMatch`
--

DROP TABLE IF EXISTS `ClubTeamGroupMatch`;
CREATE TABLE `ClubTeamGroupMatch` (
  `CTGMGroup` tinyint(3) unsigned NOT NULL,
  `CTGMMatchNo` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CTGMGroup`,`CTGMMatchNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `CTRMEventPhase` tinyint(4) NOT NULL,
  `CTRMRank` tinyint(4) NOT NULL,
  `CTRMMatchNo` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`CTRMEventPhase`,`CTRMRank`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `CTSTournament` int(10) unsigned NOT NULL default '0',
  `CTSPhase` tinyint(3) unsigned NOT NULL default '0' COMMENT '1=fase 1;2=fase2',
  `CTSRound` tinyint(3) unsigned NOT NULL,
  `CTSMatchNo` tinyint(3) unsigned NOT NULL,
  `CTSEventCode` varchar(4) NOT NULL,
  `CTSPrimary` tinyint(3) unsigned NOT NULL,
  `CTSTarget` varchar(2) NOT NULL,
  `CTSScore` smallint(6) NOT NULL default '0',
  `CTSTie` tinyint(1) NOT NULL default '0',
  `CTSArrowString` varchar(24) NOT NULL,
  `CTSArrowPosition` varchar(240) NOT NULL,
  `CTSTiebreak` varchar(9) NOT NULL,
  `CTSTiePosition` varchar(90) NOT NULL,
  `CTSPoints` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`CTSTournament`,`CTSPhase`,`CTSMatchNo`,`CTSEventCode`,`CTSRound`,`CTSPrimary`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `ClubTeamScore`
--

LOCK TABLES `ClubTeamScore` WRITE;
/*!40000 ALTER TABLE `ClubTeamScore` DISABLE KEYS */;
/*!40000 ALTER TABLE `ClubTeamScore` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Countries`
--

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
  `CoId` int(10) unsigned NOT NULL auto_increment,
  `CoTournament` int(10) unsigned NOT NULL default '0',
  `CoCode` varchar(5) NOT NULL,
  `CoName` varchar(30) NOT NULL,
  `CoNameComplete` varchar(80) NOT NULL,
  `CoSubCountry` varchar(5) NOT NULL,
  `CoFlag` mediumblob NOT NULL,
  `CoMail` varchar(30) NOT NULL,
  `CoNoPrint` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`CoId`),
  UNIQUE KEY `CoTournament` (`CoTournament`,`CoCode`)
) ENGINE=MyISAM AUTO_INCREMENT=5869 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Countries`
--

LOCK TABLES `Countries` WRITE;
/*!40000 ALTER TABLE `Countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `Countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Divisions`
--

DROP TABLE IF EXISTS `Divisions`;
CREATE TABLE `Divisions` (
  `DivId` varchar(2) NOT NULL,
  `DivTournament` int(10) unsigned NOT NULL default '0',
  `DivDescription` varchar(32) NOT NULL,
  `DivViewOrder` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`DivId`,`DivTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Divisions`
--

LOCK TABLES `Divisions` WRITE;
/*!40000 ALTER TABLE `Divisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `Divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ElabQualifications`
--

DROP TABLE IF EXISTS `ElabQualifications`;
CREATE TABLE `ElabQualifications` (
  `EqId` int(10) unsigned NOT NULL,
  `EqArrowNo` smallint(5) unsigned NOT NULL,
  `EqDistance` tinyint(4) unsigned NOT NULL,
  `EqScore` int(11) NOT NULL,
  `EqHits` int(11) NOT NULL,
  `EqGold` int(11) NOT NULL,
  `EqXnine` int(11) NOT NULL,
  `EqTimestamp` datetime default NULL,
  PRIMARY KEY  (`EqId`,`EqArrowNo`,`EqDistance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ElabQualifications`
--

LOCK TABLES `ElabQualifications` WRITE;
/*!40000 ALTER TABLE `ElabQualifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `ElabQualifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Eliminations`
--

DROP TABLE IF EXISTS `Eliminations`;
CREATE TABLE `Eliminations` (
  `ElId` int(10) unsigned NOT NULL,
  `ElElimPhase` tinyint(4) NOT NULL default '0',
  `ElEventCode` varchar(4) NOT NULL,
  `ElSession` tinyint(3) unsigned NOT NULL default '0',
  `ElTargetNo` varchar(5) NOT NULL,
  `ElScore` smallint(6) NOT NULL,
  `ElHits` smallint(6) NOT NULL,
  `ElGold` smallint(6) NOT NULL,
  `ElXnine` smallint(6) NOT NULL,
  `ElArrowString` varchar(36) NOT NULL,
  `ElRank` tinyint(3) unsigned NOT NULL,
  `ElStatus` tinyint(3) unsigned NOT NULL,
  `ElDateTime` datetime NOT NULL,
  PRIMARY KEY  (`ElId`,`ElElimPhase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Eliminations`
--

LOCK TABLES `Eliminations` WRITE;
/*!40000 ALTER TABLE `Eliminations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Eliminations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Entries`
--

DROP TABLE IF EXISTS `Entries`;
CREATE TABLE `Entries` (
  `EnId` int(10) unsigned NOT NULL auto_increment,
  `EnTournament` int(10) unsigned NOT NULL default '0',
  `EnDivision` varchar(2) NOT NULL,
  `EnClass` varchar(2) NOT NULL,
  `EnSubClass` varchar(2) NOT NULL,
  `EnAgeClass` varchar(2) NOT NULL,
  `EnCountry` int(10) unsigned NOT NULL default '0',
  `EnCtrlCode` varchar(16) NOT NULL,
  `EnDob` date NOT NULL,
  `EnCode` varchar(9) NOT NULL,
  `EnName` varchar(30) NOT NULL,
  `EnFirstName` varchar(30) NOT NULL,
  `EnPhoto` varchar(40) NOT NULL,
  `EnAthlete` tinyint(1) unsigned NOT NULL default '1',
  `EnSex` tinyint(1) unsigned NOT NULL default '0',
  `EnWChair` tinyint(1) unsigned NOT NULL default '0',
  `EnSitting` tinyint(1) unsigned NOT NULL default '0',
  `EnIndClEvent` tinyint(1) unsigned NOT NULL default '1',
  `EnTeamClEvent` tinyint(1) unsigned NOT NULL default '1',
  `EnIndFEvent` tinyint(1) unsigned NOT NULL default '1',
  `EnTeamFEvent` tinyint(1) unsigned NOT NULL default '1',
  `EnPays` tinyint(1) unsigned NOT NULL default '1',
  `EnStatus` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`EnId`),
  KEY `EnDivision` (`EnDivision`),
  KEY `EnClass` (`EnClass`),
  KEY `CalcRank` (`EnTournament`,`EnAthlete`,`EnStatus`)
) ENGINE=MyISAM AUTO_INCREMENT=21521 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Entries`
--

LOCK TABLES `Entries` WRITE;
/*!40000 ALTER TABLE `Entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `Entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EventClass`
--

DROP TABLE IF EXISTS `EventClass`;
CREATE TABLE `EventClass` (
  `EcCode` varchar(4) NOT NULL,
  `EcTeamEvent` tinyint(1) NOT NULL default '0',
  `EcTournament` int(11) NOT NULL,
  `EcClass` varchar(2) NOT NULL,
  `EcDivision` varchar(2) NOT NULL,
  `EcNumber` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY  (`EcCode`,`EcTeamEvent`,`EcTournament`,`EcClass`,`EcDivision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `EventClass`
--

LOCK TABLES `EventClass` WRITE;
/*!40000 ALTER TABLE `EventClass` DISABLE KEYS */;
/*!40000 ALTER TABLE `EventClass` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
CREATE TABLE `Events` (
  `EvCode` varchar(4) NOT NULL,
  `EvTeamEvent` tinyint(1) NOT NULL,
  `EvTournament` int(11) NOT NULL,
  `EvEventName` varchar(64) NOT NULL,
  `EvProgr` int(11) NOT NULL,
  `EvShootOff` tinyint(3) unsigned NOT NULL default '0',
  `EvE1ShootOff` tinyint(3) unsigned NOT NULL default '0',
  `EvE2ShootOff` tinyint(3) unsigned NOT NULL default '0',
  `EvSession` int(11) NOT NULL,
  `EvPrint` tinyint(1) NOT NULL,
  `EvQualPrintHead` varchar(64) NOT NULL,
  `EvQualLastUpdate` datetime default NULL,
  `EvFinalFirstPhase` tinyint(4) NOT NULL,
  `EvFinalPrintHead` varchar(64) NOT NULL,
  `EvFinalLastUpdate` datetime default NULL,
  `EvFinalTargetType` tinyint(4) NOT NULL,
  `EvFinalAthTarget` tinyint(3) unsigned NOT NULL default '0',
  `EvElim1` tinyint(3) unsigned NOT NULL default '0',
  `EvElim2` tinyint(3) unsigned NOT NULL default '0',
  `EvPartialTeam` tinyint(1) unsigned NOT NULL default '0',
  `EvMultiTeam` tinyint(1) unsigned NOT NULL default '0',
  `EvMixedTeam` tinyint(3) unsigned NOT NULL default '0',
  `EvRunning` tinyint(3) unsigned NOT NULL default '0',
  `EvMatchMode` tinyint(4) NOT NULL default '0',
  `EvMatchArrowsNo` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`EvCode`,`EvTeamEvent`,`EvTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Events`
--

LOCK TABLES `Events` WRITE;
/*!40000 ALTER TABLE `Events` DISABLE KEYS */;
/*!40000 ALTER TABLE `Events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FinGroups`
--

DROP TABLE IF EXISTS `FinGroups`;
CREATE TABLE `FinGroups` (
  `FGId` int(10) unsigned NOT NULL auto_increment,
  `FGTournament` int(10) unsigned NOT NULL default '0',
  `FGDescr` varchar(24) NOT NULL,
  PRIMARY KEY  (`FGId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `FinGroups`
--

LOCK TABLES `FinGroups` WRITE;
/*!40000 ALTER TABLE `FinGroups` DISABLE KEYS */;
/*!40000 ALTER TABLE `FinGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FinSchedule`
--

DROP TABLE IF EXISTS `FinSchedule`;
CREATE TABLE `FinSchedule` (
  `FSEvent` varchar(4) NOT NULL,
  `FSTeamEvent` tinyint(1) unsigned NOT NULL default '0',
  `FSMatchNo` tinyint(4) unsigned NOT NULL,
  `FSTournament` int(10) unsigned NOT NULL,
  `FSTarget` varchar(3) NOT NULL,
  `FSGroup` int(10) unsigned NOT NULL default '0',
  `FSScheduledDate` date NOT NULL,
  `FSScheduledTime` time default NULL,
  PRIMARY KEY  (`FSEvent`,`FSTeamEvent`,`FSMatchNo`,`FSTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `FinSchedule`
--

LOCK TABLES `FinSchedule` WRITE;
/*!40000 ALTER TABLE `FinSchedule` DISABLE KEYS */;
/*!40000 ALTER TABLE `FinSchedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FinalReportA`
--

DROP TABLE IF EXISTS `FinalReportA`;
CREATE TABLE `FinalReportA` (
  `FraQuestion` varchar(5) NOT NULL,
  `FraTournament` int(10) unsigned NOT NULL,
  `FraAnswer` text NOT NULL,
  PRIMARY KEY  (`FraQuestion`,`FraTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `FinalReportA`
--

LOCK TABLES `FinalReportA` WRITE;
/*!40000 ALTER TABLE `FinalReportA` DISABLE KEYS */;
/*!40000 ALTER TABLE `FinalReportA` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `FinalReportQ`
--

DROP TABLE IF EXISTS `FinalReportQ`;
CREATE TABLE `FinalReportQ` (
  `FrqId` varchar(5) NOT NULL,
  `FrqStatus` tinyint(4) NOT NULL default '0',
  `FrqQuestion` tinytext NOT NULL,
  `FrqTip` text NOT NULL,
  `FrqType` tinyint(4) NOT NULL,
  `FrqOptions` varchar(200) NOT NULL,
  PRIMARY KEY  (`FrqId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `FinalReportQ`
--

LOCK TABLES `FinalReportQ` WRITE;
/*!40000 ALTER TABLE `FinalReportQ` DISABLE KEYS */;
INSERT INTO `FinalReportQ` VALUES ('A',15,'Definizione Competizione','',-1,''),('a01',15,'Conforme al calendario','',2,''),('a02',15,'Tutte le classi e divisioni ammesse','',2,''),('a03',15,'in caso contrario, specificare','',1,'70|2'),('a04',3,'Direttore dei tiri','',3,'non iscritto all\'albo|iscritto all\'albo|arbitro|arbitro e GdG'),('a05',15,'Giuria d\'Appello','Pubblicata ad inizio gara; solo per gare nazionali ed internazionali',2,''),('a06',15,'Field Manager','Responsabile dell\'organizzazione appositamente nominato per la gestione del campo',2,''),('a07',15,'Calendario','',3,'Internazionale|Nazionale|Interregionale|Giovanile|Sperimentale'),('a08',15,'Squadre Nazionali straniere','Solo rappresentative ufficiali; non applicabile solo per eventi federali',3,'N.A.|No|Si'),('a09',15,'In caso affermativo, specificare','',1,'70|2'),('a10',3,'Segnalazione eventuali Record','',1,'70|10'),('a11',15,'Reclami','',1,'70|7'),('a12',15,'Programma della gara pervenuto alla Giuria di Gara','',3,'No|Pubblicato|Si'),('a13',15,'Tassa ridotta per le classi giovanili','50% o differente se stabilito da apposito regolamento (es: Campionati Italiani)',3,'Come da Regolamento|Non Applicabile|No|Gratuita'),('a14',0,'Numero Allegati','',0,'5'),('E',15,'Disposizione e attrezzatura del campo ','',-1,''),('e01',15,'Distanze in tolleranza','Distanze tra linea di tiro e bersagli',2,''),('e02',3,'Separazione interasse conforme al regolamento','',2,''),('e03',3,'Linea di tiro','Temporanea = Nastro Fissa = autobloccanti o equivalente per indoor',3,'Assente|Temporanea|Fissa'),('e04',3,'Numeri sulla linea di Tiro ','tra 1 e 2 metri',2,''),('e05',3,'Linea dei tre metri','',3,'Presente|Assente'),('e06',3,'Linea Stampa/Media','',2,''),('e08',3,'Linea di attesa conforme al regolamento','',2,''),('e09',3,'Corridoi ','Tra i paglioni ove previsto dal regolamento',3,'Presente|Assente|Non Applicabile'),('e10',7,'Battifreccia','',3,'Usurati|Nuovi'),('e11',7,'Materiale Battifreccia','',3,'Sintetico|Paglia'),('e12',3,'Supporti battifreccia','cavalletti',3,'Improvvisati|Triangolari|Rettangolari'),('e13',7,'Assicurazione battifreccia con tiranti','Per indoor = tirante tra paglione e cavalletto',3,'Non Applicabile|No|Si'),('e14',7,'Angolazione Battifreccia conforme al regolamento','per hf intendersi perpendicolarità del bersaglio rispetto alla posizione di tiro',2,''),('e15',7,'Disposizione visuali conforme','',2,''),('e16',1,'Bandiere segnavento','',3,'Assenti|Presenti non ben visibili|Presenti ben visibili'),('e17',3,'Numeratori dei battifreccia','numeri dei paglioni',3,'Assenti|Presenti non conformi|Presenti conformi'),('e18',1,'Maniche a vento','',3,'Non Applicabile|Assente|Si 1|Si 2 o più'),('e19',3,'Impianto semaforico ','Per automatico si intende la temporizzazione e la sincronizzazione delle luci dopo la chiamata tranne che per un eventuale termine anticipato\r\nNon a norma di sicurezza=deferimento automatico',3,'Assenti|Non a norma di sicurezza|Manuale|Automatico'),('e20',3,'Sistemi visivi ausiliari','Bandierine ',2,''),('e21',3,'Indicatori di sequenza ','(AB/CD) Non necessari per turno unico',3,'Non Necessari|Assenti|Manuale|Automatico'),('e22',3,'Orologi contasecondi','',2,''),('e23',3,'Indicatori acustici','Automatico se sincronizzato automaticamente con l\'impianto semforico',3,'Manuale|Automatico'),('e24',3,'Indicatori individuali di punteggio per la fase di qualifica','flip board o altri indicatori di punteggio',2,''),('e25',15,'Indicatori di punteggio per le fasi finali','Manuali ed automatici= utilizzati i manuali per le eliminatorie e elettronici solo per le finali a medaglia',3,'Non applicabile|Assenti|Manuali|Manuali ed Automatici|Automatici'),('e26',3,'Sedie e ombrelloni per gli arbitri','Nell\'indoor solo sedie',2,''),('e27',3,'Postazione direttore dei tiri rialzata','',2,''),('e28',1,'Blind per le finali','Protezione per gli arbitri in prossimità del bersaglio',2,'Non Applicabile|Si|No'),('e29',1,'Campo di tiri di allenamento','',3,'Non necessario|Assente|Utilizzate parti del campo di gara|Campo separato con orientamento differente|Campo separato con stesso orientamento '),('e30',15,'Campo per la finale','Campo per le finali singole',3,'Non necessario|Utilizzata parte del campo di gara|Campo apposito'),('e31',15,'Spazio per il pubblico','Per HF e 3D da intendersi solo per le fasi finali',3,'No|Spazio libero|Sedie|Tribune della struttura|Tribune appositamente realizzate'),('e32',12,'Campo tiri di prova','',4,'No|Si|Distante dal punto di raduno|In prossimità del punto di raduno|Sufficiente per tutti gli arcieri|Necessità di turnazione'),('e33',12,'Picchetto dello stop','',2,''),('e34',12,'Percorso alternativo','per raggiungere le piazzole senza attraversare il percorso di gara',2,''),('e35',12,'Distanza campo base / piazzola più lontana','Tempo di percorrenza',3,'Oltre i 15 minuti|Entro i 15 minuti'),('e36',12,'Interferenze','',4,'Al volo delle frecce|Alla visione del bersaglio|All\'arco|No'),('e37',4,'Distanze conosciute','Rispetto delle distanze e del numero dei bersagli per tipo indicati nel regolamento',2,''),('e38',12,'Distanze sconosciute','',3,'Inferiori alla media|nella media|Superiori alla media'),('e39',12,'Visibilità dei picchetti di tiro','dallo stop',2,''),('e40',12,'Pendenze medie','indicativo',3,'zero|Meno di 15°|Più di 15°'),('F',15,'Sicurezza','',-1,''),('f01',15,'Dietro la linea delle visuali','',3,'Non Presidiata|Presidiata|Assoluta'),('f02',15,'Accesso al campo','libero=nessun controllo; regolato=con pass nel rispetto del regolamento',3,'Libero|Regolato'),('f03',12,'Problemi generali di sicurezza del percorso','',4,'Tiri incrociati|Direzioni di tiro verso altre piazzole|Direzione di tiro verso il percorso di trasferimento|No'),('G',15,'Organizzazione','',-1,''),('g01',15,'Gestione della gara','',3,'Non adeguata|Continui e necessari interventi gdg|Con sporadici interventi gdg|Autonoma'),('g02',15,'Assegnazione piazzole','',3,'Assegnazione manuale|In ordine di rank|Sorteggio manuale|Sorteggio automatico'),('g03',15,'Abbigliamento  Personale organizzazione','',3,'Non riconoscibile|Divisa di società|Pettorine|Divisa evento '),('g04',7,'Visuale di riserva','',3,'Insufficienti|Appena Sufficienti|Abbondanti'),('g05',15,'Battifreccia o sagome di riserva','',2,''),('g06',3,'Cavalletti di riserva','',2,''),('g07',15,'Numeri di gara','Pettorali\r\nPersonalizzati (con nome dell\'atleta)',3,'Assenti|Della Società|Dell\'evento|Dell\'evento personalizzati'),('g08',15,'Ristoro','Alimenti',3,'Assente|Presente a pagamento|Gratuito'),('g09',15,'Bevande sul campo di gara','',3,'Assenti|Presenti a pagamento|Presenti gratuite'),('g10',3,'Assistenza ai disabili','',3,'Non necessaria|No|Si'),('g11',3,'Periodicità di esposizione classifiche','',3,'Mai|A fine gara|A fine distanza|Parziali di distanza|Tempo reale (ogni volèe)'),('g12',15,'Meccanismi di esposizione classifiche','',3,'Assente|Cartaceo|Monitor|Maxischermo|Monitor e Maxischermo'),('g13',3,'Raccolta punteggi parziali','Strumento utilizzato',3,'Assente|Cartaceo|Tastierini elettronici'),('g14',15,'Speaker','',3,'No|Durante la competizione|Durante le Finali|Durante qualifica e finali'),('g15',15,'Musica','',3,'No|Durante le pause|Dal vivo'),('g16',15,'Impianto di amplificazione','Per musica, comunicazioni di servizio, speaker',2,''),('g17',15,'Comunicazione sul campo','',3,'A voce|Telefoni dell\'organizzazione|Radio dell\'organizzazione'),('g18',12,'Raccolta punteggi parziali','',2,'Assente|Via Radio'),('g19',12,'Periodicità esposizione classifiche','',3,'Assente|Al termine della gara|Parziale una volta|Parziale più volte'),('g20',15,'Rinfresco finale','',2,''),('g21',15,'Connessione ad internet','per pubblico o partecipanti',3,'No|Si Gratuita|Si a pagamento'),('H',15,'Luogo della gara','',-1,''),('h01',3,'Servizi igienici','',4,'No|Comuni|Divisi per sesso|Lontani dal campo di gara|Facilmente Accessibili|Anche per disabili'),('h02',3,'Barriere architettoniche','',3,'Presenti|Assenti'),('h03',15,'Indicazioni stradali','Non mappe o indicazioni online ma cartellonistica',3,'No|Insufficiente|Sufficiente'),('h04',15,'Recettivita\' alberghiera','',4,'No|Gestita dall\'organizzazione|Oltre i 10 minuti dal campo di gara|Entro i 10 minuti dal campo di gara'),('h05',15,'Trasporto da e per il campo di gara','',3,'Non applicabile|No|Gestiti ma insufficienti|Gestiti e organizzati'),('h06',3,'Sedili','Insufficiente se < al 50% dei partecipanti',3,'Non applicabile|Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),('h07',13,'Ombrelloni e/o ombreggiatura','Insufficiente se < al 50% dei partecipanti',3,'Non applicabile|Insufficiente|Sufficiente per gli atleti|Sufficienti per atleti e accompagnatori'),('h08',15,'Sacchetti per la spazzatura','',3,'Sufficiente|Insufficiente'),('h09',1,'Orientamento campo','',3,'Non Applicabile|Sud|Est o Ovest|Entro 15 gradi|Nord'),('h10',1,'Fondo del campo','',3,'Non Applicabile|Terriccio| Sintetico|Erboso sconnesso|Erboso raso'),('h11',2,'Temperatura sulla linea di tiro','',3,'Meno di 18°|18° o più'),('h12',2,'Illuminazione artificiale ','',4,'Diffusa|Diretta|Uniforme|Non uniforme|Sufficiente|Non Sufficiente'),('h13',2,'Illuminazione naturale ','',4,'No|Filtrante|Omogenea'),('h14',12,'Servizi igienici','',3,'No|Solo al punto di ritrovo|Servizi lungo il percorso'),('h15',12,'Indicazioni di percorso','',4,'No|Scarsamente segnalato|Ben segnalato e visibile|Mappa del percorso distribuita'),('h16',12,'Pulizia del percorso','da intendersi del fondo del sentiero tra le piazzole',2,''),('I',15,'Assistenza Sanitaria','',-1,''),('i01',15,'Ospedale Entro i 10 Km','',2,''),('i02',15,'Medico Presente','',2,''),('i03',15,'Ambulanza','',2,''),('i04',15,'Stanza Attrezzata per le Emergenze','',2,''),('i05',15,'Stanza attrezzata per antidoping','',3,'Non necessaria|No|Si comune|Si separata per sesso'),('J',15,'Premiazioni','',-1,''),('j01',15,'Podio','',3,'Assente|Realizzato con paglioni o estemporaneamente|Struttura apposita'),('j02',15,'Bandiere','Nazionale, Fitarco, del comitato Regionale, della società',3,'No|Appoggiate a reti o siepi|Appese|Issate su pali'),('j03',15,'Inno Nazionale','',3,'Non Applicabile|No|Si'),('j04',15,'Tipo di Premi (Scelta multipla)','',4,'Medaglie/Coppe|Diplomi|Premi in Denaro|Premi in Natura'),('j05',15,'Premiazione conforme al Regolamento','Numero di premiati e tipologia dei premi',2,''),('j06',15,'Cerimonia di premiazione conforme al Regolamento','Chiamata nell\'ordine previsto, con posizione, società, punteggio e nome',2,''),('j07',15,'Omaggio di partecipazione','meglio conosciuto come \"premio di partecipazione\"',2,''),('j08',15,'Premi a sorteggio','',2,''),('K',15,'Promozione','',-1,''),('k01',15,'Conferenza Stampa','Nei giorni precedenti la competizione',2,''),('k02',15,'Presenza sulla carta stampata','Prima della competizione',4,'No|Stampa Locale|Stampa Nazionale|Stampa Estera'),('k03',15,'Testate','',1,'70|5'),('k04',15,'Presenza di televisioni','di emittenti televisive che trasmettono la competizione',4,'Assente|Locali|Web|Nazionali|Diretta'),('k05',15,'Emittenti','',1,'70|5'),('k06',15,'Pubblicazione risultati su internet','',3,'Nessuna|Termine gara|Rilevamenti parziali|Tempo reale (ogni volèe)'),('k07',15,'Indirizzo internet','indicare indirizzo internet sul quale sono stati pubblicati i risultati, con esclusione di quello fitarco',0,''),('Z',15,'Note','',-1,''),('z01',15,'Valutazione della Gara ed eventuali annotazioni ','',1,'70|7'),('e41',4,'Supporti Battifreccia','',4,'Pali|Cavalletti');
/*!40000 ALTER TABLE `FinalReportQ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Finals`
--

DROP TABLE IF EXISTS `Finals`;
CREATE TABLE `Finals` (
  `FinEvent` varchar(4) NOT NULL,
  `FinMatchNo` tinyint(4) unsigned NOT NULL default '0',
  `FinTournament` int(10) unsigned NOT NULL default '0',
  `FinRank` tinyint(4) unsigned NOT NULL default '0',
  `FinAthlete` int(10) unsigned NOT NULL default '0',
  `FinScore` smallint(6) NOT NULL default '0',
  `FinSetScore` tinyint(4) NOT NULL default '0',
  `FinSetPoints` varchar(15) default NULL,
  `FinTie` tinyint(1) NOT NULL default '0',
  `FinArrowstring` varchar(18) default NULL,
  `FinTiebreak` varchar(6) default NULL,
  `FinArrowPosition` varchar(180) default NULL,
  `FinTiePosition` varchar(60) default NULL,
  `FinWinLose` tinyint(1) unsigned NOT NULL default '0',
  `FinFinalRank` tinyint(4) unsigned NOT NULL default '0',
  `FinDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `FinSyncro` datetime NOT NULL,
  `FinLive` tinyint(4) NOT NULL default '0',
  `FinVxF` tinyint(4) NOT NULL default '0',
  `FinTarget` varchar(5) NOT NULL,
  PRIMARY KEY  (`FinEvent`,`FinMatchNo`,`FinTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Finals`
--

LOCK TABLES `Finals` WRITE;
/*!40000 ALTER TABLE `Finals` DISABLE KEYS */;
/*!40000 ALTER TABLE `Finals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Grids`
--

DROP TABLE IF EXISTS `Grids`;
CREATE TABLE `Grids` (
  `GrMatchNo` tinyint(3) unsigned NOT NULL default '0',
  `GrPosition` tinyint(4) NOT NULL default '0',
  `GrPhase` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`GrMatchNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Grids`
--

LOCK TABLES `Grids` WRITE;
/*!40000 ALTER TABLE `Grids` DISABLE KEYS */;
INSERT INTO `Grids` VALUES (32,1,16),(33,32,16),(34,17,16),(35,16,16),(36,9,16),(37,24,16),(38,25,16),(39,8,16),(40,5,16),(41,28,16),(42,21,16),(43,12,16),(44,13,16),(45,20,16),(46,29,16),(47,4,16),(48,3,16),(49,30,16),(50,19,16),(51,14,16),(52,11,16),(53,22,16),(54,27,16),(55,6,16),(56,7,16),(57,26,16),(58,23,16),(59,10,16),(60,15,16),(61,18,16),(62,31,16),(63,2,16),(16,1,8),(17,16,8),(18,9,8),(19,8,8),(20,5,8),(21,12,8),(22,13,8),(23,4,8),(24,3,8),(25,14,8),(26,11,8),(27,6,8),(28,7,8),(29,10,8),(30,15,8),(31,2,8),(8,1,4),(9,8,4),(10,5,4),(11,4,4),(12,3,4),(13,6,4),(14,7,4),(15,2,4),(4,1,2),(5,4,2),(6,3,2),(7,2,2),(2,4,1),(3,3,1),(0,1,0),(1,2,0),(64,1,32),(65,64,32),(66,33,32),(67,32,32),(68,17,32),(69,48,32),(70,49,32),(71,16,32),(72,9,32),(73,56,32),(74,41,32),(75,24,32),(76,25,32),(77,40,32),(78,57,32),(79,8,32),(80,5,32),(81,60,32),(82,37,32),(83,28,32),(84,21,32),(85,44,32),(86,53,32),(87,12,32),(88,13,32),(89,52,32),(90,45,32),(91,20,32),(92,29,32),(93,36,32),(94,61,32),(95,4,32),(96,3,32),(97,62,32),(98,35,32),(99,30,32),(100,19,32),(101,46,32),(102,51,32),(103,14,32),(104,11,32),(105,54,32),(106,43,32),(107,22,32),(108,27,32),(109,38,32),(110,59,32),(111,6,32),(112,7,32),(113,58,32),(114,39,32),(115,26,32),(116,23,32),(117,42,32),(118,55,32),(119,10,32),(120,15,32),(121,50,32),(122,47,32),(123,18,32),(124,31,32),(125,34,32),(126,63,32),(127,2,32);
/*!40000 ALTER TABLE `Grids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `HTTData`
--

DROP TABLE IF EXISTS `HTTData`;
CREATE TABLE `HTTData` (
  `HtdEnId` int(10) unsigned NOT NULL,
  `HtdMatchNo` tinyint(4) unsigned default NULL,
  `HtdEvent` varchar(4) NOT NULL default '',
  `HtdTargetNo` varchar(5) NOT NULL,
  `HtdDistance` tinyint(4) NOT NULL,
  `HtdFinScheduling` datetime NOT NULL default '0000-00-00 00:00:00',
  `HtdTeamEvent` tinyint(1) unsigned NOT NULL default '0',
  `HtdArrowStart` tinyint(4) NOT NULL,
  `HtdArrowEnd` tinyint(4) NOT NULL,
  `HtdArrowString` varchar(6) NOT NULL,
  PRIMARY KEY  (`HtdTargetNo`,`HtdDistance`,`HtdArrowStart`,`HtdFinScheduling`,`HtdTeamEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `HTTData`
--

LOCK TABLES `HTTData` WRITE;
/*!40000 ALTER TABLE `HTTData` DISABLE KEYS */;
/*!40000 ALTER TABLE `HTTData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `InvolvedType`
--

DROP TABLE IF EXISTS `InvolvedType`;
CREATE TABLE `InvolvedType` (
  `ItId` smallint(5) unsigned NOT NULL auto_increment,
  `ItDescription` varchar(32) NOT NULL,
  `ItJudge` tinyint(3) unsigned NOT NULL default '0',
  `ItDoS` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ItId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `InvolvedType`
--

LOCK TABLES `InvolvedType` WRITE;
/*!40000 ALTER TABLE `InvolvedType` DISABLE KEYS */;
INSERT INTO `InvolvedType` VALUES (1,'Judge',1,0),(2,'Dos',0,1),(3,'OrgResponsible',0,0),(4,'Jury',0,0);
/*!40000 ALTER TABLE `InvolvedType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LookUpEntries`
--

DROP TABLE IF EXISTS `LookUpEntries`;
CREATE TABLE `LookUpEntries` (
  `LueCode` varchar(9) NOT NULL,
  `LueFamilyName` varchar(60) NOT NULL,
  `LueName` varchar(30) NOT NULL,
  `LueSex` tinyint(1) unsigned NOT NULL default '0',
  `LueCtrlCode` varchar(16) NOT NULL,
  `LueCountry` varchar(5) NOT NULL,
  `LueCoDescr` varchar(80) NOT NULL,
  `LueCoShort` varchar(30) NOT NULL,
  `LueDivision` varchar(2) NOT NULL,
  `LueClass` varchar(2) NOT NULL,
  `LueSubClass` varchar(2) NOT NULL,
  `LueStatus` tinyint(4) NOT NULL,
  `LueAthlete` tinyint(4) NOT NULL,
  `LueJudge` tinyint(4) NOT NULL,
  `LueDoS` tinyint(4) NOT NULL,
  `LueDefault` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`LueCode`,`LueClass`),
  KEY `LueCountry` (`LueCountry`),
  KEY `LueCode` (`LueCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `LookUpEntries`
--

LOCK TABLES `LookUpEntries` WRITE;
/*!40000 ALTER TABLE `LookUpEntries` DISABLE KEYS */;
/*!40000 ALTER TABLE `LookUpEntries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Parameters`
--

DROP TABLE IF EXISTS `Parameters`;
CREATE TABLE `Parameters` (
  `ParId` varchar(8) NOT NULL,
  `ParValue` varchar(255) NOT NULL,
  PRIMARY KEY  (`ParId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Parameters`
--

LOCK TABLES `Parameters` WRITE;
/*!40000 ALTER TABLE `Parameters` DISABLE KEYS */;
INSERT INTO `Parameters` VALUES ('LueUpdat','20090908103644'),('LuePath','http://www.fitarco-italia.org/gare/ianseo/Ianseo.php'),('ResPath','http://www.fitarco-italia.org/gare/getfiles.php'),('IntEvent','0'),('SwUpdate','2010.01.02'),('HttMode','0'),('HttFlg','NNNNNNNNNNNNNNNN'),('HttSeq','0103011006'),('HttSes','0'),('HttHost','192.168.1.1'),('HttPort','9001'),('DBUpdate','2010-01-20 02:00:00');
/*!40000 ALTER TABLE `Parameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Phases`
--

DROP TABLE IF EXISTS `Phases`;
CREATE TABLE `Phases` (
  `PhId` tinyint(4) NOT NULL default '0',
  `PhDescr` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`PhId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Phases`
--

LOCK TABLES `Phases` WRITE;
/*!40000 ALTER TABLE `Phases` DISABLE KEYS */;
INSERT INTO `Phases` VALUES (0,'Final'),(1,'BronzeFinal'),(2,'SemiFinal'),(4,'4Final'),(8,'8Final'),(16,'16Final'),(32,'32Final'),(64,'64Final');
/*!40000 ALTER TABLE `Phases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Photos`
--

DROP TABLE IF EXISTS `Photos`;
CREATE TABLE `Photos` (
  `PhEnId` int(10) unsigned NOT NULL,
  `PhPhoto` blob NOT NULL,
  PRIMARY KEY  (`PhEnId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Photos`
--

LOCK TABLES `Photos` WRITE;
/*!40000 ALTER TABLE `Photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `Photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PrintOuts`
--

DROP TABLE IF EXISTS `PrintOuts`;
CREATE TABLE `PrintOuts` (
  `PoCode` varchar(25) NOT NULL,
  `PoType` varchar(1) NOT NULL COMMENT 'P=Participants, Q=qualif., F=Final, S=Stats',
  `PoFile` varchar(255) NOT NULL,
  `PoFunction` varchar(20) NOT NULL,
  `PoTeam` varchar(1) NOT NULL,
  `PoOrdine` int(11) NOT NULL,
  PRIMARY KEY  (`PoCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `PrintOuts`
--

LOCK TABLES `PrintOuts` WRITE;
/*!40000 ALTER TABLE `PrintOuts` DISABLE KEYS */;
INSERT INTO `PrintOuts` VALUES ('EP','P','PrnSession.php','PrnSession','',1),('ES','P','PrnCountry.php','PrnCountry','',2),('EA','P','PrnAlphabetical.php','PrnAlphabetical','',3),('QSc','Q','/Qualification/PrintScore.php','','',5),('QNr','Q','/Qualification/PrintBackNo.php','','',6),('QI','Q','PrnQualClass.php','PrnQualClass','',7),('QIM','Q','/Qualification/PrnMedalInd.php','','',8),('QIQ','F','PrnQualAbs.php','PrnQualAbs','',9),('FIScA','F','/Final/Individual/PDFScore.php','','',10),('FIScI','F','/Final/Individual/PDFScoreMatch.php','','',11),('FIN','F','/Final/Individual/PrnName.php','','',12),('FINr','F','/Final/Individual/PDFBackNumber.php','','',13),('FIB','F','PrnBracket.php','PrnBracket','',14),('FIR','F','PrnRanking.php','PrnRanking','',15),('SMS','S','PrnMedal.php','PrnMedal','',16),('SI','S','PrnStatClasses.php','PrnStatClasses','',17),('ST','S','PrnStatCountry.php','PrnStatCountry','',18),('EN','P','PrnName.php','','',4);
/*!40000 ALTER TABLE `PrintOuts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PrintOutsRules`
--

DROP TABLE IF EXISTS `PrintOutsRules`;
CREATE TABLE `PrintOutsRules` (
  `PorId` int(10) unsigned NOT NULL auto_increment,
  `PorTournament` int(10) unsigned NOT NULL default '0',
  `PorRuleName` varchar(64) NOT NULL,
  `PorTitle` varchar(50) NOT NULL,
  `PorPrintOuts` varchar(255) NOT NULL,
  `PorEventQual` varchar(255) NOT NULL,
  `PorQualCollate` varchar(1) NOT NULL default '1',
  `PorEventFin` varchar(255) NOT NULL,
  `PorFinCollate` varchar(1) NOT NULL default '1',
  `PorVisPar` varchar(1) NOT NULL,
  `PorVisFin` varchar(1) NOT NULL,
  `PorVisQual` varchar(1) NOT NULL,
  PRIMARY KEY  (`PorId`),
  KEY `PorTournament` (`PorTournament`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `PrintOutsRules`
--

LOCK TABLES `PrintOutsRules` WRITE;
/*!40000 ALTER TABLE `PrintOutsRules` DISABLE KEYS */;
INSERT INTO `PrintOutsRules` VALUES (1,42,'Book completo','Results Book','ES,EA,QI,FIB,FIR,SI,ST,SMS','RBSM,RBSMT,RBSW,RBSWT,RBJM,RBJMT,RBJW,RBJWT,CBSM,CBSMT,CBSW,CBSWT,CBJM,CBJMT,CBJW,CBJWT','','RM,RMT,RW,RWT,RJM,RJMT,RJW,RJWT,CM,CMT,CW,CWT,CJM,CJMT,CJW','1','','','');
/*!40000 ALTER TABLE `PrintOutsRules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Qualifications`
--

DROP TABLE IF EXISTS `Qualifications`;
CREATE TABLE `Qualifications` (
  `QuId` int(10) unsigned NOT NULL,
  `QuSession` tinyint(3) unsigned NOT NULL default '0',
  `QuTargetNo` varchar(5) NOT NULL,
  `QuD1Score` smallint(6) NOT NULL,
  `QuD1Hits` smallint(6) NOT NULL,
  `QuD1Gold` smallint(6) NOT NULL,
  `QuD1Xnine` smallint(6) NOT NULL,
  `QuD1Arrowstring` varchar(36) NOT NULL,
  `QuD1Rank` smallint(6) NOT NULL,
  `QuD1Status` tinyint(4) unsigned NOT NULL,
  `QuD2Score` smallint(6) NOT NULL,
  `QuD2Hits` smallint(6) NOT NULL,
  `QuD2Gold` smallint(6) NOT NULL,
  `QuD2Xnine` smallint(6) NOT NULL,
  `QuD2Arrowstring` varchar(36) NOT NULL,
  `QuD2Rank` smallint(6) NOT NULL,
  `QuD2Status` tinyint(4) unsigned NOT NULL,
  `QuD3Score` smallint(6) NOT NULL,
  `QuD3Hits` smallint(6) NOT NULL,
  `QuD3Gold` smallint(6) NOT NULL,
  `QuD3Xnine` smallint(6) NOT NULL,
  `QuD3Arrowstring` varchar(36) NOT NULL,
  `QuD3Rank` smallint(6) NOT NULL,
  `QuD3Status` tinyint(4) unsigned NOT NULL,
  `QuD4Score` smallint(6) NOT NULL,
  `QuD4Hits` smallint(6) NOT NULL,
  `QuD4Gold` smallint(6) NOT NULL,
  `QuD4Xnine` smallint(6) NOT NULL,
  `QuD4Arrowstring` varchar(36) NOT NULL,
  `QuD4Rank` smallint(6) NOT NULL,
  `QuD4Status` tinyint(4) unsigned NOT NULL,
  `QuD5Score` smallint(6) NOT NULL,
  `QuD5Hits` smallint(6) NOT NULL,
  `QuD5Gold` smallint(6) NOT NULL,
  `QuD5Xnine` smallint(6) NOT NULL,
  `QuD5Arrowstring` varchar(36) NOT NULL,
  `QuD5Rank` smallint(6) NOT NULL,
  `QuD5Status` tinyint(4) unsigned NOT NULL,
  `QuD6Score` smallint(6) NOT NULL,
  `QuD6Hits` smallint(6) NOT NULL,
  `QuD6Gold` smallint(6) NOT NULL,
  `QuD6Xnine` smallint(6) NOT NULL,
  `QuD6Arrowstring` varchar(36) NOT NULL,
  `QuD6Rank` smallint(6) NOT NULL,
  `QuD6Status` tinyint(4) unsigned NOT NULL,
  `QuD7Score` smallint(6) NOT NULL,
  `QuD7Hits` smallint(6) NOT NULL,
  `QuD7Gold` smallint(6) NOT NULL,
  `QuD7Xnine` smallint(6) NOT NULL,
  `QuD7Arrowstring` varchar(36) NOT NULL,
  `QuD7Rank` smallint(6) NOT NULL,
  `QuD7Status` tinyint(4) unsigned NOT NULL,
  `QuD8Score` smallint(6) NOT NULL,
  `QuD8Hits` smallint(6) NOT NULL,
  `QuD8Gold` smallint(6) NOT NULL,
  `QuD8Xnine` smallint(6) NOT NULL,
  `QuD8Arrowstring` varchar(36) NOT NULL,
  `QuD8Rank` smallint(6) NOT NULL,
  `QuD8Status` tinyint(4) unsigned NOT NULL,
  `QuScore` int(11) NOT NULL,
  `QuHits` int(11) NOT NULL,
  `QuGold` int(11) NOT NULL,
  `QuXnine` int(11) NOT NULL,
  `QuArrow` tinyint(3) NOT NULL,
  `QuClRank` smallint(6) NOT NULL,
  `QuRank` smallint(6) NOT NULL,
  `QuStatus` tinyint(4) unsigned NOT NULL,
  `QuTie` tinyint(1) NOT NULL,
  `QuTieBreak` varchar(16) NOT NULL,
  `QuTimestamp` timestamp NULL default NULL,
  PRIMARY KEY  (`QuId`),
  KEY `QuSession` (`QuSession`),
  KEY `QuTargetNo` (`QuTargetNo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Qualifications`
--

LOCK TABLES `Qualifications` WRITE;
/*!40000 ALTER TABLE `Qualifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `Qualifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reviews`
--

DROP TABLE IF EXISTS `Reviews`;
CREATE TABLE `Reviews` (
  `RevEvent` varchar(4) NOT NULL,
  `RevMatchNo` tinyint(3) unsigned NOT NULL,
  `RevTournament` int(10) unsigned NOT NULL,
  `RevTeamEvent` tinyint(4) NOT NULL,
  `RevLanguage1` text NOT NULL,
  `RevLanguage2` text NOT NULL,
  `RevDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `RevSyncro` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`RevEvent`,`RevMatchNo`,`RevTournament`,`RevTeamEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Reviews`
--

LOCK TABLES `Reviews` WRITE;
/*!40000 ALTER TABLE `Reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SubClass`
--

DROP TABLE IF EXISTS `SubClass`;
CREATE TABLE `SubClass` (
  `ScId` varchar(2) NOT NULL,
  `ScTournament` int(10) unsigned NOT NULL default '0',
  `ScDescription` varchar(32) NOT NULL,
  `ScViewOrder` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ScId`,`ScTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `SubClass`
--

LOCK TABLES `SubClass` WRITE;
/*!40000 ALTER TABLE `SubClass` DISABLE KEYS */;
/*!40000 ALTER TABLE `SubClass` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVContents`
--

DROP TABLE IF EXISTS `TVContents`;
CREATE TABLE `TVContents` (
  `TVCId` int(11) NOT NULL,
  `TVCTournament` int(11) NOT NULL,
  `TVCName` varchar(50) NOT NULL,
  `TVCContent` mediumblob NOT NULL,
  `TVCMimeType` varchar(50) NOT NULL,
  `TVCTime` tinyint(4) NOT NULL,
  `TVCScroll` tinyint(4) NOT NULL,
  PRIMARY KEY  (`TVCId`,`TVCTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TVContents`
--

LOCK TABLES `TVContents` WRITE;
/*!40000 ALTER TABLE `TVContents` DISABLE KEYS */;
INSERT INTO `TVContents` VALUES (1, -1, 'Logo Ianseo', 0x474946383961f401c001d5000000669966a4c23083aca4c8dbc0dae684b5ce5499bbd6e6ef94bed4b3d1e06ea9c5e0edf34f95b878aec80f6f9f62a0c08cbad199c1d6408cb3b8d5e3ebf2f7cbdfeaadcede7db2cb8ab8d0dce9f162a1c05a9cbd7aafcaa7cbdc4c94b72079a6d1e4edf6f9fb5296ba8fbcd3b5d3e173abc7c5dde86ba7c49dc5d8e7f0f6dfecf2cce0ebbcd6e4f9fcfdf4f8faf0f7f9ffffff10709fffffff00000000000000000000000000000000000000000000000000000000000000000000000000000021f90401070032002c00000000f401c0010006ff409970482c1a8fc8a472c96c3a9fd0a8744aad5aafd8ac76cbed7abfe0b0784c2e9bcfe8b47acd6ebbdff0b87c4eafdbeff8bc7ecfeffbff808182838485868788898a8b8c8d8e8f909192939495969798999a9b9c9d9e9fa0a1a2a3a4a5a6a7a8a9aaabacadaeafb0b1b2b3b4b5b6b7b8b9babbbcbdbebfc0c1c2c3c4c5c6c7c8c9cacbcccdcecfd0d1d2d3d4d5d6d7d8d9dadbdcdddedfe0e1e2e3e4e5e6e7e8e9eaebecedeeeff0f1f2f3f4f5f6f7f8f9fafbc119030d00030a1c4850e0880414d0504830a2a0c3870d069878b5b000c48b010b0cc8c0af100b0d1e428a1ce98101031128519a24499243c2311446b0246932a54a0633451a60b10a0487ff9c226bda5c0954c3c48e8064023d69c0c086a750373415813327cf302c0c2c15d1342ad4a6064400e5902a01d0925c9d7a951ab6eacc1148fd28c8c914ea83071af26ab8fbe0e9d49c09c0b058da75035fbd7bfbfaa53a53c1a90840d33e3dacf7ee57b1330bc4dd037926d7c91a02043841baf409d17bfdba1d09c24b06ba5df18a1e6dfa7480d4523193d44cca046cd0a26b9346ad38eccc0e9bf18098c940ed83e00a1494984ebd8482e1a98d93d4e0e527cbcfb2494baf3e3dbaede2ab43b61e05f2bbd3f027c693372f5a3163922f93d3513ab2b9e1d0f14dd700070416c84103e59da6815f2c55c0c56bdf3d05e0780019482082d629d89701e9f116ffca602ca935a180165e98e06d1ba617817e7568d5df7ba34937e005171460e38d05d0786086d99104d7169d8df49e060196c0018d38dea82382d705902249a31440138c01ce586392395eb063934f8ac41d8b724018149525cc6823061840a0a69a68daa865864e4a3592635b7827920886c568e49568aec926066e6e799b54abad078a8b3ae519e09167a6e9679b39ee785a97218129c77242e629239f238c80c0a7a022300204801620699cba79c0859d1ee0f9dc0965329aa6a7a17eda699b17303928872239180a49ae8e16eb9910d05aeba8a5e65ac2a4728a64a8a56d601a52b0d2c94a6b0411a0a0adb6d88a4a6a8e089e46a848ab8eb9019165d638ff2b02d86ecb6d04b602faa60271228adcaf771af06aba05acdbaebbdd8a2a6fb8a83a0bed1bd2fa276c033516cbaeb6030cd04107110fc0adc0e05eb720a2e586e414910a30dcafa7d9a250f1c415a300efa8912e5bb007f782622eba22634032c412a36cb1ca182b2b2eaf1e3c7bb01ad27e0cabc8c5962cb1054c378d32cf2300ca81752f774cad91233f9c73d34e47cc3304e02ebb7148317f72a7a625a87bf3d25c334d31b72ce7aa71b3420f8d06a60ca0ad76b64b27900009134c40020909b83dc0ca52bbdc6cc746636db3d64cff0db8e0845bf076047153dd9758657b1292c2b07290b5c91d443eb9e07e5b6e31e689d3fb00af75db6d06a68d8b6eff33dfa503ce0201bc13c0020b95530c2fd872175c6edecf85dcf0c3a5fffd7befbe0baebaca519b2ab69c9d7732ada62293dcf704cff7cec20485bf8df1d4272c2856ecb293b19cc2d22d1fc1d224ec6e820915e46f02efe45b4e7de2e99353b9f0842edb8d205bcd031f01f0a7bffd016f7a08205e035cc7a1ec71c263e70a5dbf986701c0f18e8115b89fef48a0bae1e5886a72625ffbc4b01c571d6d6408eca0fd5600821a826005212440ff2c26aa8c058050e5724a00e257000820c0641648c0041658011ada70050e246107fe67bd136cce829b2889be86d83dbe257189f873620d73f8c029b2ae787d61800a5708861666106b4644a21209d044101cffe00019b8e30df7473e33560f417162c0aa9a933c86dd0e05a5036305ec88c73cdeb002be2b1c0fc186be8d615113ad328c066f27b1399a808677cce301f6a843499ab0921b10c11ad9e885e5089188074464071748c30ca86001b87464081f38c92aaa6f552e8423bb0690c4dd2d128fb85c800a3240ca09f8ef8c13fca1012e99894c16b088472466fdf067c75be27299a494220a7a7801c5196095ace4822b37c0c50b1cb203733ca60a5240817aa64005a3dc9f382378c2008a00986f145d1cb5494b64d293022958003321c98204082f6a689ca6283cd0b81ac532910bece641118a4f1040b28fff2b9e54d0994e2d8080905c84213113c0824fe2919eff2f70c10b28a0508f96d28ffd942640d165d1182e719119580005623ad37b1e008abc8417a0a2f93a6a6282a2ec8cdf3be30982a00e55a6086526521d7a38884673a4252dc349b718b20dce4f862e9de70b42d0821064d5a6203d230a0db0aa8a16e0a254cd003d5dd08216bcc0a8203041521190382749f457af34245e5b5a4b7ab235047fc5e7562dd0d5a552f00061751f4acb8a8161a2750507502b5b5df0d68f3a947a27a49754eafac69e2232af42e52b648d8a54121e8eb0d633ac532f01d5764e95b155756c5b237b54c19af28f14246966ad30d642aab4982ed5eb55675a53d3e2b49c3fdb29ac5c8bd1632e200544cd6a71076b59dd4ef49569bbff2b02613b5d9a6ad5b80fc58048cfb95c16f6567953fda946eda9d0e212609fc4bbde3feb44400d0e7496d1fdae3d3b6addca4674b796802a4fef7a448c9aa09b0a4e0160ad1bd2afd2b7be6dbc6f7a2f8a60507a7399a3dca524c749490103f4019bf42c4be968475b7ef3bdff7de608522b4d0857c29a06ce265a8f69e3feee91972ceee7c6940b6229b8f19a463cab274179473de6d0997e2c6f1a074956c5c6709b8b64a41ea1584aca261995879519f23679c04e8291ca556e26574f29602637190a6e8471996028cbfa31518c38e4e38af9594ed56a2588514def813d59c71a067a843a06e01527bad9ee0999d18026a338a1e9ba0da8b114146041c5ff463db1529bbad4a38e9809f2038bf7a1577ef45be202f127c2f1958f8a6856559d80ecb858ae5481f70b61ada578db005390013ea60406aff9ce2fda8fd6fbd321b14d586869066d142c980b4d3e6317cb44a52d2db173286817d0d17d2f7cd10bded77c98465d6be1279b553407ffa640defdae7f97ab5eaeaf22b34c1612d6cdd39df86c0d41af0af8daa1d0f68b9c139ad9cc262fc5b98f487ee40abc91b5d7b833dde00657bed59def7a5af9d2bb971d3a77ae2de01bafdcf458c7e3768b3b1289129601b586f28d176ee5fad61caf54000a0ab46724e069b878a243f4e1a0283724a1932b1205b29a1d1077a5e3dad31067bd4e8bc54359500af286d8ff6b9a475dea3bc39c04411e12567bc23b579b39d4db36f59e4d30802e4af8942414230a0da43c5c52cd48b0be0a6d838e5f8f535aaa574775b9c13d2481d9028824cc75db45996f83bf58f57c264d9c881c141d607a7c9cfe3093451e6a0303394ef8be89ccbf4851143a1292b2a4250c698c521e380a2b161faca3a9cb61ff7ad7cabe75a0b9f18aae5d70917f4086b522922cf72adb7de8ad4eb65150c05c0bbbfdf14bf62e6f859e599879b92310a545d4ef894f680abf9b949577a07940e9ac50389944d62f871d0b592dcb3b6692fd04b34ccb68d592beb14005ffb075da4597170a41527bb1a27feff72da642301b121264f10915707afb3220c4d229ff14d8296c12290ad82c21c111ada002c247267bd2288f522afe877d21817e5cc01f7892419b027e231828d3c12cbfc773a5a07edec77ee1f782f1278338610066577a99923c80e77ed85284f0627d61638221c16face0819f432532a27a588281e53128a9c48062a08274072b05782538b22470f22406e02ba6608378512466822560587e9647839f602704c879ff326ad5377904332e30030b14e01dfe712e7a52260362200071221b22166916061de022e0f187152288aea721e3c2016ef81867e38768188805328870b220e34271a060278d83342737316e43311e6787bed77cb1000223001241471bd1311fd7018961a100087106093017dc0620f1211fff78871df6b10123a07d9a40011da08b43028bbe681db3787461a1011130899fb83dffc6676ce337d8583ef9d63a05437f77f68d4ae01d42c46619476fbf736fc1c343397778de088eee5804f0c63d16954db9236bf7536bd2233c84c65415f48efe28053f51608ec3417e16462b70909a1669be944aedf88feef813af46614854904ea4477266668406720de990dfc801956672ebf56635a60228164e91365f1bc9914de6919a349052d65240354f1aa64c291649f165111494922a595f1c2090022564249051a10553459547b545594a55457da1933b99593d596e71d45d7624546b355b0b904f39d64be8b3944dd99544f09450566116a05f56c5572d405aff571958597966cbc2955ee995601964b2a45f07409521305b1da54f18d96256b4014cd909146043823998845998c4880586999889c981ada6988e29983ff8893e894d13c95843594ff5d45f47795b92e697b230003fb71426911e33d100b2370620202567f139a3b99a1a3000ad906dabc99aa4c9120a009ba3c0927a663b320693183693ba7453958593e6f50a26c07d2cc11460b19ce096130d2806ac929c69c19c53519b1ec384a590010ac71cd3499d12c7121a70988de0915d069273494b359601ea996282b5693b8692afb078d2a91693c1178ac116a922248cd9053e171985519fdeb618d6b922a69001c80974ff699ff7991bd6899d9c009189b63cff72a44884796573c64f68f69796e01bdcc9700ee77089b11833017c5eb09d085a9f0df7a147c7a033e1a09f4001077a7fa091a2208a1bcd4912e2a90801596ecde6410c849022b44354142e93d60a31ca6dcfa18c44671e90888723e1895a1024a7f71f4abaa446c789da212491d909d1f984c031744bda8c71e2a45e328d1f894df39340bb336bf6866feb566d84a2a194607ac04225972810d4d124d9919f1eb0a554f07cc9998c45028878ca8ccc72853e320a6272a2e1617777972132c8a72e8a09e2c8a39e423a493439f7863a2524303e3336723a09a1894196482234e2853ab225914a12a12a03f6c7a87a1288abc77a3be2323d2224a3c01ff96289ff5178aa4ab2860af214f989829a807668a376989a44d86838a05775868513ad0a09801a84b1ca28fd127e904285f4b231a9f29c59a09abb0a20057823d80a2934c224324812fbe90926fa19c49786e56aaeaafa32e4e209e08a7f72e879a7668acd5a6d0b98873d012cde272bb3622c16982c53e37b6e41a259c02ac3c77538582c9d622b074b854aa81e1375a2c467ad05cb7fc8222f097bb108c70974fa7772487dee0235dff2a97838a9a4202dad328efc627c9f628445282a9317b21be3161db36c8d277dec62b347087f94b739229178fde6a51bab36352bb4b6023609287a06d309301b879c327d01732b36722a465b76ac9030f8cab47348872abbb5ff9a83a8eed6b0672384a243b3829732c9e776520bb0490b556cdb7e37e37970db2d2bfb766383b19e109a20c8b1125b8108382f17ebada9202d5723b6fafa754ff33500f4ac95b26bdd2773f20331a4588a5e53786ff7afd15a086bdb78eda7355bc3b9a7c88d5d9ba388209ffef12abd4aaed9aaade9f3af801bb097cb6634a7ac1c777397b38f6497b65850a93493b9d72839a9f3bb39776c746b3624c72f5196ac9243381dd7ace8537923db09e0da7d8d1a2baa77aaad87ae56982a04fab5d4687b1496a6990a3ee7383ec4f6a63a8713c7737106548e81d3be9caa8f06e74fcdeb3925218f688a441dc4bee798bf6ffab957c8ba89109d2bf82a830a88ffd5a1a785f8a4af406e3217c06a3a6b2234422bc672a8241603d4920d608df0e4671aec406e4a6d73856c195b3bd698a96b7a8f287c6b4979bd29240a521ab35f0aa6b3d8a4e6670048db0aef238f87f46b7f76908f666be6636cd68668171c659d54904d84c4166a669cd6972210ba8480415c27a16ed6520609a465b63a5e756c0aac083181287561187841a31017715e12017e8a0a4f166369ca683514678156461d766c5cf66f3d6a4c70e66882a68f7ca93e5a3c08ffeb5c81cc4d62364685cc990ba94aa6a002825965989cc9992c98d258c1bdb55d94a94dbe89472459920c654ab8b595a934482dc99b774c97b644928e7494d73557892c08bcf693ff2f4963a42ccbec896419097767fc8eebe45b784597b7a4610965947aa9952e4660500994c01554ca4c9370755a9cd663132590129a3b0545cdc0599358865a70fa616fe904ae04c86d064fd34c954595961fa5634ab65ad03c61513996bc3c4f43455defd55048895bfc78cb812062dc055b44e95e36d550f1355fc3ec8ecd95529d7556db045aa27596a57553e43c57ac3561c74c6365395cb4d59eff5c584d755e51e565af45967bd5561c355ec7255ffcd8d0e0f8d09ce559133d942fd00230e05629b050182d5786b6d1a0dcd140b55730e0578025d2923c35c5293389659eecfc49c145012170d4c43559c3c98f9875cee85c69cfc55269b5579085d0ff1c86393c46cfef665724469676495a214d5ec459d28815a1f9e5d16d2d5e5bb5d031cdd5e87c5f5ed649c654550ab660e2ec3fe40472da254c7d2694facc5f0b055f599d93262d2cdc85cff2b451cb7ccd59c6d07cdd04c50c4b3ef5cddf9550e0b4996ce94f89fd939529d5a135da4656d665accdbf32990736d15595cccac4cc3a24cfe52cd3dfb84e615999bcdc48b31cc935dc96ab5c275bb7679c044f2249dc56a6d449865db22d33c1e4ca6e869ed03d4691b49793ecdb779667e4d867302946840c69640cd37eacdc4f2d912b354383b9c758b6d40226d080a045eafc65f02d987bec9e9d49c99dbd04ae568d97fac54c943ffa036986bc9087b66bff2e1cd1e43d6b084e4642eaa94ce569f6fd075c1c64ea2bc5131e455c35ddfcf869a390000d309ff4e915d5b91b2ebbb8cf5bbf7264c2d0e3be0507d3c80dc2bb462d9686a902073d1c6cc5eb686d41ecbf02a958f438c06b2a3e9c9ade0c9ebd9e9015eed16d88f1c67e617e216114ad468ded14c07d733a81938eb8664ef26bb9320be35d1e38680ee6d476e1eb93b1f166e60187e65f5ebd6bce7ce02d08baeaa5544a1bc2411c6c911e2d0e6a9a1782d1cb36caeabb9d8b8a58ec018a7b05c65a8d8724bda9c3acc333b9e372e782d01e616b7c6b77e89c7bc0c12b8051dea8cb98a7c088742311e8a4d01e6957ba82776a9d2bb7708713e59b05f6377cffe81b788f8b6a611741aa7b850cfb095a27b3a268bab04e78fc54450543ac9a00b3dbc3ab24f2bd5a52abe5972a3ee80a740a23d56223b887b2dbd22d76e8325ddbc95630ad569b35c8f7edbb67b6a9e80150ea098cab28211878de5e87c493b0f58278d30874ff51255238853a52859c982aef8e0a30aab48b02b4411b304858b4cdd2e858a0851168800b3fb42babb37838c7c59a284258334468844f0bb254b3b314050a8bea6f7ac2b1053b2aa4922ce83af023f10aafeaae55e28215f8b1165b2f3da8f1548028eb47b837dff26ef2f2a89ee181e084dcbb30045bb807ebf2238f8764d809748af247d3b6ebd2b4fc77f13ce2a451bf0a2af831c2f27dff384282b4abf31445ee5980f4af5bad5ed82f5ff8264faf1d050f0a8bb7f6fedef6641ff0b68aea43ce09fc81ebca83b7419bb2710b7f180f3446bf08b40785d10ebed38eaeb53b2e1a80f65aa0027e27a85128ed8f4f88459f0a6acff8deebf826128648b713a2008a2c98afbbceaf92ebac5d9bf88b90c6dc3b21a56eea87ca211a90f81f41aa0e5cfbbfe88c6231023c8f7920d187bd4f1e118c1daa610073ffa09f73ace89eaccada35d62be6fd5bc10ef5757e9372dc4fbd3767392c40f961106a9b7be8dd9f72936e391d80e990a0022c50fedb7ffe36f7fdf0c4fe8440bc25a7eb51476f694ee7c0cbbf40d0910d8945e31199542e994de7131a954effa955eb159bd56eb95def95e361181e0145e952802022a88e25416211e874168b94b00c501104a480a3442160c38041e84b7191b1d1f11132527292f22b4c6443e3a484a30063a46d006e82cea4e2b4c2848065428f2f6204e3a241e1e4614324b17297b7d7f71738589832cc602360336d0d453481d47405441a642595a0b563c00f50f044c35077587c9cbcdcfc1c7d8a63ac4ca1e1e2b3ed4dce14e4e02023e31e2415cfa2a3cf880217061532102e5d42850b1936744865dd86079b3a616033a043823915ec6558f071813e7ed7126483258b568007071fb674f912664c5f1c304de4a40601330bf456e05b4001688a90d44cf8d3860043a012b6362094fff9146a54a9538dd0cc4411a74e1204381e5890e2850b172f52a838004215893d28fe2865ea946a5cb973e90eb3aae9e6b237a4387aa4e0a2458b1714169c4dbbb66db75b70eb3676fc18b296bb58f55ae00bc2ef8bc0830ba325a0964fe2a58b2397367d1a3592c979738ada6ac29e8a14145e0c2e7b604551d06cb98d6e9ada0a85040d0c78306e9c810803c6363477be5c0483e31e34146031c9c40805d3c52867eebcf9720322a61b68908002f0282c0a68e09e7c39f8e7e3a51f378f5e7d550612295fdcc9c284683c4a81c0b232e0a7a8928e4a2aa59518cb4f8911b8ebeebb071ed000430d2cbcc59043b8d3e03a474c70efbdf824b230430d393424ff3aee46807089048ae3cebbe6364cd1c2e7c8e36e00186558870c331af004148c9c2120407c545852a4dcaef92720940871d0c72632d88e46e62e0c009913bcf492cb00340cafbee35e64248209c730664b2ebffc92cb3159fc30832a8b90b0441b3570f34d30c5e4703cee1480b19863dc490314ad368ae69e7d9c64c115de065acaa007ed9461c6e3c6b0d14d05ce2801d4506b39e1cf39a73bf30b3ca7534ea23dbdfc3454503d25552532cbabd3ce30b26cb54b58639db5545ccaf460d0fc0ac5ab933522c0e83f242b88861a6bfc01e824b7bec9e5d22474d5944d573f6d808370c5e5a0015949fd0617ee12f8c2840931c904996fc11d975c734bf5f0ffb8627dec6042e6bc05755e7acb1de4dc5bf035ae00633da849487846682d23394a3181623bd4ca46d259a63cc4521f2bc8b38c13ce00f782340a3819e592ebad65cc4c3d30e00b128fab49934f392819e59c552eb7965a0ff62044185dde34e4916f3639e7812e58995483870541bd303645e6a678989967823bb466450f8cabede69be23a86b180e988a69ae49331c00082b6db5efbe4a509f679baa0b5f8785589e22de11db5d9761b02b807aaf75c43cad437bf04b86373ef9bfd063cf0a4062fb769c3cd8cdab89a44ee5bd9ab2d808384d0f3f8dceb58269d32ddb1215c5cef914dfee46104649f7d84c04f26bcd61d3d48980b5517d67b73c7d98e7dff76d9478825a9590a0abbccf420dcf6f790f9eec41308882fbe76c995af3c539881d3756a8a94610385abdff8fc1faf610164fbb08d535d3d10f26e8793343e913d8236ca2f3f7f04b21f887206a98f06bab0ad4de1a56fc36383fef6d73fff254f10a8cb14d45637b3206d2281d65be0fef8e7873f242f80b7d09df35093a6ccb5ae7e6a7898fe06d04217f2ef8183a394088db3ae6c15417ec83106da94b1c2f2b5b003d968210c4da7316f18e23805b4cf550e4524f2310388422422fbba913be3540046dcba0adfece7432862448a7d88e1f604681c0aa28605e541a1f08eb7c036e4af7fff531eea74a7821be2708908ec210bcf87bef4bd228611b4a2ff0794183d3354cd8760f423e986b80d002ec57d1e805f636686c22e928f598bfc071f02c23e5a1c714667448dd98cb127d7a9cd7ad73b9eed1e29c101de118f62b0249142f18604c4610213089d1e36e907d355118984e482ae4a8995fb85e2737120412e77b9c94e9ece8a93ac8b0e8f61cc87790e74cbd4e52d9df9b582ac443aa23c8d09a9e9aae9bd6e6de9149cdc666839e3d8ed8e392c26226bb993acd5611578e8da513c4947616e416aade31cf96c298739d48115177bc52f21894469d2e584ede8db0a99e58c3be0936bce0c6443c923ced30c8d71223b27ce52b634a6f9ec70b0c4e3d4ce30be65cd630e1443852a4802256f82f29f92f91db268ffe98651c4d414a950c5b442d34f95702c8b2fdb6245086acf52cc74a80a7524410881448f9a460520e59548f9d680797975603d6b59990c404295ca4f737ceb294c91042d69544315ad408c4034264025eeb0a55969c6339e350da2ace26251728b081f3a173104a989d7dcabb3dc0a57c0da341675bd0503ae8a55991d564fbd8295a746752fdd114ba54640eb52f53a8ab6da031f8e8a6b49045bae5a31a080ec6898d5f6e22cd4e6e32cd55845a44403cac2ca257ad67ce99102745bd5d6949fd6c245654fc3af5569094361f2d39f0045d6df02477e7845430112358fd36640052011493f76d3db6b1530adc9f28f46b8d21190a8e0404e9a0064a554a9ff2c6282a7cb700d80bae2918fc09728d3620b5d296508e6a2a6021d181749fda64e9d9994031dc062688f905d43bd83b647ea8a6c82721bcf04b62d0439a208d04b5afdeea4ad3e990d058472a00aecd624916d104b56374f44c9c332488acd8a596c969160234a7535308589bc0510c8d61db46c06807a9281d98485332e2609b5084c2a4394182f37f6e9653233961ee346377bf0038336765daa28f5900e93878631f31328b338be098a717d875c643b5be1c8881d924596f59f24c9e60521684108e0fc63d69e84aa0629a08db9bb662ecf063082f1709817a494a39a792a683e5486998c99d9083a0464314b6e202566d3cdf8007756f514f22c514ff8ff87270700b4a05d50e8170319d105b9f23019dd5d36fb0530a1be0da94133664b53a9c61756b31b34525c275300d4a20673a9b571ea8d1d78d5d9c62192f75c243830191f4ff6f2506e7de82a2b7a98f84d46a3b7dc5eaf80c5cb29d007b1c58c9402cc18d352d1f4767dcd6537c73bce909ab3900d806d6d6bdbc2b3edb7bb39cce29018e633500a3147b1dc1fada4784041f171b9078eef2cf63a27b5858dacbfa2f1b3703cc829a9f3c1595ee17d6bd9bbc525f902e07bf20421a637232eb14d98da67f6f637bc348f73c4a90c4d70dcd7c4adf133d0ff9b5b4a67aca1056ff9d4f1a86ed6f8fc198c6a94cd9f446532bf36b67a5e364cebb175a7235752ff603bfaeaf6c373256724eba86dd45fb1915cb58bc0e0542ff2686dd26d1c4b0c1ad370ac5c8b6af4e2dcd550db3d3171fbfa566905f6a684cd22b711a574c637f6f1f495ec0628ab77aacb8fa58abf08d62626d455c8d5ebca3dfc30772a5c9d581449a6a058547b39f18d2122a9f3ac48777f1a7bd35f43aab6378807f2eef9d0ca93b4f7f39c450f7afad1512bc40de27c4eb32035b1e304c7b86cfe1d50bfd0a4a85d6c496558139555d1656e3fa3187b60fbd2557cc8b0600460f56ab84a56ffb8d57f69f26f40044cb0ea1c22999eb0499944679fc488a1708afab0809896ca6130a9a0446774baa988be69faf22d2a96886a76cf01b389001949b004ff099c88cf477cc782c0634336643e3eeb657ac4ce7248a96a268140a196fa8874bc66a3fc2901c10039d6c88966509330e699d86f46262c3f2a897e34d007fd288800a9883e2992dc8f2e32c0653207baa46bba3004505450017085c25c307cb8a8077fc8855e288e408865820971aa2f8f44aa22942514c670880c10840a0c5fa0902eba878962f089e0300e7d690e41a93ecc0a3532404daaf0557c65560aa6395ac43eb830b4e68787aa678518a80fe2c87608877910a60bc8e98082e76434088e38c81221880275c7470c688730c87e40317f44d1976c878c68e86520640a89e65fba6a5cbcca5ec66a3ad21096ca6689a4a76f24b1788c478e22a87274ff079eb0006fa89089526878aec77858698ee8c6387c313514c708d1467852a918fd879570c7602e473d4890558a66a49246694eca69a6c3021fc305314b7a52c813fee66dd6896792f13808c80b2eeb5d6a868bd00970d6491cdd0968aac46542ea94ead11e23276e9011a58ec38ed4234f4ce99c1aec1eb54710dae960f891c280919ad09164904667ea657916713a88900be2f177e065132e521d77666edad13838c04eb431180192131c2726f36f260d3202f2238db4c82287311ab1e77f5a29228dc311550a4b42f216e72f17c38a7b3e0b55bcc0b94a101d71712a77912667f152ccb139a23260c24517e7065d0e061b4d839c5ef090d8087f5ab1125fff911425e8386c88c89ed219b7e4106365103aab653e2b281901279dd12545c657cc451185255f04710477852f11d32f13d1b3a6c3269f671bcfc07140510cc7708a64481f25e9ce3a40ab78854fe0245896a34c34c00ea90004a0073e32ab4b5033357587c6eea8022e6b4d4e644f66f30aabcb3ef05261302b035d0a8a82480981707d1e0901dfd134d863c190469d1cecfec625025af30a542002e885c1a6339d4a4a5caca3c82a6004b83367bc5372aa335c4660193127b8c86f033930024bc7a868c8398d0f3ff3731cacaf9a029059ceefa0b8af006fca7dee533f0f144177e112f863d3142b62806af62c46a3e84afa702f412f144313e258fae3a59aff25f0a285f652efee0c34434bd444d510af260a9924a6afb62e5a760be726c5ca6ef3446bd4462701485c2d1e8ce4329484e6004cbe34cfe36e94488b9411acc2edcacfcf04a4e40804c0fac1158c6d488d944aab5401ad4ebd2e6ee438ac36be8cde0a4f317ee3ce4060046ab146e4233ca0435006c03109532f91e34cd1143a86c50046003bb1800206004b76d334fb244ce4c443eaf44ea1800222604fe303454ef3376d4503aeb34a56234b6bab2f7e22d23803e2ca2be748a3c828007ae0544b2e24455444472684051b6100a670612a04547104507ec60338a0294d3553ced155247332c124308f035617a12d6955b32613300d467778e779b0342b24ffb5cdfe623308e35263344c49340a519555b6aa4ffc045055f032170124b5a84fab9556b27071faef11a0e71cf7862bc5e52cc58a260d2056b3402fffd15ca5125da9524c6ce565dad53420b5b4b6a22bbe222c6a6dd2220e4c7d035ae5420af3e4301353547ac667b4505bdda55be2456115b35e0d92291d815cc7f29060326964b267d6155fab402fbb85abe84f1dd7311f97b2ac8c25bdf615dc32cee102aed8eced59f31264e2755eeacf2cbdd262a1b5308dc35fe295fe5466677f3250f6b111b2d29044ca28d76620351254b8e795b6a02d15120c19f2691f326a97325b532322caa06a46efe7dc4bbc4e6ecaaaadbe904da586528b4492274fd6273fffd620bda70ba6d06a47f26427676bc3e6b30a7608661578347315dba81893321683e920b38002201183ba9170b1871a43087155f2347254e1fe8ebf6e4b1f6cce2802c2f0fc762af4f2800e296d9c36956a871a37927ba64338b3806d813670e9d1698f0775e1266eebb52317816d3bd11dda7012297114997390d6520ab2f20b33e8775d111cff70908815350a856ac6e76a2ecfafe08a6b4cc2bcd64ea548095e04571263c781fcc7748457165fb50b7c87611a179588277c8f916032d138da340b40b2989ae898de100e898879dd49a76017062f8985f8507f99137e31c758ad66af620a15a485f0528e8e40372ae29171bcf77e39a8836407162577473cffb27f89531589512e95b7884010710775094864fc10c90169d0038390729e300b32538593107d96500e01e80c6724641dc3fae8c7380beaa2106a9bd6c2d88009a9ce6a1bc5478530098c969093fc9080f997f5fc777300580c83c8892bb1766ea714df4911d67086970f02e7d3fb94e2f644100b30d09a9a4a3ec9f803bfc9434c582616508f8e4927402797986940a3ef0cc90382a1c205e769a0689897ba698ca46f470a298591109be0a0866dd093a2ee7dbe589624ea87f118fdba6fc0be4f92e5580622aa61b0cffceec90ed22f34648ca3d0f87975e808c3309316c986b7819367543afef8297228858d7394f3b899d4ef00cba89074cf8904509bf4ffd890eb337528599869abf7642a42896aaa74ed103c998a03d096eec999694a976a8f7da459954f435526388c8f138b1b4996cd78906c5926908fa710186b9acf0e34d97f70d88a9468fc06b9a248598847a78cc14f34bf0076d3acb4a8d7f142b4b57c03ef6298f2b8cbf2f80af30c1ad12ad49361022779175c4098122f788bcd18013d8072b305f4ae4fbf1ed4f74ea162b619fa66d97d10af9d796f994adaa4014b41068c9ec1a9602b5948d62ac7cace45e58bb772ee5aa8b9ed848b474fcbece84e411ce9ee26fa2554e0b9aa898235282e69071691117e73f052f8cef5d8aaf1a6c11a1a789e25eb10104fa015cbb4eaa17ac98bbe940b5b00da5895ff746c7d94e6ce0ef208acaaa42e8ddf5ae9e881e9e63ac0305546c386a95f2263d191274d77951cb295404977ae329e5ed0e296acbd3a02b7907ace068bf3624ba0d68ab818251f4462243a77e27ceb8bf55a4b9bace4beccd0bc8ee0a819e4da6d4b53dbc3502ea2ef7ab05d8225ffb16419ac63d9a970d20568e5d74e122eaf782febe4daafbf34ed282edd92aecff83a36de6bdee24a48effaa67b0dc7fc8d36006edaa2d4da8e2ad5d258bb60eed7fecd36022ea965ccb6f343695b322ac585684d526e8fd68bbb50d330ac48e0aebd6056b5a91b2046fbbc9a3bbff65ac7648dc75a0c41040e6dc5faba138fbc1f4d59856dd4284d4a3726bcafe0bec7ffaed904e4d3064dda48cdbbbf6e256efb25da3255b7ca53fc525470f52b13b70bb94dc9be2d499e4d2ca26cb53d57c4d00da0ac0ee6726c52b71b6027dc282afcd2be78bcd94dbffb02d23663d828dcde502d865354c33bedd9a2adc9a9ad5aa01c4272937bbbb55ae344585793c4eba2d5729acff6ebcfc0a2056080d0e4edc673edae176d8b1e7cbf975cd27cccc939b9c8011abb994dbbdf4cdebadbd444dc002edc0a5e2ec3fe1ce0022cbde90caf61843d48e9534355543ba43eeab43dcf0ac6cf7c490d3cd05a0060a50cd7ec5ac725a3cf933c5983edc3f3fcd868940b5a8f357cea35dc6bc566fb6c6bdb205c7bce917ca7770c28549bb6d53bd7adffd447cabc41878bcaddcc36c8adeb703cea2a8edf42cec7eda1e4ba34d041bcde08fda64d1bcdfb4bbaffaaae0d8f9ab97de9c836b9c3ecbfcf6de5883d7e323cd1198e406216cc0476b9754ec023bbd333ce49cdb6ee705d6dbb00a7938c99e3ee1e36f745a9adde833a865b7647f55be652ebecfa3d7b139addf323e1d6cdac79a223c2abe6423b4a277ee78c496c7b14bc9884ae21eb7395d9c119faf51cba7a21fa004548a8afcfac091a441f8bb525bae22d1ed6d56bb8087eee12bcaed336b38789f29819bae52ee27f9aad6f1ab2add9b44aba62a059f8c0899a9fbee7649d149cc5a44334b912cdaa76bedddbee72b5c2e5a961f0665af89088acd7ffadfce08ebf56005a1c4bc0b21ca19dfe9e5dcafc82389e15ea8d71eaeaf35e94af99ef4faf00376ac4be59ec4d03f904faed9d2af69eb98109345dec39f912258123bf62808f9b0dcf42bd008c19f97c94498f07740251279c62d883559898b5c98dbde98cc77cf1190297b708bf210693e780fbf8598c90593a94080075f97300349f86d89755daa128d99034d3959113969f788b85d08c547ff9b7cb8e5f5909a1bf9f1050f6675f21fe0fb1583f9306300ff63870a41fabab808e3fb8732a8afc79a97438f98c9d9e8aedd78b5c288b0d908a66a80ea9bf13c1100830234404851a203b1da4318218610a9746e914d818181e0f48e6fd82c3e231b96c3effa3d3ea35bbed7ec3e36ad0d623da3c4e8ac6a520241e29590c0e2e0da0384115709428043c6c886cc989716c31183c049c9470f841002611162e213e454d299c68606d75518ed579606e6c96f07d0e45141935399d4a313a424abac2ca8a18d472e26240e8f2f6ee3e41a0525911d7bdc272777b7f83878b53aad4d16a30f7fded1e21b9fb22542f363eb67a287c5b6edd692a74e6026ae7ee90294553aa5cc9622c9cbe7d786c35f0041001c58a231449a132ccde423906903d6426d1d9908a16e5654408e9639d712e5fc28c2973a617967642de52f78c083b44d380cd53c54aa18711df22985386cedfc892bb783da5664d65a496e246c8baa9494fa73eff7e9c41085b2d4ab06bf5884e825520abb2ad4cbd62002b366e816025a8a2d540732fdfbe7efb62add31622079d174d3e81824ad82a2cc53c54f8562ee986a55dbf3e2b49f1224a0e6657d629308e0e5b3c96bb7aadabfac205cf8db061d152c7282c165969b9bd5538b56a29ad35c246eb21c2dfe2c68f236f335970c8b7aae33a835ef7f76b5655b7e805d7d08e52ae11e142973e9df159d95c5c2ac87ab3f2267fb72272881f9fcaeb001a569aef4849837a65ecb9ea269f7cf4a9621f7e5951909c820b32681c52943d70da77bcf9e65a81d63d761e38cbeda3547bbab1d65b858cd477e016a2b9445a56c998b6098077c1a84081f5c4961571dd54a01eff772d9ef0228c8d5c08c975b274d0a0914722394e7ab2f8a7c1877735009f6704f67856862c8c631b3283f5e85e9403d2579f7568e1039396b7f91761006bf6d8a615066ee0987a657af3209a71aae9a29b6fde17a701199e98a4a08312ea466075b0a8669732c628e39b420a87a54b15d8344b9a6ceae11e90aa58d1e79fb27030130b952695e603116a90eaa9713a96df16b48193808edc998aea7dabfa09e8ab85f2daabaf60b0c05fa9783a89e99b70ca59070709c244c1a11d12bb26a6d2deeaa7791a483a13056be9c84032feb1eae79fae62976d382a6c278bb706801b2ebb2290eb8106dbfcea176bf7e29b6fbe04d44ba80a1d2c225f88758547d7ff6aadc5374202cc6acbc208020e4c70c1070bd801bd7b5190c0c302729c2fc70216d0810a3265bcf1c7f1797cb22722f76b1c002fc31cb3cc315fd0b2cd37e39cb3ce49cedcf3cc352ff80201048ca0af05045cbcb3d24b33dd74383e43fd32d0c5a960810212c41075cc1f307001bf4e831db6d86383a1b5cf53f305820202986d360328bc40b6dc73d3ed6bdb3ff3f5c2081fdcdd37000c5850b7e08313eeb2df52d3a4c203591feef70728141eb9e4937fd3380068bba4b8e59b3f4eb9e79f836e86e59887f3c2058c6fbeb9005f87debaeb918ffe12017ca75efbcb0ac4fdbaeebb8f1dbb92b603fff20749f35ebcf12dfbbe21dbb60b208104b4d70ef9fff1d353df6bf2de80807adf31744dc002642c40000a0f2ceff703d5a39fbe91d77383c2e1313c403c1a283060befaf7e3ff97f3fbf3df7fffd277c37d7deb1c2c2e003dad9d2f7f0a5c20d804d836027a0305da835a021968c10bb6cc815a8b01e9bc7181b6551083221c2192407037097c4f7607f4190049e8c217526d8267a349fdb4263f18e23087e1281fd462c0ba993c6083b9d321118bc80d05982d06377c4910a3c6002342318a6d20401253e8972642ed8741235abe504080211e470543d3d7bd8eb6c4b4118035fe9340d72e608133e68d8b0f58a3022ee045304a910d2f58e1cc94b806105c8001cf03c00724f000b881418c435b2423bf08061effceec0378348e051ec047997d40015a940908c607c9244ac08d23db8bd524d0b850c2711c2a1801032e1935ae21127d1f3c9c1c66d9c354aac080518b01eebc604b9ffd700132a4591c7e793732986e98af6ca14b08a0005736ee03871ce5382c5043db7de002d48489de3e69390130b378c66c5b1c5460b6708ae10548bc1b0765304e99693101bb9ca41adea9b5749e2e750248e51bf4064de0096004db8485e9feb9b9070c7443580cdecb38484fd7d9336a715868cf9e78060b28336a12586716c5604aa875300d11855a18469051bfa19312f964a8d62490d0374890a53d0b2925d429d33ed2147423f5191cccb94b345054a69b5cc03cdfb0d39e7d41ff05de4c1d9d604180a5de34669b6c0308a02a53977e03a351edd93e7977d499c121a8324b80195e6055e04d55ac3083151bbe2a332f9834aa6c2de656cd36d5358ca0ae3ef3e311f5dab318a4d4736e8dd91b7c0a350994f5ac681d0351a1f601a336ee05d78cea5dd52059bf46adb240c5ec5ff95986177c94b3326b6ae8060b33c866d60c93adeb5dd5fa32cd92c1b4c2f3ab24e16056d102b39fa1c52dccf8da4fc5ea35843a6ddc1b4e8ad832e495b377a52205dd205bd1e6d40cbbe52dcc603b86db5257660278e819b09bdd980957b0c46d833c534b06138ab6b2d08c8173bf9b44ee9681a3ee058075c5b05af792560df7756f600bf75c37b8160002ff300370012081040c5168ba6c5b65e53bb3fa82e1b9a2edef18983bdfd7b6c1c19633e8dd20fc0509d7d5b372fb6f1b4e1a580df650b304d82f3ccb80de19b6f5c2511b301b38dcb6e64db76f1e968185fd063f140c540509589ce58e3b07194712be8223f11a7a8cd43270d8b7d2d59a66334ae3358098b34a06038ab77748f9896f6d0c5ec31e1b7701255fb671815343817dd6b5042c7204cfac5d74ebc66435683866161d43977bc6cf1cb7b80c2c7e591bb28c590a8361d03d9cebfc14ede13ccfd8b37d6ea91a269dc433bb58d15a7b29e1ee9c0640c32cb08aadf317405d5d332437b7580e9e001e802f06b4d967f94566e3a4dc86bdf50cc286bddbff760bdd384e87c1c633ebeafc4eda5cf1d2920d5ab362185edcc32d979aca6670b2cc186d06433380ace0cbb291cf505e94c22201d3857080fb28e22f081b6675b6f48ca1ed056adfcddd63f3f419e00db3c7c6d66ce19db279cb1035527f78730a60b68b8dbdd77a366ec7664001f4eafb82c3015c0cece62a1a62ddd073a7da6f11171bbd17eec4322856c4a6c6b01932da6d343cf70104aff7e6f09cf06fccb2be196f9bbcc3f0f0c3c1d7d97ddb78b40f87efc975bc0c23a56912db3072fa9ec1d4273f43d0cfa0699e7eba71fb9643f8d490eebfc9e1e80f4675ad6bfe057bdb90724d1f83a2b52d06b0c36ccf6a383a6c471ee36493d772a9d47acf10ff2d8e5db7cdee428738811b376bab9b59ece35d7bbf4b6a369eb39de9125d35dcdb60b9fa96fbb07af7c6cced2a8789cf4ced36973b5d0fb7f4c18d5d0c56ddbbb4d990f86b2f1ee18daff1cb516ebbeec9e4e96f9503da7bf6f97747530e3a3f26d0079f867b9241eb0a3f3de97de6d9d09781ee813e03e6fb2680ee015b0e06971919ab5ffdc8cb8cbdf93edcd4d7307d999dbb69c82f5bea3d6a36af137ffbaa16a9efd900e9c297a1b1518d412881cc0dbc2319ea640f7cd61b3779b08ddf1700df18689d1ba4df188c948705a0183c17845d1de7bc4df4b15cfe0da0f91d8ec289c1fb8154efadde1954601814a0d1955ef1e51ae3f90d1c34a0eae1d6ffdbc014057e20187c5fcc845f183cd7dfd1cd02cac00b7e81c119e008aadf93a960dfc041f32d1f1ac42043415010bae0ccc88fe5c00211c6cceddd60fb7960f981a0d9f420fc31a0152a5e07aa41ed95a012e216b1b1df12ae5f1838212580e1cc48e1dce0a00e7a41089a9e0f222017a29e17a6c11a1661c5cd97b5d5a11986a118a421ed9d12079ee01afc53f29d9f086a611828dadb1da21be8a154b141f6cc57f7251a200622184c2261a961214a0e0e8e1ca7091f237614bfd91e240ae11b7422c9fdd111ca549d299f8c4d552b12da277a9e21ae22e19d62065ade1c36221840531b6e211ea2812d62a025ba57db69e2d68dc120c6812d1223d9e0a0ff069a1d0d9a8d1f2a62300a20d4d8e01f46e214b55e1666d7cf918106bae05d41231c48a32ef29e1812d3187c9b379ae2199e5dd4682338eea2385e601c24c003064f7f3d97f51164411ae4ca79c13ab22228460e0ea29de67dc1ee0d5b3d6ea2c471a33ebe233ffa0d0686010a581cf3dca1df789d4b588e04ba1ef7b92339ad81fcf58c398681c1d5dc01868106fa9a314ee0467283da00e4e6dcd03c769882cce2a9d512ff3524159ec14991e23901632f8e0154b9e44986231b20e3372cc0f8ec64df68a32de6e35e04e5cb6c251ac89eccfc9fd3e0a00c8c5c4a551e1b2ea53d82c1cdf90c2662a44a4ae2388603d15c0d2c2290e81c0e44fe45ca51c2ff47cee0d294e5484d1d4b42cd9aa1810538001d7259d48c65597e1d5dc2441a618dedb4e1478ea438f8e48dc9815b4665a719e5b4fd54f0998d036c59f35596a2216448eea3544ae64ce8e4e6b4213ace948294641c4021cc4c23358666c9392619349f0000db0b601fd25dd78cb5974d8a663f1e2347a2415a165d196ce67bc5c4d09cc1477ee536facd37ba616f964180f1a517d89803685318e4d262fe249f450d77c625162e2473562119521e439281e5846737a8c0479d016d46524f715e4aba271b4c67cc705a6e1692f300e45d5955f84126eec1a75e220e38d0e71814a7ccc06531a1ce1948a45292e30021db67a6c14975df47f2d653962188e6e1ff6b3ee3d620263734ce7d3aa8d48103c3cd5edf1d8e03986447b28f7f79677c6dda7935e3eda0168a1ea38a0ae2b08d2519e0dfe1a14157ea668e7617ae8115f37d93bc81007a1ece6a2e598f824f5e920174e69f96b26646bae683fa9bcf641394924158ca6091a60e42c101081099fef966e308671e62a9fd7c686baec1d1dd5085f2d66eb6271cba294ea281d904d4b9a599e3f0a23e8d402abd80d570d889de28a62929a0ca8c98da19979241edb521a672d635aa2299369991a2213b3dc0083892928e0f5e5e8e1a081343b111195dcdd5015eea74cf2259c0059028ccb0e7142a272afa4c3ef2e75e8d0f4b99283c12ea4dead8ef714effb86acc689fff1a80e9857da119262b8f066b1980e11225005e26506e6a4d9206dcb65698a9921f05662718802a66ad41b1a6576971ea776a4d6d5ddbf7a95c4432d4950d299f5eab99d627050a6a18f46a5db141c1cad4ba022b91a641acb61b1a0c9937b51a3a319473fad2bcde23c0ae2892652b1fba601b206cf05c28e861ec97226acd814060f64583f218ba76a38cd95a1bb0a9bb82ec858d2cc99aabb0fa4caf895fc972a2cb26a48c0980a652aab5ba41c81e94eeb0ec4b22aa9af60acb4ee5a15e188c4ed155b2141ccc6c40eeced2fe6c1291aba0402dd0e6a07b015638181af0e066b41e8e7c4294cf9601113240662249d86aac182c8002a8ade5dcec1b2c40ffbbd60e385142dffa553b8993dbfa68db106eced0ada1aac190e5eda5cdc482e95536116d93652de2ee2d802e2b800d90a582c30bc86d7232ec7232ae545e8093eed203882a2789994c31c008542edfce19407d6d1e012afcb46839590ddb582ce8a411f96cd8ebf6ae3724c05d1659aaaaac1b009293720fece61125542b9a4e139989cfe94ed0f0beced0148df57951f2cac4d0a040415a2772804002a8d11ab591f73c6f04a9edac9291202916f6aeeffcce6f555d98fcd26ffee611bcde14feeaefff1ad1537d97ff027001eb505cf116011bf002c3d04a2917034370047b81473eb0045bf002dfadd56a0ddb5e7007ebef02b09206d35faa2ab0079bf0ff0871912021a880f54f1d5d009c9d700ccbf00cd3700ddbf00de3700eebf00ef3700ffbf00f0371100bf1101371111bf1112371122bf1123371133bf1134371144bf1145371155bf1156371166bf1167371177bf1178371188bf1189371199bb111658009248120104202b8f11b27c02828c1128000c31849f9aef128c0f11b8f820528c100b080f70e0e0580c01a77801eef711c13c21c0f800964c019834304d844a214cb9eb809b27cca3d980bf92e89a5448b9eecc99a788a4218c008d8b1eeb04027b3088f587295c0c9b878402943726dd8042dfc878ffc487dd0085128c02323c77650727b684a2e3bca2b3fc600ec0e0574f27a68c293e4f25d708aff7d58cb1618c026cff21ac80aa2708997a00cbe4c8998988801f872712c73933c097c78b36b9448b214c5eb64402db7859378073adfcb7c4073703cc68d5cf31a9cc98ec83394448c8894c53a1385019cf25e947373808840530c344bf390e833e8c0b3427f07434f476bdc45a7b0b335ef73194cb493f8032e60c64570064a504770c846a8f845073089421706c18405498fc0584c077060883684ceb370c9427f42667086c11c8450d4482c1f74478b014b5f828738074954445424863c1c4470d4c138efc524e3444410cc61f0c44fccf46254c750a388e75000486c454e64b553387557db85501345441bf518084b2c3fc45243432fd8f53418c4675c87ff5bcbc499d00244184640d8b54f48455034864d800e526b0560fb81530c363ccc745d08c35950f35b97c1583b44594bc433b08321fff12124c2541cb616a8f45ed84977ec813ae882110cc01c7f7613c443144c76366808e5e8c339a4436387426bbb36134c833cccf67550b565472473a08359834211f0f620f0b11ffb765e6f4431f4853ef0430030856e2bf72133b7223b7741c83642d44891780e4b54776a37362fb4f6762bf21f9b0204a4c461eb07717b016994372e24772058400290c004f0377f93401c97420428022358418d4c373557062778c21fe0b77eef777fff77773b817ba7c209648378534e522fc33f084176e7f78343388083763c6444ff74d7b67ccf37821ff7828f4076ebf704b04023b1c0049040779f428567c38133c05cdb3711a47702c0782311000b44f8887f77817f04864f4e665b371f30f88f073923cd789123c28057c1509c388ad30126f4438f07827ec7b80954c09857800998c090d7f8124cf83cc0f781dfc18a9f370a1cf27e1380999379990ff904c4316843412ad4030328b9e4a8b882fb0120cc79988b39999ff98c2740293c41893f421608b217d3013f107a87cbb90590000b88f90aa4eca7afc0a2d7389f63809f3f40161cb832587709f401281c417e4f409d57c0a7a7ec0ae0b99eab398913382b8840a0478ea574791fb4f8ab6f7a9d7b3aaddbba0910b91f57f99113c3ffa477311d74c73fb8fa00e4778ccf3a081c400670fb018040a81300ae0f8013387b244c379737f90560fa21c7ba0978fab677bbb797f9b21fc2a35f003d6081af17ce2ce0c1a5b7786b03b9b16b3bb767c001c83b9a5b40bdbbf7ae6301b473b1b4d7823f087b11b0bbb16fbb0a2c400a2c800a787ba8d33b0ae8fa959b3b5f70c08ee70187fbf8b58bb9b6637c0a687cc1834005847ba337bba91b80be13ce4dc079b56b3aa7bf7bcb2fc002143cb84f00b30b78a9378023343c8a8301c4a77b87037caccf7a06643c05587d0a707ccc87bbd18f801488bc014cf79b133acf9380c5ab400a583d05bcfcb72bfb9e937b01247da4e3fce0e83c27b43a02647affbbb33cda5fbdd06b3d0b34fab8f779dc9fbac36f3108a0fb1e9cb7b52780cf6f3bdabb4008bc0005f8bdcce37a957bbdd2833dc98b3db5e37dc5cffa012c0005bc800bb880da73bcc78f3ac897fbdc0b8e5cdbc2c4cb79c04ffde8bf40089c3ed61f80ea27fcdb13be011c00d34764e23740a14780b56fbabb1fc0d9bf400bc04008a87d0668fde51ffdbd6b7ed82738ab17c0b0833e08503d05b8400bb4c0e42f80b79ff9ea87bc856f80ebd70dec4bfcf6533ced7b3fda87000c8cbfeef3feb8234014fc7ef00bbf0c000188617804148d0206111958482cd3eaa04abd422d1725950155089344071519612e2545606390b5dd6f785c3e8f73441bcda974ff294010a80e8b09820a908c050a97969097149503101302128b01140488028e9293878d0eba50d151d252d3520f838d003dbe9108c084c1c28c94c4c5c6c7151316ca8108048c8286b4078383d364e565e666e767e8686990d4a2a30b8cd7818e04a81543c49717ad0548af090b31b282b313353669533b3c3d8ebebf2612021390295b712d190eecea550998b04d013c818ad7d021a954f4f61478054810a17e0bfe8d5bc08560985f6586153bf6d0e449942955aea4a6ca48036c15b9cdeaa742638a141d074af27529d3a6131a36885859e7ce837af750e4f3060e27ce475d08b0004946183185061816e5daccc39d3c25ecf959ea649f94434f17443d17ff664c990b58d780e85ad7ee5dbcd322b29a489669947e19540c162895aa414c9a3809256a9703830dd69064633213e301c18421ed9a94eead19b95af38ea6f3357252254c2c74bb9c594561823d118c60c7e91d5dd2b975efae4b0d2cea25db649d05d1ef00a42ebc28a9838bd51383bb8f5d5ea36cf109a16fc78f83e00ca6924fc541d76ce53ddac310c93161057a62a29071e4ddab2606ca187779fcf9f52fa306397df52686736f050257a8c0045ec0e8c092c4ce48630d0fa28b28acb182638f857d2a28d040490a620e214e3c1181bcfdbaf2602f05fac267260c072cf0c02f12f8ceaab88ad98081fb4ad471471da9d92b293fd65b6d82161144702affef2cb12a3cc62274cc34a44a80299b1513b86e1f2309f862b95f1a44232b0648e431a5137f132b898a02bccec82359f04e1d2f1f34c0831cc7b4f3cebc7c3473ca112c5c8d04225910d44d4a668c134c27ebe2e0bce9ce043010590615740212645cf01291ea83504c3c1d3a113d235484a50927021d94524bbb6c6eb1a1e8ecf4555859fad425d4fa1c15522b49a8d48274946cb00156e74cb4ab45a13ce19a7b6ee535d74a65ecf533c56c64c0034e6385e6c409f58029d9a502f953d704521d633610e59cb64e6bd355973f6c8f0a950f2a6ff596d70e16f415827215baf14409195da51e3e8244a1db79d3b91418916a144fd86ad75d065b5051137880ff6d0aaed7d7849d6bd555873bf658141ffd0d6bdb7861598a628a074618df68f5157658ae8a8532540e02b6756094511e036161e2b26d8d69a9fdf8196c23b2c651086e3e59e79dc96507d88583467768aa1d0e79666de14d1a812522f0fa6bae458a366a6cfb3d5195a36b4e62ebafdb5eb967a8f51501db86ab16e5e521e849516d2abb763b6c967d7647446ca7b6fbf05729281a6b29f94e7a843ec31e01df9e375160e1b94fe4e0ae088ace5b8350b75d1b72ae25675993b83d799963c4e5f15c953cf6e663f4c867833c700e52dff844155af73d560d16479b9514619a1d03e49317869ddcd1c0bce808ee6261f1cff96a40ed249447be00ee2fb07c70ffa08bfedd940e161701f6d0b1d77e7b767cbedce5a235187ffe3bcb173e32d0f568fc82d9bbe7bff9f709c50099c356efeeb2baaf0cef588dab59ffdad73ce7b92c68272a00fd46a138f3a16f81d7e39f03ff072ce76940758bab9b054d981b049a2632c44b91941a70bd1782f07d2e232005f362bfd7e18f782570210c5fc8c3080a257c4533e009e73082c52550870bec61ee7e8886f779627767336215f15381242a110f0160a10252e4c5cbb9435f43c4960128301a0524316f4b3c9617a1e8c513b843846ba8e189a267c539a4708d0fc85f1bbf084631ce71804964011e0d999be9a9f17c1bd021171dc9450dcc918e49344011f392c6242e32327c7cffe423256900034c105b2338a41c5490422df211749d84a41407284a3b9652967861012a19a049463e4097ba64e41a5e99440558723432cba02a72b94b29321294b03c5109f148014c52ef7cc6dce42e7b4947661aa090b3e4a65d2210bc2cde1294e324a70898e901056c933720284016cf334d728e5304e6a4e408ced8cd38b0209ad28c6739cf690052e253a05d510108ea7550842654a1074d0008eeb91f10b060a1139d6821064a070a4494a21b452808847951908654a4232569494d7a5294a654a52b65694b5dfa5298c654a633a5694d6d7a539ce654a73be5694f7dfa53a00655a843256a518d7ad49452200298dc23275911473106e093f46c4002ff907a55acc60389ed425b1fdd084428c651aacacc9c01069055b4a6b5141900a7bfb6c8c4177240ae72956114276943b5e655af6da000de14d842b93ad07f1094232309b8b9bd2636ab32cb5b1116a83ee44140b2ece31fb02e3747023a53b19bd56922cfb6426dd98376a5335de52268582a7256b5416d6799402b25add9ca6b03dbd9cab897bbcb0ed1aaabe52d4f2106bbaca1690938cb59cabc8689722d0cafbd65ae4d43065c64c58b60bca2eec5c695a92f114e7ecde5ee4c7d04aabda14959dfd2d5ae0ee6344d09abbbeb85a99e24c2a715fd49505a429279c795891ae96b5aece52f4b7dd4a880f909505a625387de04adf4c2acbf0b1e697f5648ff1d9938a1451a2a0e77609424f038e81d0a66708707ea1b8954a83202ca8e768a7361cf2c4963d0f1708b2fda927f4da44fd6690a666cf21a734c45460cfa09ab1ae362207313c62f514f2c68e20f5be444209c594e867d1c6428cbb225ffd106379a520b0a8883235c680b736ae38e354459cc781402115e9284e048380afeb00216022215057d46c3611e739d4d5866c9a05935ed910215da1c82377759ce5fa2b39d0dfd3b3c9f993201f20666fee1822dc3d92deb98333c0e7de9c30d19c2eb194e21fc4101503b021292408718bc249e1f635ad51fd3b48c85a40fc0086601b3668b72be439f27af5ad71d03318594c268ec0446209b217555b0db891bffed5ad9ea0271942613e1357da3c21c8ad1ad69935f63b078d9dbee547f009cac6d48184b1520f7912895e260300942dc66f79dfe1b62f176ab5418a22f9252050cfc6a6c6eede6f78ebefbe0893cea5b1320f8ae9e85de9fa9b7df0bcfcf73df6bb351859bbad5c558cfb2bb3186677c9dbf75ac586c365c941d3465d7855bb0caa671948fe66aee929d70bb465b95bded69092760ca6d7e1715dcef6824cb46ed483bb9e5350f7c04dceecd8d5e94d5552fb4c7936cd329d7bdefc16f9447a7ba4ab6fa5bd0160f7bdce3fa032d0bbe50164d9d55277b43ae36ab2516ef7a739dab0c0b4b460f14bdec739746e7a867cc22b010ac4084a3545557470f548099ee8397c63eb99acb554255f1ad542633ef4878c83ba3b5fceca52e2379795e92358bbb8d7ce799514b77c2f39abd04253d93c801c3795ef5a4a84004d8fefa0676d07bb09f6b073eba7adce75ef7bbe77def7dff7be0075ff8c3277ef18d7f7ce4275ff9cb677ef39dff7ce8475ffad3a77ef5ad7f7dec675ffbdbe77ef7bdff7df0875ffce3277ff9cd7f7ef4a75ffdeb677ffbddcfee2000003b, 'image/gif', 5, 50);
/*!40000 ALTER TABLE `TVContents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVOut`
--

DROP TABLE IF EXISTS `TVOut`;
CREATE TABLE `TVOut` (
  `TVId` int(10) unsigned NOT NULL auto_increment,
  `TVTime` smallint(5) unsigned NOT NULL default '5',
  `TVTournament` int(10) unsigned NOT NULL default '0',
  `TVRuleName` varchar(64) NOT NULL,
  `TVNumInd` smallint(5) unsigned NOT NULL default '0',
  `TVNumTeam` smallint(5) unsigned NOT NULL default '0',
  `TVMaxPage` tinyint(3) unsigned NOT NULL default '0',
  `TVSession` tinyint(3) unsigned NOT NULL default '0',
  `TVViewNationName` tinyint(1) unsigned NOT NULL default '0',
  `TVNameComplete` tinyint(1) unsigned NOT NULL default '0',
  `TVViewTeamComponents` tinyint(1) unsigned NOT NULL default '1',
  `TVEventInd` varchar(255) NOT NULL,
  `TVEventTeam` varchar(255) NOT NULL,
  `TVPhasesInd` varchar(255) NOT NULL,
  `TVPhasesTeam` varchar(255) NOT NULL,
  `TVStartPage` varchar(1) NOT NULL,
  `TVChain` varchar(64) NOT NULL,
  `TV_TR_BGColor` varchar(7) NOT NULL default '#FFFFFF',
  `TV_TRNext_BGColor` varchar(7) NOT NULL default '#FFFFCC',
  `TV_TR_Color` varchar(7) NOT NULL default '#000000',
  `TV_TRNext_Color` varchar(7) NOT NULL default '#000000',
  `TV_Content_BGColor` varchar(7) NOT NULL default '#FEFEFE',
  `TV_Page_BGColor` varchar(7) NOT NULL default '#FFFFFF',
  `TV_TH_BGColor` varchar(7) NOT NULL default '#CCCCCC',
  `TV_TH_Color` varchar(7) NOT NULL default '#000000',
  `TV_THTitle_BGColor` varchar(7) NOT NULL default '#585858',
  `TV_THTitle_Color` varchar(7) NOT NULL default '#F4F4F4',
  `TV_Carattere` smallint(5) unsigned NOT NULL default '30',
  PRIMARY KEY  (`TVId`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TVOut`
--

LOCK TABLES `TVOut` WRITE;
/*!40000 ALTER TABLE `TVOut` DISABLE KEYS */;
/*!40000 ALTER TABLE `TVOut` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVParams`
--

DROP TABLE IF EXISTS `TVParams`;
CREATE TABLE `TVParams` (
  `TVPId` int(11) NOT NULL,
  `TVPTournament` int(11) NOT NULL,
  `TVPTimeStop` smallint(5) unsigned NOT NULL,
  `TVPTimeScroll` smallint(5) unsigned NOT NULL,
  `TVPNumRows` smallint(5) unsigned NOT NULL,
  `TVMaxPage` tinyint(3) unsigned NOT NULL,
  `TVPSession` tinyint(3) unsigned NOT NULL,
  `TVPViewNationName` tinyint(1) unsigned NOT NULL,
  `TVPNameComplete` tinyint(1) unsigned NOT NULL,
  `TVPViewTeamComponents` tinyint(1) unsigned NOT NULL,
  `TVPEventInd` varchar(255) NOT NULL,
  `TVPEventTeam` varchar(255) NOT NULL,
  `TVPPhasesInd` varchar(255) NOT NULL,
  `TVPPhasesTeam` varchar(255) NOT NULL,
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
  `TVP_Carattere` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`TVPId`,`TVPTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TVParams`
--

LOCK TABLES `TVParams` WRITE;
/*!40000 ALTER TABLE `TVParams` DISABLE KEYS */;
/*!40000 ALTER TABLE `TVParams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVRules`
--

DROP TABLE IF EXISTS `TVRules`;
CREATE TABLE `TVRules` (
  `TVRId` int(11) NOT NULL,
  `TVRTournament` int(11) NOT NULL,
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
  `TV_Carattere` int(11) NOT NULL,
  PRIMARY KEY  (`TVRId`,`TVRTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TVRules`
--

LOCK TABLES `TVRules` WRITE;
/*!40000 ALTER TABLE `TVRules` DISABLE KEYS */;
/*!40000 ALTER TABLE `TVRules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TVSequence`
--

DROP TABLE IF EXISTS `TVSequence`;
CREATE TABLE `TVSequence` (
  `TVSId` int(11) NOT NULL,
  `TVSTournament` int(11) NOT NULL,
  `TVSRule` int(11) NOT NULL,
  `TVSContent` int(11) NOT NULL,
  `TVSCntSameTour` tinyint(4) NOT NULL,
  `TVSTime` tinyint(4) NOT NULL,
  `TVSScroll` tinyint(4) NOT NULL,
  `TVSTable` varchar(5) NOT NULL,
  `TVSOrder` tinyint(4) NOT NULL,
  PRIMARY KEY  (`TVSId`,`TVSTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TVSequence`
--

LOCK TABLES `TVSequence` WRITE;
/*!40000 ALTER TABLE `TVSequence` DISABLE KEYS */;
/*!40000 ALTER TABLE `TVSequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Targets`
--

DROP TABLE IF EXISTS `Targets`;
CREATE TABLE `Targets` (
  `TarId` tinyint(3) unsigned NOT NULL auto_increment,
  `TarDescr` varchar(24) NOT NULL,
  `TarArray` varchar(24) NOT NULL default '0',
  PRIMARY KEY  (`TarId`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Targets`
--

LOCK TABLES `Targets` WRITE;
/*!40000 ALTER TABLE `Targets` DISABLE KEYS */;
INSERT INTO `Targets` VALUES (1,'TrgIndComplete','TrgIndComplete'),(2,'TrgIndSmall','TrgIndSmall'),(4,'TrgCOIndSmall','TrgCOIndSmall'),(3,'TrgCOIndComplete','TrgCOIndComplete'),(5,'TrgOutdoor','TrgOutdoor'),(6,'TrgField','TrgField'),(7,'TrgHMOutComplete','TrgHMOutComplete');
/*!40000 ALTER TABLE `Targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TeamComponent`
--

DROP TABLE IF EXISTS `TeamComponent`;
CREATE TABLE `TeamComponent` (
  `TcCoId` int(11) NOT NULL,
  `TcSubTeam` varchar(1) NOT NULL default '0',
  `TcTournament` int(11) NOT NULL,
  `TcEvent` varchar(4) NOT NULL,
  `TcId` int(10) unsigned NOT NULL,
  `TcFinEvent` tinyint(3) unsigned NOT NULL default '0',
  `TcOrder` tinyint(4) NOT NULL,
  PRIMARY KEY  (`TcCoId`,`TcSubTeam`,`TcTournament`,`TcEvent`,`TcId`,`TcFinEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TeamComponent`
--

LOCK TABLES `TeamComponent` WRITE;
/*!40000 ALTER TABLE `TeamComponent` DISABLE KEYS */;
/*!40000 ALTER TABLE `TeamComponent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TeamFinComponent`
--

DROP TABLE IF EXISTS `TeamFinComponent`;
CREATE TABLE `TeamFinComponent` (
  `TfcCoId` int(11) NOT NULL,
  `TfcSubTeam` varchar(1) NOT NULL default '0',
  `TfcTournament` int(11) NOT NULL,
  `TfcEvent` varchar(4) NOT NULL,
  `TfcId` int(10) unsigned NOT NULL,
  `TfcOrder` tinyint(4) NOT NULL,
  PRIMARY KEY  (`TfcCoId`,`TfcSubTeam`,`TfcTournament`,`TfcEvent`,`TfcId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TeamFinComponent`
--

LOCK TABLES `TeamFinComponent` WRITE;
/*!40000 ALTER TABLE `TeamFinComponent` DISABLE KEYS */;
/*!40000 ALTER TABLE `TeamFinComponent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TeamFinals`
--

DROP TABLE IF EXISTS `TeamFinals`;
CREATE TABLE `TeamFinals` (
  `TfEvent` varchar(4) NOT NULL,
  `TfMatchNo` tinyint(4) unsigned NOT NULL default '0',
  `TfTournament` int(10) unsigned NOT NULL default '0',
  `TfSession` tinyint(4) unsigned NOT NULL default '0',
  `TfTarget` varchar(6) NOT NULL,
  `TfScheduledtime` datetime NOT NULL,
  `TfRank` tinyint(4) unsigned NOT NULL default '0',
  `TfTeam` int(10) unsigned NOT NULL default '0',
  `TfSubTeam` varchar(1) NOT NULL default '0',
  `TfScore` smallint(6) NOT NULL default '0',
  `TfSetScore` tinyint(4) NOT NULL default '0',
  `TfSetPoints` varchar(15) default NULL,
  `TfTie` tinyint(1) NOT NULL default '0',
  `TfArrowstring` varchar(24) default NULL,
  `TfTiebreak` varchar(9) default NULL,
  `TfArrowPosition` varchar(240) default NULL,
  `TfTiePosition` varchar(90) default NULL,
  `TfWinLose` tinyint(1) unsigned NOT NULL default '0',
  `TfFinalRank` tinyint(4) unsigned NOT NULL default '0',
  `TfDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `TfSyncro` datetime NOT NULL,
  `TfLive` tinyint(4) NOT NULL default '0',
  `TfVxF` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`TfEvent`,`TfMatchNo`,`TfTournament`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TeamFinals`
--

LOCK TABLES `TeamFinals` WRITE;
/*!40000 ALTER TABLE `TeamFinals` DISABLE KEYS */;
/*!40000 ALTER TABLE `TeamFinals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Teams`
--

DROP TABLE IF EXISTS `Teams`;
CREATE TABLE `Teams` (
  `TeCoId` int(11) NOT NULL,
  `TeSubTeam` varchar(1) NOT NULL default '0',
  `TeEvent` varchar(4) NOT NULL,
  `TeTournament` int(11) NOT NULL,
  `TeFinEvent` tinyint(3) unsigned NOT NULL default '0',
  `TeScore` smallint(6) NOT NULL,
  `TeHits` smallint(6) NOT NULL,
  `TeGold` smallint(6) NOT NULL,
  `TeXnine` smallint(6) NOT NULL,
  `TeTie` tinyint(1) NOT NULL,
  `TeTieBreak` varchar(15) NOT NULL,
  `TeRank` tinyint(4) NOT NULL,
  `TeTimeStamp` timestamp NULL default NULL,
  `TeFinal` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`TeCoId`,`TeSubTeam`,`TeEvent`,`TeTournament`,`TeFinEvent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Teams`
--

LOCK TABLES `Teams` WRITE;
/*!40000 ALTER TABLE `Teams` DISABLE KEYS */;
/*!40000 ALTER TABLE `Teams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tournament`
--

DROP TABLE IF EXISTS `Tournament`;
CREATE TABLE `Tournament` (
  `ToId` int(10) unsigned NOT NULL auto_increment,
  `ToType` smallint(5) unsigned NOT NULL,
  `ToCode` varchar(8) NOT NULL,
  `ToName` tinytext NOT NULL,
  `ToCommitee` varchar(5) NOT NULL,
  `ToComDescr` tinytext NOT NULL,
  `ToWhere` tinytext NOT NULL,
  `ToWhenFrom` date NOT NULL,
  `ToWhenTo` date NOT NULL,
  `ToIntEvent` tinyint(3) unsigned NOT NULL default '0',
  `ToCurrency` varchar(8) default NULL,
  `ToPrintLang` varchar(5) NOT NULL,
  `ToPrintChars` tinyint(3) unsigned NOT NULL default '0',
  `ToPrintPaper` tinyint(3) unsigned NOT NULL default '0' COMMENT '0: ansi A4, 1: Letter',
  `ToImpFin` tinyint(3) unsigned NOT NULL default '0',
  `ToImgL` blob NOT NULL,
  `ToImgR` blob NOT NULL,
  `ToImgB` blob NOT NULL,
  `ToImgB2` blob NOT NULL,
  `ToNumSession` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session1` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session2` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session3` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session4` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session5` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session6` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session7` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session8` tinyint(3) unsigned NOT NULL default '0',
  `ToTar4Session9` tinyint(3) unsigned NOT NULL default '0',
  `ToAth4Target1` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target2` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target3` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target4` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target5` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target6` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target7` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target8` tinyint(3) unsigned NOT NULL default '4',
  `ToAth4Target9` tinyint(3) unsigned NOT NULL default '4',
  `ToIndFinVxA` tinyint(3) unsigned NOT NULL default '0',
  `ToTeamFinVxA` tinyint(3) unsigned NOT NULL default '0',
  `ToDbVersion` datetime NOT NULL default '0000-00-00 00:00:00',
  `ToBlock` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ToId`),
  UNIQUE KEY `ToCode` (`ToCode`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Tournament`
--

LOCK TABLES `Tournament` WRITE;
/*!40000 ALTER TABLE `Tournament` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tournament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TournamentDistances`
--

DROP TABLE IF EXISTS `TournamentDistances`;
CREATE TABLE `TournamentDistances` (
  `TdClasses` varchar(4) NOT NULL,
  `TdType` smallint(5) unsigned NOT NULL,
  `Td1` varchar(10) NOT NULL,
  `Td2` varchar(10) NOT NULL,
  `Td3` varchar(10) NOT NULL,
  `Td4` varchar(10) NOT NULL,
  `Td5` varchar(10) NOT NULL,
  `Td6` varchar(10) NOT NULL,
  `Td7` varchar(10) NOT NULL,
  `Td8` varchar(10) NOT NULL,
  PRIMARY KEY  (`TdClasses`,`TdType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TournamentDistances`
--

LOCK TABLES `TournamentDistances` WRITE;
/*!40000 ALTER TABLE `TournamentDistances` DISABLE KEYS */;
INSERT INTO `TournamentDistances` VALUES ('AN__',1,'60 m','50 m','40 m','30 m','','','',''),('OLVM',4,'90 m','70 m','50 m','30 m','','','',''),('OLRM',4,'50 m','40 m','30 m','20 m','','','',''),('OLSM',1,'90 m','70 m','50 m','30 m','','','',''),('OLJM',1,'90 m','70 m','50 m','30 m','','','',''),('OLRM',1,'50 m','40 m','30 m','20 m','','','',''),('OLVM',1,'90 m','70 m','50 m','30 m','','','',''),('OLGM',1,'30 m','25 m','20 m','15 m','','','',''),('OLAM',1,'70 m','60 m','50 m','30 m','','','',''),('OLGF',1,'30 m','25 m','20 m','15 m','','','',''),('OLVF',1,'70 m','60 m','50 m','30 m','','','',''),('OLRF',1,'50 m','40 m','30 m','20 m','','','',''),('OLJF',1,'70 m','60 m','50 m','30 m','','','',''),('OLSF',1,'70 m','60 m','50 m','30 m','','','',''),('OLAF',1,'60 m','50 m','40 m','30 m','','','',''),('COSM',1,'90 m','70 m','50 m','30 m','','','',''),('COJM',1,'90 m','70 m','50 m','30 m','','','',''),('CORM',1,'50 m','40 m','30 m','20 m','','','',''),('COVM',1,'90 m','70 m','50 m','30 m','','','',''),('COGM',1,'30 m','25 m','20 m','15 m','','','',''),('COAM',1,'70 m','60 m','50 m','30 m','','','',''),('COGF',1,'30 m','25 m','20 m','15 m','','','',''),('COVF',1,'70 m','60 m','50 m','30 m','','','',''),('CORF',1,'50 m','40 m','30 m','20 m','','','',''),('COJF',1,'70 m','60 m','50 m','30 m','','','',''),('COSF',1,'70 m','60 m','50 m','30 m','','','',''),('COAF',1,'60 m','50 m','40 m','30 m','','','',''),('__S_',3,'70m-1','70m-2','','','','','',''),('__V_',3,'70m-1','70m-2','','','','','',''),('__J_',3,'70m-1','70m-2','','','','','',''),('__AM',3,'70m-1','70m-2','','','','','',''),('__AF',3,'60m-1','60m-2','','','','','',''),('AN__',2,'60 m','50 m','40 m','30 m','60 m','50 m','40 m','30 m'),('OLSM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('OLJM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('OLRM',2,'50 m','40 m','30 m','20 m','50 m','40 m','30 m','20 m'),('OLVM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('OLGM',2,'30 m','25 m','20 m','15 m','30 m','25 m','20 m','15 m'),('OLAM',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('OLGF',2,'30 m','25 m','20 m','15 m','30 m','25 m','20 m','15 m'),('OLVF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('OLRF',2,'50 m','40 m','30 m','20 m','50 m','40 m','30 m','20 m'),('OLJF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('OLSF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('OLAF',2,'60 m','50 m','40 m','30 m','60 m','50 m','40 m','30 m'),('COSM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('COJM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('CORM',2,'50 m','40 m','30 m','20 m','50 m','40 m','30 m','20 m'),('COVM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('COGM',2,'30 m','25 m','20 m','15 m','30 m','25 m','20 m','15 m'),('COAM',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('COGF',2,'30 m','25 m','20 m','15 m','30 m','25 m','20 m','15 m'),('COVF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('CORF',2,'50 m','40 m','30 m','20 m','50 m','40 m','30 m','20 m'),('COJF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('COSF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('COAF',2,'60 m','50 m','40 m','30 m','60 m','50 m','40 m','30 m'),('OLGM',4,'30 m','25 m','20 m','15 m','','','',''),('OLJM',4,'90 m','70 m','50 m','30 m','','','',''),('OLSM',4,'90 m','70 m','50 m','30 m','','','',''),('AN__',4,'60 m','50 m','40 m','30 m','','','',''),('__R_',5,'40 m','35 m','30 m','','','','',''),('__G_',5,'25 m','20 m','15 m','','','','',''),('__A_',5,'60 m','50 m','40 m','','','','',''),('__J_',5,'60 m','50 m','40 m','','','','',''),('__S_',5,'60 m','50 m','40 m','','','','',''),('__V_',5,'60 m','50 m','40 m','','','','',''),('__M_',5,'60 m','50 m','40 m','','','','',''),('OLAM',4,'70 m','60 m','50 m','30 m','','','',''),('OLGF',4,'30 m','25 m','20 m','15 m','','','',''),('OLVF',4,'70 m','60 m','50 m','30 m','','','',''),('OLRF',4,'50 m','40 m','30 m','20 m','','','',''),('OLJF',4,'70 m','60 m','50 m','30 m','','','',''),('OLSF',4,'70 m','60 m','50 m','30 m','','','',''),('OLAF',4,'60 m','50 m','40 m','30 m','','','',''),('COSM',4,'90 m','70 m','50 m','30 m','','','',''),('COJM',4,'90 m','70 m','50 m','30 m','','','',''),('CORM',4,'50 m','40 m','30 m','20 m','','','',''),('COVM',4,'90 m','70 m','50 m','30 m','','','',''),('COGM',4,'30 m','25 m','20 m','15 m','','','',''),('COAM',4,'70 m','60 m','50 m','30 m','','','',''),('COGF',4,'30 m','25 m','20 m','15 m','','','',''),('COVF',4,'70 m','60 m','50 m','30 m','','','',''),('CORF',4,'50 m','40 m','30 m','20 m','','','',''),('COJF',4,'70 m','60 m','50 m','30 m','','','',''),('COSF',4,'70 m','60 m','50 m','30 m','','','',''),('COAF',4,'60 m','50 m','40 m','30 m','','','',''),('OLMM',4,'90 m','70 m','50 m','30 m','','','',''),('COMM',4,'90 m','70 m','50 m','30 m','','','',''),('OLMF',4,'70 m','60 m','50 m','30 m','','','',''),('COMF',4,'70 m','60 m','50 m','30 m','','','',''),('OLMM',1,'90 m','70 m','50 m','30 m','','','',''),('COMM',1,'90 m','70 m','50 m','30 m','','','',''),('OLMF',1,'70 m','60 m','50 m','30 m','','','',''),('COMF',1,'70 m','60 m','50 m','30 m','','','',''),('OLMM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('OLMF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('COMM',2,'90 m','70 m','50 m','30 m','90 m','70 m','50 m','30 m'),('COMF',2,'70 m','60 m','50 m','30 m','70 m','60 m','50 m','30 m'),('__M_',3,'70m-1','70m-2','','','','','',''),('%',6,'18m-1','18m-2','','','','','',''),('%',7,'25m-1','25m-2','','','','','',''),('%',8,'25m-1','25m-2','18m-1','18m-2','','','',''),('%',9,'H&F','','','','','','',''),('%',10,'Hunter','Field','','','','','',''),('%',12,'Hunter','Field','','','','','',''),('__F_',15,'20m-1','20m-2','','','','','',''),('__M_',15,'20m-1','20m-2','','','','','',''),('__G_',15,'15m-1','15m-2','','','','','',''),('__P_',15,'10m-1','10m-2','','','','','',''),('%',14,'Day 1','Day 2','Day 3','S.O.','','','',''),('%',13,'Perc.','Perc.','','','','','',''),('%',11,'Perc.','','','','','','','');
/*!40000 ALTER TABLE `TournamentDistances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TournamentInvolved`
--

DROP TABLE IF EXISTS `TournamentInvolved`;
CREATE TABLE `TournamentInvolved` (
  `TiId` int(10) unsigned NOT NULL auto_increment,
  `TiTournament` int(10) unsigned NOT NULL,
  `TiType` smallint(5) unsigned NOT NULL,
  `TiCode` varchar(9) NOT NULL,
  `TiName` varchar(64) NOT NULL,
  PRIMARY KEY  (`TiId`)
) ENGINE=MyISAM AUTO_INCREMENT=297 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TournamentInvolved`
--

LOCK TABLES `TournamentInvolved` WRITE;
/*!40000 ALTER TABLE `TournamentInvolved` DISABLE KEYS */;
/*!40000 ALTER TABLE `TournamentInvolved` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TournamentType`
--

DROP TABLE IF EXISTS `TournamentType`;
CREATE TABLE `TournamentType` (
  `TtId` smallint(5) unsigned NOT NULL auto_increment,
  `TtName` varchar(25) NOT NULL,
  `TtNumDist` tinyint(3) unsigned NOT NULL,
  `TtNumEnds` tinyint(3) unsigned NOT NULL,
  `TtMaxDistScore` mediumint(8) unsigned NOT NULL,
  `TtMaxFinIndScore` mediumint(8) unsigned NOT NULL,
  `TtMaxFinTeamScore` mediumint(8) unsigned NOT NULL,
  `TtCategory` tinyint(4) NOT NULL default '0' COMMENT '0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D',
  `TtElabTeam` tinyint(4) NOT NULL default '0' COMMENT '0:Standard, 1:Field, 2:3DI',
  `TtElimination` tinyint(4) NOT NULL default '0' COMMENT 'o: No Eliminations, 1: Elimination Allowed',
  `TtGolds` varchar(5) NOT NULL,
  `TtXNine` varchar(5) NOT NULL,
  `TtOrder` int(11) NOT NULL,
  PRIMARY KEY  (`TtId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `TournamentType`
--

LOCK TABLES `TournamentType` WRITE;
/*!40000 ALTER TABLE `TournamentType` DISABLE KEYS */;
INSERT INTO `TournamentType` VALUES (1,'Type_FITA',4,12,360,120,240,1,0,0,'10','X',10),(2,'Type_2xFITA',8,12,360,120,240,1,0,0,'10','X',20),(3,'Type_70m Round',2,12,360,120,240,1,0,0,'10','X',40),(4,'Type_FITA 72',4,6,180,120,240,1,0,0,'10','X',30),(5,'Type_900 Round',3,10,300,0,0,1,0,0,'10','X',140),(6,'Type_Indoor 18',2,10,300,120,240,2,0,0,'10','9',50),(7,'Type_Indoor 25',2,10,300,120,240,2,0,0,'10','9',60),(8,'Type_Indoor 25+18',4,10,300,120,240,2,0,0,'10','9',70),(9,'Type_HF 12+12',1,24,432,72,144,4,1,1,'6+5','6',90),(10,'Type_HF 24+24',2,24,432,72,144,4,1,1,'6+5','6',110),(11,'Type_3D',1,20,200,40,240,8,2,1,'10','X',120),(12,'Type_HF 12+12',2,12,216,72,144,4,1,1,'6+5','6',100),(13,'Type_3D',2,20,200,40,240,8,2,1,'10','X',130),(14,'Type_Las Vegas',4,10,300,0,0,0,0,0,'10','X',80),(15,'Type_GiochiGioventu',2,8,240,0,0,1,0,0,'10','X',150),(16,'Type_GiochiGioventuWinter',2,8,240,0,0,2,0,0,'10','9',160);
/*!40000 ALTER TABLE `TournamentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VxA`
--

DROP TABLE IF EXISTS `VxA`;
CREATE TABLE `VxA` (
  `VxAId` tinyint(3) unsigned NOT NULL auto_increment,
  `VxAValue` varchar(5) NOT NULL,
  `VxATeam` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`VxAId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `VxA`
--

LOCK TABLES `VxA` WRITE;
/*!40000 ALTER TABLE `VxA` DISABLE KEYS */;
INSERT INTO `VxA` VALUES (1,'4x3',0),(2,'2x6',0),(3,'4x6',1);
/*!40000 ALTER TABLE `VxA` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-02-05 10:46:00