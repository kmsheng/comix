$(function() {

	$.ajax({
		url: url,
        success: function(data) {

            generatePins(data);
            setPins();
        }
	});

	setInterval('setPins();', 1000);

	$(window).resize(function() {
		setPins();
	});

});
