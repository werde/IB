<?php
include_once('globals.php');

$last_post = $_GET['lastPost'];
$id = $_GET['thread'];
$acronim = $_GET['board'];

$res  = '';

$posts = getThreadPosts($db, $id, $acronim) ;

foreach ($posts as $key => $value) {
 	if (($value->id + 0) > 60) {
 		$res .= $value->formHTML();
 	}
 } 

echo $res;

?>