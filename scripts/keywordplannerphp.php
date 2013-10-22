<?php
class Post {
	public $keywords;
	public $path;
	public $areas ;
	public $mode;

	function run() {
		for ($i = 0; $i < count($this->keywords); $i++) {
			$keyword = trim($this->keywords[$i]);
			
			if (!$keyword) {
				continue;
			}

			foreach ($this->keywords as $value) {
				if ($this->areas === NULL) {
					continue;
				}
			}

			$section = "[data-section='"+j+"']";
			$loader = "[data-loader='"+j+"'] i";


		}
	}

	function buildAreas($area){
		if(!$area) {
			return NULL;
		}
		if(strpos($area, ",") !== false) {
           		$area = split(",", $area);
                       
			$this->areas["state"] = trim($area[0]);
			$this->areas["city"] = trim($area);
		} else { //state only
			$this->areas["state"] = trim($area);
		}
	}

	function __construct(){
		$this->areas = array("national"=>"", "state"=>NULL, "city"=>NULL);
	}

	function _load($keyword, $area, $callback) {
		$url = $this->path;
		$data = array("keyword" => $keyword, "area"=>$area, "mode"=>$this->mode);
		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);

		var_dump($result);
	}


}

var_dump(new Post());
$ll = new Post();
$ll->path = "http://google.com";

$ll->_load("google.com", "ru", "jk");
echo "".phpversion();
?>