<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Mods";
	include("header.php");
?><?php
	if(isset($_GET['submit'])){
		//If authors have been filtered
		// put the checked categories and content into arrays;
		$categories=postTableCheckboxes($db,"categories",$_GET);
		$content=postSetCheckboxes($db,"authors","content",$_GET);

		if(!empty($categories)){
			//If there are checked categories
			// Query to get the authors who have any of the checked categories
			$query="SELECT authors.* FROM authors INNER JOIN authorCategories ON authors.id=authorCategories.author WHERE ( ";

			foreach($categories as $value){
				$query.="category=$value OR ";
			}
			$query =rtrim($query," RO");
			$query.=") AND ";
		}else{
			//If no categories where checked
			$query="SELECT * FROM authors WHERE ";
		}

		if(!empty($content)){
			//If content was checked
			// Query authors who have that content in their set
			foreach($content as $value){
				$query.="FIND_IN_SET('$value', content)>0 OR ";
			}
			$query=rtrim($query," RO");
		}else{
			//If no content was checked, ignore this step by ouputting 1
			$query.="1";
		}

	}else{
		//If there is no filter, get all authors
		$query="SELECT * FROM mods";
	}
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<h2>Mods</h2>
		<?php
			// Get authors from the querty described above
			$result=dbquery($db,$query);
		?>
		
		<table>
			<tr>
				<th>Name</th>
				<th>Nexus ID</th>
				<th>Link</th>
				<th>Categories</th>
				<th>Author</th>
				<th></th>
			</tr>
			<?php
				while($row=mysqli_fetch_assoc($result)){
					$output ="<tr>";
					$output.="<td>";
						$output.=$row["name"];
					$output.="</td>";
					$output.="<td>";
						// If the author has a Nexus ID, create a link to their account.
						#$output.=($row["nexusId"])?createNexusLink($row["nexusId"],0):"";
						$query="SELECT nexus FROM modsNexus WHERE id={$row["id"]}";
						$result2=dbquery($db,$query);
						$row2=mysqli_fetch_assoc($result2);
						$nexus=$row2["nexus"];
						if($nexus!=="") $output.=createNexusLink($nexus,1);
						mysqli_free_result($result2);
					$output.="</td>";
					$output.="<td>";
						// Create a link to the mod's external site
						$query="SELECT link FROM modsExternal WHERE id={$row["id"]}";
						$result2=dbquery($db,$query);
						$row2=mysqli_fetch_assoc($result2);
						$other=$row2["link"];
						if($other!=="") $output.="<a href=\"$other\" target=\"blank\">$other</a>";
						mysqli_free_result($result2);
					$output.="</td>";
					$output.="<td>";
						// Query the categories this author has
						$query="SELECT categories.name FROM modCategories INNER JOIN categories ON modCategories.category=categories.id WHERE mod_id={$row["id"]}";
						$result2=dbquery($db,$query);
						// Output the author's categories
						while($row2=mysqli_fetch_assoc($result2)){
							$output.="{$row2["name"]}, ";
						}
						$output=rtrim($output,', ');	// Remove a trailing comma
					$output.="</td>";
					$output.="<td>";
						// Get the mod author's name
						$query="SELECT name FROM authors WHERE id={$row["author"]}";
						$result2=dbquery($db,$query);
						$row2=mysqli_fetch_assoc($result2);
						$output.=$row2["name"];
					$output.="</td>";
					$output.="<td>";
					echo $output;
					// Add a button to edit or delete author with a confirmation pop-up box.
					?>
						<form action= "edit_author" method="get">
							<input type="submit" value="Edit">
							<input type="hidden" name="id" value=<?php echo '"'.$row["id"].'"' ?> >
						</form>
						<form action="delete_author" method="POST" >
							<input type="submit" name="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this category?');">
							<input type="hidden" name="id" value=<?php echo '"'.$row["id"].'"' ?> >
						</form>
						
					<?php
					$output ="</td>";
					$output.="</tr>";
					echo $output;
				}
			?>
		</table>

		<?php
			//Free query result
			mysqli_free_result($result);
		?>

		<p><a href="new_mod">New Mod</a></p>
		
		<h3>Filter</h3>
		<p>Will show mod authors who have any of the checked items. Having nothing in a set checked acts as having everything checked.</p>
		<form action="" method="GET">
			<?php echo tableCheckboxes($db,"categories","Categories",$categories); ?>
			<?php echo setCheckboxes($db,"authors","content","Content",true,$content); ?>
			<p><input type="submit" name="submit" value="Filter"></p>
		</form>
	</div>
</div>

<?php include("footer.php"); ?>
