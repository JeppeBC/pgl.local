<?php
require 'vendor/autoload.php';
require __DIR__ . '/utility.php';
require __DIR__ . '/mqtt.php';

session_start();
?>

<!DOCTYPE HTML>
<html>

<?php
echo (file_get_contents('templates/main_template.html'));


session_unset();
session_destroy();

echo 'You have signed out';

?>



</html>