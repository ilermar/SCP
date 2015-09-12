<?php
$needle = "SC:";
$haystack="";
$todayfile="\log-".date("Y-m-d").".php";
$file = fopen("..\api\application\logs".$todayfile, "r") or exit("Unable to open file!");
$myfile = fopen("..\api\application\logs\scplogs".$todayfile, "w+") or exit("Unable to open file!");

//echo date("Y-m-d");
//echo "<br/>";
//echo "<br/>";
while(!feof($file))
{
	$haystack = fgets($file);
	if(strpos($haystack,$needle)){
		echo $haystack."<br/>";
		fwrite($myfile,$haystack);
	}
//echo fgets($file). "<br />";
}

fclose($file);
fclose($myfile);

?>