<?php
function makeBoardIndex($acronim, $desc) {
	$result = "";
	$path = "http://" . $_SERVER["HTTP_HOST"] . "/scripts/post.php"; 
	$host = $_SERVER['HTTP_HOST'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	
	// form for making board	
	$result .= <<<EOF
	<!DOCTYPE html>
	<html>
	<title>/$acronim/ &nbsp;-&nbsp; $desc</title>
	<link href="http://$host/css/css.css" rel="stylesheet">
	<body>
	<div><h1> /$acronim/ &nbsp;-&nbsp; $desc </h1></div>
	<hr>
	<form action="$path" method="post">
		<input type="hidden" name="mode" value="thread">
		<input type="hidden" name="acronim" value="$acronim">
		<input type="hidden" name="parent_id" value="0">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<button type="submit">make Thread</button>
	</form>
EOF;
	$result .= '<?php include_once ("' . $root . '\scripts\globals.php");';
	$result .= 'include_once ("' . $root . '.\scripts\class.php");';
	$result .= '$posts = getPosts( $db, "' . $acronim . '");';
	$result .= 'foreach ($posts as $post) { echo $post->formHTML();}?>';
	$result .= '</body></html>';

	return $result;
}

function buildThreadHTML($posts, $acronim, $desc) {
	$result = "";
	$path = "http://" . $_SERVER["HTTP_HOST"] . "/scripts/post.php";
	$host = $_SERVER['HTTP_HOST'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	$fpost = $posts[0]->id;

	// form for making post
	$result .= <<<EOF
	<!DOCTYPE html>
	<html>
	<title>/$acronim/ &nbsp;-&nbsp; $desc</title>
	<link href="http://$host/css/css.css" rel="stylesheet">
	<body>
	<div><h1> /$acronim/ &nbsp;-&nbsp; $desc </h1></div>
	<hr>
	<form action="$path" method="post">
		<input type="hidden" name="mode" value="post">
		<input type="hidden" name="acronim" value="$acronim">
		<input type="hidden" name="parent_id" value="$fpost">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<button type="submit">make Post</button>
	</form>
EOF;
	$result .= '<div class="post_over">';
	foreach ($posts as $post) {
		$result .= $post->formHTML();
	}
	$result .= '</div>';
return $result;
}

function rebuildThread($db, $id, $acronim) {

	$path = $_SERVER['DOCUMENT_ROOT'] . "/boards/" . $acronim . "/res/" . $id . ".html";

	$fhandle = fopen($path, 'wb');
	$posts = array();
	$posts[] = getPostById($db, $id, $acronim);
	$posts = getThreadPosts($db, $id, $acronim);
	$desc = $db->query("SELECT `desc` FROM `boards` WHERE `acronim`=" . $acronim);

	fwrite($fhandle, buildThreadHTML($posts, $acronim, $desc['desc']));
	fclose($fhandle);
}

?>