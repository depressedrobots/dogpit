<?php


// a client requests a new match. look for open matches or create a new one.


//get a random string for player tokens. player tokens will be given to each new player
function createPlayerToken()
{
	$arr = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'); // get all the characters into an array
    shuffle($arr); // randomize the array
    $arr = array_slice($arr, 0, 10); // get the first ten (random) characters out
    $str = implode('', $arr); // smush them back into a string
	
	return $str;
}

if( !isset($_GET['playername']) )
{
	die("playername not set");
}

include("connect.php");

$playername = mysql_real_escape_string($_GET['playername']);

$waitingForPlayersString = "waiting for second player";

$matchesTableName = 'aimatches';

$result = mysql_query("SELECT * FROM $matchesTableName WHERE status='$waitingForPlayersString' AND player1!='$playername'");

$num_rows = mysql_num_rows($result);

if( $num_rows == 0 )	//no match is wating for a second player
{
	// create new match and register as player1
	$p1token = createPlayerToken();
	mysql_query("INSERT INTO $matchesTableName (player1,p1token,status) VALUES ('$playername', '$p1token', '$waitingForPlayersString')");
	$result = mysql_query("SELECT * FROM $matchesTableName WHERE status='$waitingForPlayersString' AND player1='$playername' AND p1token='$p1token'");
	
	//get id of new match and return it
	$num_rows = mysql_num_rows($result);
	if( $num_rows != 1 )
	{
		die("error! created new match but could not find it!");
	}
	$row = mysql_fetch_array($result);
	$matchID = $row['ID'];
	echo $matchID.";".$p1token;
}
else	//found an open game. register as second player and return game ID
{
	$row = mysql_fetch_array($result);
	$matchID = $row['ID'];
	
	//check for same player name
	$p1name = $row['player1'];	
	if( $p1name == $playername )
	{
		die("error! player names are equal!");
	}
	
	//register as second player
	$p2token = createPlayerToken();
	mysql_query("UPDATE $matchesTableName SET player2='$playername' WHERE ID=$matchID");
	mysql_query("UPDATE $matchesTableName SET p2token='$p2token' WHERE ID=$matchID");
	
	//set new match status: pick a player to start
	$startPlayerIndex = mt_rand(0,1);
	$startPlayerName = $p1name;
	if( $startPlayerIndex == 1 )
	{
		$startPlayerName = $playername;
	}
	mysql_query("UPDATE $matchesTableName SET status='$startPlayerName' WHERE ID=$matchID");
	
	echo $matchID.";".$p2token;
}

mysql_close($con);

?>