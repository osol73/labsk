<?php
  require_once "functions.php";

  $deletepage = "delete-main.php";
  $viewpage = "view-main.php";

  /******* search variable handler *******/
  if (isset($_GET['search']))
    $search = clean2SQL($_GET['search']);
  else
    $search = "";
  /******* sort variable handler *******/
  $sort = "id";
  if (isset($_GET['sort']))
  {
    foreach($tabledisplay as $index => $value)
    {
      if ($_GET['sort'] == $index)
      {
        $sort = $_GET['sort'];
        break;
      }
    }
  }
  makeheader(compact('search', 'sort'));  
  if ($search == "")
    echo "<h2>Daftar Perangkat</h2>";
  else
    echo "<h2>Hasil Pencarian \"$search\"</h2>";
  echo "<form action=\"$deletepage\" method=\"get\">";
  echo "<div>";
  makedevicelist(searchdevice($search, $sort));
  echo "</div><div><button>Delete</button></div></form>";
?>
