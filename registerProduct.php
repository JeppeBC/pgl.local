<?php
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';
require('vendor/autoload.php');

session_start();    //start new session

?>

<!DOCTYPE HTML>
<html>

<?php
echo (file_get_contents('templates/main_template.html'));
?>




<?php
