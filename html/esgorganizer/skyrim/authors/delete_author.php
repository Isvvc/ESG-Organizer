<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
?><?php
	print_r($_POST);
	if(isset($_POST['submit'])){
		// Store the data POSTed
		$id=mysqli_real_escape_string($db,$_POST['id']);
		
		// Create the MySQL query to delete the author's category entries
		$query ="DELETE FROM authorCategories ";
		$query.="WHERE author=$id";

		$result=dbquery($db,$query);	
		
		// Create the MySQL query to delete the author
		$query ="DELETE FROM authors ";
		$query.="WHERE id=$id";

		$result=dbquery($db,$query);	
		redirect_to(".");
	}
?>