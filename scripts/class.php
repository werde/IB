<?php
class Post {
	public $id;
	public $title;
	public $text;
	public $parent_id;
	public $date;
	public $url;

	function formHTML() {
		$return = "";
		$img_thumb = "//" . $_SERVER['HTTP_HOST'] . "/boards/b/src/" . 't' . $this->url;
		$img_src = "//" . $_SERVER['HTTP_HOST'] . "/boards/b/src/" . $this->url;
		$text = stripcslashes(preg_replace("(\r\n|\n|\r)", "<br />", $this->text));
		if ($this->parent_id != 0) {
			$return .= <<<EOF
			<div class="post" id="$this->id">
			<span><a href='res/$this->parent_id.html#$this->id'>$this->id</a> &nbsp;&nbsp; $this->date &nbsp;&nbsp; $this->parent_id</span><br>

			<div class="file"> <a class="thumbFile" href="$img_src"> <img src="$img_thumb"> </a> </img></div>
			<div class="post_message"> $text </div>
		</div>
EOF;
			} else {
			$return .= <<<EOF
			<hr>
			<div class="first_post" id="$this->id">
			<span>$this->id &nbsp;&nbsp; $this->date &nbsp;&nbsp; $this->parent_id</span>
			<span class="open_thread"><a class="open_thread" href='res/$this->id.html'>Thread</a></span><br />

			<div class="file"> <a class="thumbFile" href="$img_src"> <img src="$img_thumb"> </a> </img></div>
			<span><div class="post_message"> $text </div></span>
		</div>
EOF;

			}

		return $return;
	} 


	function firtsPost() {
		return ($this->parent_id != 0);
	} 

	function deleteSelf() {
		$db->exec("DELETE * FROM `" . 'b' . "_posts` WHERE id=" . $this->id);
		buildThread($this->id);
	}

	function getThreadFull() {

	}

	function getThreadShort() {

	}
}

?>