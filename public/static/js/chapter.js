var chapter = document.location.search.match(/(\w+:\/\/www\.8comic\.com\/html\/\d+\.html)/g);
var url = 'http://comix.kmsheng.com/crawler/provide-chapter-data?url=' + chapter;

var generatePins = function(data) {
	var pinBox = $('<div>').addClass('pin-box');

	$.each(data, function(index, value) {
		var a = $('<a>').attr('href', value.href);
		var img = $('<img>').addClass('pin').attr('src', value.src).attr('alt', value.value);
		var name = $('<p>').addClass('name').append($('<a>').attr('href', value.href).text(value.value));
		var box = pinBox.clone();

		if ('true' === value.flag + "") {
			a.append(img);
		}

		box.append(a).append(name);

		$('#container').append(box);
	});
}
