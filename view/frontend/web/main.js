// 2018-03-13
define(['df', 'df-lodash', 'jquery'], function(df, _, $) {return (
	/**
	 * @param {Object} config
	 * @param {HTMLAnchorElement} element
	 * @returns void
	 */
	function(config, element) {
		/** @type {jQuery} HTMLDivElement */ var $c = $(element);
		/** @type {jQuery} HTMLDivElement */ var $main = $('.product.media');
		var $logo = $('<img>').attr('class', 'dfe-logo-applied').hide().prependTo($main);
		$('img', $c).click(function() {
			$logo.attr('src', this.src).show();
		});
	});
});