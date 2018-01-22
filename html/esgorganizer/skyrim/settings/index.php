<?php
	$thisFilePath="../../";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include("db_connection.php");
	include("functions.php");
	$title="ESG Organizer - Skyrim Settings";
	include("header.php");
?>

<div id="main">
	<div id="navigation">
		<?php echo navigation(); ?>
	</div>

	<div id="page">
		<h2>Categories</h2>
		<?php
			// Query to get all of the categories
			$query="SELECT * FROM categories";
			$result=dbquery($db,$query);
		?>
		
		<table>
			<tr><th>Category</th><th></th></tr>
			<?php
				while($row=mysqli_fetch_assoc($result)){
					$output ="<tr>";
					$output.="<td>";
						$output.=$row["name"];
					$output.="</td>";
					$output.="<td>";
					echo $output;
					// Add delete button with confirmation box.
					?>
						<form action="delete_category" method="post" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
		
		<h3>New Category</h3>
		<form action="new_category" method="POST">
			<label for="name">Name:</label>
			<input type="text" name="name" id="name">
			<input type="submit" name="submit" value="Add character">
		</form>
		
	</div>
</div>

<?php include("footer.php"); ?>