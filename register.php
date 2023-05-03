<!DOCTYPE HTML>
<html>

<head>
  <title>PGL</title>
  <link rel="icon" type="image/x-icon" href="/img/logo.png">
  <style>
    body {
      background-color: lightgrey;
    }

    h1 {
      color: black;
    }

    p {
      color: black;
    }

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
    <h1>Sign up for PGL</h1>
  </div>


  <!--Navigation bar design -->
  <div class="topnav">
    <ul>
      <a style="float:left" href="/pgl.php">Home</a>
      <!-- <a style="float:left" href="/Journeys.php">Resident</a> -->
      <a style="float:left" href="/db.php">Database</a>
      <a style="float:right" href="/register.php">Sign up</a>
      <a style="float:right" href="/login.php">Log in</a>
    </ul>
  </div>


  <?php
  // define variables and set to empty values
  $userErr = $passErr = "";
  $user = $pass_ = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["user"])) {
      $userErr = "Username is required";
    } else {
      $user = test_input($_POST["user"]);
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $user)) {
        $userErr = "Only letters and white space allowed";
      }
    }

    if (empty($_POST["pass_"])) {
      $passErr = "Password is required";
    } else {
      $pass_ = test_input($_POST["pass_"]);
      // check if name only contains letters and whitespace
      if (!preg_match("/^[a-zA-Z-' ]*$/", $pass_)) {
        $passErr = "Only letters and white space allowed";
      }
    }
  }

  function test_input($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
  }
  ?>

  <?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

function redirect($url) {
  header('Location: '.$url);
  die();
}

function create_user($user, $pass_) {

  $RESPONSE_VALIDATE_USER_TOPIC = "PGL/response/valid_user";
  $REQUEST_STORE_USER_IN_DB_TOPIC = 'PGL/request/store_user';
  $hostname = "test.mosquitto.org"; 
  $port = 1883;
  $cleanSession = false;

  $connectionSettings = (new ConnectionSettings)
    ->setKeepAliveInterval(1)
    ->setConnectTimeout(3);

  $mqtt = new MqttClient($hostname, $port, $_SESSION['clientId']);

  try {
    $mqtt->connect($connectionSettings, $cleanSession);
    
    // checks if the user is valid
    $mqtt->subscribe($RESPONSE_VALIDATE_USER_TOPIC.'/'.$user.'/response', function ($topic, $message) use ($mqtt) {

      if ($message == 'VALID') {  
        echo 'valid';
        redirect('/login.php');
        $mqtt->disconnect();
      }

      else if($message == 'INVALID') {
        echo 'Invalid credentials';
        $mqtt->disconnect();
      }

     
    }, 0);  

    $type = 'user'; //OBS VI SKAL HAVE ET FELT TIL USERTYPE

    $mqtt->publish($REQUEST_STORE_USER_IN_DB_TOPIC, $user .';'.$pass_.';'.$type.';', 0, true);

    $mqtt->loop(true);

  } catch (Exception $e) {
    // echo sprintf("Error: %s\n", $e->getMessage());
  }
}
  ?>

  <div class="homebody">
    <h2>Sign up for PGL</h2>
    <p><span class="error">* required field</span></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      Username: <input type="text" name="user" value="<?php echo $user; ?>">
      <span class="error">* <?php echo $userErr; ?></span>
      <br><br>
      Password: <input type="text" name="pass_" value="<?php echo $pass_; ?>">
      <span class="error">* <?php echo $passErr; ?></span>
      <br><br>
      <input type="submit" name="register_bt" value="Register">
    </form>
  </div>

  <?php
  if (array_key_exists('register_bt', $_POST)) {
    if ($user != "" && $pass_ != "") {
      create_user($user, $pass_);
    }
  }
  ?>

</body>

</html>