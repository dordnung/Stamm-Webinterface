CREATE TABLE `payments` (
  `id` bigint(20) NOT NULL auto_increment,
  `txnid` varchar(200) collate latin1_general_ci NOT NULL default '',
  `payer_id` varchar(200) collate latin1_general_ci NOT NULL default '',
  `payed` varchar(200) collate latin1_general_ci NOT NULL default '',
  `full_name` varchar(220) collate latin1_general_ci NOT NULL default '',
  `user_email` varchar(220) collate latin1_general_ci NOT NULL default '',
  `points` varchar(200) collate latin1_general_ci NOT NULL default '',
  `steamid` varchar(200) collate latin1_general_ci NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `txnid` (`txnid`),
  FULLTEXT KEY `idx_search` (`user_email`, `full_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;