<?php
require __DIR__ . '/utility.php';

session_start();
?>

<!DOCTYPE HTML>
<html>

<?php
echo (file_get_contents('templates/main_template.html'));       //get the template for the main page

session_unset();     //remove all session variables
session_destroy();   //destroy the session

echo 'You have signed out';

?>



</html>