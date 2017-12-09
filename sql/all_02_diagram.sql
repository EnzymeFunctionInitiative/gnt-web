
DROP TABLE IF EXISTS `diagram`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diagram` (
  `diagram_id` int(11) NOT NULL AUTO_INCREMENT,
  `diagram_key` varchar(255) DEFAULT NULL,
  `diagram_email` varchar(255) DEFAULT NULL,
  `diagram_status` enum('NEW','RUNNING','FINISH','FAILED') DEFAULT 'NEW',
  `diagram_title` varchar(255) DEFAULT NULL,
  `diagram_time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diagram_time_started` datetime NOT NULL,
  `diagram_time_completed` datetime NOT NULL,
  `diagram_type` varchar(10) DEFAULT NULL,
  `diagram_params` text,
  PRIMARY KEY (`diagram_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2048 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

