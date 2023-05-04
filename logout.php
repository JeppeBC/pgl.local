<?php
require 'vendor/autoload.php';
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';

use \PhpMqtt\Client\MqttClient;
use \PhpMqtt\Client\ConnectionSettings;
?>

<!DOCTYPE HTML>
<html>

<?php
echo (file_get_contents('templates/main_template.html'));
?>