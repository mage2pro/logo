// 2018-03-13
define(['df', 'df-lodash', 'jquery'], function(df, _, $) {return (
	/**
	 * @param {Object} config
	 * @param {Number} config.left
	 * @param {Number} config.optionId
	 * @param {Number} config.scale
	 * @param {Number} config.top
	 * @param {HTMLAnchorElement} element
	 * @returns void
	 */
	function(config, element) {
		var fotoramaInitialized = false;
		/** @type {jQuery} HTMLDivElement */ var $main = $('.product.media');
		$('.gallery-placeholder', $main).bind('DOMSubtreeModified', function() {
			if (!fotoramaInitialized) {
				var $f = $('.fotorama', $main);
				if ($f.length) {
					fotoramaInitialized = true;
					/** @type {jQuery} HTMLDivElement */ var $c = $(element);
					var $stage = $('.fotorama__stage', $main);
					var init = function() {
						var mh = $stage.height();
						var mw = $stage.width();
						var left = mw * config.left / 100;
						var top = mh * config.top / 100;
						var scale = config.scale / 100;
						var $logo = $('<img>').attr('class', 'dfe-logo-applied').hide().insertBefore($stage);
						/**
						 * 2018-03-16
						 * «When we scroll to next product image remove the logo».
						 * https://www.upwork.com/d/contracts/19713405
						 * http://fotorama.io/customize/api#events
						 */
						$f.on('fotorama:show', function() {$logo.hide();});
						var $select = $('#select_' + config.optionId + '.product-custom-option');
						var lsbz = {w: null, h: null};
						$('img', $c).click(function() {
							var $this = $(this);
							lsbz = {h: $this.height() * scale, w: $this.width() * scale};
							$select.val($this.data('id'));
							$logo
								.attr('src', this.src)
								.css({
									'margin-left': (left - lsbz.w / 2) + 'px'
									,'margin-top' : (top - lsbz.h / 2) + 'px'
									,height: lsbz.h + 'px'
								})
								.show()
							;
						});
						var $logoZ = null;
						var logoWasHidden = false;
						$(window).bind('dfe.zoom.move', function(e, i, l, t) {
							if ($logo.is(':visible')) {
								$logo.hide();
								logoWasHidden = true;
							}
							if (logoWasHidden) {
								if (!$logoZ) {
									$logoZ = $('<img>').attr({class: 'dfe-logo-applied-zoom', src: $logo[0].src});
								}
								var scaleZ = i.width / mh;
								var hZ = lsbz.h * scaleZ;
								var wZ = lsbz.w * scaleZ;
								var lZ = i.width * config.left / 100;
								var tZ = i.height * config.top / 100;
								$logoZ.css({
									left: (lZ + l - wZ / 2) + 'px'
									,top : (tZ + t - hZ / 2) + 'px'
									,height: hZ + 'px'
								});
								if (!$logoZ.parent().length) {
									$logoZ.insertBefore($stage);
								}
							}
						});
						$(window).bind('dfe.zoom.end', function(e, i) {
							if ($logoZ) {
								$logoZ.remove();
								$logoZ = null;
							}
							if (logoWasHidden) {
								$logo.show();
								logoWasHidden = false;
							}
						});
					};
					var logoInitialized = false;
					var check = function() {
						if (!logoInitialized && $stage.width() && $stage.height()) {
							console.log('stage exists');
							init();
							logoInitialized = true;
						}
						else {
							console.log('stage is not exist');
						}
					};
					check();
					if (!logoInitialized) {
						console.log('stage is not exist');
						$stage.bind('DOMSubtreeModified', check);
					}
				}
			}
		});
	});
});