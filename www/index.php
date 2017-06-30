<html>
  <head>
    <title>Welcome Page</title>
    <link rel="stylesheet" type="text/css" href="html/stylesheet.css">    
    <style>
      th {text-align:right;}
      td {padding: 4px 14px 4px 14px}
    </style>
  </head>
  <script>
    function click_button(id, action) {
      if(action==null)
        document.aq.action = "year_cal.php";
      else document.aq.action = action;
      
      document.aq.id.value=id;
      document.aq.submit();
    }
  </script>
  <body>
<?php
require_once ("Carbon/Carbon.php");
use Carbon\Carbon;
include 'globals.php';

$result=mysqli_query($conn,"SELECT * from groups order by name");

echo "<form action='year_cal.php' method='get' name='aq'>\n";
echo "<input type='hidden' name='id' value='0'>\n";

// Icons from thenounproject
echo "<table border=0 style='border-spacing:10px;'>\n";
echo "<tr>";
echo "<td colspan=6><h2>Sensor Groups</h2></td>";
echo "<td colspan=2 style='text-align:right;'><input type='button' value='Add New Sensor Group' onclick='click_button(null,\"add_new_sensor.php\");'></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=8 style='text-align:center;'>";
echo "<div style='display:inline-block;border:2px solid darkmagenta;padding:10px;font-weight:bold;'>";
echo "<a href='help/resources.html'>Resources / Help</a>";
echo "</div>";
echo "</td>";
echo "</tr>";
echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
echo "<th style='text-align:center;'>Last Reading</th><th style='text-align:center;'>Group Type</th></tr>";
while($row = mysqli_fetch_array($result)) {
  $result_geo=mysqli_query($conn,"SELECT name from geographical where group_id=".$row['id']." order by ts desc");
  $row_geo=mysqli_fetch_array($result_geo);
  $result_group=mysqli_query($conn,"SELECT max(ts) as ts from readings where group_id=".$row['id']); 
  $row_group=mysqli_fetch_array($result_group);
  $geo_row = get_current_geographical($row_group['ts'],$row['id']);
  $result_location=mysqli_query($conn,"select name from locations where type=1 and group_id=".$row['id']." and ts=".
                                      "(select max(ts) as ts from locations where type=1 and group_id=".$row['id'].")"); 
  $row_location=mysqli_fetch_array($result_location);
  
  echo "<tr>\n";
  echo "<td style='text-align:right;'><span style='font-size:20px;font-weight:bold;'>".$row['name']."</span><br/>";
  echo "<i style='font-size:16px;'>".$row_location['name']."</i>";
  echo "</td>\n";
  if(strlen($geo_row['zoom_temp_hum'])>0 && strlen($geo_row['zoom_sewer'])>0) {
    echo "<td><img src='images/calendar.png' onclick='click_button(".$row['id'].");' height=40 width=40 style='cursor:pointer;'></td>";
    echo "<td><img src='images/graph.png' onclick='click_button(".$row['id'].",\"dashboard.php\");' height=40 width=40 style='cursor:pointer;'></td>";
    echo "<td><img src='images/averages.png' onclick='click_button(".$row['id'].",\"averages.php\");' height=40 width=40 style='cursor:pointer;'></td>";
    echo "<td><img src='images/barchart.png' onclick='click_button(".$row['id'].",\"events/event_monthly.php\");' height=40 width=40 style='cursor:pointer;'></td>";
    echo "<td><img src='images/download.png' onclick='click_button(".$row['id'].",\"transfer/download.php\");' height=40 width=40 style='cursor:pointer;'></td>";
    $name=get_sensor_type_name($row['id']);
    echo "<td style='text-align:center'>";
    echo "<img src='images/location.png' onclick='click_button(".$row['id'].",\"add_geographical.php\");' height=30 width=30 style='cursor:pointer;'><br/>";
    if(strlen($row_geo['name'])>0) {
      echo "<i>(".$row_geo['name'].")</i>";
    } else {
      echo "<i>&lt;Not Set&gt;</i>";
    }
    echo "</td>";  
    echo "<td style='text-align:center;'>";
    if(!is_null($row_group['ts'])) {
      $curr_date=Carbon::createFromTimeStamp($row_group['ts']);
      $curr_date->setTimezone($user_timezone);
      if($curr_date->isToday()) echo "Today ".$curr_date->format("H:i");
        else echo $curr_date->format("F jS Y H:i");
      echo "<br/>(".$user_timezone.")";
    } else echo "&lt;NONE&gt;";
    echo "</td>";
    echo "<td style='text-align:center;'>";
    if($sensor_count==1) {
      echo "(".$name.")";
    }
    echo "</td>";  
  } else {
	  if(strlen($row_group['ts'])==0) {
		echo "<td colspan=6>No Readings Found</td>";  
	  } else { 
	    echo "<td colspan=5>Enter Current Location</td>";
	    echo "<td style='text-align:center'>";
	    echo "<img src='images/location.png' onclick='click_button(".$row['id'].",\"add_geographical.php\");' height=30 width=30 style='cursor:pointer;'><br/>";
	    echo "&nbsp;";
	    echo "</td>";
      }
  }
  echo "</tr>\n";

}
echo "</table>\n";
echo "</form>\n";
   
mysqli_close($conn);   
?>
  </body>  
</html>


