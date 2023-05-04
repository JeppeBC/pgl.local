<?php
// this file contains utility functions that are used by other files


// method to redirect to another page
function redirect($url) {
    header('Location: '.$url);
    die();
  }

  
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}

?>