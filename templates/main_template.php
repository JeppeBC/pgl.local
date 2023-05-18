<!-- CSS template for all pages - https://www.w3schools.com/css/css_website_layout.asp -->
<head>
    <title>PGL</title>
    <link rel="icon" type="image/x-icon" href="/img/Logo.png">
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

#session-value {
			position: fixed;
			top: 0;
			right: 0;
			padding: 10px;
			background-color: #f1f1f1;
			font-weight: bold;
			font-size: 16px;
}

table {
  /* overflow: auto; */
  border-collapse: collapse;
}

th, td {
  padding: 5px;
  text-align: left;
  border-bottom: 1px solid #333;
  border-top: 3px solid #333;
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

<div id="session-value">
	<?php
		if (isset($_SESSION['clientId'])) {
			echo "Currently logged in as: " . $_SESSION['clientId'];
		}
    else {
      echo "Not logged in";
    }
	?>
</div>
  

<!--Header design-->
<div class="header">
<!-- <img src="/img/logo.png" alt="PGL" style="float:left;width:100px;height:100px;"> -->
<h1>Pigeon Guiding Light</h1>
</div>


<!--Navigation bar design -->
<div class="topnav">
<ul>
  <a style="float:left" href="/pgl.php">Home</a>
  <!-- <a style="float:left" href="/Journeys.php">Resident</a> -->
  <a style="float:left" href="/db.php">Database</a>
  <a style="float:right" href="/register.php">Sign up</a>
  <a style="float:right" href="login.php">Sign in</a>
  <a style="float:right" href="/logout.php">Sign out</a>
  <a style="float:right" href="/registerProduct.php">Register product</a>
</ul>
</div>