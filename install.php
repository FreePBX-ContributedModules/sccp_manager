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


?>


