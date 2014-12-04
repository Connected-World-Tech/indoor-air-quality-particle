<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Location</title>
  <link rel="stylesheet" href="../html/stylesheet.css" type="text/css" >
  <link rel="stylesheet" href="../calendar/calendar.css" type="text/css" />
  <link rel="stylesheet" href="../javascript/tipsy.css" type="text/css" />
  <script src="../javascript/jquery-1.11.1.min.js"></script>
  <script src="../javascript/jquery.tipsy.js"></script>  
</head>
  <script>
    function change_month(year,month) {
      document.cal.year.value=year;
      document.cal.month.value=month;    
      document.cal.submit();
    }
    function draw_charts(start_date) {
      document.cal.add.value='true';
      document.cal.start_date.value=start_date;
      document.cal.hour.value=document.getElementById('hour').value;
      document.cal.submit();      
    }
    function back_button() {
      document.back.submit();
    }
  </script>
<body>
<?php
require_once ("Carbon/Carbon.php");
use Carbon\Carbon;
require('../calendar/calendar.php');
include '../globals.php';

$conn=mysqli_connect("", "", "", $db_name);

// Check connection
if (mysqli_connect_errno()) {
  exit('Failed to connect to MySQL: ' . mysqli_connect_error());
} 
$year  = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);
$month = filter_input(INPUT_GET, 'month', FILTER_VALIDATE_INT);

// initialize the calendar object
$calendar = new calendar();

// get the current month object by year and number of month
$currentMonth = $calendar->month($year, $month);

// get the previous and next month for pagination
$prevMonth = $currentMonth->prev();
$nextMonth = $currentMonth->next();
  
echo "<table border=0 class='form_table'>\n";
echo "<tr>\n";
echo "<td colspan=2><h2>Choose Event</h2></td>\n";
echo "<td align=right><input type='button' value='Back' style='padding:2px;' onclick='back_button()'></td>\n";
echo "</tr>\n";
echo "<table border=0 style='width:240px'>\n";
echo "<tr>\n";
echo "<td><input type='button' value='&lt; Previous' style='padding:2px;' onclick='change_month(\"".$prevMonth->year()->int()."\",\"".$prevMonth->int()."\")'></td>\n";
echo "<td width='300' align=center ><b>".$currentMonth->year()->name()."</b></td>\n";
echo "<td align=right><input type='button' value='Next    &gt;' style='padding:2px;' onclick='change_month(\"".$nextMonth->year()->int()."\",\"".$nextMonth->int()."\")'></td>\n";
echo "</tr>\n";
echo "</table>\n";

echo "<section class='year' style='background:white;'>\n";

echo "<ul>\n";
echo "<li>\n";
echo "<h2>".$currentMonth->name()."</h2>\n";
    
echo "<table border=0 >\n";
echo "<tr>\n";
  foreach($currentMonth->weeks()->first()->days() as $weekDay): 
    echo "<th>".$weekDay->shortname()."</th>\n";
  endforeach; 
  echo "</tr>\n";
  foreach($currentMonth->weeks(6) as $week): 
  echo "<tr>\n";
  foreach($week->days() as $day): 
    if($day->month() != $currentMonth) {
      echo "<td class='inactive'>".$day->int()."</td>\n";
    } else {
      $curr_date=Carbon::createFromDate($day->year()->int(),$day->month()->int(),$day->int(), $user_timezone);
      $curr_date_start_utc=$curr_date->startOfDay()->format('U');
      $curr_date_end_utc=$curr_date->endOfDay()->format('U');
      $result=mysqli_query($conn,"SELECT * from events WHERE core_id=$id and ts>=$curr_date_start_utc and ts<=$curr_date_end_utc order by ts");
      if(mysql_errno()) {
        exit('Error: '.mysqli_error($conn));
      }
      $found_event=false;
      $background_color='white';
      $tooltip_str="";
      
      while($row = mysqli_fetch_array($result)) {
	if(!$found_event) {
	  $tooltip_str.="Events<br/>";
	}
	$ts_carbon = Carbon::createFromTimeStamp($row['ts']);	
	error_log("name=".$row['name']."||ts=".$row['ts']."||ts=".$ts_carbon->format($param_date_format));
	$ts[]=$row['ts'];
	$tooltip_str.=$ts_carbon->format($user_date_format)."=".$row['name'];
	$found_event=true;
      }     
      if($found_event) {
	$background_color='cyan';
      }
      if($day->isToday()) {
	$day_html="<strong style='color:red;'>" . $day->int() . "</strong>\n";
      } else {
	$day_html=$day->int() . "\n";
      }
      if(strlen($tooltip_str)>0) {
        echo "<td onclick='alert(\"nothing\");' id='cal-day' style='background-color:$background_color;cursor:pointer;' title='$tooltip_str'>\n";     
        echo $day_html;
        echo "</td>\n";
      } else {
	echo "<td>".$day_html."</td>\n";
      }

    }
  endforeach;
  echo "</tr>\n";
  endforeach;
echo "</table>\n";
echo "</li>\n";
echo "</ul>\n";
echo "</section>\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
// ------------------------------------------------------------------- Form
echo "<form action='event_monthly.php' method='get' name='cal'>\n";
echo "<input type='hidden' name='id' value='$id'>\n";
echo "<input type='hidden' name='year' value='$year'>\n";
echo "<input type='hidden' name='month' value='$month'>\n";
echo "</form>\n";
// ------------------------------------------------------------------- Back
echo "<form action='../index.php' method='get' name='back'>\n";
echo "</form>\n";
echo "<script type='text/javascript'>\n";

echo "$.fn.tipsy.defaults = {\n";
echo "      delayIn: 0,\n";      // delay before showing tooltip (ms)
echo "      delayOut: 0,\n";     // delay before hiding tooltip (ms)
echo "      fade: false,\n";     // fade tooltips in/out?
echo "      fallback: '',\n";    // fallback text to use when no tooltip text
echo "      gravity: 'n',\n";    // gravity
echo "      html: true,\n";      // is tooltip content HTML?
echo "      live: false,\n";     // use live event support?
echo "      offset: 0,\n";       // pixel offset of tooltip from element
echo "      opacity: 1.0,\n";    // opacity of tooltip
echo "      title: 'title',\n";  // attribute/callback containing tooltip text
echo "      trigger: 'hover'\n"; // how tooltip is triggered - hover | focus | manual
echo "    };\n";
echo "$(function() {\n";
echo "  $('#cal-day').tipsy({gravity: 'nw'});\n";
echo "});\n";
echo "</script>\n";
echo "</body>\n";
echo "</html>\n";
mysqli_close($conn);  
?>    

