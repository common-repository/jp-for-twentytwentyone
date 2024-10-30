(function($) {
	wp.customize('jp_for_twentytwentyone_customize[powered_by]', function(value) {
		value.bind( function(newval) {
			$('#colophon .powered-by').html(newval);
		});
	});
})(jQuery);
