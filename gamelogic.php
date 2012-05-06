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
        //0 is passed as $playerNumber because this is only a test if there is space left.
        //the returned array will not be used in any way
	if( applyActionToGridArray($gridArray, 0, $action) == null )
	{
		return false;
	}

	return true;
}

//helper function: changes x-y-coordinates to an index in the one-dimensional grid array
function getGridArrayIndex($x, $y)
{
    global $GRID_COLUMNS;
    $ret = ($x + ($y*$GRID_COLUMNS));
    return $ret;
}

//helper function: computes x-y-coordinates from an GridArray index. Pass x & y as references
function getCoordinatesOfGridArrayIndex($index, &$x, &$y)
{
    global $GRID_COLUMNS;
    $x = $index % $GRID_COLUMNS;
    $index -= $x;
    $y = $index / $GRID_COLUMNS;
}

// returns the gridIndex of the next piece in $direction from $pieceGrindIndex
// i.e. $pieceGridIndex = 3, $direction = right -> returns 4
// 0 = up, 2 = right, 4 = down, 6 = left
function getNextPieceInDirection($pieceGridIndex, $direction)
{
    $x = -1;
    $y = -1;
    getCoordinatesOfGridArrayIndex($pieceGridIndex, $x, $y);
    
    global $GRID_COLUMNS, $GRID_ROWS;
    
    //apply $direction to coordinates
    if( $direction >= 5 )
    {
        $x -= 1;
    }
    else if( $direction >= 1 && $direction <= 3)
    {
        $x += 1;
    }
    
    if( $direction == 7 || $direction == 0 || $direction == 1 )    //7, 0, 1 are all upish
    {
        $y -= 1;
    }
    else if( $direction == 3 || $direction == 4 || $direction == 5 )    //3, 4, 5 are all downish
    {
        $y += 1;
    }
    
    //check for illegal moves
    if( $x < 0 || $x >= $GRID_COLUMNS || $y < 0 || $y >= $GRID_ROWS )
    {
        return -1;
    }
    
    return getGridArrayIndex($x, $y);
}

//checks grid for four of $player's pieces in a row
function hasPlayerWon($gridArray, $player)
{
    // naive implementation: check from every of the player's stones recursively in every direction
    // maybe I'll optimize this later on...
    global $GRID_COLUMNS, $GRID_ROWS;
	
    for( $i = 0; $i < $GRID_COLUMNS*$GRID_ROWS; $i++ )
    {
        //ignore empty fields or opponent's pieces
        if( $gridArray[$i] != $player )
        {
                continue;
        }
        
        //check in every direction
        for( $direction = 0; $direction < 8; $direction++)
        {
            $piecesInARow = 1;      //the starting piece counts already as first piece in the row
            $piecesInARow += checkPiecesInDirection($i, $player, $direction, $gridArray);   //check for other pieces in same direction
            if( $piecesInARow >= 4 )
            {
                return true;
            }
        }
    }
    
    return false;
}

function checkPiecesInDirection($gridIndex, $player, $direction, $gridArray)
{
    $return = 0;
    
    //get next piece in direction
    $nextPieceIndex = getNextPieceInDirection($gridIndex, $direction);
    
    if( $nextPieceIndex == -1 )
    {
        return $return;
    }
    
    if( $gridArray[$nextPieceIndex] == $player )
    {
        $return++;
        //check next piece in same direction
        $return += checkPiecesInDirection($nextPieceIndex, $player, $direction, $gridArray);
    }
    
    return $return;
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
        if( hasPlayerWon($gridArray, "1") )
        {
            return 1;
        }
        else if ( hasPlayerWon($gridArray, "2") )
        {
            return 2;
        }
    
	if( isDrawGame($gridArray) == true)
	{
		return 0;
	}
	
	return -1;
}

?>