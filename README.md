aibattle-server
===============

the AIBattle PHP matchmaking server


table information:
==================

1) matches table (aka. "$matchesTableName" ):

CREATE TABLE `aimatches` (
  `ID` int(11) NOT NULL auto_increment,
  `player1` varchar(50) collate latin1_german1_ci NOT NULL default 'unknown',
  `player2` varchar(50) collate latin1_german1_ci NOT NULL default 'unknown',
  `p1token` char(10) collate latin1_german1_ci NOT NULL,
  `p2token` char(10) collate latin1_german1_ci NOT NULL,
  `status` varchar(50) collate latin1_german1_ci NOT NULL default 'waiting for p1',
  `lastupdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci COMMENT='manages all the ai matches' AUTO_INCREMENT=18 ;


2) actions table (aka. "$actionsTableName"):

CREATE TABLE `aimatches_actions` (
  `ID` int(11) NOT NULL,
  `matchID` int(11) NOT NULL,
  `playername` varchar(50) collate latin1_german1_ci NOT NULL,
  `playertoken` char(10) collate latin1_german1_ci NOT NULL,
  `action` char(1) collate latin1_german1_ci NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;