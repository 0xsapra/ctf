<?php
	ini_set("session.cookie_httponly", 1);
	define('DBHOST', 'There is no place like home dude!!!');
	define('DBUSER', '#CENSORED#');
	define('DBPASS', '#CENSORED#');
	define('DBNAME', '#CENSORED#');
	define('SALT', '#CENSORED#');
	define('SERVER_ROOT', 'http://meepwntube.0x1337.space/');
	$conn = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
	
	if ( !$conn ) {
		die("Connection failed : " . mysql_error());
	}
	
?>