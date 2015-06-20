<?php

  require_once "../../connectdb.php";
  require_once "../../functions.php";
  
  $typedisplay = array(
    "Lock Door",
    "Visitor Panel",
    "Remote");

  $monthdisplay = array(
    1 => "Januari",
    "Pebruari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember");

  function searchdevice($search, $sort)
  {
    global $typedisplay, $monthdisplay;
    $searchquery = "";
    $sortquery = "";
    if ($sort != "")
      $sortquery = "ORDER BY $sort";
    if ($search != "")
    {
      /******* SEARCH in id, room, ip, date of regtime *******/
      $searchquery = "WHERE id LIKE '%$search%' OR room LIKE '%$search%' OR INET_NTOA(ip) LIKE '%$search%' OR cast(regtime as date) LIKE '%$search%'";
      /******* SEARCH in type *******/
      foreach($typedisplay as $index => $value)
      {
        if (strpos(strtolower($value), strtolower($search)) !== FALSE)
          $searchquery .= " OR type=$index";
      }
      /******* SEARCH in month of regtime *******/
      foreach($monthdisplay as $index => $value)
      {
        if (strpos(strtolower($value), strtolower($search)) !== FALSE)
          $searchquery .= " OR cast(regtime as date) LIKE '%-%$index-%'";
      }
      /******* GENERATE search query *******/
      $query = "SELECT id,type,room,INET_NTOA(ip) as ip, UNIX_TIMESTAMP(regtime) as regtime, UNIX_TIMESTAMP(lastactive) as lastactive FROM devices $searchquery $sortquery"; 
      echo $query;

      /******* SQL Query *******/
      $sqlresult = queryMysql($query);
      if (!$sqlresult)
        return FALSE;

      /******* Fetch Query Result And Save Them to Array *******/
      $num = $sqlresult->num_rows;
      $result = array();
      for ($i = 0; $i < $num; ++$i)
      {
        $result[$i] = $sqlresult->fetch_array(MYSQLI_ASSOC);
      }
      return $result;
    }
  }
?>
