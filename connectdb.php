<?php
  $hostname = "localhost";
  $user = "osol";
  $pass = "torpedokuda";
  $db = "skdb";

  $connect_error = FALSE;
  $connection = new mysqli($hostname, $user, $pass, $db);
  if ($connection->connect_error) 
    $connect_error = TRUE;
?>
