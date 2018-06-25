<?php

  //Stop the Ongoing session by emptying all session variables and destroying it	
  session_start();

  $_SESSION = array();

  session_destroy();

  //Redirect back to login page
  header("location: login.php");
 ?>
