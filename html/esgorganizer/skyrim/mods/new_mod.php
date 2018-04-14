<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Add Mod";
	include("header.php");
?><?php
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add mod form
		$name=mysqli_real_escape_string($db,$_POST['name']);
		$nexus=$_POST['nexus'];
		$other=mysqli_real_escape_string($db,$_POST['other']);
		$author=mysqli_real_escape_string($db,$_POST['authorId']);
		
		// Get the mod mod's Nexus ID if a nexusmods url was provided
		if ($nexus) $nexus=nexusIdFromURL($nexus);
		
		// Get the mod mod's name if the box was selected to automatically get it
		if($_POST['autoNameCheckbox']) $name=nameFromNexusId($nexus,1);
		
		//Initialize categories and content
		$categories=postTableCheckboxes($db,"categories",$_POST);
		$content=postSetCheckboxes($db,"authors","content",$_POST);
		
		// Create the MySQL query to insert a author based on the stored POST values
		$query ="INSERT INTO mods(";
		$query.="name,author";
		$query.=") VALUES (";
		$query.=" '$name','$author'";
		$query.=")";

		echo $query;
		$result=dbquery($db,$query);
		
		//Get the id of the author that was just inserted
		$modId=mysqli_insert_id($db);
		
		// Insert the categories
		if(!empty($categories)){
			$query ="INSERT INTO modCategories(";
			$query.="mod_id,category";
			$query.=") VALUES ";
			// Go through each value of $categories (which got its values from the function postSetCheckboxes) and add the values from it to the query
			foreach($categories as $value){
				$query.="( ".mysqli_real_escape_string($db,$modId).", ".mysqli_real_escape_string($db,$value)." ),";
			}
			$query =rtrim($query,', ');
			$query.=";";

			$result=dbquery($db,$query);			
			
		}

		if(isset($nexus) && $nexus!==''){
			$query ="INSERT INTO modsNexus(";
			$query.="id,nexus";
			$query.=") VALUES (";
			$query.="$modId,$nexus";
			$query.=")";

			$result=dbquery($db,$query);
		}
		
		if(isset($other) && $other!==''){
			$query ="INSERT INTO modsExternal(";
			$query.="id,link";
			$query.=") VALUES (";
			$query.="$modId,'$other'";
			$query.=")";

			$result=dbquery($db,$query);
		}

		// Redirect to the the mods page
		redirect_to(".");
	}
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<p><a href=".">Cancel</a></p>
		<h2>New Mods</h2>
		<form action="" method="POST">
			<p>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name">
			</p>
			<p>
				<?php hideTextOnCheckbox("autoName","autoNameCheckbox","name"); ?>
				<input type="checkbox" onClick="autoName()" name="autoNameCheckbox" id="autoNameCheckbox">
				<label for="autoNameCheckbox">Automatically fetch mod name from the Nexus</label>
			</p>
			<p>
				<label for="name">Nexusmods URL:</label>
				<input type="text" name="nexus" id="nexus">
			</p>
			<p>
				<label for="name">Other URL:</label>
				<input type="text" name="other" id="other">
			</p>
			<p>
				<?php echo authorDropdown($db,true); ?>
			</p>
				<?php echo tableCheckboxes($db,'categories','Categories'); ?>
			<p><input type="submit" name="submit" value="Add mod"></p>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>
