<?php
$inversions = 0;
/*
$arrayName = array(8,7,6,5,4,3,2,1);
echo print_r($arrayName) . "</br>";
echo print_r(mergeSort($arrayName)) . "</br>";
echo $inversions;
*/
$fhandle = fopen($_SERVER['DOCUMENT_ROOT'] . "/scripts/IntegerArray.txt", 'r');
$babah = array();
while (!feof($fhandle)) {
	$babah[] = fgets($fhandle);
}

$sorted = mergeSort($babah);
foreach ($sorted as $key => $value) {
	if ($key + 1 != $value) {
		echo $key . " = ". $value;
	}
}
echo $inversions;

function mergeSort($a) {
	$cA = count($a);
	if (($cA == 0) || ($cA == 1)) return $a;

	$hcA = ceil($cA/2);

	$h1 = array_slice($a, 0, $hcA);
	$h2 = array_slice($a, $hcA, $hcA);

	$x = mergeSort($h1);
	$y = mergeSort($h2);
	return mergeArrays($x, $y);
}

function mergeArrays($x, $y) {
	$i = 0;
	$j = 0;
	$r = array();
	$cX = count($x); 
	$cY = count($y); 
	global $inversions;

	while (($i < $cX) || ($j < $cY)) {
		if (($i < $cX) && ($j < $cY)) {		
			if ($x[$i] + 0 <= $y[$j] + 0) {
				$r[] = $x[$i];
				$i++;
			} elseif ($x[$i] + 0 > $y[$j] + 0) {
				$r[] = $y[$j];
				$inversions += $cX - $i;
				$j++;
			}
		} elseif ($j >= $cY) {
			$r[] = $x[$i];
			$i++;
		} elseif ($i >= $cX) {
			$r[] = $y[$j];
			$j++;
		}
	}
	return $r;
}

?>