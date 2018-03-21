// 2018-03-21
// http://devdocs.magento.com/guides/v2.0/javascript-dev-guide/javascript/js_mixins.html
// https://magento.stackexchange.com/questions/142970
define(['jquery'], function($) {'use strict';
return function(widget) {$.widget('mage.productGallery', widget, {
	_addItem: function(event, imageData) {
		// http://api.jqueryui.com/jQuery.widget/#method-_super
		this._super(event, $.extend(imageData, {
			logo_top: 50
		}));
	}
});return $.mage.productGallery;};});