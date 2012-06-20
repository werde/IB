	<!DOCTYPE html>
	<html>
	<title>/a/ &nbsp;-&nbsp; kokoko</title>
	<link href="http://localhost/css/css.css" rel="stylesheet">
	<body>
	<div><h1> /a/ &nbsp;-&nbsp; kokoko </h1></div>
	<hr>
	<form action="http://localhost/scripts/post.php" method="post">
		<input type="hidden" name="mode" value="thread">
		<input type="hidden" name="acronim" value="a">
		<input type="hidden" name="parent_id" value="0">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<button type="submit">make Thread</button>
	</form>
	<?php include_once ("D:/Dropbox/Public/board\scripts\globals.php");include_once ("D:/Dropbox/Public/board.\scripts\class.php");$posts = getPosts( $db, "a");foreach ($posts as $post) { echo $post->formHTML();}?></body></html>