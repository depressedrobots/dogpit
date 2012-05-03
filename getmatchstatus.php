<?php
// a player querys the status of a match
// requires match-ID


if( !isset($_GET['matchid']) )
{
	die("error! matchid not set");
}

include("connect.php");

$matchid = mysql_real_escape_string($_GET['matchid']);

$matchesTableName = 'aimatches';
$actionsTableName = 'aimatches_actions';

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

//prepare string for script result
$returnString = $status;

//check for actions in this match
$result = mysql_query("SELECT * FROM $actionsTableName WHERE ID='$matchid'");
$num_rows = mysql_num_rows($result);
if( $num_rows != 0 )
{
	while($row = mysql_fetch_array($result))
	{
		$playername = $row['playername'];
		$action = $row['action'];
		$returnString .= ";$playername:$action";
	}
}

echo $returnString;


mysql_close($con);
?>