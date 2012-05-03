<?php

if( !isset($_GET['action']) )
{
	die("action not set");

}
include("connect.php");

$action = mysql_real_escape_string($_GET['action']);


mysql_query("INSERT INTO $table (action) VALUES ('$action')");

mysql_close($con);

header( 'Location: index.php' ) ;

?>