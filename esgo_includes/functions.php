<?php
	// Performs a query based on the inputted database and query string and stops if the query fails
	function dbquery($db,$query){
		$result=mysqli_query($db,$query);
		if(!$result){
			echo $query;
			die("Database query failed");
		}
		return $result;
	}
	
	// Returns the general ESG Organizer site navigation
	function navigation(){
		global $esgoPath;
		$output ="<ul class=\"subjects\">";
			$output.="<li>";
				$output.="<a href=\"";
				$output.=$esgoPath."/skyrim";
				$output.="\" >Skyrim</a>";
				$output.="<ul class=\"pages\">";
					$output.="<li>";
						$output.="<a href=\"";
						$output.=$esgoPath."/skyrim/characters/";
						$output.="\" >Characters</a>";
						$output.="</li>";
					$output.="<li>";
						$output.="<a href=\"";
						$output.=$esgoPath."/skyrim/authors/";
						$output.="\" >Mod authors</a>";
						$output.="</li>";
					$output.="<li>";
						$output.="<a href=\"";
						$output.=$esgoPath."/skyrim/mods/";
						$output.="\" >Mods</a>";
						$output.="</li>";
					$output.="<li>Modules</li>";
					$output.="<li>";
						$output.="<a href=\"";
						$output.=$esgoPath."/skyrim/settings/";
						$output.="\" >Settings</a>";
						$output.="</li>";
				$output.="</ul>";
			$output.="</li>";
			$output.="<li>Daggerfall</li>";
		$output.="</ul>";
		return $output;
	}
	
	// Returns a dropdown menu for an enum in a database table
	function enumDropdown($db,$tableName,$columnName,$label=false,$optional=true,$selected=NULL){
		// enumDropdown is a modified version of the functions from
		// https://jadendreamer.wordpress.com/2011/03/16/php-tutorial-put-mysql-enum-values-into-drop-down-select-box/

		// Create the label and link it to the dropdown menu for that specific column. Add a space before capital letters and capitalize the first letter(example: turn "armorTypes" to "Armor Types")
		$selectDropdown =$label?'<label for="'.$columnName.'">'.ucfirst(preg_replace('/(?<!\ )[A-Z]/', ' $0', $columnName)).': </label>':'';
		// Create the dropdown menu
		$selectDropdown.='<select name="'.$columnName.'" id="'.$columnName.'">';

		// Create the MySQL query to get all of the enum values from the column
		$query="SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$tableName' AND COLUMN_NAME = '$columnName'";
		$result=dbquery($db,$query);

		// Extract the induvidual enum values from the query
		$row = mysqli_fetch_array($result);
		$enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));

		// Create a blank drop down menu option
		$selectDropdown.='<option value=""></option>';

		// Create a drop down menu option for each enum value
		// Select the given value if one is inputted as an argument
		foreach($enumList as $value){
			$selectDropdown.='<option value="'.$value.'"';
			if($selected==$value) $selectDropdown.=' selected="selected"';
			$selectDropdown.='>'.$value.'</option>';
		}

		//end the dropdown menu
		$selectDropdown .= "</select>";

		//Free query result
		mysqli_free_result($result);

		return $selectDropdown;
	}
	
	// Returns a dropdown menu for a database table that contains a list of names
	function tableDropdown($db,$tableName,$label=NULL,$optional=true,$selected=NULL){
		// Create the label and link it to the dropdown menu for that specific column. Name it 
		if($label) $selectDropdown="<label for=\"$tableName\">$label: </label>";
		// Create the dropdown menu
		$selectDropdown.="<select name=\"$tableName\" id=\"$tableName\">";
		
		// Create the MySQL query to get the names of the values to appear in the dropdown menu
		$query="SELECT * FROM $tableName";
		$result=dbquery($db,$query);
		
		// Put the names from the query into a single array with the key as the id and the value as the name
		$names=array();
		while($row=mysqli_fetch_assoc($result)){
			$names[$row["id"]]=$row["name"];
		}
		
		//Insert a blank option if the field is optional
		if($optional) $selectDropdown.='<option value=""></option>';
		
		// Create a drop down menu option for each value
		// The id from the table will be passed as the value to be POSTed while the name from the table will be what is displayed
		// Select the given value if one is inputted as an argument
		foreach($names as $key => $value){
			$selectDropdown.="<option value=\"$key\"";
			if($selected==$value) $selectDropdown.=' selected="selected"';
			$selectDropdown.=">$value</option>";
		}
		
		//end the dropdown menu
		$selectDropdown .= "</select>";
		
		//Free query result
		mysqli_free_result($result);
		
		return $selectDropdown;
	}
	
	// Returns an unordered list of checkboxes for a database table of names
	function tableCheckboxes($db,$tableName,$label=NULL,$checked=NULL){
		if($label) $selectCheckboxes="<p>$label: </p>";
		
		// Start the unordered list
		$selectCheckboxes.="<ul>";
		
		// Create the MySQL query to get the names of the values to appear in the checkboxes
		$query="SELECT * FROM ".mysqli_real_escape_string($db,$tableName);
		$result=dbquery($db,$query);
		
		// Put the names from the query into a single array
		$names=array();
		while($row=mysqli_fetch_assoc($result)){
			$names[$row["id"]]=$row["name"];
		}
		
		// Create a checkbox for each set value with a matching label
		// The name that will be POSTed will be the name of the table contatinated with the id from that table
		// The checkbox will be identified and labeled by the name from the name from the table
		foreach($names as $key => $value){
			$selectCheckboxes.='<li>';
			$selectCheckboxes.="<input type=\"checkbox\" name=\"$tableName$key\" id=\"$value\"";
			if(in_array($key,$checked)){
				$selectCheckboxes.=' checked';
			}
			$selectCheckboxes.='>';
			$selectCheckboxes.="<label for=\"$value\">$value</label>";
			$selectCheckboxes.='</li>';
		}
		
		// End the unordered list
		$selectCheckboxes.="</ul>";
		
		return $selectCheckboxes;
	}
	
	// Returns an array of what checkboxes were checked from using either tableCheckboxes from a POST
	function postTableCheckboxes($db,$tableName,$post){
		$names=array();
		$checked=array();
		//$checked will be the array that is returned that contains all of the ids for the values that were checked
		
		// Create the MySQL query to get the names of ecah value to check
		$query="SELECT * FROM $tableName";
		$result=dbquery($db,$query);
		
		// Put the values from the query into a single array
		while($row=mysqli_fetch_assoc($result)){
			$names[$row["id"]]=$row["name"];
		}
		
		// If the POST contains the contatination of the table name and string (from the tableCheckboxes function), add the id from the table to the list of checked boxes
		foreach($names as $key => $value){
			if(array_key_exists($tableName.$key,$post)){
				array_push($checked,$key);
			}
		}
		
		return $checked;
	}
	
	// Returns an unordered list of checkboxes for a set in a database table
	function setCheckboxes($db,$tableName,$columnName,$labelTitle=NULL,$labels=false,$checked=NULL){
		if($labelTitle) $selectCheckboxes="<p>$labelTitle: </p>";
		
		// Start the unordered list
		$selectCheckboxes.="<ul>";
		
		// Create the MySQL query to get all of the set values from the column
		$query="SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".mysqli_real_escape_string($db,$tableName)."' AND COLUMN_NAME = '".mysqli_real_escape_string($db,$columnName)."'";
		$result=dbquery($db,$query);
		
		// Extract the induvidual set values from the query
		$row = mysqli_fetch_array($result);
		$setList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));

		// Create a checkbox for each set value with a matching label
		// The name that will be POSTed will be the name of the table concatinated with the the column name concatinated with the set value
		// The checkbox will be identified and lebeled by the set value
		foreach($setList as $value){
			$selectCheckboxes.='<li>';
			$selectCheckboxes.="<input type=\"checkbox\" name=\"$tableName$columnName$value\" id=\"$value\"";
			if(in_array($value,$checked)){
				$selectCheckboxes.=' checked';
			}
			$selectCheckboxes.='>';
			$selectCheckboxes.="<label for=\"$value\">$value</label>";
			$selectCheckboxes.='</li>';
		}
		
		// End the unordered list
		$selectCheckboxes.="</ul>";
		
		return $selectCheckboxes;
	}
	
	// Returns an array of what checkboxes were checked from using either setCheckboxes from a POST
	function postSetCheckboxes($db,$tableName,$columnName,$post){
		$checked=array();
		
		// Create the MySQL query to get all of the set values from the column
		$query="SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".mysqli_real_escape_string($db,$tableName)."' AND COLUMN_NAME = '".mysqli_real_escape_string($db,$columnName)."'";
		$result=dbquery($db,$query);

		// Extract the induvidual set values from the query
		$row = mysqli_fetch_array($result);
		$setList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
		
		// If the POST contains the concatination of the table name, column name, and set value (from the setCheckboxes function), add the set value to the list of checked boxes
		foreach($setList as $value){
			if(array_key_exists($tableName.$columnName.$value,$post)){
				array_push($checked,$value);
			}
		}
		
		return $checked;
	}
	
	// Returns a Nexusmods ID for a mod or user given a URL.
	function nexusIdFromURL($url){
		// ltrim removes characters from the left of the string. It will remove any number of any of the characters in the second argument
		$url=ltrim($url,"htps:/w");
		$url=ltrim($url,".");
		$url=ltrim($url,"nexusmod");
		$url=ltrim($url,".");
		$url=ltrim($url,"com/");
		$url=ltrim($url,"pyfgcrlaoeuidhtnsqjkxbmwvz123456789");
		$url=ltrim($url,"/usermod");
		// Once it's gotten to the ID, loop through, adding each number to the end of the id until it reaches the end of the number
		$i=1;
		$nexus=substr($url,0,1);
		while(is_numeric($nexus)&&$i<20){
			$nexus.=substr($url,$i,1);
			$i++;
		}
		$nexus=rtrim($nexus,"/");

		return $nexus;
	}
	
	// Returns a checkbox that will disable a text input field
	function hideTextOnCheckbox($function_name,$checkboxId,$textId){
		// I don't know javascript lol. I just sort of hobbled this together from an example I found somewhere online
		?>
		<script type="text/javascript">
		    function <?php echo $function_name ?>(){
		        if(document.getElementById("<?php echo $checkboxId ?>").checked != 1){
		            document.getElementById("<?php echo $textId ?>").removeAttribute("disabled");
		        }else{
		            document.getElementById("<?php echo $textId ?>").setAttribute("disabled","disabled");
		        }
		    }
		</script>
		<?php
	}
	
	// Returns the Nexusmods author ID from a mod Nexus ID
	function authorNameFromNexusId($nexusModId){
		$url="http://www.nexusmods.com/skyrim/users/$nexusModId/?";
		$data=file_get_contents($url);
		//find on the page the h1 tag as that is where the author's name is
		$regex='/<h1>(.+?)</';
		preg_match($regex,$data,$match);
		return $match[1];
	}
	
	function createNexusLink($id,$type,$linkText=NULL){
		// Type 0 = user/author
		// Type 1 = mod

		$output="<a href=\"http://www.nexusmods.com/skyrim/";
		if($type==0){
			$output.="users";
		}elseif($type==1){
			$output.="mods";
		}
		$output.="/{$id}/";
		// Make it open in a new tab
		$output.="\" target=\"blank\">";
		// If link text is provided, show that, otherwise, show the Nexus ID
		$output.=($linkText)?($linkText):($id);
		$output.="</a>";

		return $output;
	}
	
	function redirect_to($new_location){
		header("Location: ".$new_location);
		exit;
	}
?>
