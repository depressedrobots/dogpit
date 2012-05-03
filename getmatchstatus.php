<?php
// a player querys the status of a match
// requires match-ID


if( !isset($_GET['matchid']) )
{
	die("error! matchid not set");
}

include_once("connect.php");

$matchid = mysql_real_escape_string($_GET['matchid']);

$result = mysql_query("SELECT * FROM $matchesTableName WHERE ID='$matchid'");

$num_rows = mysql_num_rows($result);

if( $num_rows == 0 )
{
	die("error! match id not found.");
}
else if( $num_rows != 1 )
{
	die("error! multiple matches found with this id.");
}

//get match details and print them
$row = mysql_fetch_array($result);
$status = $row['status'];
$p1name = $row['player1'];
$p2name = $row['player2'];

//prepare string for script result
$returnString = $status.";".$p1name." vs ".$p2name;

//check for actions in this match
$result = mysql_query("SELECT * FROM $actionsTableName WHERE matchID='$matchid'");
$num_rows = mysql_num_rows($result);
if( $num_rows != 0 )
{
	while($row = mysql_fetch_array($result))
	{
		$playerNumber = $row['playernumber'];
		$action = $row['action'];
		$returnString .= ";$playerNumber:$action";
	}
}

echo $returnString;


mysql_close($con);
?>