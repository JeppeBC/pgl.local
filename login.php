<!DOCTYPE HTML>  
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
<h1>Log in to PGL</h1>
</div>


<!--Navigation bar design -->
<div class="topnav">
<ul>
  <a style="float:left" href="/pgl.php">Home</a>
  <a style="float:left" href="/Journeys.php">Resident</a>
  <a style="float:left" href="/db.php">Database</a>
  <a style="float:right" href="/register.php">Sign up</a>
  <a style="float:right" href="/login.php">Log in</a>
</ul>
</div>

<?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

// define variables and set to empty values
$userErr = $passErr = "";
$user = $pass_ = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["user"])) {
    $userErr = "Username is required";
  } else {
    $user = test_input($_POST["user"]);
  }
  
  if (empty($_POST["pass_"])) {
    $passErr = "Password is required";
  } else {
    $pass_ = test_input($_POST["pass_"]);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}

function validate_user($user, $pass_) {

  $RESPONSE_VALIDATE_USER_TOPIC = "PGL/response/valid_user";
  $REQUEST_VALIDATE_USER_TOPIC = "PGL/request/valid_user";
  $hostname = "test.mosquitto.org"; 
  $port = 1883;
  $clientId = "login_validater";
  $cleanSession = false;

  $connectionSettings = (new ConnectionSettings)
    ->setKeepAliveInterval(1)
    ->setConnectTimeout(3);

  $mqtt = new MqttClient($hostname, $port, $clientId);

  try {
    $mqtt->connect($connectionSettings, $cleanSession);
    printf("client connected\n");

    $mqtt->subscribe($RESPONSE_VALIDATE_USER_TOPIC.'/'.$clientId, function ($topic, $message, $mqtt) {
      echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);  
      $mqtt->interrupt();
    }, 0);    

      $mqtt->publish($REQUEST_VALIDATE_USER_TOPIC, $user .';'.$pass_.';'.$clientId.';', 0, true);
    
    $mqtt->loop(true);
    $mqtt->disconnect();

  } catch (Exception $e) {
    echo sprintf("Error: %s\n", $e->getMessage());
  }
}
?>


<!--Body design-->
<div class="homebody">
<h2>Log in</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  Username: <input type="text" name="user" value="<?php echo $user;?>">
  <span class="error">* <?php echo $userErr;?></span>
  <br><br>
  Password: <input type="text" name="pass_" value="<?php echo $pass_;?>">
  <span class="error">* <?php echo $passErr;?></span>
  <br><br>
    <input type="submit" name="login_bt" value="Log in">
</form>

<?php  
if(array_key_exists('login_bt', $_POST)) {
  $validity = validate_user($user, $pass_);
  echo sprintf("[%s] \n", $validity);

  if ($validity == 'VALID') {
    echo $validity;
    include('db.php'); 

  }

  else if($validity == 'INVALID') {

  }

}
?>

</div>

</body>
</html>