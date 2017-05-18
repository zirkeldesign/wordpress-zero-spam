(function ($) {
	"use strict";
	$(function () {
		var zerospam = window.zerospam || {};
		if (!zerospam.length) {
			return;
		}
		var forms = "#commentform";
		forms += ", #contactform";
		forms += ", #registerform";
		forms += ", #buddypress #signup_form";
		forms += ", .zerospam";
		forms += ", .ninja-forms-form";
		forms += ", .wpforms-form";
		forms += ", .gform_wrapper form";
		if (typeof zerospam.key !== 'undefined') {
			var appendInput = function () {
				$('<input>')
					.attr({
						type: 'hidden',
						name: 'zerospam_key',
						value: zerospam.key
					})
					.appendTo($(this));
				return true;
			};
			$(forms)
				.on('submit.zerospam', $.proxy(appendInput, forms));
			$(document)
				.on('gform_post_render.zerospam', $.proxy(appendInput, $('.gform_wrapper form')));
			$('.wpcf7-form [type="submit"]')
				.addClass('wpcf7-submit');
			$('.wpcf7-submit')
				.on('click.zerospam', $.proxy(appendInput, $('.wpcf7-form')));
		}
	});
})(jQuery);
