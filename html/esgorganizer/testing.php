<?
	$db = mysqli_connect('localhost', 'esguser', '2&A]?g[!r\"?>WkXM', 'esgorganizer');
	if(mysqli_connect_errno()) {
	die("Database connection failed: " . 
		mysqli_connect_error() . 
		" (" . mysqli_connect_errno() . ")"
	);
	}else{
		echo "Databse connection successfull";
	}
?>