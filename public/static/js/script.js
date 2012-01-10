/* Author: David */

function setPins() {
    // 圖片寬度包含額外的 14px margin
    var margin = 14;
    var pinWidth = $('.pin-box').width() + margin;
    var cols = parseInt($('#container').width() / pinWidth, 10);
    var pinHeights = [cols];
    var pinWidths = [cols];

    for (var i = 0; i < cols; i++) {
        pinWidths[i] = i * pinWidth;
        pinHeights[i] = 15;
    }

    $.each($('.pin-box'), function(index, value) {
        var i = index % cols;
        $(this).css('left', pinWidths[i]);
        $(this).css('top', pinHeights[i])
        pinHeights[i] += $(this).height() + margin;
    });

    var max = 0;
    $.each(pinHeights, function(index, value) {
        if (value > max) {
            max = value;
        }
    });

    $('#container').css('min-height', max);
}
