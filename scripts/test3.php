<?php

$fhandle = fopen($_SERVER['DOCUMENT_ROOT'] . "/scripts/QuickSort.txt", 'r');
$babah = array();
$bi = 0;
while ((!feof($fhandle)) && ($bi < 995000)) {
	$babah[] = fgets($fhandle);
	$bi++;
}
fclose($fhandle);

$babah2 = array();
$babah2 = $babah;
$babah3 = array();
$babah3 = $babah;
$test = array(3, 2, 1); 

$comp1 = 0;
$comp2 = 0;
$comp3 = 0;

function swapElements(&$array, $i, $j) {
	$b = $array[$i];
	$array[$i] = $array[$j];
	$array[$j] = $b;
}

function QuickSort(&$array, $f, $l) {
	global $comp1;
	$comp1 += $l - $f;
	$pivot = $array[$f];
	$i = $f + 1; 
	$j = $f + 1;
//	echo $f . ":" . $l . " __ ". $n . "</br>";
	for ($i; $i <= $l ; $i++) { 
		if ($array[$i] + 0 < $pivot + 0) {
			swapElements($array, $i, $j);
			$j++;
		}
	}
	swapElements($array, $f, $j - 1);
	if ( $j - $f - 1> 1 + 0) {
		QuickSort($array, $f, $j - 2);
	}
	if ( $l - $j + 1 > 1 + 0) {
		QuickSort($array, $j, $l);
	}
}
function QuickSort2(&$array, $f, $l) {
	swapElements($array, $f, $l);
	global $comp2;
	$comp2 += $l - $f;
	$pivot = $array[$f];
	$i = $f + 1; 
	$j = $f + 1;
//	echo $f . ":" . $l . " __ ". $n . "</br>";
	for ($i; $i <= $l ; $i++) { 
		if ($array[$i] + 0 < $pivot + 0) {
			swapElements($array, $i, $j);
			$j++;
		}
	}
	swapElements($array, $f, $j - 1);
	if ( $j - $f - 1> 1 + 0) {
		QuickSort2($array, $f, $j - 2);
	}
	if ( $l - $j + 1 > 1 + 0) {
		QuickSort2($array, $j, $l);
	}
}
function QuickSort3(&$array, $f, $l) {
	global $comp3;
	$comp3 += $l - $f;
	$median = $f + floor(($l - $f)/2);
	if (($l - $f + 1) > 2) {
		if ((($array[$median] + 0< $array[$f]+ 0) && ($array[$median]+ 0 > $array[$l]+ 0)) || (($array[$median]+ 0 > $array[$f]+ 0) && ($array[$median]+ 0 < $array[$l]+ 0))) swapElements($array, $median, $f);
//		if ((($array[$f] > $array[$median]) && ($array[$f] < $array[$l])) || (($array[$f] < $array[$median]) && ($array[$f] > $array[$l])));
		if ((($array[$l]+ 0 > $array[$median]+ 0) && ($array[$l]+ 0 < $array[$f]+ 0)) || (($array[$l]+ 0 < $array[$median]+ 0) && ($array[$l]+ 0 > $array[$f]+ 0))) swapElements($array, $f, $l);
	}
	$pivot = $array[$f];
//	echo $median . " ";
	$i = $f + 1; 
	$j = $f + 1;
//	echo $f . ":" . $l . " __ ". $n . "</br>";
	for ($i; $i <= $l ; $i++) { 
		if ($array[$i] + 0 < $pivot + 0) {
			swapElements($array, $i, $j);
			$j++;
		}
	}
	swapElements($array, $f, $j - 1);
	if ( $j - $f - 1> 1 + 0) {
		QuickSort3($array, $f, $j - 2);
	}
	if ( $l - $j + 1 > 1 + 0) {
		QuickSort3($array, $j, $l);
	}
}


//echo print_r($test) . "</br>";
QuickSort($babah, 0, count($babah) - 1);
QuickSort2($babah2, 0, count($babah2) - 1);
QuickSort3($babah3, 0, count($babah3) - 1);
/*foreach ($babah3 as $key => $value) {
	if ($key + 1 != $value) {
		echo $key . " = ". $value;
	}
}*/

echo " comp1 " . $comp1 . "</br>";
echo " comp2 " . $comp2 . "</br>";
echo " comp3 " . $comp3 . "</br>";
echo " _ " . floor(4/2) . "</br>";
//echo print_r($babah3) . "</br>"

?>