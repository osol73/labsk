<?php
  require_once "functions.php";
  $indexpage = "index-main.php";
  /******* get device id variable *******/
  if ($_GET['id'])
  {
    if(is_array($_GET['id']))
    {
      $id = array();
      foreach($_GET['id'] as $index => $value)
        $id[$index] = clean2SQL($value);
    }
    else
    {
      $id = clean2SQL($_GET['id']);
      $id = array($id);
    }
  }
  else
  {
    header("Location: $indexpage");
    exit;
  }

  $confirmed = 0;
  if (isset($_GET['confirmed']))
    $confirmed = $_GET['confirmed'];

  /******* verify and get device from database *******/
  $verified = 0;
  $device = array();
  foreach($id as $value)
  {
    if ($temp = getdevicedata($value))
    {
      $device[] = $temp;
      $verified++;
    }
  }
  if (!$verified)
    header("Location: $indexpage");

  if (!$confirmed)
  {
    echo <<<_END
      <div>
        <div>Apakah Anda yakin ingin menghapus $verified item ini?</div>
        <div>
          <form method="get">
          <table><tr><th>ID</th><th>Tipe</th>
_END;
    foreach($device as $value)
    {
      $id = $value['id'];
      $type = $value['type'];
      global $typedisplay;
      echo "<tr><td><input type=\"hidden\" name=\"id[]\" value=\"$id\">$id</td><td>$typedisplay[$type]</td></tr>";
    }
    echo <<<_END
        </table>
        </div>
        <div><button name="confirmed" value="1">Ya</button></form><form method="link" action="$indexpage"><button>Tidak</button></form></div>
      </div>
_END;
  }
  else
  {
    $total = count($device);
    $success = 0;
    foreach($device as $value)
    {
      $id = $value['id'];
      if(!deletedevice($id))
        echo "<div>Gagal menghapus perangkat dengan ID $id</div>";
      else
        $success++;
    }
    if ($total == $success)
      echo "<div>$total item berhasil dihapus</div>";
    else
      echo "<div>$success dari $total item berhasil dihapus</div>";
    echo "<a href=\"$indexpage\">Kembali ke daftar perangkat</a>";
  }

  function deletedevice($id)
  {
    $query = "DELETE FROM devices WHERE id='$id'";
    $result = queryMysql($query);
    if (!$result)
      return FALSE;
    else
      return TRUE;
  }

  /*
  $query = "SELECT * FROM devices WHERE id='$id'";
  $result = queryMysql($query);
  if ($result->num_rows < 1)
  {
    header('Location: index-main.php');
    exit;
  }
  else
    $device = $result->fetch_array(MYSQLI_ASSOC);
   */ 
  

?>
