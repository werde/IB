<html>
<head>
	<title>dota</title>
	
</head>
<body>
<?php
	header('Content-type: text/html');
	header('Access-Control-Allow-Origin: *');
	$babah ='';
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_URL, 'http://api.steampowered.com/IEconItems_<ID>/GetPlayerItems/v0001/ &key=6FA7597D757C426E318CC7B4D482482A');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$babah = curl_exec($ch);
	echo curl_error($ch);
	curl_close($ch);
	echo 'n' . $babah;


?>
<script type="text/javascript" src="dota.js"></script>
</body>
</html>