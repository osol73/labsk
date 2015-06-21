<?php

  require_once "../../connectdb.php";
  require_once "../../functions.php";
  
  $minactive = 5;

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

  /****** Array for setting table header text and column order *******/
  $tabledisplay = array(
    "id" => "ID",
    "type" => "Tipe",
    "room" => "Ruang",
    "ip" => "Alamat IP",
    "regtime" => "Terdaftar",
    "status" => "Status");

  $statusdisplay = array(
    "Belum Aktif",
    "Offline",
    "Online");

  function makeheader($data)
  {
    global $tabledisplay;
    echo <<<_END
      <div>
        <div>
          <form>
            <span>Urut Berdasarkan: </span>
            <select name="sort">
_END;

    /******* Display Select Element of Sort By *******/
    foreach($tabledisplay as $index => $value)
    {
      if ($data['sort'] == $index)
        $selected = ' selected="selected"';
      else
        $selected = "";
      echo "<option value=\"$index\"$selected>$value</option>";
    }

    echo <<<_END
            </select>
            <button>Sort</button>
        </div>
        <div>
            <input type="text" name="search">
            <input type="submit" value="Cari">
        </div>
      </div>
_END;
  }


  /******* Function for searching device *******/
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
    }
    /******* GENERATE search query *******/
    $query = "SELECT id,type,room,INET_NTOA(ip) as ip, UNIX_TIMESTAMP(regtime) as regtime, UNIX_TIMESTAMP(lastactive) as lastactive, activated FROM devices $searchquery $sortquery"; 
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
      /******* GENERATE status             *******/
      /******* 0 for not activated yet     *******/
      /******* 1 for offline, 2 for online *******/
      if ($result[$i]['activated'] == 0)
        $status = 0;
      else
      {
        global $minactive;
        if (time() - $result[$i]['lastactive'] > $minactive)
          $status = 1;
        else
          $status = 2;
      }
      $result[$i]['status'] = $status;
      unset($result[$i]['activated']);
    }
    return $result;
  }

  /*******  Function for display list for data             *******/
  /*******  It's expecting data from searchdevice function *******/
  function makelist($data)
  {
    date_default_timezone_set('asia/jakarta');
    global $tabledisplay;
    echo "<table><tr><th></th>";
    /****** Display Table Header ******/
    foreach($tabledisplay as $header)
    {
      echo "<th>$header</th>";
    }
    echo "<th></th></tr>";
    /******* Display Content *******/
    foreach($data as $device)
    {
      echo '<tr><td><input type="checkbox" name="device[]"></td>';
      foreach($tabledisplay as $index => $value)
      {
        echo "<td>";
        if ($index == "status")
        {
          global $statusdisplay;
          $status = $device['status'];
          echo "$statusdisplay[$status]";
        }
        else if($index == "regtime")
        {
          global $monthdisplay;
          $timestamp = $device['regtime'];
          $day = date("j", $timestamp);
          $month = date("n", $timestamp);
          $year = date("Y");
          echo "$day $monthdisplay[$month] $year";
        }
        else
        {
          echo $device[$index];
        }
        echo "</td>";
      }
      $id = $device['id'];
      echo "<td><span><a href=\"view.php?id=$id\">View</a></span><span><a href=\"delete.php?id=$id\">Delete</a></span></td></tr>";
    }
    echo "</table>";
  }
?>
