<?php
$fhandle = fopen($_SERVER['DOCUMENT_ROOT'] . "/scripts/kargerMinCut.txt", 'r');
$babah = array();
$m = 0;

while (!feof($fhandle)) {
	$nodeArray = explode('	', chop(fgets($fhandle)));
	$node = $nodeArray[0];
	array_shift($nodeArray);
	$babah[$node] = $nodeArray;

}
fclose($fhandle);

/*$babah = array('1' => array(2, 4),
				'2' => array(1, 3, 4),
				'3' => array(2, 4),
				'4' => array(1, 2, 3) 
				);
*/
function printGraph($array) {
	foreach ($array as $key => $value) {
		$str = "";
		foreach ($value as $k => $v) {
			$str .= $v . " ";
		}
		echo $key . "----" . $str . "</br>";
	}
	echo "____________________________________</br>";
}

function chooseEdge($array) {
	$edges = 0;
	foreach ($array as $key => $value) {
		$edges += count($value);
	}

	$randomEdge = rand(1, $edges);
//	echo "randomEdge = " . $randomEdge;
	foreach ($array as $k => $v) {
		$count = count($v);
		$randomEdge -= $count;
		if ($randomEdge < 1) {
			$randomEdge += $count;
			return $k . "-" . $v[$randomEdge - 1];
		}	
	}
}

function countEdges($array) {
	$edges = 0;
	foreach ($array as $key => $value) {
		$edges += count($value);
	}
	return $edges/2;
}

function deleteEdge(&$array) {
	$edge = chooseEdge($array);
	$nodes = explode('-', $edge);
//	echo "</br>" . "mn = " . $edge . ";". "</br>"; 
	$delNode = $array[$nodes[1]];
	//delete 2nd node
	unset($array[$nodes[1]]);
	//add nodes from 2nd to 1st
	foreach ($delNode as $key => $value) {
		if ($value != $nodes[0]){
				$array[$nodes[0]][] = $value;
			}
	}
	//delete 1st->2nd
	foreach ($array[$nodes[0]] as $key => $value) {
		if ($value == $nodes[1]) {
			unset($array[$nodes[0]][$key]);
		}
	}
	//replace x->2nd with x->1st
	foreach ($delNode as $key => $value) {
		foreach ($array[$value] as $k => $v) {
			if ($v == $nodes[1] + 0) {
				$array[$value][$k] = $nodes[0];
			}
		} 
	}

	foreach ($array as $key => $value) {
		$i = 0;
		$new_arr = array();
		foreach ($value as $k => $v) {
			$new_arr[$i] = $value[$k];
			$i++;
		}
		$array[$key] = $new_arr;
	}
}

function minCut(&$array) {
//	printGraph($array);
	if (count($array) < 3) return;
	deleteEdge($array);
	minCut($array);
}

//printGraph($babah);
echo "</br>" . countEdges($babah);

function w40k($array, $iter = 1) {
	$min = 99999;
	$numberOfIt = 0;

	for ($i=0; $i < $iter; $i++) {
		$reserve = $array;
		minCut($reserve);
		$new = countEdges($reserve);
		if ( $new < $min) {
			$min = $new;
			$numberOfIt = $i;
		}
	}
	echo "</br>numberOfIt = " . $numberOfIt . "; min = " . $min;
}
print 30*5 . 7;



?>