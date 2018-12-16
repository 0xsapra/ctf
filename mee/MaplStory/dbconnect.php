<?php
	define('DBHOST', 'xxxxxxxxxxxxxxxxxxxxx'); // sorry, can't give u this
	define('DBUSER', 'xxxxxxxxxxxxxxxxxxxxx');
	define('DBPASS', 'xxxxxxxxxxxxxxxxxxxxx');
	define('DBNAME', 'xxxxxxxxxxxxxxxxxxxxx');
	
	$conn = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
	
	
	if ( !$conn ) {
		die("Connection failed : " . mysql_error());
	}
	
