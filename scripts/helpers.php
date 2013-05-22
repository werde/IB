<?php

function rError($str) {
	$res = "";
	$host = $_SERVER['HTTP_HOST'];
	$res .= <<<EOF
<!DOCTYPE html>
	<html>
	<title>ib</title>
	<link href="http://$host/css/css.css" rel="stylesheet">
	<body>
	<h1>$str</h1>
	</body>
	</html>
EOF;
	return $res;
}

function makeBoardIndex($acronim, $desc) {
	$result = "";
	$path = "http://" . $_SERVER["HTTP_HOST"] . "/scripts/post.php"; 
	$host = $_SERVER['HTTP_HOST'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	
	// form for making board	
	$result .= head($acronim, $desc, $host);
	$result .= <<<EOF
	<body>
	<div><h1 id="$acronim"> /$acronim/ &nbsp;-&nbsp; $desc </h1></div>
	<hr>
	<form action="$path" method="post" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="thread">
		<input type="hidden" name="acronim" value="$acronim">
		<input type="hidden" name="parent_id" value="0">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<input size="25" name="file" value="" type="file">
		<button type="submit">make Thread</button>
	</form>
	</div>
	</body>
	</html>
EOF;
return $result;
}

function buildThreadHTML($posts, $acronim, $desc) {
	$result = "";
	$path = "http://" . $_SERVER["HTTP_HOST"] . "/scripts/post.php";
	$host = $_SERVER['HTTP_HOST'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	$fpost = $posts[0]->id;

	
	$result .= head($acronim, $desc, $host);
	$result .= <<<EOF
	<body>
	<div><h1 id="$acronim"> /$acronim/ &nbsp;-&nbsp; $desc </h1></div>
	<hr>
	<form action="$path" method="post" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="post">
		<input type="hidden" name="acronim" value="$acronim">
		<input type="hidden" name="parent_id" value="$fpost">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<input size="25" name="file" value="" type="file">
		<button type="submit">make Post</button>
	</form>
EOF;
	$result .= '<div class="post_over">';
	foreach ($posts as $post) {
		$result .= $post->formHTML();
	}
	$result .= '</div>';
	$result .= '</br ><div id="update">[Update]</div>';
	return $result;
}

function rebuildThread($db, $id, $acronim) {

	$path = $_SERVER['DOCUMENT_ROOT'] . "/boards/" . $acronim . "/res/" . $id . ".html";

	$fhandle = fopen($path, 'wb');
	$posts = array();
	$posts[] = getPostById($db, $id, $acronim);
	$posts = getThreadPosts($db, $id, $acronim);
	$desc = $db->query("SELECT `desc` FROM `boards` WHERE `acronim`='" . $acronim . "'");
	fwrite($fhandle, buildThreadHTML($posts, $acronim, $desc->fetchColumn()));
	fclose($fhandle);
}

function saveImage($img, $acronim) {
	$path = $_SERVER['DOCUMENT_ROOT'] . "/boards/" . $acronim . "/src/";
	if (($img["type"] == "image/gif") || ($img["type"] == "image/png") || ($img["type"] == "image/jpeg") && ($img["size"] < 2048000)) {
		if ($_FILES["file"]["error"] > 0) {
			return "Error: " . $_FILES["file"]["error"];
		} else {
			switch ($img["type"]) {
				case "image/png":
					$type = ".png";
					break;
				case "image/jpeg":
					$type = ".jpeg";
					break;
				case "image/gif":
					$type = ".gif";
					break;
			}
			$name = time() . $type;
			$res = move_uploaded_file($img["tmp_name"], $path . $name);
			if (thumbnailImage($path, $name)) {
				return $name;
			}
		}
	} else {
			return false;
	}
}
function thumbnailImage($path, $name){
	$type = explode(".", $name);
	$type = array_reverse($type);

	if (preg_match("/gif/", $type[0])) {
		$source_image = imagecreatefromgif($path . $name);
		$transparent_index = ImageColorTransparent($source_image);
        if ($transparent_index!=(-1)) $transparent_color = ImageColorsForIndex($source_image,$transparent_index); 
	} elseif (preg_match("/jpg|jpeg/", $type[0])) {
		$source_image = imagecreatefromjpeg($path . $name);
	} elseif (preg_match("/png/", $type[0])) {
		$source_image = imagecreatefrompng($path . $name);
		ImageAlphaBlending($source_image, true);
        ImageSaveAlpha($source_image, true); 
	} else {
		return false;
	}

	if (!$source_image) { 
		return false;
	}

	$src_w = imageSX($source_image);
	$src_h = imageSY($source_image);
	$t_height = 200;
	$t_width = 200;

	$thumbnail = imagecreatetruecolor($t_width, $t_height); //  Thumbnail width & height

	imagecopyresampled($thumbnail, $source_image, 0, 0, 0, 0, $t_width, $t_height, $src_w, $src_h);
	
	if (preg_match("/png/", $type[0])) {
		ImageAlphaBlending($thumbnail,false);
        ImageSaveAlpha($thumbnail,true); 
        if (!imagepng($thumbnail, $path . 't' . $name)) {
			return false;
		}
	} elseif (preg_match("/jpg|jpeg/", $type[0])) {
		if (!imagejpeg($thumbnail, $path . 't' . $name, 70)) {
			return false;
		}
	} else if (preg_match("/gif/", $type[0])) {
		if(!empty($transparent_color))
        {
            $transparent_new = ImageColorAllocate($thumbnail,$transparent_color['red'],$transparent_color['green'],$transparent_color['blue']);
            $transparent_new_index = ImageColorTransparent($thumbnail,$transparent_new);
            ImageFill($thumbnail, 0,0, $transparent_new_index);
        } 
		if (!imagegif($thumbnail, $path . 't' . $name)) {
			return false;
		}
	}
	imagedestroy($source_image);
	imagedestroy($thumbnail);
	return true;
}

function rebuildAll($db, $acronim) {
	$threads = getThreads($db, $acronim, PAGES * THREADS_ON_PAGE);
	$footer = footer(ceil(count($threads)/THREADS_ON_PAGE), $acronim);
	for ($i = 0 ; $i < PAGES ; $i++) {
		$threads_slice = array_slice($threads, $i * THREADS_ON_PAGE, THREADS_ON_PAGE);
		if (!$threads_slice) break;
		$something_awful = array();
		foreach ($threads_slice as $thread) {
			$posts = getLatestPosts($db, $acronim, $thread->id);
			$something_awful[] = $thread;
			foreach ($posts as $post) {
				$something_awful[] = $post;
			}
		}
		buildPage($db, $acronim, $i, $something_awful, $footer);
	}
}

function buildPage($db, $acronim, $page, $posts, $footer) {

	if ($page == 0) {
		$path = $_SERVER['DOCUMENT_ROOT'] . "/boards/" . $acronim . "/index.html";
	} else {
		$path = $_SERVER['DOCUMENT_ROOT'] . "/boards/" . $acronim . "/" . $page . ".html";
	}
	
	$fhandle = fopen($path, 'wb');

	$desc = $db->query("SELECT `desc` FROM `boards` WHERE `acronim`='" . $acronim . "'");

	fwrite($fhandle, buildPageHTML($posts, $acronim, $desc->fetchColumn(), $footer));
	fclose($fhandle);
}

function buildPageHTML($posts, $acronim, $desc, $footer) {
	$result = "";
	$path = "http://" . $_SERVER["HTTP_HOST"] . "/scripts/post.php"; 
	$host = $_SERVER['HTTP_HOST'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	
	$result .= head($acronim, $desc, $host);
	// form for making board	
	$result .= <<<EOF
	<body>
	<div><h1 id="$acronim"> /$acronim/ &nbsp;-&nbsp; $desc </h1></div>
	<hr>
	<form action="$path" method="post" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="thread">
		<input type="hidden" name="acronim" value="$acronim">
		<input type="hidden" name="parent_id" value="0">
		<input name="title" value="">
		<textarea type="textarea" name="text" value=""></textarea>
		<input size="25" name="file" value="" type="file">
		<button type="submit">make Thread</button>
	</form>
EOF;
	$result .= '<div class="post_over">';
	foreach ($posts as $post) {
		$result .= $post->formHTML();
	}
	$result .= '</div>';
	$result .= $footer;
	return $result;
}

//

function footer($pages, $acronim) {

	$path = "http://" . $_SERVER["HTTP_HOST"] . "/boards/" . $acronim . "/"; 
	$result = '<hr>';
	$result .= '<div><a href="' . $path . 'index.html">[0]</a>';

	for ($i = 1; $i < $pages; $i++) {
		$result .= '<a href="' . $path . $i . '.html">[' . $i . ']</a>';
	}
	$result .= '</div><hr>';
	return $result;
}

function head($acronim, $desc, $host) {
	$result = <<<EOF
	<!DOCTYPE html>
	<html>
	<title>/$acronim/ &nbsp;-&nbsp; $desc</title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<link href="http://$host/css/css.css" rel="stylesheet">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script src="http://$host/js/st.js"></script>
EOF;

	return $result;
}

?>
