<?php

/*
 * make sure all parameters are set
 */
if( !isset($_GET['action']) )
{
	die("error! action not set");
}

if( !isset($_GET['matchid']) )
{
	die("error! match id not set");
}

if( !isset($_GET['playername']) )
{
	die("error! player name not set");
}

if( !isset($_GET['token']) )
{
	die("error! token not set");
}


/*
 * security check. is the player allowed to post an action at all?
 */
include_once("connect.php");

$action = mysql_real_escape_string($_GET['action']);
$action = strtolower($action);
$matchid = mysql_real_escape_string($_GET['matchid']);
$playername = mysql_real_escape_string($_GET['playername']);
$token = mysql_real_escape_string($_GET['token']);

$result = mysql_query("SELECT * FROM $matchesTableName WHERE ID='$matchid'");

$num_rows = mysql_num_rows($result);

if( $num_rows == 0 )
{
	die("error! match id not found.");
}
else if( $num_rows != 1 )
{
	die("error! multiple matches found with this id. wtf?");
}

$row = mysql_fetch_array($result);

//is the player name registered in the match?
if( $playername != $row['player1'] && $playername != $row['player2'] )
{
	die("error! player name is not registered in match.");
}

//compare secret tokens
$p1name = $row['player1'];
$p2name = $row['player2'];
if( 	($playername == $p1name && $token != $row['p1token'] )
	||	($playername == $p2name && $token != $row['p2token'] )	)
{
	die("error! wrong secret token.");
}

//check game status. has it already started at all?
if( $row['status'] == $waitingForPlayersString )
{
	die("error! match hasn't started, yet. Still waiting for player 2.");
}

// is it the player's turn?
if( $row['status'] != $playername )
{
	die("error! it's not your turn.");
}

//verify legal action
include_once("gamelogic.php");
$gridArray = getGridArray($matchid);
if( !isLegalAction($gridArray, $action) )
{
	die("error! illegal action");
}

//save action
$playernumber = $playername == $p1name ? 1 : 2;
$query = "INSERT INTO $actionsTableName (matchID,playernumber,action) VALUES ($matchid,$playernumber,'$action')";
mysql_query($query);

echo "query: $query<br>";

//game over?
$gameOver = isGameOver($gridArray);

if( $gameOver == -1 )	// game still in progress
{	
	//change status to next player's turn in matches table
	$nextPlayer = ++$playernumber;
	if($nextPlayer == 3)
	{
		$nextPlayer = 1;
	}
	$nextPlayerName = $nextPlayer == 1 ? $p1name : $p2name;
	
	$query = "UPDATE $matchesTableName SET status='$nextPlayerName' WHERE ID='$matchid'";
	mysql_query($query);
}
else if( $gameOver == 0 )	// draw game
{
	mysql_query("UPDATE $matchesTableName SET status='$drawGameString' WHERE ID='$matchid'");
}
else				// game over
{
	//with the last action, this player must have won.
	//update database accordingly and close the match.
	$statusString = $playerWonSring1.$playername.$playerWonSring2;
	mysql_query("UPDATE $matchesTableName SET status='$statusString' WHERE ID='$matchid'");
}

mysql_close($con);

echo "$gameOver";

?>