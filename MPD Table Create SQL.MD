CREATE TABLE `mpd` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `document_id` bigint(20) NOT NULL,
  `document_version_id` bigint(20) NOT NULL,
  `A` text,
  `B` text,
  `C` text,
  `D` text,
  `E` text,
  `F` text,
  `G` text,
  `H` text,
  `I` text,
  `J` text,
  `K` text,
  `L` text,
  `M` text,
  `N` text,
  `O` text,
  `P` text,
  `Q` text,
  `R` text,
  `S` text,
  `T` text,
  `U` text,
  `V` text,
  `W` text,
  `X` text,
  `Y` text,
  `Z` text,
  `AA` text,
  `AB` text,
  `AC` text,
  `AD` text,
  `AE` text,
  `AF` text,
  `AG` text,
  `AH` text,
  `AI` text,
  `AJ` text,
  `AK` text,
  `AL` text,
  `AM` text,
  `AN` text,
  `AO` text,
  `AP` text,
  `AQ` text,
  `AR` text,
  `AS` text,
  `AT` text,
  `AU` text,
  `AV` text,
  `AW` text,
  `AX` text,
  `AY` text,
  `AZ` text,
  `sheet_name` varchar(64) DEFAULT NULL,
  `revision_number` varchar(64) DEFAULT NULL,
  `crea_usr_id` varchar(64) DEFAULT NULL,
  `crea_dtm` timestamp NULL DEFAULT NULL,
  `updt_usr_id` varchar(64) DEFAULT NULL,
  `updt_dtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14505 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT