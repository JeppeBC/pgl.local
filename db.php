<?php
session_start();    //start new session
?>

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

<!--Header design-->
<div class="header">
<img src="/img/logo.png" alt="PGL" style="float:left;width:100px;height:100px;">
<h1>Pigeon Guiding Light</h1>
</div>


<!--Navigation bar design -->
<div class="topnav">
<ul>
  <a style="float:left" href="/pgl.php">Home</a>
  <!-- <a style="float:left" href="/Journeys.php">Resident</a> -->
  <a style="float:left" href="/db.php">Database</a>
  <a style="float:right" href="/register.php">Sign up</a>
  <a style="float:right" href="login.php">Log in</a>
</ul>
</div>

<!--Body design-->
<div class="homebody">
<h2>Database</h2>
<p>Here you can find all the information about residents </p>
</div>

<?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

  $RESPONSE_VALIDATE_USER_TOPIC = "PGL/response/valid_user";
  $REQUEST_VALIDATE_USER_TOPIC = "PGL/request/valid_user";
  $RESPONSE_SEND_EVENTS_TOPIC = "PGL/response/send_events";
  $RESPONSE_EMERGENCY_TOPIC = 'PGL/response/emergency';
  $REQUEST_GET_EMERGENCIES_TOPIC = 'PGL/request/get_emergencies';
  $REQUEST_GET_EVENTS_TOPIC = "PGL/request/get_events";
  $hostname = "test.mosquitto.org"; 
  $port = 1883;
  $clientId = $_SESSION['clientId']; // access session variable updated at login
  $cleanSession = false;

  $connectionSettings = (new ConnectionSettings)
    ->setKeepAliveInterval(1)
    ->setConnectTimeout(3);
 
  $mqtt = new MqttClient($hostname, $port, $clientId);

  // Get journey Data
  try {
    $mqtt->connect($connectionSettings, $cleanSession);
    
    $mqtt->subscribe($RESPONSE_EMERGENCY_TOPIC.'/'.$clientId.'/response', function (string $topic, string $message) use ($mqtt, $RESPONSE_EMERGENCY_TOPIC, $clientId) {
      # Data goes in here!
      $data = json_decode($message, true);    //decode json data into an array

      // Generate the HTML table code
      echo '<table style="float: right">';
      echo '<thead><tr><th>Emergency ID</th><th>Date</th><th>Emergency</th><th>Device ID</th><th>User ID</th></tr></thead>';
      echo '<tbody>';
      foreach($data as $event) {
          echo '<tr>';
          echo '<td>' . $event['emergency_id'] . '</td>';
          echo '<td>' . $event['datetime'] . '</td>';
          echo '<td>' . $event['et'] . '</td>';
          echo '<td>' . $event['device_id'] . '</td>';
          echo '<td>' . $event['user_id'] . '</td>';
          echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';

      $mqtt->unsubscribe($RESPONSE_EMERGENCY_TOPIC.'/'.$clientId.'/response');
      $mqtt->disconnect();
    }, 0);

    $mqtt->publish($REQUEST_GET_EMERGENCIES_TOPIC, $clientId.';', 0, true);

    $mqtt->loop(true);



  } catch(Exception $e){
    
  }  

  // Get emergency Data
  try {
    
    $mqtt->connect($connectionSettings, $cleanSession);
    
    $mqtt->subscribe($RESPONSE_SEND_EVENTS_TOPIC.'/'.$clientId.'/response', function (string $topic, string $message) use ($mqtt, $RESPONSE_SEND_EVENTS_TOPIC, $clientId) {
      # Data goes in here!
      $data = json_decode($message, true);    //decode json data into an array

      // Generate the HTML table code
      echo '<table style="float: left">';
      echo '<thead><tr><th>Emergency ID</th><th>Date</th><th>Emergency</th><th>Device ID</th><th>User ID</th></tr></thead>';
      echo '<tbody>';
      foreach($data as $event) {
          echo '<tr>';
          echo '<td>' . $event['journey_id'] . '</td>';
          echo '<td>' . $event['datetime'] . '</td>';
          echo '<td>' . $event['rtt'] . '</td>';
          echo '<td>' . $event['tt'] . '</td>';
          echo '<td>' . $event['device_id'] . '</td>';
          echo '<td>' . $event['user_id'] . '</td>';
          echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';

      $mqtt->unsubscribe($RESPONSE_SEND_EVENTS_TOPIC.'/'.$clientId.'/response');
      $mqtt->disconnect();
    }, 0);

    $mqtt->publish($REQUEST_GET_EVENTS_TOPIC, $clientId.';', 0, true);

    $mqtt->loop(true);

  } catch(Exception $e){
    
  } 
 
?>


</body>
</html>