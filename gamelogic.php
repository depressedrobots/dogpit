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
	global $GRID_COLUMNS, $GRID_ROWS, $matchesTableName, $actionsTableName;
	
	// init empty grid 
	$gridArray = Array();
	for( $i = 0; $i < ($GRID_COLUMNS*$GRID_ROWS); $i++ )
	{
		array_push( $gridArray, 0 );
	}
	
	// get all actions of this match
	include_once("connect.php");	
	$result = mysql_query("SELECT * FROM $actionsTableName WHERE matchID=$matchid");
	if( !$result )
	{
		return $gridArray;
	}
	
	$num_rows = mysql_num_rows($result);
	if( $num_rows != 0 )
	{
		//iterate over all actions and build array
		while($row = mysql_fetch_array($result))
		{
			$playerNumber = $row['playernumber'];
			$action = $row['action'];
			$gridArray = applyActionToGridArray($gridArray, $playerNumber, $action);
		}
	}
	
	return $gridArray;
}

// throws in a piece in a grid. expects a legal action and playerNumber 
function applyActionToGridArray($gridArray, $playerNumber, $action)
{	
	global $GRID_COLUMNS, $GRID_ROWS, $matchesTableName, $actionsTableName;
	
	$actionColumn = 0;
	if( $action == 'b' ) $actionColumn = 1;
	if( $action == 'c' ) $actionColumn = 2;
	if( $action == 'd' ) $actionColumn = 3;
	if( $action == 'e' ) $actionColumn = 4;
	if( $action == 'f' ) $actionColumn = 5;
	if( $action == 'g' ) $actionColumn = 6;
	
	//compute landing row for the piece
	$nextFreeRow = $GRID_ROWS - 1;		//begin at the bottom
	$gridIndex = $actionColumn + ($GRID_COLUMNS * $nextFreeRow);
	while( $gridArray[$gridIndex] != 0 )
	{
		$nextFreeRow--;		//work your way up
		if( $nextFreeRow < 0 )
		{	
			// error! no space left in this column!
			return null;
		}
		
		$gridIndex = $actionColumn + ($GRID_COLUMNS * $nextFreeRow);
	}
	
	// free space found! mark it and return the grid array
	$gridArray[$gridIndex] = $playerNumber;
	
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
	if( applyActionToGridArray($gridArray, $playerNumber, $action) == null )
	{
		return false;
	}

	return true;
}

//CAUTION: checks only whether the grid is fully occupied, not if someone has four connected!
function isDrawGame($gridArray)
{
	global $GRID_COLUMNS, $GRID_ROWS, $matchesTableName, $actionsTableName;
	
	for( $i = 0; $i < $GRID_COLUMNS*$GRID_ROWS; $i++ )
	{
		if( $gridArray[$i] == 0 )
		{
			return false;
		}
	}

	return true;
}

//computes the game status.
//@return: -1 game still open; 0 draw game; 1 player one won; 2 player two won
function isGameOver($gridArray)
{
	if( isDrawGame($gridArray) == true)
	{
		return 0;
	}
	
	return -1;
}

?>