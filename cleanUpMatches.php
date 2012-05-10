<?php

include_once("connect.php");

//delete all timed out matches

//5 min
$timeout = date("Y-m-d H:i:s",mktime(date("H"),(date("i")-5),date("s"),date("m"),date("d"),date("Y")));

//matches that nver started
mysql_query("DELETE FROM $matchesTableName WHERE lastupdate < '$timeout' AND status='waiting for second player'") 
or die(mysql_error()); 

//matches that have been begun but abandoned
mysql_query("DELETE FROM $matchesTableName WHERE lastupdate < '$timeout' AND status=player1") 
or die(mysql_error()); 
mysql_query("DELETE FROM $matchesTableName WHERE lastupdate < '$timeout' AND status=player2") 
or die(mysql_error()); 

?>
