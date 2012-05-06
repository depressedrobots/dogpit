<?php

include_once("connect.php");

$result = mysql_query("SELECT * FROM $matchesTableName") 
or die(mysql_error());  

echo "<table border='1'>";
echo "<tr> <th>ID</th> <th>P1</th><th>P2</th> <th>status/turn</th><th>last update</th> </tr>";
while($row = mysql_fetch_array( $result )) {
	echo "<tr><td>"; 
	echo $row['ID'];
	echo "</td><td>"; 
	echo $row['player1'];
	echo "</td><td>"; 
	echo $row['player2'];
	echo "</td><td>"; 
	echo $row['status'];
	echo "</td><td>"; 
	echo $row['lastupdate'];
	echo "</td></tr>"; 
} 

echo "</table><br><br>";

echo "usage:<br><br>";

echo "<strong>Request new match: </strong>aibattle.soulmates-game.de/requestmatch.php?playername=YourName<br>returns: \"[match-ID];[YOUR PLAYER TOKEN]\", i.e. \"13;NIHWPBV06M\"";

echo "<br><br>";

echo "<strong>Request match status: </strong>aibattle.soulmates-game.de/getmatchstatus.php?matchid=MatchID<br>returns: \"[STATUS or TURN];[PLAYER NUMBER]:[ACTION];[PLAYER NUMBER]:[ACTION];...\"";

echo "<br><br>";

echo "<strong>Post action: </strong>aibattle.soulmates-game.de/postaction.php?matchid=MatchID&playername=YourPlayerName&token=YourSecretToken&action=YourAction";

?>