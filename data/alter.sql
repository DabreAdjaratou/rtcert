--ilahir 13-APR-2016
ALTER TABLE  `spi_form_v_3` ADD  `status` VARCHAR( 100 ) NULL DEFAULT  'pending';


--ilahir 23-Apr-2016

CREATE TABLE IF NOT EXISTS `spi_rt_3_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facility_id` varchar(255) DEFAULT NULL,
  `` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--ilahir 26-Apr-2016


CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `status`) VALUES
(1, 'SPI Form', 'active');


CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `role_code` varchar(255) DEFAULT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


ALTER TABLE  `users` ADD  `first_name` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `id` ;
ALTER TABLE  `users` ADD  `last_name` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `first_name` ;
ALTER TABLE  `users` ADD  `status` VARCHAR( 255 ) NULL DEFAULT NULL ;
ALTER TABLE  `users` ADD  `created_on` DATETIME NULL DEFAULT NULL ;
ALTER TABLE  `users` ADD  `email` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `password` ;


CREATE TABLE IF NOT EXISTS `user_role_map` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`map_id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Constraints for table `user_role_map`
--
ALTER TABLE `user_role_map`
  ADD CONSTRAINT `user_role_map_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_role_map_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--saravanna 03-may-2016
CREATE TABLE IF NOT EXISTS  `global_config` (
 `config_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `display_name` VARCHAR( 255 ) NOT NULL ,
 `global_name` VARCHAR( 255 ) DEFAULT NULL ,
 `global_value` VARCHAR( 255 ) DEFAULT NULL ,
PRIMARY KEY (  `config_id` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1 AUTO_INCREMENT =1;

INSERT INTO `global_config` (`config_id`, `display_name`, `global_name`, `global_value`) VALUES (NULL, 'Auto Approve Status', 'approve_status', 'yes');



--ilahir 04-May-2016
CREATE TABLE IF NOT EXISTS `event_log` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `actor` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `resource_name` varchar(255) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `actor` (`actor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

ALTER TABLE `event_log`
  ADD CONSTRAINT `event_log_ibfk_1` FOREIGN KEY (`actor`) REFERENCES `users` (`id`);
  
--ilahir 10-MAY-2016

CREATE TABLE IF NOT EXISTS `resources` (
  `resource_id` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `privileges` (
  `resource_id` varchar(255) NOT NULL DEFAULT '',
  `privilege_name` varchar(255) NOT NULL DEFAULT '',
  `display_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`resource_id`,`privilege_name`),
  UNIQUE KEY `resource_id_2` (`resource_id`,`privilege_name`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `resources` (`resource_id`, `display_name`) VALUES
('Application\\Controller\\Config', 'Global Config'),
('Application\\Controller\\Facility', 'Manage Facility'),
('Application\\Controller\\Index', 'Dashboard'),
('Application\\Controller\\Roles', 'Manage Roles'),
('Application\\Controller\\SpiV3', 'Manage SpiV3 Form'),
('Application\\Controller\\Users', 'Manage Users');


INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES
('Application\\Controller\\Config', 'edit-global', 'Edit'),
('Application\\Controller\\Config', 'index', 'Access'),
('Application\\Controller\\Facility', 'add', 'Add'),
('Application\\Controller\\Facility', 'edit', 'Edit'),
('Application\\Controller\\Facility', 'get-facility-name', 'Merge Facilities'),
('Application\\Controller\\Facility', 'index', 'Access'),
('Application\\Controller\\Index', 'index', 'Access'),
('Application\\Controller\\Roles', 'add', 'Add'),
('Application\\Controller\\Roles', 'edit', 'Edit'),
('Application\\Controller\\Roles', 'index', 'Access'),
('Application\\Controller\\SpiV3', 'approve-status', 'Approved Status'),
('Application\\Controller\\SpiV3', 'download-pdf', 'Download pdf'),
('Application\\Controller\\SpiV3', 'edit', 'Edit'),
('Application\\Controller\\SpiV3', 'index', 'Access'),
('Application\\Controller\\SpiV3', 'manage-facility', 'Access to edit SPI Form'),
('Application\\Controller\\Users', 'add', 'Add'),
('Application\\Controller\\Users', 'edit', 'Edit'),
('Application\\Controller\\Users', 'index', 'Access');

--Pal 12-MAY-2016
INSERT INTO `resources` (`resource_id`, `display_name`) VALUES ('Application\\Controller\\Email', 'Manage Email');

INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\Email', 'index', 'Access');


INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'corrective-action-pdf', 'Download corrective action pdf');

--Pal 13-MAY-2016
CREATE TABLE IF NOT EXISTS `temp_mail` (
  `temp_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_full_name` varchar(255) DEFAULT NULL,
  `from_mail` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `cc` varchar(500) DEFAULT NULL,
  `bcc` varchar(500) DEFAULT NULL,
  `subject` mediumtext,
  `message` mediumtext,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`temp_id`)
)

--Pal 17-MAY-2016
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'delete', 'Delete');


--Amit 17-MAY-2016
ALTER TABLE `spi_form_v_3` CHANGE `avgMonthTesting` `avgMonthTesting` INT NULL DEFAULT '0';


--Pal 17-MAY-2016
ALTER TABLE `spi_rt_3_facilities` ADD `status` VARCHAR(255) NOT NULL DEFAULT 'active' AFTER `longitude`;

--Pal 24th-Aug-2016
CREATE TABLE `user_token_map` (
  `user_id` int(11) NOT NULL,
  `token` varchar(45) NOT NULL
)

CREATE TABLE `audit_mails` (
  `mail_id` int(11) NOT NULL,
  `from_full_name` varchar(255) DEFAULT NULL,
  `from_mail` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) NOT NULL,
  `cc` varchar(500) DEFAULT NULL,
  `bcc` varchar(500) DEFAULT NULL,
  `subject` mediumtext,
  `message` mediumtext,
  `status` varchar(255) NOT NULL DEFAULT 'pending'
)

ALTER TABLE `audit_mails`
  ADD PRIMARY KEY (`mail_id`);
  
ALTER TABLE `audit_mails`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT;
  
--Pal 25th-Aug-2016
INSERT INTO `global_config` (`config_id`, `display_name`, `global_name`, `global_value`) VALUES (NULL, 'Header', 'header', NULL), (NULL, 'Logo', 'logo', NULL);


--ilahir 16-NOV-2016
INSERT INTO `odkdash`.`global_config` (`config_id`, `display_name`, `global_name`, `global_value`) VALUES (NULL, 'Language', 'language', 'English');

INSERT INTO `resources` (`resource_id`, `display_name`) VALUES ('Application\\Controller\\Dashboard', 'Manage Dashboard');
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\Dashboard', 'index', 'Access'), ('Application\\Controller\\Dashboard', 'audi-details', 'Manage Audit details');
UPDATE `resources` SET `display_name` = 'Home' WHERE `resources`.`resource_id` = 'Application\\Controller\\Index';

ALTER TABLE `roles` ADD PRIMARY KEY(`role_id`);
ALTER TABLE `roles` ADD UNIQUE(`role_id`);
ALTER TABLE `roles` CHANGE `role_id` `role_id` INT(11) NOT NULL AUTO_INCREMENT;

--Pal 04-Apr-2017
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\Facility', 'map-province', 'Map Province');

UPDATE `privileges` SET `privilege_name` = 'get-province-list' WHERE `privileges`.`resource_id` = 'Application\\Controller\\Facility' AND `privileges`.`privilege_name` = 'map-province';

--Pal 06-Apr-2017
CREATE TABLE `r_spi_form_v_3_download` (
  `r_download_id` int(11) NOT NULL,
  `auditroundno` varchar(255) DEFAULT NULL,
  `assesmentofaudit` varchar(255) DEFAULT NULL,
  `testingpointtype` varchar(255) DEFAULT NULL,
  `testingpointname` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `level_name` varchar(255) DEFAULT NULL,
  `AUDIT_SCORE_PERCANTAGE` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `download_status` int(11) NOT NULL DEFAULT '0'
);

ALTER TABLE `r_spi_form_v_3_download`
  ADD PRIMARY KEY (`r_download_id`);
  
ALTER TABLE `r_spi_form_v_3_download`
  MODIFY `r_download_id` int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `r_spi_form_v_3_download` ADD `user` INT(11) NOT NULL AFTER `r_download_id`;

--saravanan 05-apr-2017
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'duplicate', 'Duplicate');

CREATE TABLE `spi_form_v_3_duplicate` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `formId` varchar(255) NOT NULL,
  `formVersion` varchar(255) NOT NULL,
  `meta-instance-id` varchar(255) NOT NULL,
  `meta-model-version` varchar(255) NOT NULL,
  `meta-ui-version` varchar(255) NOT NULL,
  `meta-submission-date` varchar(255) NOT NULL,
  `meta-is-complete` varchar(255) NOT NULL,
  `meta-date-marked-as-complete` varchar(255) NOT NULL,
  `start` varchar(255) DEFAULT NULL,
  `end` varchar(255) DEFAULT NULL,
  `today` date DEFAULT NULL,
  `deviceid` varchar(255) DEFAULT NULL,
  `subscriberid` varchar(255) DEFAULT NULL,
  `text_image` varchar(255) DEFAULT NULL,
  `info1` varchar(255) DEFAULT NULL,
  `info2` varchar(255) DEFAULT NULL,
  `assesmentofaudit` date NOT NULL,
  `auditroundno` varchar(255) DEFAULT NULL,
  `facilityname` varchar(255) DEFAULT NULL,
  `facilityid` varchar(255) DEFAULT NULL,
  `testingpointname` varchar(255) DEFAULT NULL,
  `testingpointtype` varchar(255) DEFAULT NULL,
  `testingpointtype_other` varchar(255) DEFAULT NULL,
  `locationaddress` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `level_other` varchar(255) DEFAULT NULL,
  `level_name` varchar(255) DEFAULT NULL,
  `affiliation` varchar(255) DEFAULT NULL,
  `affiliation_other` varchar(255) DEFAULT NULL,
  `NumberofTester` varchar(255) DEFAULT NULL,
  `avgMonthTesting` int(11) DEFAULT '0',
  `name_auditor_lead` varchar(255) DEFAULT NULL,
  `name_auditor2` varchar(255) DEFAULT NULL,
  `info4` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_1` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_1` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_2` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_2` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_3` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_3` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_4` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_4` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_5` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_5` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_6` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_6` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_7` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_7` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_8` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_8` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_9` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_9` varchar(255) DEFAULT NULL,
  `PERSONAL_Q_1_10` varchar(255) DEFAULT NULL,
  `PERSONAL_C_1_10` varchar(255) DEFAULT NULL,
  `PERSONAL_SCORE` varchar(255) DEFAULT NULL,
  `PERSONAL_Display` varchar(255) DEFAULT NULL,
  `PERSONALPHOTO` varchar(255) DEFAULT NULL,
  `PHYSICAL_Q_2_1` varchar(255) DEFAULT NULL,
  `PHYSICAL_C_2_1` varchar(255) DEFAULT NULL,
  `PHYSICAL_Q_2_2` varchar(255) DEFAULT NULL,
  `PHYSICAL_C_2_2` varchar(255) DEFAULT NULL,
  `PHYSICAL_Q_2_3` varchar(255) DEFAULT NULL,
  `PHYSICAL_C_2_3` varchar(255) DEFAULT NULL,
  `PHYSICAL_Q_2_4` varchar(255) DEFAULT NULL,
  `PHYSICAL_C_2_4` varchar(255) DEFAULT NULL,
  `PHYSICAL_Q_2_5` varchar(255) DEFAULT NULL,
  `PHYSICAL_C_2_5` varchar(255) DEFAULT NULL,
  `PHYSICAL_SCORE` varchar(255) DEFAULT NULL,
  `PHYSICAL_Display` varchar(255) DEFAULT NULL,
  `PHYSICALPHOTO` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_1` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_1` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_2` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_2` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_3` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_3` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_4` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_4` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_5` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_5` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_6` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_6` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_7` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_7` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_8` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_8` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_9` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_9` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_10` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_10` varchar(255) DEFAULT NULL,
  `SAFETY_Q_3_11` varchar(255) DEFAULT NULL,
  `SAFETY_C_3_11` varchar(255) DEFAULT NULL,
  `SAFETY_SCORE` varchar(255) DEFAULT NULL,
  `SAFETY_DISPLAY` varchar(255) DEFAULT NULL,
  `SAFETYPHOTO` varchar(255) DEFAULT NULL,
  `PRE_Q_4_1` varchar(255) DEFAULT NULL,
  `PRE_C_4_1` varchar(255) DEFAULT NULL,
  `PRE_Q_4_2` varchar(255) DEFAULT NULL,
  `PRE_C_4_2` varchar(255) DEFAULT NULL,
  `PRE_Q_4_3` varchar(255) DEFAULT NULL,
  `PRE_C_4_3` varchar(255) DEFAULT NULL,
  `PRE_Q_4_4` varchar(255) DEFAULT NULL,
  `PRE_C_4_4` varchar(255) DEFAULT NULL,
  `PRE_Q_4_5` varchar(255) DEFAULT NULL,
  `PRE_C_4_5` varchar(255) DEFAULT NULL,
  `PRE_Q_4_6` varchar(255) DEFAULT NULL,
  `PRE_C_4_6` varchar(255) DEFAULT NULL,
  `PRE_Q_4_7` varchar(255) DEFAULT NULL,
  `PRE_C_4_7` varchar(255) DEFAULT NULL,
  `PRE_Q_4_8` varchar(255) DEFAULT NULL,
  `PRE_C_4_8` varchar(255) DEFAULT NULL,
  `PRE_Q_4_9` varchar(255) DEFAULT NULL,
  `PRE_C_4_9` varchar(255) DEFAULT NULL,
  `PRE_Q_4_10` varchar(255) DEFAULT NULL,
  `PRE_C_4_10` varchar(255) DEFAULT NULL,
  `PRE_Q_4_11` varchar(255) DEFAULT NULL,
  `PRE_C_4_11` varchar(255) DEFAULT NULL,
  `PRE_Q_4_12` varchar(255) DEFAULT NULL,
  `PRE_C_4_12` varchar(255) DEFAULT NULL,
  `PRETEST_SCORE` varchar(255) DEFAULT NULL,
  `PRETEST_Display` varchar(255) DEFAULT NULL,
  `PRETESTPHOTO` varchar(255) DEFAULT NULL,
  `TEST_Q_5_1` varchar(255) DEFAULT NULL,
  `TEST_C_5_1` varchar(255) DEFAULT NULL,
  `TEST_Q_5_2` varchar(255) DEFAULT NULL,
  `TEST_C_5_2` varchar(255) DEFAULT NULL,
  `TEST_Q_5_3` varchar(255) DEFAULT NULL,
  `TEST_C_5_3` varchar(255) DEFAULT NULL,
  `TEST_Q_5_4` varchar(255) DEFAULT NULL,
  `TEST_C_5_4` varchar(255) DEFAULT NULL,
  `TEST_Q_5_5` varchar(255) DEFAULT NULL,
  `TEST_C_5_5` varchar(255) DEFAULT NULL,
  `TEST_Q_5_6` varchar(255) DEFAULT NULL,
  `TEST_C_5_6` varchar(255) DEFAULT NULL,
  `TEST_Q_5_7` varchar(255) DEFAULT NULL,
  `TEST_C_5_7` varchar(255) DEFAULT NULL,
  `TEST_Q_5_8` varchar(255) DEFAULT NULL,
  `TEST_C_5_8` varchar(255) DEFAULT NULL,
  `TEST_Q_5_9` varchar(255) DEFAULT NULL,
  `TEST_C_5_9` varchar(255) DEFAULT NULL,
  `TEST_SCORE` varchar(255) DEFAULT NULL,
  `TEST_DISPLAY` varchar(255) DEFAULT NULL,
  `TESTPHOTO` varchar(255) DEFAULT NULL,
  `POST_Q_6_1` varchar(255) DEFAULT NULL,
  `POST_C_6_1` varchar(255) DEFAULT NULL,
  `POST_Q_6_2` varchar(255) DEFAULT NULL,
  `POST_C_6_2` varchar(255) DEFAULT NULL,
  `POST_Q_6_3` varchar(255) DEFAULT NULL,
  `POST_C_6_3` varchar(255) DEFAULT NULL,
  `POST_Q_6_4` varchar(255) DEFAULT NULL,
  `POST_C_6_4` varchar(255) DEFAULT NULL,
  `POST_Q_6_5` varchar(255) DEFAULT NULL,
  `POST_C_6_5` varchar(255) DEFAULT NULL,
  `POST_Q_6_6` varchar(255) DEFAULT NULL,
  `POST_C_6_6` varchar(255) DEFAULT NULL,
  `POST_Q_6_7` varchar(255) DEFAULT NULL,
  `POST_C_6_7` varchar(255) DEFAULT NULL,
  `POST_Q_6_8` varchar(255) DEFAULT NULL,
  `POST_C_6_8` varchar(255) DEFAULT NULL,
  `POST_Q_6_9` varchar(255) DEFAULT NULL,
  `POST_C_6_9` varchar(255) DEFAULT NULL,
  `POST_SCORE` varchar(255) DEFAULT NULL,
  `POST_DISPLAY` varchar(255) DEFAULT NULL,
  `POSTTESTPHOTO` varchar(255) DEFAULT NULL,
  `EQA_Q_7_1` varchar(255) DEFAULT NULL,
  `EQA_C_7_1` varchar(255) DEFAULT NULL,
  `EQA_Q_7_2` varchar(255) DEFAULT NULL,
  `EQA_C_7_2` varchar(255) DEFAULT NULL,
  `EQA_Q_7_3` varchar(255) DEFAULT NULL,
  `EQA_C_7_3` varchar(255) DEFAULT NULL,
  `EQA_Q_7_4` varchar(255) DEFAULT NULL,
  `EQA_C_7_4` varchar(255) DEFAULT NULL,
  `EQA_Q_7_5` varchar(255) DEFAULT NULL,
  `EQA_C_7_5` varchar(255) DEFAULT NULL,
  `EQA_Q_7_6` varchar(255) DEFAULT NULL,
  `EQA_C_7_6` varchar(255) DEFAULT NULL,
  `EQA_Q_7_7` varchar(255) DEFAULT NULL,
  `EQA_C_7_7` varchar(255) DEFAULT NULL,
  `EQA_Q_7_8` varchar(255) DEFAULT NULL,
  `EQA_C_7_8` varchar(255) DEFAULT NULL,
  `sampleretesting` varchar(255) DEFAULT NULL,
  `EQA_Q_7_9` varchar(255) DEFAULT NULL,
  `EQA_C_7_9` varchar(255) DEFAULT NULL,
  `EQA_Q_7_10` varchar(255) DEFAULT NULL,
  `EQA_C_7_10` varchar(255) DEFAULT NULL,
  `EQA_Q_7_11` varchar(255) DEFAULT NULL,
  `EQA_C_7_11` varchar(255) DEFAULT NULL,
  `EQA_Q_7_12` varchar(255) DEFAULT NULL,
  `EQA_C_7_12` varchar(255) DEFAULT NULL,
  `EQA_Q_7_13` varchar(255) DEFAULT NULL,
  `EQA_C_7_13` varchar(255) DEFAULT NULL,
  `EQA_Q_7_14` varchar(255) DEFAULT NULL,
  `EQA_C_7_14` varchar(255) DEFAULT NULL,
  `EQA_MAX_SCORE` varchar(255) DEFAULT NULL,
  `EQA_REQ` varchar(255) DEFAULT NULL,
  `EQA_OPT` varchar(255) DEFAULT NULL,
  `EQA_SCORE` varchar(255) DEFAULT NULL,
  `EQA_DISPLAY` varchar(255) DEFAULT NULL,
  `EQAPHOTO` varchar(255) DEFAULT NULL,
  `FINAL_AUDIT_SCORE` varchar(255) DEFAULT NULL,
  `MAX_AUDIT_SCORE` varchar(255) DEFAULT NULL,
  `AUDIT_SCORE_PERCANTAGE` varchar(255) DEFAULT NULL,
  `staffaudited` varchar(255) DEFAULT NULL,
  `durationaudit` varchar(255) DEFAULT NULL,
  `personincharge` varchar(255) DEFAULT NULL,
  `endofsurvey` varchar(255) DEFAULT NULL,
  `info5` varchar(255) DEFAULT NULL,
  `info6` varchar(255) DEFAULT NULL,
  `info10` varchar(255) DEFAULT NULL,
  `info11` varchar(255) DEFAULT NULL,
  `summarypage` varchar(255) DEFAULT NULL,
  `SUMMARY_NOT_AVL` varchar(255) DEFAULT NULL,
  `info12` varchar(255) DEFAULT NULL,
  `info17` varchar(255) DEFAULT NULL,
  `info21` varchar(255) DEFAULT NULL,
  `info22` varchar(255) DEFAULT NULL,
  `info23` varchar(255) DEFAULT NULL,
  `info24` varchar(255) DEFAULT NULL,
  `info25` varchar(255) DEFAULT NULL,
  `info26` varchar(255) DEFAULT NULL,
  `info27` varchar(255) DEFAULT NULL,
  `correctiveaction` text,
  `sitephoto` varchar(255) DEFAULT NULL,
  `Latitude` varchar(255) DEFAULT NULL,
  `Longitude` varchar(255) DEFAULT NULL,
  `Altitude` varchar(255) DEFAULT NULL,
  `Accuracy` varchar(255) DEFAULT NULL,
  `auditorSignature` text,
  `instanceID` varchar(255) DEFAULT NULL,
  `instanceName` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT 'pending'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


ALTER TABLE `spi_form_v_3_duplicate`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `spi_form_v_3_duplicate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  

--Pal 06-Apr-2017
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'download-files', 'Download Zipped Files');

ALTER TABLE `r_spi_form_v_3_download` DROP `file_name`;

INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'view-data', 'Dashboard - View Details');

--saravnana 24-may-2017
INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\Facility', 'export-facility', 'Export Facilities');
--Pal 06-Jun-2017
ALTER TABLE `spi_form_v_3` ADD `facility` INT(11) NULL DEFAULT NULL AFTER `auditroundno`;
--saravanna 21-jun-2017
INSERT INTO `odkdash`.`privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES ('Application\\Controller\\SpiV3', 'spiv3-import-csv-file', 'SPIV3 Import CSV');

--Pal 27-Jun-2017
ALTER TABLE `spi_form_v_3_duplicate` DROP `facilityname`;

ALTER TABLE `spi_form_v_3` DROP `facilityname`;

ALTER TABLE `spi_form_v_3` DROP `facilityid`;

ALTER TABLE `spi_form_v_3_duplicate` CHANGE `facilityid` `facility` INT(11) NULL DEFAULT NULL;

ALTER TABLE `spi_form_v_3` ADD `facilityid` INT(11) NULL DEFAULT NULL AFTER `facility`, ADD `facilityname` VARCHAR(500) NULL DEFAULT NULL AFTER `facilityid`;

ALTER TABLE `spi_form_v_3_duplicate` ADD `facilityid` INT(11) NULL DEFAULT NULL AFTER `facility`, ADD `facilityname` VARCHAR(500) NULL DEFAULT NULL AFTER `facilityid`;