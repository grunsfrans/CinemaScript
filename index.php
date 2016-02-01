<?php
error_reporting(E_ALL);

//require 'functions/dbconfig.php';
require 'functions/testcase_cinema.php';
require 'functions/testcase_cinema_frans.php';



?><!DOCTYPE>
<head>
    <title>Bios</title>
    <link rel="stylesheet" href="css/style.css">
    <?php
        // echo '<meta http-equiv="refresh" content="0">';
    ?>
</head>
<body>
<pre>
<?php

$roomSize = 500000;
$groupSize = 5000;
  
$time_start_cinema = microtime(true);
$cinema = new FCinema($roomSize);
$time_end_cinema = microtime(true);
echo $time_end_cinema - $time_start_cinema . " seconden initaliseren \n\n" ;
 
 $time_start = microtime(true);
 $cinema->getSeatsForVisitors($groupSize);
 $time_end = microtime(true);
 
 echo "\n\nDe groep plaatsen duurde " . ($time_end - $time_start) . " seconden \n\n" ;
  
?>
</pre>

<div id="cinema">
   <?php echo $cinema->display();
   ?>
</div>
<p>&nbsp; </p>
</body>
</html>
