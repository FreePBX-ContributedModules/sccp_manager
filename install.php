<?php

global $db;

$autoincrement = (($amp_conf["AMPDBENGINE"] == "sqlite") || ($amp_conf["AMPDBENGINE"] == "sqlite3")) ? "AUTOINCREMENT":"AUTO_INCREMENT";

$sql = "CREATE TABLE IF NOT EXISTS `sccpdevmodel` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `model` varchar(20) NOT NULL DEFAULT '',
  `vendor` varchar(40) DEFAULT '',
  `dns` int(2) DEFAULT '1',
  `buttons` int(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1
";

$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not create sccpdevmodel table\n");
}

$sql = "INSERT IGNORE INTO `sccpdevmodel` VALUES (1,'790X','CISCO',1,0),(2,'791X','CISCO',1,0),(3,'792X','CISCO',1,0),(4,'793X','CISCO',1,0),(5,'794X','CISCO',2,2),(6,'796X','CISCO',2,6),(7,'797X','CISCO',2,6),(8,'IP Communicator','CISCO',2,8)
";

$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not insert into sccpdevmodel table\n");
}

$sql = "CREATE TABLE IF NOT EXISTS `sccpline` (
  `id` varchar(45) default NULL,
  `pin` varchar(45) default NULL,
  `label` varchar(45) default NULL,
  `description` varchar(45) default NULL,
  `context` varchar(45) default NULL,
  `incominglimit` varchar(45) default NULL,
  `transfer` varchar(45) default NULL,
  `mailbox` varchar(45) default NULL,
  `vmnum` varchar(45) default NULL,
  `cid_name` varchar(45) default NULL,
  `cid_num` varchar(45) default NULL,
  `trnsfvm` varchar(45) default NULL,
  `secondary_dialtone_digits` varchar(45) default NULL,
  `secondary_dialtone_tone` varchar(45) default NULL,
  `musicclass` varchar(45) default NULL,
  `language` varchar(45) default NULL,
  `accountcode` varchar(45) default NULL,
  `echocancel` varchar(45) default NULL,
  `silencesuppression` varchar(45) default NULL,
  `callgroup` varchar(45) default NULL,
  `pickupgroup` varchar(45) default NULL,
  `amaflags` varchar(45) default NULL,
  `dnd` varchar(5) default 'on',
  `setvar` varchar(50) default NULL,
  `name` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;";
$check = $db->query($sql);
if(DB::IsError($check)) {
    die_freepbx("Can not add sccpline table\n");
}

$sql = "ALTER TABLE sccpline
       ALTER COLUMN incominglimit SET DEFAULT '2',
       ALTER COLUMN transfer SET DEFAULT 'on',
       ALTER COLUMN vmnum SET DEFAULT '*97',
       ALTER COLUMN musicclass SET DEFAULT 'default',
       ALTER COLUMN echocancel SET DEFAULT 'on',
       ALTER COLUMN silencesuppression SET DEFAULT 'off',
       ALTER COLUMN dnd SET DEFAULT 'on'
" ;

$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not modify sccpline table\n");
}

$sql = "CREATE TABLE IF NOT EXISTS `sccpdevice` (
  `type` varchar(45) default NULL,
  `addon` varchar(45) default NULL,
  `description` varchar(45) default NULL,
  `tzoffset` varchar(5) default NULL,
  `transfer` varchar(5) default 'on',
  `cfwdall` varchar(5) default 'on',
  `cfwdbusy` varchar(5) default 'on',
  `dtmfmode` varchar(10) default NULL,
  `imageversion` varchar(45) default NULL,
  `deny` varchar(45) default NULL,
  `permit` varchar(45) default NULL,
  `dndFeature` varchar(5) default 'on',
  `directrtp` varchar(3) default 'off',
  `earlyrtp` varchar(8) default 'off',
  `mwilamp` varchar(5) default 'on',
  `mwioncall` varchar(5) default 'off',
  `pickupexten` varchar(5) default 'on',
  `pickupcontext` varchar(100) default '',
  `pickupmodeanswer` varchar(5) default 'on',
  `private` varchar(5) default 'off',
  `privacy` varchar(100) default 'full',
  `nat` varchar(15) default 'off',
  `softkeyset` varchar(100) default '',
  `audio_tos` varchar(11) default NULL,
  `audio_cos` varchar(1) default NULL,
  `video_tos` varchar(11) default NULL,
  `video_cos` varchar(1) default NULL,
  `conf_allow` varchar(3) default 'on',
  `conf_play_general_announce` varchar(3) default 'on',
  `conf_play_part_announce` varchar(3) default 'on',
  `conf_mute_on_entry` varchar(3) default 'off',
  `conf_music_on_hold_class` varchar(80) default 'default',
  `setvar` varchar(100) default NULL,
  `disallow` varchar(255) DEFAULT NULL,
  `allow` varchar(255) DEFAULT NULL,
  `backgroundImage` varchar(255) DEFAULT NULL,
  `ringtone` varchar(255) DEFAULT NULL,
  `name` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;";
$check = $db->query($sql);
if(DB::IsError($check)) {
    die_freepbx("Can not add sccpdevice table\n");
}

$sql = "ALTER TABLE sccpdevice

       ALTER COLUMN transfer SET DEFAULT 'on',
       ALTER COLUMN cfwdall SET DEFAULT 'on',
       ALTER COLUMN cfwdbusy SET DEFAULT 'on',
       ALTER COLUMN dtmfmode SET DEFAULT 'outofband',
       ALTER COLUMN dndFeature SET DEFAULT 'on',
       ALTER COLUMN directrtp SET DEFAULT 'off',
       ALTER COLUMN earlyrtp SET DEFAULT 'progress',
       ALTER COLUMN mwilamp SET DEFAULT 'on',
       ALTER COLUMN mwioncall SET DEFAULT 'on',
       ALTER COLUMN pickupexten SET DEFAULT 'on',
       ALTER COLUMN pickupmodeanswer SET DEFAULT 'on',
       ALTER COLUMN private SET DEFAULT 'on',
       ALTER COLUMN privacy SET DEFAULT 'off',
       ALTER COLUMN nat SET DEFAULT 'off',
       ALTER COLUMN softkeyset SET DEFAULT 'softkeyset'
" ;

$check = $db->query($sql);
if(DB::IsError($check)) {
	die_freepbx("Can not modify sccpdevice table\n");
}


$sql = "CREATE TABLE IF NOT EXISTS `buttonconfig` (
  `device` varchar(15) NOT NULL default '',
  `instance` tinyint(4) NOT NULL default '0',
  `type` enum('line','speeddial','service','feature','empty') NOT NULL default 'line',
  `name` varchar(36) default NULL,
  `options` varchar(100) default NULL,
  PRIMARY KEY  (`device`,`instance`),
  KEY `device` (`device`),

  FOREIGN KEY (device) REFERENCES sccpdevice(name) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;";

$check = $db->query($sql);
if(DB::IsError($check)) {
    die_freepbx("Can not add buttonconfig table\n");
}
