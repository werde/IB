<!DOCTYPE html>
<html>
<head>
	<title>аиб</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	
	<?php
	echo '<link href="http://' . $_SERVER['HTTP_HOST'] . '/css/css.css" rel="stylesheet">';
	?>
</head>
<body>

<?php

$path = $_SERVER['HTTP_HOST'];
echo $path;
echo strtotime(date('r'));
?>

<form action="./scripts/post.php" method="post" >
	<input type="hidden" name="mode" value="board">
	<input name="acronim" value="b">
	<input name="desc" value="random">	
	<button type="submit">make Board</button>
</form>
</ br>

</body>
</html>