<?php
// this file contains utility functions that are used by other files


// method to redirect to another page
function redirect($url) {
    header('Location: '.$url);
    die();
  }

// method to trim and Un-quote a quoted string 
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  return $data;
}

?>