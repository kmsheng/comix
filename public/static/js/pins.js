$(function() {

	$.ajax({
		url: url,
	success: function(data) {
		generatePins(data);
		setPins();
	}
	});

	setInterval('setPins();', 3000);

	$(window).resize(function() {
		setPins();
	});

});
