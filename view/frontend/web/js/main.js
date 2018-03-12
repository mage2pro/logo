
define([
	'jquery',
	'mage/template',
	'jquery/ui'
], function ($, mageTemplate) {  

  return {

	oldO   : [],
	preloaded : [],


	_create : function(){

	  $.extend(this, this.options);
	  $.extend(this, this.options.config);

	  this.imageTemplate = mageTemplate('[data-template=oi-thumbnail]');
	  this.loadImages();
	},

  
	loadImages : function(){
	  var e,optionId,dd,isNewOption,valueId,prevVId;
	  var elements = $('.product-custom-option');
	  for (var n = 0;n < elements.length; n++){
		e = $(elements[n]);

		var optionIdStartIndex, optionIdEndIndex;
		if (e.is(":file")) {
			optionIdStartIndex = e.attr('name').indexOf('_') + 1;
			optionIdEndIndex = e.attr('name').lastIndexOf('_');
		} else {
			optionIdStartIndex = e.attr('name').indexOf('[') + 1;
			optionIdEndIndex = e.attr('name').indexOf(']');
		}

		optionId = parseInt(e.attr('name').substring(optionIdStartIndex, optionIdEndIndex), 10);

		if (!this.oldO[optionId]){
		  this.oldO[optionId] = {};
		  dd = e[0].type == 'radio' || e[0].type == 'checkbox' ? e.closest('.options-list').closest('.field') : e.closest('.field');
		  isNewOption = true;
		}

		if (e[0].type == 'radio') {

			valueId = e.val();
			if (this.image[valueId]){
				this.preloaded[valueId] = new Image();
				this.preloaded[valueId].src = this.image[valueId];
				if (isNewOption){
				  dd.addClass('oi-above-radio');
				  dd.find('.control').before(this.imageTemplate({id : optionId, image : this.spacer}));
				  isNewOption = false;
				}
			}
			e.click($.proxy(this.observeRadio, this, optionId, valueId));

		} else if (e[0].type == 'checkbox') {

			valueId = e.val();
			if (this.image[valueId]){
			  var imageHtml = this.imageTemplate({id : 'value_'+valueId, image : this.image[valueId]});
			  if (isNewOption){
				dd.addClass('oi-above-checkbox');
				dd.find('.control').before(imageHtml);
				isNewOption = false;
			  } else {
				$('#option_image_value_' + prevVId).after(imageHtml);
			  }
			  prevVId = valueId;
			  e.click($.proxy(this.observeCheckbox, this, e, valueId));
			}

		} else if (e[0].type == 'select-one' && !e.hasClass('datetime-picker')) {

			var options = e[0].options;
			for (var i = 0, len = options.length; i < len; ++i){
			  if (options[i].value){
				valueId = options[i].value;
				if (this.image[valueId]){
				  this.preloaded[valueId] = new Image();
				  this.preloaded[valueId].src = this.image[valueId];
				}
			  }
			}
			dd.addClass('oi-above-select');
			dd.find('.control').before(this.imageTemplate({id : optionId, image : this.spacer}));
			e.change($.proxy(this.observeSelectOne, this, e, optionId));

		} else if (e[0].type == 'select-multiple') {

			var options = e[0].options;
			for (var i = 0, len = options.length; i < len; ++i){
			  if (options[i].value){
				valueId = options[i].value;
				if (this.image[valueId]){
				  var imageHtml = this.imageTemplate({id : 'value_'+valueId, image : this.image[valueId]});
				  if (isNewOption){
					dd.addClass('oi-above-select-multiple');
					dd.find('.control').before(imageHtml);
					isNewOption = false;
				  } else {
					$('#option_image_value_' + prevVId).after(imageHtml);
				  }
				  prevVId = valueId;
				}
			  }
			}
			e.change($.proxy(this.observeSelectMultiple, this, e));
		}

	  };
	},


	observeRadio : function(optionId, valueId){
	  this.updateImage(optionId, valueId);
	},


	observeCheckbox : function(element, valueId){
	  if (element[0].checked){
		$('#option_image_value_' + valueId).show();
	  } else {
		$('#option_image_value_' + valueId).hide();
	  }
	},
 
 
	observeSelectOne : function(element, optionId){
	  var valueId = element.val();
	  this.updateImage(optionId, valueId);
	},
 
 
	observeSelectMultiple : function(element){
	  var vId;
	  var options = element[0].options;
	  for (var i = 0; i < options.length; ++i){
		if (options[i].value){
		  vId = options[i].value;
		  if (options[i].selected){
			$('#option_image_value_' + vId).show();
		  } else {
			$('#option_image_value_' + vId).hide();
		  }
		}
	  }
	},
	updateImage : function(oid, vid) {
		var $image = $('#option_image_' + oid);
		if ('' != vid && this.image[vid]) {
			$image.attr('src', this.preloaded[vid].src);
			$image.show();
		}
		else {
			$image.hide();
		}
	}
  };
});