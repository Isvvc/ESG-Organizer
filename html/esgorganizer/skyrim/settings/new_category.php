<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	#$title="ESG Organizer - Skyrim Add Category";
	#include("header.php");
?><?php
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add author form
		$name=mysqli_real_escape_string($db,$_POST['name']);
		
		// Create the MySQL query to insert a author based on the stored POST values
		$query ="INSERT INTO categories(";
		$query.="name";
		$query.=") VALUES (";
		$query.="'$name'";
		$query.=")";
		
		$result=dbquery($db,$query);
		#echo $query;
		
		redirect_to(".");
	}
?>
