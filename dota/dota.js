var key = '6FA7597D757C426E318CC7B4D482482A';
var link = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=27110133&key="

var request = new XMLHttpRequest();

request.onreadystatechange = function() {
	if (request.readyState == 4) {
		console.log(request.responseText);
//		console.log(JSON.parse(request.responseText));
	}

	console.log('r');
}
request.open('GET', "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=27110133&key=6FA7597D757C426E318CC7B4D482482A'&callback=?'", true);
//request.setRequestHeader('X-PINGOTHER', 'pingpong');
request.setRequestHeader('Origin', 'application/xml');
request.send();

console.log(link + key);