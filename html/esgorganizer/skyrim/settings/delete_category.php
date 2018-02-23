<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	#$title="ESG Organizer - Skyrim Add Category";
	#include("header.php");
?><?php
	//print_r($_POST);
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add author form
		$id=mysqli_real_escape_string($db,$_POST['id']);
		
		// Create the MySQL query to insert a author based on the stored POST values
		$query ="DELETE FROM categories ";
		$query.="WHERE id=$id";

		$result=dbquery($db,$query);
		//echo $query;
		//echo "ayy lmao";	
		redirect_to(".");
	}
	//echo "you're right";
?>
