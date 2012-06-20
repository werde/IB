<?php
include_once ('globals.php');

if (!isset($_POST['mode'])) {
	echo $_SERVER['DOCUMENT_ROOT'];
	//header('Location: ' . $_SERVER['HTTP_HOST'] ); 	
	//exit;
}
	
if ($_POST['mode'] == 'thread') {	
	$lastInsertId = makeThread($db, $_POST['acronim'], $_POST['title'], $_POST['text'], 0);
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/boards/' . $_POST['acronim'] . '/res/' . $lastInsertId . '.html'); 
}

if ($_POST['mode'] == 'board') {	
	makeBoard($db, $_POST['acronim'], $_POST['desc']);
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/boards/' . $_POST['acronim']); 	
	exit;
}

if ($_POST['mode'] == 'post') {	
	makePost($db, $_POST['acronim'], $_POST['title'], $_POST['text'], $_POST['parent_id']);
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/boards/' . $_POST['acronim'] . '/res/' . $_POST['parent_id'] . '.html');
	exit;
}

?>