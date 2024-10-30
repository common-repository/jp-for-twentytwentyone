(function($) {
	wp.customize.bind('ready', function() {
		$('.customize-control-multiple-checkbox input[type="checkbox"]').on('change', function() {
			checkbox_values = $(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
				function() {
					return this.value;
				}
			).get().join(',');
			$(this).parents('.customize-control').find('input[type="hidden"]').val(checkbox_values).trigger('change');
		});
	});
})(jQuery);
