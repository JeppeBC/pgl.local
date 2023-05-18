<?php
require __DIR__ . '/utility.php';

session_start();
?>

<!DOCTYPE HTML>
<html>

<?php
include 'templates/main_template.php';

session_unset();     //remove all session variables
session_destroy();   //destroy the session

echo 'You have signed out';

//redirect to login page
redirect('/login.php');

?>



</html>