<?php
	 function OpenCon()

	 {
		 $dbhost = "localhost";
		 $dbuser = "root";
		 $dbpass = "pgl"; //or whatever you choose when you installed it
		 $db = "PGL_Journey";
		 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db)
			or die("Connect failed: %s\n". $conn -> error);
		 return $conn;
	 }
	 
	 function CloseCon($conn)
	 {
		$conn -> close();
	 }
?>