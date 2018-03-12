// 2018-03-13
define(['df', 'df-lodash', 'jquery'], function(df, _, $) {return (
	/**
	 * @param {Object} config
	 * @param {Number} config.left
	 * @param {Number} config.optionId
	 * @param {Number} config.top
	 * @param {HTMLAnchorElement} element
	 * @returns void
	 */
	function(config, element) {
		/** @type {jQuery} HTMLDivElement */ var $c = $(element);
		/** @type {jQuery} HTMLDivElement */ var $main = $('.product.media');
		var left = config.left;
		var top = config.top;
		var scale = 0.4;
		var $logo = $('<img>').attr('class', 'dfe-logo-applied').hide().prependTo($main);
		var $select = $('#select_' + config.optionId + '.product-custom-option');
		//$select.closest('.field').hide();
		$('img', $c).click(function() {
			var $this = $(this);
			var h = $this.height() * scale;
			var w = $this.width() * scale;
			$select.val($this.data('id'));
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