(function($) {
	$.entwine(function($) {
		/**
		 * Class: .cms-edit-form .field.switchable
		 *
		 * Say hello
		 */
		var button = $('#Form_EditForm_action_updateMailing');
		button.entwine({
			onclick: function() {
				button.addClass('loading');
				$.get('./admin/settings/updateMailing').done(function () {
					button.removeClass('loading');
				}).fail(function () {
					button.removeClass('loading');
				});
			}
		});
	});
})(jQuery);
