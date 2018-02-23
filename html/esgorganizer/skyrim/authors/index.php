<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Mod Authors";
	include("header.php");
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<h2>Mod Authors</h2>
		<?php
			// Query to get all of the authors
			$query="SELECT * FROM authors";
			$result=dbquery($db,$query);
		?>
		
		<table>
			<tr>
				<th>Name</th>
				<th>Nexus ID</th>
				<th>Link</th>
				<th>Categories</th>
				<th>Content</th>
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
						if($row["nexusId"]) $output.=createNexusLink($row["nexusId"],0);
					$output.="</td>";
					$output.="<td>";
						// Create a link to the author's external site
						$output.=($row["link"])?"<a href=\"{$row["link"]}\" target=\"blank\">{$row["link"]}</a>":"";
					$output.="</td>";
					$output.="<td>";
						// Query the categories this author has
						$query="SELECT categories.name FROM authorCategories INNER JOIN categories ON authorCategories.category=categories.id WHERE author={$row["id"]}";
						$resultCategories=dbquery($db,$query);
						// Output the author's categories
						while($rowCategories=mysqli_fetch_assoc($resultCategories)){
							$output.="{$rowCategories["name"]}, ";
						}
						$output=rtrim($output,', ');	// Remove a trailing comma
					$output.="</td>";
					$output.="<td>";
						// Output the author's content types
						$output.=($row["content"]?(preg_replace('(,)','$0 ',$row["content"])):"");
					$output.="</td>";
					$output.="<td>";
					echo $output;
					// Add a button to edit or delete author with a confirmation pop-up box.
					?>
						<form action= "edit_author" method="get">
							<input type="submit" value="Edit">
							<input type="hidden" name="id" value=<?php echo '"'.$row["id"].'"' ?> >
						</form>
						<form action="delete_author" method="post" onsubmit="return confirm('Are you sure you want to delete this author?');">
							<input type="submit" value="Delete">
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

		<p><a href="new_author">New Author</a></p>
		
	</div>
</div>

<?php include("footer.php"); ?>
