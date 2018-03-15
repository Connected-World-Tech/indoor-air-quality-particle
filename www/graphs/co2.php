<?php // content="text/plain; charset=utf-8"
include 'graph_base.php';

$result=mysqli_query($conn,"SELECT * from readings WHERE group_id=$id and ts>= $start_ts and ts<= ($end_ts + 1) order by ts"); 
$ts=Array();
$co2=Array();
$found=false;
while($row = mysqli_fetch_array($result)) {
  $ts_str=gmdate('r', $row['ts']);
  //error_log("co2=".$row['co2']."||ts=".$row['ts']."||ts=".$ts_str);
  if(strlen($row['co2'])>0) {
    $ts[]=$row['ts'];
    $co2[]=$row['co2'];
    $found=true;
  }
}
if(!$found) {
  $name = 'no_data.png';
  $fp = fopen($name, 'rb');

  // send the right headers
  header("Content-Type: image/png");
  header("Content-Length: " . filesize($name));

  // dump the picture and stop the script
  fpassthru($fp);
  exit;
}
$co2_plot=new LinePlot($co2,$ts);
$co2_plot->SetColor('darkgray');
$co2_plot->SetWeight(2);
$co2_plot->SetFillColor($line_fill_color);

$graph = new Graph($width,$height);
$graph->SetFrame(false);
$graph->SetBackgroundImage('background_h_33_66.png',BGIMG_FILLPLOT);
$graph->SetBackgroundImageMix(35);
$graph->SetMargin(60,60,40,50);
$graph->SetMarginColor('white');
$graph->SetScale('datlin',$CO2_MIN,$CO2_MAX);
$graph->Add($co2_plot);

$graph->ygrid->SetColor("azure3");

$graph->xaxis->SetLabelAngle(90);
$graph->xaxis->scale->SetDateFormat('g a');
$graph->xaxis->SetWeight(2);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,$font_size-3);

$graph->yaxis->SetWeight(2);
$graph->yaxis->SetColor('darkgray');
$graph->yaxis->SetFont(FF_ARIAL,FS_NORMAL,$font_size-3);
$graph->yaxis->title->SetColor('darkgray');
$graph->yaxis->title->Set('Co2');
$graph->yaxis->title->SetFont(FF_ARIAL,FS_BOLD,$font_size);
$graph->yaxis->title->SetAngle(90);
$graph->yaxis->title->SetMargin(10);
add_plotlines($start_ts, $ts);

// Display the graph
$graph->Stroke();
?>
