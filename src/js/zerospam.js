/**
 * WordPress Zero Spam JS
 *
 * Required JS for the WordPress Zero Spam plugin to work properly.
 *
 * @link http://www.benmarshall.me/wordpress-zero-spam-plugin
 * @since 1.0.0
 *
 * @package WordPress
 * @subpackage Zero Spam
 */

(function (window, $) {

	"use strict";

	$(function () {
		var zerospam = window.zerospam || {};
		if (typeof zerospam.key === 'undefined') {
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

		// Gravity Forms
		$(document)
			.on('gform_post_render.zerospam', $.proxy(appendInput, $('.gform_wrapper form')));

		// Contact Form 7
		$('.wpcf7-form [type="submit"]')
			.addClass('wpcf7-submit');
		$('.wpcf7-submit')
			.on('click.zerospam', $.proxy(appendInput, $('.wpcf7-form')));

	});

})(window, window.jQuery || {});

