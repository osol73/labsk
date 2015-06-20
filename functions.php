<?php

  function queryMysql($query)
  {
    global $connection;
    return $connection->query($query);
  }
  function clean2SQL($string)
  {
    global $connection;
    $string = stripslashes($string);
    return $connection->real_escape_string($string);
  }
?>
