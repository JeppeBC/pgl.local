<?php
session_start();
require 'vendor/autoload.php';
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;
?>

<!DOCTYPE HTML>
<html>

<?php
include 'templates/main_template.php';
?>

  <?php
  // define variables and set to empty values
  $userErr = $passErr = $userTypeErr = "";
  $user = $pass_ = $userType = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["user"])) {
      $userErr = "Username is required";
    } else {
      $user = test_input($_POST["user"]);
      // check if name only contains letters and whitespace
      // if (!preg_match("/^[a-zA-Z-' ]*$/", $user)) {
      //   $userErr = "Only letters and white space allowed";
      // }
    }

    if (empty($_POST["pass_"])) {
      $passErr = "Password is required";
    } else {
      $pass_ = test_input($_POST["pass_"]);
      // check if name only contains letters and whitespace
      // if (!preg_match("/^[a-zA-Z-' ]*$/", $pass_)) {
      //   $passErr = "Only letters and white space allowed";
      // }
    }

    if (empty($_POST["userType"])) {
      $userTypeErr = "User role is required";
    } else {
      $userType = test_input($_POST["userType"]);
    }

  }

  function create_user($user, $pass_, $userType)
  {

    $cleanSession = false;

    $connectionSettings = (new ConnectionSettings)
      ->setKeepAliveInterval(1)
      ->setConnectTimeout(3);

    $mqtt = new MqttClient($GLOBALS['hostname'], $GLOBALS['port'], $user);

    try {
      $mqtt->connect($connectionSettings, $cleanSession);

      // checks if the user is valid
      $mqtt->subscribe(
        $GLOBALS['RESPONSE_VALIDATE_TOPIC'] . '/' . $user . '/response',
        function ($topic, $message) use ($mqtt) {

          if ($message == 'VALID') {
            echo 'valid';
            redirect('/login.php');
            $mqtt->disconnect();
          } else if ($message == 'INVALID') {
            echo 'Invalid credentials';
            $mqtt->disconnect();
          }
        },
        0
      );

      $mqtt->publish($GLOBALS['REQUEST_STORE_USER_IN_DB_TOPIC'], $user . ';' . $pass_ . ';' . $userType . ';', 0, false);

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
      Password: <input type="password" name="pass_" value="<?php echo $pass_; ?>">
      <span class="error">* <?php echo $passErr; ?></span>
      <br><br>
      Role: 
      <input type="radio" name="userType" <?php if (isset($userType) && $userType=="caregiver") echo "checked";?> value="caregiver"> Caregiver
      <input type="radio" name="userType" <?php if (isset($userType) && $userType=="resident") echo "checked";?> value="resident"> Resident
      <span class="error">* <?php echo $userTypeErr; ?></span>
      <br><br>
      <input type="submit" name="register_bt" value="Register">
    </form>
  </div>

  <?php
  if (array_key_exists('register_bt', $_POST)) {
    if ($user != "" && $pass_ != "" and $userType != "" && 
    ($userType == 'caregiver' || $userType == 'resident')) {
      create_user($user, $pass_, $userType);
    }
  }
  ?>

</body>

</html>