<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Add Character";
	include("header.php");
?><?php
	if(isset($_POST['submit'])){
		// Store the data POSTed from the add character form
		$name=mysqli_real_escape_string($db,$_POST['name']);
		$race=mysqli_real_escape_string($db,$_POST['races']);
		$gender=mysqli_real_escape_string($db,$_POST['gender']);
		$morality=mysqli_real_escape_string($db,$_POST['morality']);
		$roleplay=mysqli_real_escape_string($db,$_POST['roleplay']);
		
		//Initialize combatStyles, armorTypes, and skills
		$combatStyles=postTableCheckboxes($db,"combatStyles",$_POST);
		$armorTypes=postSetCheckboxes($db,"characters","armorTypes",$_POST);
		$skills=postTableCheckboxes($db,"skills",$_POST);
		
		// Create the MySQL query to insert a character based on the stored POST values
		$query ="INSERT INTO characters(";
		$query.="name,race,gender,morality,roleplay";
		$query.=",armorTypes";
		$query.=") VALUES (";
		$query.=" '$name','$race','$gender','$morality','$roleplay'";
		$query.=",'";
		// Add a the armor type and a comma if that armor type was selected. Leave blank if not
		foreach($armorTypes as $value) {
			$query.="$value,";
		}
		$query.="'";
		$query.=")";
		
		$result=dbquery($db,$query);
		
		// Get the id of the character that was just inserted
		$characterId=mysqli_insert_id($db);
		
		// Insert the combat styles
		if(!empty($combatStyles)){
			$query ="INSERT INTO characterCombatStyles(";
			$query.="character_id,combatStyle";
			$query.=") VALUES ";
			// Go through each value of $armorTypes (which got its values from the function postSetCheckboxes) and add the values from it to the query
			foreach($combatStyles as $value){
				$query.="( ".mysqli_real_escape_string($db,$characterId).", ".mysqli_real_escape_string($db,$value)." ),";
			}
			// Remove the extra comma from the end
			$query =rtrim($query,', ');
			$query.=";";
			
			$result=dbquery($db,$query);
		}
		
		// Insert the skills
		if(!empty($skills)){
			$query ="INSERT INTO characterSkills(";
			$query.="character_id,skill";
			$query.=") VALUES ";
			// Go through each value of $skills (which got its values from the function postTableCheckboxes) and add the values from it to the query
			foreach($skills as $value){
				$query.="( ".mysqli_real_escape_string($db,$characterId).", ".mysqli_real_escape_string($db,$value)." ),";
			}
			$query =rtrim($query,', ');
			$query.=";";
			
			$result=dbquery($db,$query);
		}
	}
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<p><a href=".">Back</a></p>
		<h2>New Character</h2>
		<form action="" method="POST">
			<p>
				<label for="name">Name:</label>
				<input type="text" name="name" id="name">
			</p>
			<p><?php echo tableDropdown($db,'races','Race',false); ?></p>
			<p><?php echo enumDropdown($db,'characters','gender',true); ?></p>
			<p><?php echo enumDropdown($db,'characters','morality',true); ?></p>
			   <?php echo tableCheckboxes($db,'combatStyles','Combat Styles'); ?>
			   <?php echo setCheckboxes($db,"characters","armorTypes","Armor Types",true); ?>
			   <?php echo tableCheckboxes($db,'skills','Skills'); ?>
			<label for="roleplay">Roleplay: </label>
			<table>
				<tr>
					<th style="border:0px;"><input type="range" name="roleplay" id="roleplay" min="0" max="4" oninput="roleplayUpdate(this.value)"></th>
					<th id="roleplayth" style="border:0px;">2</th>
					<script type="text/javascript">
						function roleplayUpdate(val){
							document.getElementById("roleplayth").innerHTML = val;
						}
					</script>
				</tr>
			</table>
			<p><input type="submit" name="submit" value="Add character"></p>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>