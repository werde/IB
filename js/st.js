imgs = document.getElementsByTagName("img");

for (var i in imgs) {
	if (imgs[i].src) {
		if (imgs[i].src.match(/thumb/)) {
        hreff = imgs[i].parentNode.href;
        imgs[i].src = hreff;
        imgs[i].style.width = "";
		imgs[i].style.height = "";
		}
	}
}

function updateThread(lastPost, thread, board) {
	
	var xhr = new XMLHttpRequest();
	var params  = 'lastPost=' + encodeURIComponent(lastPost) + '&thread=' + encodeURIComponent(thread) + '&board=' + encodeURIComponent(board);

	xhr.open('GET', '/scripts/ajax.php?' + params, false); // need to be sync?

	xhr.onreadystatechanfe = function() {
		if (this.readyState != 4) return;

		//append posts
		posts = xhr.responseText.split('my split string'); // split  

		for (var post in posts) {
			document.getElementsById('posts').innerHTML += post;
		}
	}

	xhr.send(params);

}

$(document).ready(function() {

	var update = $('#update');
	update.show();
	update.click(function() {

		var lastPost = $('div .post:last').attr('id');
		var thread = $('div .first_post').attr('id');
		var board = $('h1:first').attr('id');

		updateThread(lastPost, thread, board);
	})

});

function parseText(event, text) {
	if ( typeof text !== "string" ) 
		return('u r fucked');

	var arr = text.split('\n');
	text = '';

	for (var k in arr) {
		text += ( /^>/ig.test(arr[k]) ) ? '<span>' + arr[k] + '</span> <br>' : arr[k] + ' <br>';
	}
	console.log(arr);
	return text;
}

(function() {
	$('button').on('click', function(e) {
		var input = $('#in');
		var out = $('#out');
		out.html(parseText(e, input.val()));
		e.preventDefault();
	});
})() 