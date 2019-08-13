<?php

$installer = $this;

$installer->startSetup();

/*
  `subscriptions_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
*/

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('subscriptions')};
CREATE TABLE {$this->getTable('subscriptions')} (
  `subscriptions_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `api_url` varchar(50) NOT NULL default '',
  `api_key` varchar(75) NOT NULL default '',
  `account_url` varchar(50) NOT NULL default '',
  `list_value` text NULL,
  `lists` text NULL,
  `form_value` text NULL,
  `forms` text NULL,
  `status` smallint(6) NOT NULL default '0',
  `cdate` datetime NULL,
  PRIMARY KEY (`subscriptions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('subscriptions')} (title, status, cdate) VALUES ('Default', 2, NOW());
    ");

$installer->endSetup();