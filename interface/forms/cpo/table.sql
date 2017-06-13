--
-- Table structure for table `tbl_form_cpo`
--

CREATE TABLE IF NOT EXISTS `tbl_form_cpo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `cpo_data` longtext,
  `provider_id` bigint(20) DEFAULT NULL,
  `authorized` bigint(20) DEFAULT NULL,
  `activity` bigint(20) DEFAULT NULL,
  `authProvider` varchar(255) DEFAULT NULL,
  `count` bigint(20) DEFAULT NULL,
  `signed_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

