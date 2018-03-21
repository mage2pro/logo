// 2018-03-21
// http://devdocs.magento.com/guides/v2.0/javascript-dev-guide/javascript/js_mixins.html
// https://magento.stackexchange.com/questions/142970
define(['jquery', 'df-lodash'], function($, _) {'use strict';
return function(widget) {$.widget('mage.productGallery', widget, {
	_addItem: function(event, imageData) {
		_.each(['logo_left', 'logo_top', 'logo_scale'], function(k) {
			var v = imageData[k];
			var vr = Math.round(v);	
			imageData[k] = v == vr ? vr : v;
		});
		this._super(event, imageData);
	}
	,_initDialog: function() {
		this._super(); // 2018-03-21 http://api.jqueryui.com/jQuery.widget/#method-_super
		this.$dialog.on('change', '#logo_left,#logo_top,#logo_scale', $.proxy(function(e) {
			this.$dialog.data('imageData')[e.currentTarget.id] = e.currentTarget.value;
			this._contentUpdated();
		}, this));
	}
});return $.mage.productGallery;};});