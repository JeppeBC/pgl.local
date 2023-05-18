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

// define variables and set to empty values
$deviceId = "";
$deviceIdErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["deviceId"])) {
        $deviceIdErr = "Device id is required";
    } else {
        $deviceId = test_input($_POST["deviceId"]);
    }
}

// function to store a product to an existing user
function store_product($user, $deviceId)
{
    $cleanSession = false;
    $connectionSettings = (new ConnectionSettings)
        ->setKeepAliveInterval(1)
        ->setConnectTimeout(3);
        
    // create a new mqtt client
    $mqtt = new MqttClient($GLOBALS['hostname'], $GLOBALS['port'], $user);

    try {
        $mqtt->connect($connectionSettings, $cleanSession);
        
        // subscribe to the response topic
        $mqtt->subscribe(
            $GLOBALS['RESPONSE_VALIDATE_TOPIC'] . '/' . $user . '/response',
            function ($topic, $message) use ($mqtt, $user) {
                // if the response is valid, echo this
                if ($message == 'VALID') {
                    echo 'Product registered';
                    $mqtt->disconnect();
                // if the response is invalid, echo this
                } else if ($message == 'INVALID') {

                    echo '<div style="text-align: center; font-size: 16px; line-height: 1.5;">';
                    echo '<p>Product not registered. If you are a resident, you can only have one product registered. Please contact customer support for assistance.</p>';
                    echo '<p>Perhaps you entered the wrong device id? You can find the device id at the back of your device. </p>';
                    echo '</div>';

                    $mqtt->disconnect();
                }
            },
            0
        );
        
        // publish the request to create a product
        $mqtt->publish($GLOBALS['REQUEST_CREATE_PRODUCT_TOPIC'], $deviceId . ';' . $user . ';', 0, false);

        $mqtt->loop(true);
    } catch (Exception $e) {
    }
}

?>
<!-- Register product form  -->
<!-- //html to echo if user is logged in -->
<div class="homebody">
    <h2>Register new product</h2>
    <p><span class="error">* required field</span></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Device id: <input type="text" name="deviceId" value="<?php echo $deviceId; ?>">
        <span class="error">* <?php echo $deviceIdErr; ?></span>
        <br><br>
        <input type="submit" name="register_bt" value="Register">
    </form>
</div>

<?php
// if user is not logged in, echo this
if ($_SESSION['clientId'] == null) {
    echo 'You are not logged in';
} else {
    // if user is logged in, store product
    if (array_key_exists('register_bt', $_POST)) {
        if ($deviceId != "") {
            store_product($_SESSION['clientId'], $deviceId);
        }
    }
}
?>




<?php
