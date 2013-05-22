<?php
$m = 3;
$n = 3;

class Cell {
	public $x;
	public $y;
	public $isl; 

	function __construct ($x, $y, $isl) {
		$this->x = $x;
		$this->y = $y;
		$this->isl = $isl;	
	}
} 

$A = array();
$B = array();
$index = 0;

for ($i = 0; $i < $m; $i++) {
	$B[$i] = array() ;
	$A[$i] = array();
	for ($j = 0; $j < $n; $j++){
		$B[$i][$j] = 0;
		$A[$i][$j] = new Cell($i, $j, rand(0, 1));
	}
}

for ($i = 0; $i < $m; $i++) {
	echo "<br>";
	for ($j = 0; $j < $n; $j++) {
		if ($A[$i][$j]->isl == 1) {
			echo ' # ';
		} else {
			echo ' _ '; 
		}
	}
}

for ($i = 0; $i < $m; $i++) {
	for ($j = 0; $j < $n; $j++) {
		if (($A[$i][$j]->isl == 1) && ($B[$i][$j] == 0)) {
			$index++;
			$B[$i][$j] = $index;
			recursiveF($i, $j);
			echo "<br>" . $index;
		} 
	}
}

function recursiveF($i, $j) {
	global $m, $n, $A, $B, $index;
	if ( ($i + 1 < $m) && checkCell($A[$i + 1][$j])) {
		$B[$i + 1][$j] = $index;
		recursiveF($i + 1, $j, $m, $n, $A, $B);
	}
		if (($i >= 1) && checkCell($A[$i - 1][$j]) ) {
		$B[$i - 1][$j] = $index;
		recursiveF($i - 1, $j);
	}
		if (($j + 1 < $n) && checkCell($A[$i][$j + 1])) {
		$B[$i][$j + 1] = $index;
		recursiveF($i, $j + 1);
	}
		if (($j >= 1) && checkCell($A[$i][$j - 1])) {
		$B[$i][$j - 1] = $index;
		recursiveF($i, $j - 1);
	}
}

function checkCell( $cell ) {
	global $m, $n, $A, $B, $index;
	if (get_class($cell) != 'Cell' ) return false;
	if (($cell->isl == 1) && ($B[$cell->x][$cell->y] == 0)) {
		return true;
	} else {
		return false;
	}
}

for ($i = 0; $i < $m; $i++) {
	echo "<br>";
	for ($j = 0; $j < $n; $j++) {
		echo '_' . $B[$i][$j] . '_';
	}
}

?>
