var optionImages;
  
require([
    "jquery",
    "mage/template",     
    "jquery/ui"
], function ($, mageTemplate) {  

  "use strict";
  $.widget("pektsekye.optionImages", {
                      
    loadedUploaders: {},
                      
    _create: function(){
      $.extend(this, this.options);    
      $.extend(this, this.options.config);

      this._on({     
          'click button.oi-delete-button': $.proxy(this.deleteImage, this)    
      });       
    
    },

  
    addUploader : function(uid){

      var maxFileSize = this.maxFileSize;
      var maxWidth = this.maxWidth;
      var maxHeight = this.maxHeight;
      
      var widget = this;
      
      $('#oi_image_file_'+uid).fileupload({
          dataType: 'json',
          dropZone: '[data-tab-panel=image-management]',
          sequentialUploads: true,
          acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
          maxFileSize: maxFileSize,
          add: function(e, data) {
              $.each(data.files, function (index, file) {
                  data.fileId = Math.random().toString(33).substr(2, 18);
                  var progressTmpl = $('#oi_image_uploader_'+uid+'-template').children(':first').clone();
                  progressTmpl.attr('id', data.fileId);
                  var fileInfoHtml = progressTmpl.html().replace('{{size}}', byteConvert(file.size))
                      .replace('{{name}}', file.name);
                  progressTmpl.html(fileInfoHtml) ;

                  progressTmpl.appendTo('#oi_image_uploader_'+uid);

              });
              $(this).fileupload('process', data).done(function () {
                  data.submit();
              });
              $('#oi_image_browse_button_'+uid).hide();              
          },
          done: function(e, data) {
              if (data.result && !data.result.error) {
                 $('#oi_image_uploader_'+uid).hide();                        
                 $('#oi_image_input_'+uid).val(data.result.file);
                 $('#oi_image_saved_as_'+uid).val('');                 
                 $('#oi_image_placeholder_'+uid).prop('src', data.result.url).show();
                 $('#oi_delete_image_button_'+uid).show();    
                 widget.loadedUploaders[uid] = {
                   image_saved_as : '', 
                   image_input : data.result.file,
                   image_src : data.result.url, 
                   state : 1                        
                 }                              
              } else {
                  $('#' + data.fileId)
                      .delay(2000)
                      .hide('highlight');
                  alert($.mage.__('File extension not known or unsupported type.'));
              }
              $('#' + data.fileId).remove();
              $('#oi_image_browse_button_'+uid).show();               
          },
          progress: function(e, data) {
              var progress = parseInt(data.loaded / data.total * 100, 10);
              var progressSelector = '#' + data.fileId + ' .progressbar-container .progressbar';
              $(progressSelector).css('width', progress + '%');
          },
          fail: function(e, data) {
              var progressSelector = '#' + data.fileId;
              $(progressSelector).removeClass('upload-progress').addClass('upload-failure')
                  .delay(2000)
                  .hide('highlight')
                  .remove();
              $('#oi_image_browse_button_'+uid).show();                  
          }
      });
        
      $('#oi_image_file_'+uid).fileupload('option', {
          process: [
              {
                  action: 'load',
                  fileTypes: /^image\/(gif|jpeg|png)$/
              },
              {
                  action: 'resize',
                  maxWidth: maxWidth,
                  maxHeight: maxHeight
              },
              {
                  action: 'save'
              }
          ]
      });
      
      if (this.loadedUploaders[uid]){
        $('#oi_delete_image_'+uid).val(this.loadedUploaders[uid].delete_image);
        $('#oi_image_saved_as_'+uid).val(this.loadedUploaders[uid].image_saved_as);
        $('#oi_image_input_'+uid).val(this.loadedUploaders[uid].image_input);
        if (this.loadedUploaders[uid].state == 1){
          $('#oi_image_uploader_'+uid).hide();                                        
          $('#oi_image_placeholder_'+uid).prop('src', this.loadedUploaders[uid].image_src).show();
          $('#oi_delete_image_button_'+uid).show();        
        } else {
          $('#oi_image_uploader_'+uid).show();                                        
          $('#oi_image_placeholder_'+uid).hide();
          $('#oi_delete_image_button_'+uid).hide();        
        }       
      } else {
        this.loadedUploaders[uid] = {
          delete_image : $('#oi_delete_image_'+uid).val(),
          image_saved_as : $('#oi_image_saved_as_'+uid).val(), 
          image_input : $('#oi_image_input_'+uid).val(),
          image_src : $('#oi_image_placeholder_'+uid).prop('src'),           
          state : $('#oi_image_placeholder_'+uid).is(':visible')                        
        };
      }
    },


    deleteImage : function(e){
    
      var uid = e.target.id.replace('oi_delete_image_button_', '');
      
      $('#oi_delete_image_'+uid).val(1);
      $('#oi_image_input_'+uid).val('');
      $('#oi_image_saved_as_'+uid).val('');                 
      $('#oi_image_placeholder_'+uid).hide();
      $(e.target).hide();//delete button
      $('#oi_image_uploader_'+uid).show(); 
      
      this.loadedUploaders[uid] = {
        delete_image : 1,
        image_saved_as : '', 
        image_input : '', 
        state : 0                        
      };      
         
    }
      
  });

}); 


