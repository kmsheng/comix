var url = 'http://comix.kmsheng.com/crawler/provide-home-page-data';

var generatePins = function(data) {
	var pinBox = $('<div>').addClass('pin-box');

	$.each(data, function(index, value) {
		var a = $('<a>').attr('href', value.href);
		var img = $('<img>').addClass('pin').attr('src', value.src).attr('alt', value.name);

		a.append(img);
		var name = $('<p>').addClass('name').text(value.name);
		var description = $('<p>').addClass('description').text(value.description);
		var box = pinBox.clone();

		box.append(a).append(name).append(description);

		$('#container').append(box);
	});
}
