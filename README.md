dogpit formerly known as aibattle-server
========================================

the AIBattle PHP matchmaking server


connect.php
===========

<?php

// db connection info. very secret!
$host = "XXXXX";
$user = "XXXXX"; 
$pass= "XXXXXX";
$database = "XXXXXXXX";

// table names
$matchesTableName = 'aimatches';
$actionsTableName = 'aimatches_actions';

// strings
$waitingForPlayersString = "waiting for second player";
$drawGameString = "draw game";
$playerWonSring1 = "player ";
$playerWonSring2 = " won";


$con = @mysql_connect($host,$user, $pass);

if (!$con) 
{
  die( "error! Unable to connect to the database server at this time." );
}

if (! @mysql_select_db($database) )
{
	die( "error! Unable to find database" );
}

?>


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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci COMMENT='manages all the ai matches' AUTO_INCREMENT=1 ;


2) actions table (aka. "$actionsTableName"):

CREATE TABLE `aimatches_actions` (
  `ID` int(11) NOT NULL auto_increment,
  `matchID` int(11) NOT NULL,
  `playernumber` tinyint(4) NOT NULL default '0',
  `action` char(1) collate latin1_german1_ci NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=1 ;
