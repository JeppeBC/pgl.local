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
  <a style="float:left" href="/database.php">Database</a>
  <a style="float:right" href="/register.php">Sign up</a>
  <a style="float:right" href="login.php">Log in</a>
</ul>
</div>

<!--Body design-->
<div class="homebody">
<h2>Database</h2>
<p>Here you can find all the information about residents </p>

<?php
// create MQTT subscriber
require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

// define MQTT topic to subscribe to
$topic = 'PGL/response/send_events';

// define MQTT server host
$host = 'test.mosquitto.org';

// connect to MQTT server and subscribe to topic
$client = new MqttClient($host, 1883, 'client_id');
$client->connect();
$client->subscribe($topic);

// // parse received MQTT message
// $msg = $client->messages();
// $data = json_decode($msg->payload);

// create MySQL connection
$servername = 'localhost';
$username = 'username';
$password = 'password';
$dbname = 'database_name';

$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

// insert data into MySQL database
$sql = "INSERT INTO events (date_time, r, t, device) VALUES ('$data->date_time', $data->r, $data->t, '$data->device')";

if ($conn->query($sql) === TRUE) {
  echo 'New record created successfully';
} else {
  echo 'Error: ' . $sql . '<br>' . $conn->error;
}

// retrieve data from MySQL database
$sql = 'SELECT * FROM events';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo 'Date and Time: ' . $row['date_time'] . ' - R: ' . $row['r'] . ' - T: ' . $row['t'] . ' - Device: ' . $row['device'] . '<br>';
  }
} else {
  echo '0 results';
}

$conn->close();
?>


</div>

</body>
</html>