<?php
require 'vendor/autoload.php';
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;

session_start();    //start session

?>


<!DOCTYPE html>
<html> 

<?php
include 'templates/main_template.php';
?>



<!--Body design-->
<div class="homebody">
  <h2>Database</h2>
  <p>Here you can find all the information about residents journeys and emergencies </p>
</div>


<?php
$clientId = $_SESSION['clientId']; // access session variable updated at login

if ($clientId != null) {

  // MQTT connection settings
  $connectionSettings = (new ConnectionSettings)
    ->setKeepAliveInterval(1) // keep alive ping interval in seconds
    ->setConnectTimeout(3); // connection timeout in seconds

  //initialize the MQTT client
  $mqtt = new MqttClient($hostname, $port, $clientId);

  // Get emergency Data and present them in a table
  try {
    $mqtt->connect($connectionSettings, $cleanSession);

    $mqtt->subscribe(
      $RESPONSE_EMERGENCY_TOPIC . '/' . $clientId . '/response',
      function (string $topic, string $message) use ($mqtt, $RESPONSE_EMERGENCY_TOPIC, $clientId) { // callback function
        // get data from the message in json format
        $data = json_decode($message, true);

        if (count($data) == 0) {
          echo '<p style="float: right">No emergencies found</p>';
        } else {
          // Generate the HTML table code
          echo '<table style="float: right">';
          echo '<thead><tr><th>Emergency ID</th><th>Date, Time</th><th>Emergency time</th><th>Device ID</th><th>User ID</th></tr></thead>';
          echo '<tbody>';
          //access the data and print them in the table
          foreach ($data as $event) {
            echo '<tr>';
            echo '<td style="text-align: center;">'. $event['emergency_id'] . '</td>';
            echo '<td style="text-align: center;">'. $event['datetime'] . '</td>';
            echo '<td style="text-align: center;">'. $event['et'] . '</td>';
            echo '<td style="text-align: center;">'. $event['device_id'] . '</td>';
            echo '<td style="text-align: center;">'. $event['user_id'] . '</td>';
            echo '</tr>';
          }
          echo '</tbody>';
          echo '</table>';
        }

        $mqtt->unsubscribe($RESPONSE_EMERGENCY_TOPIC . '/' . $clientId . '/response'); // unsubscribe from the topic
        $mqtt->disconnect();  // disconnect from the broker
      },
      0
    );

    $mqtt->publish($REQUEST_GET_EMERGENCIES_TOPIC, $clientId . ';', 0, false);

    $mqtt->loop(true);
  } catch (Exception $e) {
  }

  // Get journey Data and present them in a table
  try {

    $mqtt->connect($connectionSettings, $cleanSession);

    $mqtt->subscribe(
      $RESPONSE_SEND_EVENTS_TOPIC . '/' . $clientId . '/response',
      function (string $topic, string $message) use ($mqtt, $RESPONSE_SEND_EVENTS_TOPIC, $clientId) {
        $data = json_decode($message, true);    //decode json data into an array

        if (count($data) == 0) {
          echo '<p style="float: left">No journeys found</p>';
        } else {
          // Generate the HTML table code
          echo '<table style="float: left">';
          echo '<thead><tr><th>Journey ID</th><th>Date, Time</th><th>Round trip time</th><th>Toilet time </th><th>Device ID</th><th>User ID</th></tr></thead>';
          echo '<tbody>';
          foreach ($data as $event) {
            echo '<tr>';
            echo '<td style="text-align: center;">' . $event['journey_id'] . '</td>';
            echo '<td style="text-align: center;">' . $event['datetime'] . '</td>';
            echo '<td style="text-align: center;">' . $event['rtt'] . '</td>';
            echo '<td style="text-align: center;">' . $event['tt'] . '</td>';
            echo '<td style="text-align: center;">' . $event['device_id'] . '</td>';
            echo '<td style="text-align: center;">' . $event['user_id'] . '</td>';
            echo '</tr>';
          }
          echo '</tbody>';
          echo '</table>';
        }

        $mqtt->unsubscribe($RESPONSE_SEND_EVENTS_TOPIC . '/' . $clientId . '/response');
        $mqtt->disconnect();
      },
      0
    );

    $mqtt->publish($REQUEST_GET_EVENTS_TOPIC, $clientId . ';', 0, false);

    $mqtt->loop(true);
  } catch (Exception $e) {
  }
} else {
  echo '<p style="text-align: center">Please log in to see the data</p>';
}

?>


</body>

</html>