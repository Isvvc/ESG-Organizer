<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Edit Author";
	include("header.php");
?><?php
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add author form
		$name=mysqli_real_escape_string($db,$_POST['name']);
		$nexus=$_POST['nexus'];
		$other=mysqli_real_escape_string($db,$_POST['other']);
		$id=$_POST['id'];
		
		// Get the mod author's Nexus ID if a nexusmods url was provided
		if ($nexus) $nexus=nexusIdFromURL($nexus);
		
		// Get the mod author's name if the box was selected to automatically get it
		if($_POST['autoNameCheckbox']) $name=authorNameFromNexusId($nexus);
		
		//Initialize categories and content
		$categories=postTableCheckboxes($db,"categories",$_POST);
		$content=postSetCheckboxes($db,"authors","content",$_POST);
		
		// Create the MySQL query to update the author based on the stored POST values
		$query ="UPDATE authors SET ";
		$query.="name='".mysqli_real_escape_string($db,$name)."',";
		$query.="nexusId='".mysqli_real_escape_string($db,$nexus)."',";
		$query.="link='".mysqli_real_escape_string($db,$other)."',";

		$query.="content='";
		// Add a the content type and a comma if that armor type was selected. Leave blank if not
		foreach($content as $value) {
			$query.="$value,";
		}
		$query.="'";

		$query.=" WHERE id=$id";

		$result=dbquery($db,$query);

		// Modify the categories
		$toDelete=array();
		$toAdd=array();

		// Get the categories the author currently has
		$query ="SELECT category FROM authorCategories WHERE author=$id";
		$result=dbquery($db,$query);
		$current=array();
		while($row=mysqli_fetch_assoc($result)){
			array_push($current, $row["category"]);
		}

		// Go through the current categories. If one isn't in the new categories list, mark it for removal
		foreach($current as $value){
			if(!in_array($value,$categories)){
				array_push($toDelete,$value);
			}
		}

		// Go through the new categories. If one isn't already in the current categories, mark it for addition
		foreach($categories as $value){
			if(!in_array($value,$current)){
				array_push($toAdd,$value);
			}
		}

		// Add new categories
		if(!empty($toAdd)){
			$query ="INSERT INTO authorCategories(";
			$query.="author,category";
			$query.=") VALUES ";
			// Go through each value of $categories (which got its values from the function postSetCheckboxes) and add the values from it to the query
			foreach($toAdd as $value){
				$query.="( ".mysqli_real_escape_string($db,$id).", ".mysqli_real_escape_string($db,$value)." ),";
			}
			$query =rtrim($query,', ');
			$query.=";";

			$result=dbquery($db,$query);
		}

		// Remove old categories
		if(!empty($toDelete)){
			$query ="DELETE FROM authorCategories WHERE author=$id AND (";
			foreach($toDelete as $value){
				$query.="category=$value OR ";
			}
			$query =rtrim($query,' RO');
			$query.=")";

			$result=dbquery($db,$query);
		}

		// Redirect to the the Authors page
		redirect_to(".");
	}else{
		// Fill the forms with data from GET
		$authorId=$_GET['id'];

		$query="SELECT * FROM authors WHERE id=$authorId";
		$result=dbquery($db,$query);

		$row=mysqli_fetch_assoc($result);
		$name=$name=$row["name"];
		$nexus=$row["nexusId"];
		$other=$row["link"];
		$content=explode(",",$row["content"]);

		$query="SELECT category FROM authorCategories WHERE author=$authorId";
		$result=dbquery($db,$query);

		$categories=array();
		while($row=mysqli_fetch_assoc($result)){
			array_push($categories, $row["category"]);
		}
	}
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<p><a href=".">Cancel</a></p>
		<h2>Edit Author</h2>
		<form action="" method="POST">
			<p>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name" value="<?php echo htmlentities($name); ?>">
			</p>
			<p>
				<?php hideTextOnCheckbox("autoName","autoNameCheckbox","name"); ?>
				<input type="checkbox" onClick="autoName()" name="autoNameCheckbox" id="autoNameCheckbox">
				<label for="autoNameCheckbox">Automatically fetch author name from the Nexus</label>
			</p>
			<p>
				<label for="name">Nexusmods URL:</label>
				<input type="text" name="nexus" id="nexus" value="<?php echo htmlentities("http://www.nexusmods.com/skyrim/users/$nexus/"); ?>">
			</p>
			<p>
				<label for="name">Other URL:</label>
				<input type="text" name="other" id="other" value="<?php echo htmlentities($other); ?>">
			</p>
			<?php echo tableCheckboxes($db,'categories','Categories',$categories); ?>
			<?php echo setCheckboxes($db,"authors","content","Content",true,$content); ?>
			<input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
			<p><input type="submit" name="submit" value="Edit author"></p>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>
