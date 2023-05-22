<?
include("../application.php");
require_once($CFG->libdir."/ChartDirector/lib/phpchartdir.php");

# data for the gantt chart, representing the start date, end date and names for # various activities 
$startDate = array(
	chartTime(2004, 8, 16, 03, 30, 00), 
	chartTime(2004, 8, 16, 06, 00, 00), 
	chartTime(2004, 8, 16, 10, 00, 00),
	chartTime(2004, 8, 16, 14, 00, 00),
	chartTime(2004, 8, 16, 22, 00, 00)

	); 
$endDate = array(
	chartTime(2004, 8, 16, 04, 00, 00), 
	chartTime(2004, 8, 16, 07, 00, 00), 
	chartTime(2004, 8, 16, 22, 00, 00) ,
	chartTime(2004, 8, 16, 18, 00, 00)	,
	chartTime(2004, 8, 17, 04, 00, 00)

	); 
$labels = array("Market Research", "Define Specifications", "Overall Archiecture", "Project Planning", "Detail Design", "Software Development", "Test Plan", "Testing and QA", "User Documentation"); 

# Create a XYChart object of size 620 x 280 pixels. Set background color to light # blue (ccccff), with 1 pixel 3D border effect. 
$c = new XYChart(700, 280, 0xccccff, 0x000000, 1); 

# Add a title to the chart using 15 points Times Bold Itatic font, with white # (ffffff) text on a deep blue (000080) background 
$textBoxObj = $c->addTitle("Simple Gantt Chart Demo", "timesbi.ttf", 15, 0xffffff); 
$textBoxObj->setBackground(0x000080); 

# Set the plotarea at (140, 55) and of size 460 x 200 pixels. Use alternative # white/grey background. Enable both horizontal and vertical grids by setting their # colors to grey (c0c0c0). Set vertical major grid (represents month boundaries) 2 # pixels in width 

$plotAreaObj = $c->setPlotArea(140, 55, 500, 200, 0xffffff, 0xeeeeee, LineColor, 0xc0c0c0, 0xc0c0c0); 

$plotAreaObj->setGridWidth(1, 1, 1, 1); 

# swap the x and y axes to create a horziontal box-whisker chart 
$c->swapXY(); 

$c->yAxis()->setDateScale(chartTime(2004, 8, 16, 1, 0, 0), chartTime(2004, 8, 17, 10, 0, 0), 3600);

$c->yAxis()->setMultiFormat(StartOfDayFilter(),"<*font=Arial Bold*>{value|mmm d}", Chart.StartOfHourFilter(), "-{value|hh:nn}");
//$c->yAxis()->setMultiFormat(StartOfDayFilter(), "<*font=arialbd.ttf*>{value|dd}", AllPassFilter(), "{value|hh}");


# Set the y-axis scale to be date scale from Aug 16, 2004 to Nov 22, 2004, with ticks # every 7 days (1 week) 
//$c->yAxis->setDateScale(chartTime(2004, 8, 16, 1, 0, 0), chartTime(2004, 8, 17, 10, 0, 0),3660,3660); 

# Set multi-style axis label formatting. Month labels are in Arial Bold font in "mmm # d" format. Weekly labels just show the day of month and use minor tick (by using # '-' as first character of format string). 
//$c->yAxis->setMultiFormat(StartOfMonthFilter(), "<*font=arialbd.ttf*>{value|mmm d}", StartOfDayFilter(), "-{value|d}"); 
$c->yAxis->setMultiFormat(StartOfDayFilter(), "<*font=arialbd.ttf*>{value|dd}", AllPassFilter(), "{value|hh}");

# Set the y-axis to shown on the top (right + swapXY = top) 
$c->setYAxisOnRight(); 

# Set the labels on the x axis 
$c->xAxis->setLabels($labels); 

# Reverse the x-axis scale so that it points downwards. 
$c->xAxis->setReverse(); 

# Set the horizontal ticks and grid lines to be between the bars 
//$c->xAxis->setTickOffset(0.5); 

# Add a green (33ff33) box-whisker layer showing the box only. 
$c->addBoxWhiskerLayer($startDate, $endDate, null, null, null, 0x00cc00, SameAsMainColor, SameAsMainColor); 

# Output the chart 
header("Content-type: image/png"); 
print($c->makeChart2(PNG)); 


?>



