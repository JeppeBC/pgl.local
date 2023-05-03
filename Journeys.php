<!DOCTYPE html>
<html>
<head>
    <title>PGL</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.png">
<style>
    body {background-color: lightgrey;}
    h1 {color: black;}
    p {color: black;}

* {
  box-sizing: border-box;
}

body {
  margin: 0;
}

/* Style the header */
.header {
  background-color: #f1f1f1;
  padding: 20px;
  text-align: center;  
}

/* Style the top navigation bar */
.topnav {
  overflow: hidden;
  background-color: #333;
}

/* Style the topnav links */
.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

/* Change color on hover */
.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.homebody {
  background-color: lightgrey;
  padding: 20px;
  text-align: center; 
}

</style>
</head>
<body>

<!--Header design-->
<div class="header">
<img src="/img/logo.png" alt="PGL" style="float:left;width:100px;height:100px;">
<h1>Pigeon Guiding Light</h1>
</div>


<!--Navigation bar design -->
<div class="topnav">
<ul>
  <a style="float:left" href="/pgl.php">Home</a>
  <a style="float:left" href="/Journeys.php">Resident</a>
  <a style="float:left" href="/db.php">Caregiver</a>
  <a style="float:right" href="/register.php">Sign up</a>
  <a style="float:right" href="login.php">Log in</a>
</ul>
</div>

<?php
	$servername = "localhost";
	$username = "root";
	$password = "pgl";
	$dbname = "PGL_Journey";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname); // Check connection
	if ($conn->connect_error) {
	 die("Connection failed: " . $conn->connect_error);
	}

	$sql = "SELECT * FROM journeys";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	 // output data of each row
	 while($row = $result->fetch_assoc()) {
	 echo "The resident left their bed at " . $row["timeOffDeparture"]. ", they entered the bathroom at: " . $row["arrival"]. " and they returned to bed at: " . $row["return"]. "<br>";
	 }
	} else {
	 echo "0 results";
	}
$conn->close();
?> 


</body>
</html>