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
		var left = 248;
		var top = 140;
		var scale = 0.4;
		var $logo = $('<img>').attr('class', 'dfe-logo-applied').hide().prependTo($main);
		$('img', $c).click(function() {
			var $this = $(this);
			var h = $this.height() * scale;
			var w = $this.width() * scale;
			$logo
				.attr('src', this.src)
				.css({
					'margin-left': (left - w / 2) + 'px'
					,'margin-top' : (top - h / 2) + 'px'
					,height: h + 'px'
				})
				.show()
			;
		});
	});
});