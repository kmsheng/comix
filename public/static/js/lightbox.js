$(function() {
	var comicUrl = document.location.search;
	var url = 'http://comix.kmsheng.com/crawler/provide-comic' + comicUrl;

	$.ajax({
		url: url,
		success: function(data) {
			makePages(data);
			$('#gallery a').lightBox();
		}
	});
});

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

var addButtons = function(extra) {
    var chapter = $('#chapter');
    var home = $('<a>').addClass('button').attr('href', 'http://comix.kmsheng.com').text('回首頁');
    var matches = document.location.search.match(/(\?url=http:\/\/www\.8comic\.com\/love\/drawing-(\d+)\.html\?ch=)(\d+)/);
    var backUrl = '/index/chapter?url=http://www.8comic.com/html/' + matches[2] + '.html';
    var back = $('<a>').addClass('button').attr('href', backUrl).text('回上一頁');

    $('body').prepend($('<h2>').addClass('chapter-title').text('第 ' + matches[3] + ' 話'));
    chapter.append(home).append(back);

    if (undefined != extra.prev) {
        var url = '/index/browse' + matches[1] + extra.prev.num + '&text=' + extra.prev.num;
        var text = '上一話';
        var a = $('<a>').addClass('button').attr('href', url).text(text);

        chapter.append(a);
    }

    if (undefined != extra.next) {
        var url = '/index/browse' + matches[1] + extra.next.num + '&text=' + extra.next.num;
        var text = '下一話';
        var a = $('<a>').addClass('button').attr('href', url).text(text);

        chapter.append(a);
    }

}
