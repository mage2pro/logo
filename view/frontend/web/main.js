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
		console.log('main.js started');
		var $main = $('.product.media');
		console.log('.gallery-placeholder length: ' + $('.gallery-placeholder', $main).length);
		var fotoramaInitialized = false;
		var onFotoramaInit = function() {
			if (!fotoramaInitialized) {
				var $f = $('.fotorama', $main);
				if (!$f.length) {
					console.log('Fotorama is not yet exist');
				}
				else {
					console.log('Fotorama exists');
					fotoramaInitialized = true;
					var $stage = $('.fotorama__stage', $main);
					var onStageInit = function() {
						var scale = config.scale / 100;
						var $logo = $('<img>').attr('class', 'dfe-logo-applied').hide().insertBefore($stage);
						var logoEnabled = false;
						var $select = $('#select_' + config.optionId + '.product-custom-option');
						var normal = null;
						$('img', element).click(function() {
							// 2018-03-18
							// I intentionally do not cache these values outside of the handler
							// because they will be changed on the browser's window resize.
							var $mi = $('div.fotorama__active', $stage).children('img.fotorama__img');
							var mh = $mi[0].height;
							var mw = $mi[0].width;
							var left = mw * config.left / 100;
							var top = mh * config.top / 100;
							var $this = $(this);
							logoEnabled = true;
							normal = {h: $this.height() * scale, w: $this.width() * scale, mw: mw};
							$select.val($this.data('id'));
							var p = $mi.position();
							$logo
								.attr('src', this.src)
								.css({
									'margin-left': (left + p.left - normal.w / 2) + 'px'
									,'margin-top' : (top + p.top - normal.h / 2) + 'px'
									,height: normal.h + 'px'
								})
								.show()
							;
						});
						var $logoZ = null;
						var inZ = false;
						var inF = false;
						var $window = $(window);
						$window.on('dfe.zoom.start', function() {
							inZ = !inF;
						});
						$window.on('dfe.zoom.move', function(e, i, l, t) {
							if (!inF && logoEnabled) {
								$logo.hide();
								console.log('dfe.zoom.move');
								if (!$logoZ) {
									$logoZ = $('<img>').attr({class: 'dfe-logo-applied', src: $logo[0].src});
								}
								var scaleZ = i.width / normal.mw;
								var hZ = normal.h * scaleZ;
								var wZ = normal.w * scaleZ;
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
						var deleteZ = function() {
							if ($logoZ) {
								$logoZ.remove();
								$logoZ = null;
							}
						};
						$window.on('dfe.zoom.end', function() {
							if (!inF && inZ) {
								inZ = false;
								if (logoEnabled) {
									console.log('dfe.zoom.end');
									deleteZ();
									$logo.show();
								}
							}
						});
						var $logoF = null;
						var deleteF = function() {
							if ($logoF) {
								$logoF.remove();
								$logoF = null;
							}
						};
						/**
						 * 2018-03-18 Dmitry Fedyuk https://mage2.pro/u/dmitry_fedyuk
						 * The Porto design theme removes all the `fotorama:fullscreenenter` handlers
						 * by the code above, so we are unable to listen to this event,
						 * and trigger the `dfe.fotorama.fullscreenenter` event instead.
						 * http://api.jquery.com/off/#off-event
						 */
						$window.on('dfe.fotorama.fullscreenenter', function() {
							inF = true;
							deleteZ();
							$logo.hide();
							if (logoEnabled) {
								if (!$logoF) {
									$logoF = $('<img>').attr({class: 'dfe-logo-applied', src: $logo[0].src});
								}
								var init = function() {
									var $i = $('div.fotorama__active', $stage).children('img.fotorama__img--full');
									var i = $i[0];
									if (i) {
										var scaleF = i.width / normal.mw;
										var hF = normal.h * scaleF;
										var wF = normal.w * scaleF;
										var lF = i.width * config.left / 100;
										var tF = i.height * config.top / 100;
										/**
										 * 2018-03-18
										 * $i.position() does not work here: it returns [0, 0].
										 * http://api.jquery.com/offset
										 * https://api.jquery.com/position
										 */
										var p = $i.offset();
										$logoF.css({
											left: (lF + p.left - wF / 2) + 'px'
											,top : (tF + p.top - hF / 2) + 'px'
											,height: hF + 'px'
										});
										if (!$logoF.parent().length) {
											$logoF.insertBefore($stage);
										}
									}
									return !!i;
								};
								if (!init()) {
									var onLoad = function(e, f, d) {
										console.log(d.frame);
										if (init()) {
											$f.off('fotorama:load', onLoad);
										}
									};
									$f.on('fotorama:load', onLoad);
								}
							}
						});
						/**
						 * 2018-03-18 Dmitry Fedyuk https://mage2.pro/u/dmitry_fedyuk
						 * The Porto design theme removes all the `fotorama:fullscreenexit` handlers
						 * by the code above, so we are unable to listen to this event,
						 * and trigger the `dfe.fotorama.fullscreenexit` event instead.
						 * http://api.jquery.com/off/#off-event
						 */
						$window.on('dfe.fotorama.fullscreenexit', function() {
							inF = false;
							deleteF();
							$logo.toggle(logoEnabled);
						});
						/**
						 * 2018-03-16
						 * «When we scroll to next product image remove the logo».
						 * https://www.upwork.com/d/contracts/19713405
						 * http://fotorama.io/customize/api#events
						 */
						$f.on('fotorama:show', function() {
							$logo.hide();
							logoEnabled = false;
							inF = false;
							deleteF();	
							inZ = false;
							deleteZ();
						});
					};
					var logoInitialized = false;
					var check = function() {
						if (!logoInitialized) {
							if (!$stage.width() || !$stage.height()) {
								console.log('stage is not yet exist');
							}
							else {
								console.log('stage exists');
								onStageInit();
								logoInitialized = true;
							}
						}
					};
					check();
					if (!logoInitialized) {
						console.log('stage is not yet exist');
						$stage.bind('DOMSubtreeModified', check);
					}
				}
			}
		};
		$('.gallery-placeholder', $main).bind('DOMSubtreeModified', onFotoramaInit);
		onFotoramaInit();
	});
});