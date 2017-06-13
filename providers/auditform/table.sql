--
-- Table structure for table `tbl_form_cpo`
--

CREATE TABLE IF NOT EXISTS `tbl_form_audit` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `authorized` bigint(20) DEFAULT NULL,
  `activity` bigint(20) DEFAULT NULL,
  `authProvider` varchar(255) DEFAULT NULL,
  `audit_data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB   ;