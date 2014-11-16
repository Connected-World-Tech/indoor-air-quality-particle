<html>
<head>
  <title>Manage Event</title>
  <link rel="stylesheet" href="../html/stylesheet.css" type="text/css" >
</head>
  <script>
    function change_event(op) {
      document.event.op.value=op;
      if(document.getElementById('name')!==null)
        document.event.name.value=document.getElementById('name').value;
      document.event.submit();      
    }
  </script>
<body>
<?php
require_once ("Carbon/Carbon.php");
use Carbon\Carbon;
include '../globals.php';

$conn=mysqli_connect("", "", "", $db_name);

// Check connection
if (mysqli_connect_errno()) {
  exit('Failed to connect to MySQL: ' . mysqli_connect_error());
} 
if(!isset($_GET["id"])) exit("Must specify id parameter");
$id = htmlspecialchars($_GET["id"]);
$ts = htmlspecialchars($_GET["ts"]);
$op = filter_input(INPUT_GET, 'op', FILTER_VALIDATE_INT);
$name = htmlspecialchars($_GET["name"]);
$event_ts = Carbon::createFromTimeStamp($ts);
$finish_event=false;

if(strlen($op)>0) {
  if($op==2) { // Delete event
    $sql = "DELETE from events where ts='$ts'";
    if(!mysqli_query($conn,$sql)) {
       exit('Error: '.mysqli_error($conn));
    }
    echo "<div class='alerthead'>Event Deleted</div>";
  } else {
    if($op==1) {
      if(strlen($name)<=0) exit('Please enter Name');
      $sql_name="'$name'";
    } else {
      $sql_name="NULL";
    }
    
    $sql = "INSERT into events (name, core_id, ts) VALUES ($sql_name, '$id', '$ts')";
    if(!mysqli_query($conn,$sql)) {
       exit('Error: '.mysqli_error($conn));
    }
    if($op==1) echo "<div class='alerthead'>".$name." added</div>";
      else echo "<div class='alerthead'>Event Finished</div>";
  }
} else { // Retrieve existing events
  $result=mysqli_query($conn,"SELECT * from events where ts=$ts and core_id=$id");
  $row = mysqli_fetch_array($result);
  $name=$row['name'];
  if(mysqli_num_rows($result)>0) {
    $delete_only=true;
    if(!isset($row['name'])) $finish_event=true;
  }
}
echo "<h2>Manage Event (".$event_ts->format($user_date_format).")</h2>";
echo "<table border=0 class='form_table'>";

echo "<tr>";
if($finish_event) {
  echo "<th align=right style='vertical-align:top'>Delete Finish Event</th>";
} else {
  echo "<th align=right style='vertical-align:top'>Name:</th>";
  echo "<td style='vertical-align:top'><input type='text' name='name' maxlength=40 size=40 id='name' value='$name'></td>";
  echo "<td style='font-size:110%;font-style:italic;'>e.g. Sealed Bathroom door<br/>";
  echo "e.g. Sealed sink with tape";
  echo "</td>";    
}

echo "</tr>";
echo "<tr><td>&nbsp;</td>";

echo "<td colspan=2>";
if($delete_only) {
  echo "<input type='button' value='Delete Event' onclick='change_event(2);'>";
} else {
  echo "<input type='button' value='Finish Previous Event' onclick='change_event(3);'>";
  echo "&nbsp;&nbsp;OR&nbsp;&nbsp;";
  echo "<input type='button' value='Add Event' onclick='change_event(1);'>";
}
echo "</td>";
echo "</tr>";

echo "</table>";  

// ------------------------------------------------------------------- Form
echo "<form action='manage_event.php' method='get' name='event'>";
echo "<input type='hidden' name='id' value='$id'>";
echo "<input type='hidden' name='name' value=''>";
echo "<input type='hidden' name='op' value=''>";
echo "<input type='hidden' name='ts' value='$ts'>";
echo "</form>";

echo "</body>\n";
echo "</html>\n";
mysqli_close($conn);  
?>    