<?php


//Get the current date and time.
$date = new DateTime();
 
//Create a DateInterval object with P1D.
$interval = new DateInterval('P1D');
 
//Add a day onto the current date.
$date->add($interval);
 
//Print out tomorrow's date.
$tommorrow = $date->format("Y-m-d")."T00:00:00.000Z";

$today= date("Y-m-d")."T00:00:00.000Z";

echo $today."====".$tommorrow;

?>