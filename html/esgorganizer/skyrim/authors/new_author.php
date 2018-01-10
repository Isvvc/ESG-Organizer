<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Add Author";
	include("header.php");
?><?php
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add author form
		$name=mysqli_real_escape_string($db,$_POST['name']);
		$nexus=$_POST['nexus'];
		$other=mysqli_real_escape_string($db,$_POST['other']);
		
		// Get the mod author's Nexus ID if a nexusmods url was provided
		if ($nexus) $nexus=nexusIdFromURL($nexus);
		
		// Get the mod author's name if the box was selected to automatically get it
		if($_POST['autoNameCheckbox']) $name=authorNameFromNexusId($nexus);
		
		//Initialize categories and content
		$categories=postTableCheckboxes($db,"categories",$_POST);
		$content=postSetCheckboxes($db,"authors","content",$_POST);
		
		// Create the MySQL query to insert a author based on the stored POST values
		$query ="INSERT INTO authors(";
		$query.="name,nexusId,link";
		$query.=",content";
		$query.=") VALUES (";
		$query.=" '$name','".mysqli_real_escape_string($db,$nexus)."','$other'";
		$query.=",'";
		// Add a the content type and a comma if that armor type was selected. Leave blank if not
		foreach($content as $value) {
			$query.="$value,";
		}
		$query.="'";
		$query.=")";
		
		$result=dbquery($db,$query);
		
		//Get the id of the author that was just inserted
		$authorId=mysqli_insert_id($db);
		
		// Insert the categories
		if(!empty($categories)){
			$query ="INSERT INTO authorCategories(";
			$query.="author,category";
			$query.=") VALUES ";
			// Go through each value of $categories (which got its values from the function postSetCheckboxes) and add the values from it to the query
			foreach($categories as $value){
				$query.="( ".mysqli_real_escape_string($db,$authorId).", ".mysqli_real_escape_string($db,$value)." ),";
			}
			$query =rtrim($query,', ');
			$query.=";";

			$result=dbquery($db,$query);
			
			// Redirect to the the Authors page
			redirect_to(".");
		}
	}
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<p><a href=".">Back</a></p>
		<h2>New Author</h2>
		<form action="" method="POST">
			<p>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name">
			</p>
			<p>
				<?php hideTextOnCheckbox("autoName","autoNameCheckbox","name"); ?>
				<input type="checkbox" onClick="autoName()" name="autoNameCheckbox" id="autoNameCheckbox">
				<label for="autoNameCheckbox">Automatically fetch author name from the Nexus</label>
			</p>
			<p>
				<label for="name">Nexusmods URL:</label>
				<input type="text" name="nexus" id="nexus">
			</p>
			<p>
				<label for="name">Other URL:</label>
				<input type="text" name="other" id="other">
			</p>
			<?php echo tableCheckboxes($db,'categories','Categories'); ?>
			<?php echo setCheckboxes($db,"authors","content","Content",true); ?>
			<p><input type="submit" name="submit" value="Add author"></p>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>