var makePages = function(data) {
    var gallery = $('#gallery');
    var ul = $('<ul>');
	var li = '';

	$.each(data, function(index, value) {
         var text = '第' + index + '頁';
         li += '<li><a href="' + value + '" title="' + text + '">' + text + '</a></li>';

	});

	ul.append(li);
    gallery.append(ul);
}
