<?php
include_once ('helpers.php');
include_once ('class.php');

define('DBHOST', "localhost");
define('DBNAME', "board");
define('DBUSER', "root");
define('DBPASS', "");

$db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
	
$db->exec("CREATE TABLE `boards` (
	`id` mediumint(7) NOT NULL  auto_increment,
	`acronim` varchar(5),
	`desc` varchar(100) NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE = MyISAM");

function makeBoard($db, $acronim, $desc, $opts = 0) {

	//search boards-table 
/*	if (findBoard($db, $acronim)) {
		return 'board already exists';
	}*/
	//adding board to boards table
	$db->exec("INSERT INTO `boards` (`acronim`, `desc`) VALUES ( '" . $acronim . "', '" .  $desc . "' )");

	//creating $acronim_posts table
	$db->exec("CREATE TABLE `" . $acronim . "_posts` (
		`id` mediumint(7) NOT NULL  auto_increment,
		`parent_id` mediumint(7),
		`title` varchar(100) NOT NULL,
		`text` text NOT NULL,
		`date` varchar(100) NOT NULL,
		PRIMARY KEY (`id`)
		) ENGINE = MyISAM");

	//creating /boards/$acronim/ and /boards/$acronim/src folders

	$path = dirname( __FILE__ );
	$path = substr($path, 0 , strrpos($path, 'scripts'));

	mkdir($path . "/boards/" . $acronim , 0644);
	mkdir($path . "/boards/" . $acronim . "/src/", 0644);
	mkdir($path . "/boards/" . $acronim . "/res/", 0644);

	$fHandle = fopen($path . "/boards/" . $acronim . "/index.php", 'wb' ) or die("can't open file");
	fwrite($fHandle, makeBoardIndex($acronim, $desc));
	fclose($fHandle);
};

//

function insertPost($db, $acronim, $title, $text, $parent_id) {
	$db->query("INSERT INTO `" . $acronim . "_posts` (`title`, `text`, `parent_id`, `date`) VALUES ('" . $title . "', '" . $text . "', '" . $parent_id . "', '" . date('r') . "' )");
	return $db->lastInsertId();
}

function makePost($db, $acronim, $title, $text, $parent_id) {
	$post = insertPost($db, $acronim, $title, $text, $parent_id);
	rebuildThread($db, $parent_id, $acronim);
}

function makeThread($db, $acronim, $title, $text, $parent_id) {
	$threadId = insertPost($db, $acronim, $title, $text, $parent_id);
	rebuildThread($db, $threadId, $acronim);
	return $threadId;
}

function getPosts($db, $acronim) {
	$query = $db->prepare("SELECT * FROM `" . $acronim . "_posts` WHERE `parent_id`=0 ORDER BY id DESC LIMIT 20");
	$query->setFetchMode(PDO::FETCH_CLASS, 'Post');
	$query->execute();

	$return = array();
	if ($query)
	{	
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

function getLatestPosts() {

}



?>