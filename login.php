<?php
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';
require('vendor/autoload.php');
use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

session_start();    //start new session

?>

<!DOCTYPE HTML>
<html>

<?php
include 'templates/main_template.php';
?>

<?php

// define variables and set to empty values
$userErr = $passErr = "";
$user = $pass_ = "";

//call error function if user or password are empty
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

//function to validate user and redirect to db.php if user is valid
function validate_user($user, $pass_)
{
  $_SESSION['clientId'] = $user;
  $cleanSession = false;
  $connectionSettings = (new ConnectionSettings)
    ->setKeepAliveInterval(1)
    ->setConnectTimeout(3);

  //initialize the MQTT client
  $mqtt = new MqttClient($GLOBALS['hostname'], $GLOBALS['port'], $user);

  try {
    $mqtt->connect($connectionSettings, $cleanSession);

    // checks if the user is valid
    $mqtt->subscribe(
      $GLOBALS['RESPONSE_VALIDATE_TOPIC'] . '/' . $user . '/response',
      function ($topic, $message) use ($mqtt, $user) {

        if ($message == 'VALID') {
          $_SESSION['clientId'] = $user;                  //set session variable
          redirect('/db.php');
          $mqtt->disconnect();
        } else if ($message == 'INVALID') {
          echo 'Invalid credentials';
          $mqtt->disconnect();
        }
      },
      0
    );
    $mqtt->publish($GLOBALS['REQUEST_VALIDATE_USER_TOPIC'], $user . ';' . $pass_ . ';' . $_SESSION['clientId'] . ';', 0, false);

    $mqtt->loop(true);
  } catch (Exception $e) {
  }
}
?>


<!--Body design-->
<!-- Log in form -->
<div class="homebody">
  <h2>Log in</h2>
  <p><span class="error">* required field</span></p>
  <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Username: <input type="text" name="user" value="<?php echo $user; ?>">
    <span class="error">* <?php echo $userErr; ?></span>
    <br><br>
    Password: <input type="password" name="pass_" value="<?php echo $pass_; ?>">
    <span class="error">* <?php echo $passErr; ?></span>
    <br><br>
    <input type="submit" name="login_bt" value="Log in">
  </form>

  <?php
  //call validate_user function if login button is pressed
  if (array_key_exists('login_bt', $_POST)) {
    if ($_SESSION['clientId'] != $user){
      validate_user($user, $pass_);
    }
    else{
        echo 'User already logged in';
    }  
  }
  ?>

</div>

</body>

</html>