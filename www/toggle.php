<?php
$gpio=$_GET["call"];
if(system("gpio -g read $gpio"))
system("gpio -g write $gpio 0");
else
system("gpio -g write $gpio 1");
?>
