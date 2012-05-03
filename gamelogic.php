<?php

/*
 * all game logic matters go here
 */
$GRID_COLUMNS = 7;
$GRID_ROWS = 6;

//generates the current grid of a match and returns it as an array. the array starts in the topleft corner.
//It's an integer array with either 0 for empty space, 1 for player 1's stone or 2 for player 2's stone.
function getGridArray($matchid)
{
	$gridArray = Array();
	
	// get all actions of this match
	include_once("connect.php");	
	$result = mysql_query("SELECT * FROM $actionsTableName WHERE ID='$matchid'");
	if( !$result )
	{
		return $gridArray;
	}
	
	$num_rows = mysql_num_rows($result);
	if( $num_rows != 0 )
	{
		//iterate over actions and build array
		while($row = mysql_fetch_array($result))
		{
			$playername = $row['playername'];
			$action = $row['action'];
			$returnString .= ";$playername:$action";
		}
	}
	
	return $gridArray;
}

 
//check for legal action
//action must be a single char from 'a' to 'g'
function isLegalAction($gridArray, $action)
{
	//trivial check first
	if( 	$action != 'a'
		&&	$action != 'b'
		&&	$action != 'c'
		&&	$action != 'd'
		&&	$action != 'e'
		&&	$action != 'f'
		&&	$action != 'g' )
	{
		return false;
	}
	
	//now the not-so-trivial check: is there space left in this columns?

	return true;
}

//computes the game status.
//@return: -1 game still open; 0 draw game; 1 player one won; 2 player two won
function isGameOver($gridArray)
{
	return -1;
}

?>