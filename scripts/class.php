<?php
class Post {
	public $id;
	public $title;
	public $text;
	public $parent_id;
	public $date;

	function __construct($id=0, $title='', $text='', $parent_id = 0, $date='') {
		if (!empty($id))
		{
			$this->id = $id;
		}

		if (!empty($title))
		{
			$this->title = $title;
		}

		if (!empty($text))
		{
			$this->text = $text;		
		}
		if (!empty($parent_id))
		{
			$this->parent_id = $parent_id;
		}	
		if (!empty($date))
		{
			$this->date = $date;
		}			
	}

	function formHTML() {
		$return = "";
		$text = stripcslashes(preg_replace("(\r\n|\n|\r)", "<br />", $this->text));
		if ($this->parent_id != 0) {
			$return .= <<<EOF
			<div class="post" id="$this->id">
			<span>$this->id &nbsp;&nbsp; $this->date &nbsp;&nbsp; $this->parent_id</span><br>
			<span><a href='res/$this->id.html'>Thread</a></span>
			<span>$this->title </span><br>
			<div>
				$text
			</div>
		</div>
EOF;
			} else {
			$return .= <<<EOF
			<hr>
			<div class="post" id="$this->id">
			<span>$this->id &nbsp;&nbsp; $this->date &nbsp;&nbsp; $this->parent_id</span><br>
			<span><a href='res/$this->id.html'>Thread</a></span>
			<span>$this->title </span><br>
			<div>
				$text
			</div>
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