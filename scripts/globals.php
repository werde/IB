<?php
include_once ('helpers.php');
include_once ('class.php');
// INIT
define('DBHOST', "localhost");
define('DBNAME', "board");
define('DBUSER', "root");
define('DBPASS', "");
define('PAGES', "5");
define('THREADS_ON_PAGE', "10");

$options = array(PDO::ATTR_AUTOCOMMIT=>FALSE);
$db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS, $options);
	
$db->exec("CREATE TABLE `boards` (
	`id` mediumint(7) NOT NULL  auto_increment,
	`acronim` varchar(5),
	`desc` varchar(100) NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE = MyISAM");

function makeBoard($db, $acronim, $desc, $opts = 0) {

	if ( $db->query("SELECT count(*) FROM `boards` WHERE `acronim`='" . $acronim . "'")->fetchColumn() != 0 ) {
		return rError("board " . $acronim . " already exist");
	} else {
		$db->exec("INSERT INTO `boards` (`acronim`, `desc`) VALUES ( '" . $acronim . "', '" .  $desc . "' )");
	}
	//creating $acronim_posts table
	$db->exec("CREATE TABLE `" . $acronim . "_posts` (
		`id` mediumint(7) NOT NULL  auto_increment,
		`parent_id` mediumint(7),
		`title` varchar(100) NOT NULL,
		`text` text NOT NULL,
		`date` timestamp NOT NULL,
		`bumped` timestamp NOT NULL,
		`url` varchar(200),
		PRIMARY KEY (`id`)
		) ENGINE = MyISAM");

	//creating /boards/$acronim/ and /boards/$acronim/src folders

	$path = dirname( __FILE__ );
	$path = substr($path, 0 , strrpos($path, 'scripts'));

	mkdir($path . "/boards/" . $acronim , 0644);
	mkdir($path . "/boards/" . $acronim . "/src/", 0644);
	mkdir($path . "/boards/" . $acronim . "/res/", 0644);

	$fHandle = fopen($path . "/boards/" . $acronim . "/index.html", 'wb' ) or die("can't open file");
	fwrite($fHandle, makeBoardIndex($acronim, $desc));
	fclose($fHandle);
};

//Post
function bump($acronim, $parent_id) {
	global $db;
	$db->query("UPDATE `" . $acronim . "_posts` SET `bumped`='" . date('c') . "' WHERE `id`='" . $parent_id . "'");
}

function insertPost($db, $acronim, $title, $text, $parent_id, $url = '') {
	if ($parent_id != 0 ) {
		bump($acronim, $parent_id);
	}

	$db->query("INSERT INTO `" . $acronim . "_posts` (`title`, `text`, `parent_id`, `date`, `url`, `bumped`) VALUES ('" . $title . "', '" . $text . "', '" . $parent_id . "', '" . date('c') . "', '" . $url . "', '" . date('c') . "' )");
	return $db->lastInsertId();
}

function makePost($db, $acronim, $title, $text, $parent_id, $img = '') {
	$url = saveImage($img, $acronim);
	$post = insertPost($db, $acronim, $title . $url, $text, $parent_id, $url);
	rebuildThread($db, $parent_id, $acronim);
	rebuildAll($db, $acronim);
}

function makeThread($db, $acronim, $title, $text, $parent_id, $img = '') {
	$url = saveImage($img, $acronim);
	$threadId = insertPost($db, $acronim, $title, $text, $parent_id, $url);
	rebuildThread($db, $threadId, $acronim);
	rebuildAll($db, $acronim);
	return $threadId;
}

// GET
function getPosts($db, $acronim) {
	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `parent_id`=0 ORDER BY `bumped` DESC LIMIT 20");
	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute();

	$return = array();
	if ($query)
	{	
		while ($post = $query->fetch()) {
			$return[] = clone $post;
			$latest_posts = getLatestPosts($db, $acronim, $post->id);
			foreach ($latest_posts as $latest_post) {
				$return[] = $latest_post;
			}
		}
	}
	return $return;	
}

function getThreads($db, $acronim, $limit) {
	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `parent_id`=0 ORDER BY `bumped` DESC LIMIT " . $limit);
	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute();

	$return = array();
	if ($query) {
		while ($post = $query->fetch()) {
			$return[] = clone $post;
		}
	}
	return $return;
}

function getPostById($db, $id, $acronim) {
	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `id`= " . $id . " ORDER BY id ASC LIMIT 1");
	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute(); 

	$post = $query->fetch();
	return $post;
}

function getThreadPosts($db, $id, $acronim) {
	$f_post = getPostById($db, $id, $acronim);

	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `parent_id`= " . $id . " ORDER BY id ASC LIMIT 500");
	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute();

	$return = array();
	$return[] = clone $f_post;
	if ($query)
	{	
		while ($post = $query->fetch()) {
			$return[] = clone $post;
		}
	}
	return $return;	 	
}

function getLatestPosts($db, $acronim, $threadId) {
	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `parent_id`= " . $threadId . " ORDER BY id DESC LIMIT 5");

	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute();

	$return = array();
	if ($query)
	{	
		while ($post = $query->fetch()) {
			$return[] = clone $post;
		}
	}
	return array_reverse($return, false);
}	 
?>