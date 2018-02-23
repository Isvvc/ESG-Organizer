<?php
	$thisFilePath="./";
	require_once($thisFilePath."includes.php");
	set_include_path($thisFilePath.$baseIncludesPath);

	include ("db_connection.php");
	include ("functions.php");
?><?php
	echo "<h1>ESG Organizer Installer</h1>";
	
	$tablesReference=array(
		"authorCategories",
		"authors",
		"categories",
		"characterCombatStyles",
		"characterSkills",
		"characters",
		"combatStyles",
		"imagesAuthorsExternal",
		"imagesAuthorsNexus",
		"imagesCharacters",
		"imagesModsExternal",
		"imagesModsNexus",
		"imagesModulesExternal",
		"imagesModulesNexus",
		"modCategories",
		"modsExternal",
		"modsNexus",
		"mods",
		"moduleTypes",
		"modules",
		"elementTypes",
		"elements",
		"moduleElements",
		"races",
		"skills"
	);

	$query="show tables";
	$result=dbquery($db,$query);
	
	$tablesActual=array();
	while($row=mysqli_fetch_assoc($result)){
		#print_r($row);
		array_push($tablesActual, $row["Tables_in_".DB_NAME]);
	}

	$tablesToAdd=array();
	for($i=0;$i<count($tablesReference);$i++){
		if(!in_array($tablesReference[$i], $tablesActual)){
			array_push($tablesToAdd,$tablesReference[$i]);
		}
	}
	#print_r($tablesToAdd);
	if(count($tablesToAdd)>0){
		if(in_array("races", $tablesToAdd)){
			$query ="CREATE TABLE races(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Races - List table created.<br/>";
			$query ="INSERT INTO races(name)";
			$query.="VALUES";
			$query.="('Altmer'),";
			$query.="('Argonian'),";
			$query.="('Bosmer'),";
			$query.="('Breton'),";
			$query.="('Dunmer'),";
			$query.="('Imperial'),";
			$query.="('Kahjiit'),";
			$query.="('Nord'),";
			$query.="('Orc'),";
			$query.="('Redguard');";
			$result=dbquery($db,$query);
			echo "Races - List table populated with default values.<br/>";
		}
		if(in_array("characters", $tablesToAdd)){
			$query ="CREATE TABLE characters(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="race int unsigned,";
			$query.="gender enum('M','F'),";
			$query.="armorTypes set('Light','Heavy','Unarmored'),";
			$query.="roleplay tinyint,";
			$query.="morality enum('Good','Neutral','Evil'),";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (race) REFERENCES races (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Characters table created.<br/>";
		}
		if(in_array("skills", $tablesToAdd)){
			$query ="CREATE TABLE skills(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Skills table created.<br/>";
			$query ="INSERT INTO skills (name)";
			$query.="VALUES";
			$query.="('Alteration'),";
			$query.="('Archery'),";
			$query.="('Alchemy'),";
			$query.="('Conjuration'),";
			$query.="('Block'),";
			$query.="('Light Armor'),";
			$query.="('Destruction'),";
			$query.="('Heavy Armor'),";
			$query.="('Lockpicking'),";
			$query.="('Enchanting'),";
			$query.="('One-handed'),";
			$query.="('Pickpocket'),";
			$query.="('Illusion'),";
			$query.="('Smithing'),";
			$query.="('Sneak'),";
			$query.="('Restoration'),";
			$query.="('Two-handed'),";
			$query.="('Speech');";
			$result=dbquery($db,$query);
			echo "Skills table populated with default values.<br/>";
		}
		if(in_array("characterSkills", $tablesToAdd)){
			$query ="CREATE TABLE characterSkills(";
			$query.="character_id int unsigned NOT NULL,";
			$query.="skill int unsigned NOT NULL,";
			$query.="type enum('Primary','Major','Minor'),";
			$query.="PRIMARY KEY (character_id, skill),";
			$query.="FOREIGN KEY (character_id) REFERENCES characters (id),";
			$query.="FOREIGN KEY (skill) REFERENCES skills (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Characters' Skills table created.<br/>";
		}
		if(in_array("combatStyles", $tablesToAdd)){
			$query ="CREATE TABLE combatStyles(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Combat Styles table created.<br/>";
			$query ="INSERT INTO combatStyles(name)";
			$query.="VALUES";
			$query.="('Sword'),";
			$query.="('Dagger'),";
			$query.="('War Axe'),";
			$query.="('Mace'),";
			$query.="('Greatsword'),";
			$query.="('Battleaxe'),";
			$query.="('Warhammer'),";
			$query.="('Bow'),";
			$query.="('Crossbow'),";
			$query.="('Dual wield'),";
			$query.="('Bound weapons'),";
			$query.="('Destruction'),";
			$query.="('Summoning'),";
			$query.="('Reanimation'),";
			$query.="('Followers');";
			$result=dbquery($db,$query);
			echo "Combat Styles table populated with default values.<br/>";
		}
		if(in_array("characterCombatStyles", $tablesToAdd)){
			$query ="CREATE TABLE characterCombatStyles(";
			$query.="character_id int unsigned NOT NULL,";
			$query.="combatStyle int unsigned NOT NULL,";
			$query.="PRIMARY KEY (character_id, combatStyle),";
			$query.="FOREIGN KEY (character_id) REFERENCES characters (id),";
			$query.="FOREIGN KEY (combatStyle) REFERENCES combatStyles (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Characters' Combat Styles table created.<br/>";
		}
		if(in_array("categories", $tablesToAdd)){
			$query ="CREATE TABLE categories(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Categories table created.<br/>";
			$query ="INSERT INTO categories (name)";
			$query.="VALUES";
			$query.="('Houses'),";
			$query.="('Armor'),";
			$query.="('Weapons'),";
			$query.="('Followers'),";
			$query.="('Quests'),";
			$query.="('Gameplay'),";
			$query.="('Armor Textures'),";
			$query.="('Character Presets'),";
			$query.="('Character Creation'),";
			$query.="('ENB Presets'),";
			$query.="('General'),";
			$query.="('Visuals');";
			$result=dbquery($db,$query);
			echo "Categories populated with default values.<br/>";
		}
		if(in_array("authors", $tablesToAdd)){
			$query ="CREATE TABLE authors(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(30),";
			$query.="nexusId int unsigned,";
			$query.="link text,";
			$query.="content set('Mods','Screenshots'),";
			$query.="PRIMARY KEY (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Authors table created.<br/>";
		}
		if(in_array("authorCategories", $tablesToAdd)){
			$query ="CREATE TABLE authorCategories(";
			$query.="author int unsigned NOT NULL,";
			$query.="category int unsigned NOT NULL,";
			$query.="PRIMARY KEY (author, category),";
			$query.="FOREIGN KEY (author) REFERENCES authors (id),";
			$query.="FOREIGN KEY (category) REFERENCES categories (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Author Categories table created.<br/>";
		}
		if(in_array("mods", $tablesToAdd)){
			$query ="CREATE TABLE mods(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(500),";
			$query.="author int unsigned NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (author) REFERENCES authors (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Mods table created.<br/>";
		}
		if(in_array("modsNexus", $tablesToAdd)){
			$query ="CREATE TABLE modsNexus(";
			$query.="id int unsigned NOT NULL,";
			$query.="nexus int unsigned NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (id) REFERENCES mods (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Mod links - Nexus table created.<br/>";
		}
		if(in_array("modsExternal", $tablesToAdd)){
			$query ="CREATE TABLE modsExternal(";
			$query.="id int unsigned NOT NULL,";
			$query.="link text NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (id) REFERENCES mods (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Mod links - external table created.<br/>";
		}
		if(in_array("modCategories", $tablesToAdd)){
			$query ="CREATE TABLE modCategories(";
			$query.="mod_id int unsigned NOT NULL,";
			$query.="category int unsigned NOT NULL,";
			$query.="PRIMARY KEY (mod_id, category),";
			$query.="FOREIGN KEY (mod_id) REFERENCES mods (id),";
			$query.="FOREIGN KEY (category) REFERENCES categories (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Mods' Categories table created.<br/>";
		}
		if(in_array("moduleTypes", $tablesToAdd)){
			$query ="CREATE TABLE moduleTypes(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Module Types table created.<br/>";
		}
		if(in_array("modules", $tablesToAdd)){
			$query ="CREATE TABLE modules(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="mod_id int unsigned NOT NULL,";
			$query.="type_id int unsigned NOT NULL,";
			$query.="name varchar(255),";
			$query.="aquire text,";
			$query.="notes text,";
			$query.="level tinyint,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (mod_id) REFERENCES mods (id),";
			$query.="FOREIGN KEY (type_id) REFERENCES moduleTypes (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Modules table created.<br/>";
		}
		if(in_array("elementTypes", $tablesToAdd)){
			$query ="CREATE TABLE elementTypes(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Element Types table created.<br/>";
		}
		if(in_array("elements", $tablesToAdd)){
			$query ="CREATE TABLE elements(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="type_id int unsigned NOT NULL,";
			$query.="name varchar(255),";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (type_id) REFERENCES elementTypes (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Elements table created.<br/>";
		}
		if(in_array("moduleElements", $tablesToAdd)){
			$query ="CREATE TABLE moduleElements(";
			$query.="module_id int unsigned NOT NULL,";
			$query.="element_id int unsigned NOT NULL,";
			$query.="FOREIGN KEY (module_id) REFERENCES modules (id),";
			$query.="FOREIGN KEY (element_id) REFERENCES elements (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Modules' Elements table created.<br/>";
		}
		if(in_array("imagesCharacters", $tablesToAdd)){
			$query ="CREATE TABLE imagesCharacters(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="url text,";
			$query.="characterId int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (characterId) REFERENCES characters (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Characters table created.<br/>";
		}
		if(in_array("imagesModsNexus", $tablesToAdd)){
			$query ="CREATE TABLE imagesModsNexus(";
			$query.="nexusUrlExtension varchar(255),";
			$query.="mod_id int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (nexusUrlExtension),";
			$query.="FOREIGN KEY (mod_id) REFERENCES mods (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Mods Nexus table created.<br/>";
		}
		if(in_array("imagesModsExternal", $tablesToAdd)){
			$query ="CREATE TABLE imagesModsExternal(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="url text,";
			$query.="mod_id int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (mod_id) REFERENCES mods (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Mods external table created.<br/>";
		}
		if(in_array("imagesModulesNexus", $tablesToAdd)){
			$query ="CREATE TABLE imagesModulesNexus(";
			$query.="nexusUrlExtension varchar(255),";
			$query.="module int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (nexusUrlExtension),";
			$query.="FOREIGN KEY (module) REFERENCES modules (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Modules Nexus table created.<br/>";
		}
		if(in_array("imagesModulesExternal", $tablesToAdd)){
			$query ="CREATE TABLE imagesModulesExternal(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="url text,";
			$query.="module int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (module) REFERENCES modules (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Modules external table created.<br/>";
		}
		if(in_array("imagesAuthorsNexus", $tablesToAdd)){
			$query ="CREATE TABLE imagesAuthorsNexus(";
			$query.="nexusUrlExtension varchar(255),";
			$query.="author int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (nexusUrlExtension),";
			$query.="FOREIGN KEY (author) REFERENCES authors (id)";
			$query.=")";
			$result=dbquery($db,$query);
			echo "Images - Authors Nexus table created.<br/>";
		}
		if(in_array("imagesAuthorsExternal", $tablesToAdd)){
			$query ="CREATE TABLE imagesAuthorsExternal(";
			$query.="id int unsigned NOT NULL AUTO_INCREMENT,";
			$query.="url text,";
			$query.="author int unsigned NOT NULL,";
			$query.="main bool NOT NULL,";
			$query.="PRIMARY KEY (id),";
			$query.="FOREIGN KEY (author) REFERENCES authors (id)";
			$query.=");";
			$result=dbquery($db,$query);
			echo "Images - Authors external table created.<br/>";
		}
	}else{
		echo "Database appears to be setup.";
	}
?>