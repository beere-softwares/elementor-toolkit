(function($) {
    'use strict';

    var ElementorTypingAnimation = function($scope, $) {
        var $widget = $scope.find('.typing-animation-widget');
        var text = $widget.data('text');
        var speed = $widget.data('speed');
        var i = 0;

        function typeWriter() {
            if (i < text.length) {
                $widget.html($widget.html() + text.charAt(i));
                i++;
                setTimeout(typeWriter, speed);
            }
        }

        typeWriter();
    };

    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/typing-animation.default', ElementorTypingAnimation);
    });

})(jQuery);