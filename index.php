<?php
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

  ////////////////////////////////////////////////////////////
  echo "Frans \n";
  
  $time_start_cinema = microtime(true);
$cinema = new FCinema(20);
$time_end_cinema = microtime(true);
echo $time_end_cinema - $time_start_cinema . " seconden initaliseren \n\n" ;
 //print_r($cinema->getSeatsForVisitors(5000));
 
 $time_start = microtime(true);
 $cinema->getSeatsForVisitors(10);
 //$cinema->giveSeatNumbers(5000);
 $time_end = microtime(true);
 
 echo $time_end - $time_start . " seconden \n\n" ;
  
?>
</pre>

<div id="cinema">
   <?php //echo $cinema->display();
   ?>
</div>
<p>&nbsp; </p>
</body>
</html>
