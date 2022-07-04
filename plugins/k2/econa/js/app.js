/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

// Override elfinder update function to trigger a change event
function elFinderUpdate(fieldID, value) {
  $K2('#' + fieldID).val(value).trigger('change');
  if (typeof(window.parent.SqueezeBox.close == 'function')) {
    SqueezeBox.close();
  } else {
    parent.$K2('#sbox-window').close();
  }
}

jQuery(document).ready(function() {

  var econaContainer = jQuery('#econaContainer');

  // Remove K2 native image preview
  if (jQuery('#k2TabImage .itemAdditionalField').length === 5) {
    jQuery('#k2TabImage .itemAdditionalField:eq(1)').remove();
  }
  jQuery('#k2Tab2 tr:eq(3)').remove();

  // Add K2 version specific class to apply styles
  if (econaK2Version === '2.6.9') {
    econaContainer.parents('fieldset').get(0).addClass('econaK2Version26');
  } else if (econaK2Version.indexOf('2.10') === 0) {
    var fieldset = econaContainer.parents('fieldset');
    var append = jQuery('<div class="itemAdditionalField itemAdditionalField-K2-2-10">' + fieldset.html() + '</div>');
    append.insertBefore('#k2TabImage .itemPlugins');
    fieldset.remove();
  } else {
    econaContainer.parents('fieldset').get(0).addClass('econaK2Version27');
  }

  // Init some vars/cache dom
  econaContainer = jQuery('#econaContainer');
  var econaImage = jQuery('#econaImage > img');
  var econaToolbar = jQuery('#econaToolbar');
  var econaCropperButtons = jQuery('#econaCropperButtons');
  var econaAspectRatio = jQuery('select[name="econaAspectRatio"]');
  var econaMoveButton = jQuery('#econaCropperButtons [data-option="move"]');
  var econaCropButton = jQuery('#econaCropperButtons [data-option="crop"]');
  var econaUpload = jQuery('input[name="plugins[econaupload]"]');
  var econaDeleteFlag = jQuery('input[name="plugins[econadelete]"]');
  var econaFile = jQuery('input[name="image"]');
  var econaFilename = jQuery('input[name="plugins[econafilename]"]');
  var econaKey = jQuery('input[name="plugins[econakey]"]').val();
  var econaParams = JSON.parse(econaContainer.find('.econa-params').text());

  // Reset some values stored in the plugins
  econaUpload.val('');
  econaDeleteFlag.val('');

  // File upload - First lines are a simple fix for Joomla! HTML5 fallback form validation bug
  econaFile.change(function(event) {
    event.stopPropagation();
  });
  econaFile.fileupload({
    dataType: 'json',
    url: 'index.php?option=com_ajax&plugin=econa&group=k2&format=json',
    formData: function() {
      return [{
        name: econaSessionToken,
        value: 1
      }, {
        name: 'econaKey',
        value: econaKey
      }, {
        name: 'econaUpload',
        value: econaUpload.val()
      }];
    },
    done: function(event, response) {
      if (response.result.success === true) {
        econaContainer.show();
        econaDeleteFlag.val('');
        econaImage.cropper('destroy');
        econaImage.attr('src', response.result.data[0].preview + '?t=' + event.timeStamp);
        econaFilename.val(response.result.data[0].filename);
        econaUpload.val(response.result.data[0].upload);
        econaToolbar.show();
        econaCropperButtons.hide();
        if (econaParams.auto_edit) {
          econaContainer.find('[data-action="edit"]').click();
        }
      } else {
        alert(response.result.message);
      }
      econaContainer.removeClass('econaLoading');
    },
    fail: function(event, response) {
      econaContainer.removeClass('econaLoading');
      alert(response.jqXHR.status + ' ' + response.jqXHR.statusText + ' ' + response.jqXHR.responseText);
    }
  }).bind('fileuploadsubmit', function(e, data) {
    econaFile.val('');
    return true;
  }).bind('fileuploadstart', function(e) {
    econaContainer.addClass('econaLoading');
    econaContainer.show();
  });

  // Browse server
  jQuery('#existingImageValue').on('change', function(event) {
    var data = {};
    data.econaKey = econaKey;
    data.path = jQuery(this).val();
    data.econaUpload = econaUpload.val();
    jQuery(this).val('');
    data[econaSessionToken] = 1;
    econaContainer.addClass('econaLoading');
    econaContainer.show();
    jQuery.ajax({
      dataType: 'json',
      type: 'POST',
      url: 'index.php?option=com_ajax&plugin=econa&group=k2&format=json',
      data: data
    }).done(function(response, status, xhr) {
      if (response.success === true) {
        econaContainer.show();
        econaDeleteFlag.val('');
        econaImage.cropper('destroy');
        econaImage.attr('src', response.data[0].preview + '?t=' + event.timeStamp);
        econaFilename.val(response.data[0].filename);
        econaUpload.val(response.data[0].upload);
        econaToolbar.show();
        econaCropperButtons.hide();
        if (econaParams.auto_edit) {
          econaContainer.find('[data-action="edit"]').click();
        }
      } else {
        alert(response.message);
      }
      econaContainer.removeClass('econaLoading');
    }).fail(function(xhr, status, error) {
      econaContainer.removeClass('econaLoading');
      alert(xhr.status + ' ' + xhr.statusText + ' ' + xhr.responseText);
    });
  });

  // Preview
  econaContainer.find('[data-action="preview"]').on('click', function(event) {
    event.preventDefault();
    if (typeof(SqueezeBox) !== 'undefined') {
      var url = econaImage.attr('src');
      SqueezeBox.open(url, {
        handler: 'image'
      });
    }
  });

  // Cropper Buttons
  econaCropperButtons.on('click', '[data-method]', function() {
    var button = jQuery(this);
    var method = button.data('method');
    var option = button.data('option');
    var secondOption = button.data('second-option');
    var direction = button.data('direction');
    var degrees = jQuery('input[name="econaRotateDegree"]').val();

    if (button.prop('disabled') || button.hasClass('disabled')) {
      return;
    }

    if (econaImage.data('cropper') && method) {

      var data = econaImage.cropper('getData');

      if (method === 'rotate') {

        if (data.scaleX === -1 || data.scaleY === -1) {
          direction = direction === 'left' ? 'right' : 'left';
        }

        if (direction === 'left') {
          option = '-' + degrees;
        } else {
          option = degrees;
        }

      }

      if (method === 'scaleX' && data.scaleX === -1) {
        option = 1;
      }
      if (method === 'scaleY' && data.scaleY === -1) {
        option = 1;
      }

      econaImage.cropper(method, option, secondOption);

      if (method === 'setDragMode') {
        if (option === 'move') {
          econaCropButton.removeClass('active');
        } else {
          econaMoveButton.removeClass('active');
        }
        button.addClass('active');
      }

    }
  });
  econaAspectRatio.on('change', function() {
    if (econaImage.data('cropper')) {
      econaImage.cropper('setAspectRatio', jQuery(this).val());
    }
  });

  // Edit
  econaContainer.find('[data-action="edit"]').on('click', function(event) {
    event.preventDefault();
    econaImage.cropper({
      aspectRatio: econaAspectRatio.val(),
      zoomOnWheel: false
    });
    econaToolbar.hide();
    econaMoveButton.removeClass('active');
    econaCropButton.addClass('active');
    econaCropperButtons.show();
  });

  // Apply
  econaContainer.find('[data-action="apply"]').on('click', function(event) {
    event.preventDefault();
    econaContainer.addClass('econaLoading');
    var data = econaImage.cropper('getData');
    data.task = 'process';
    data[econaSessionToken] = 1;
    data.econaUpload = econaUpload.val();
    data.id = jQuery('input[name="id"]').val();
    data.econaKey = econaKey;
    jQuery.ajax({
      method: 'post',
      dataType: 'json',
      url: 'index.php?option=com_ajax&plugin=econa&group=k2&format=json',
      data: data
    }).done(function(response, status, xhr) {
      econaContainer.removeClass('econaLoading');
      if (response.success === true) {
        econaImage.cropper('destroy');
        econaToolbar.show();
        econaCropperButtons.hide();
        econaImage.attr('src', response.data[0].preview + '?t=' + event.timeStamp);
        econaUpload.val(response.data[0].upload);
      } else {
        alert(response.message);
      }
    }).fail(function(xhr, status, error) {
      econaContainer.removeClass('econaLoading');
      alert(xhr.status + ' ' + xhr.statusText + ' ' + xhr.responseText);
    });

  });

  // Cancel
  econaContainer.find('[data-action="cancel"]').on('click', function(event) {
    econaImage.cropper('destroy');
    econaToolbar.show();
    econaCropperButtons.hide();
  });

  // Delete
  econaContainer.find('[data-action="delete"]').on('click', function(event) {
    econaDeleteFlag.val('1');
    econaFilename.val('');
    econaContainer.hide();
    var upload = econaUpload.val();
    if (upload) {
      var data = {};
      data.task = 'delete';
      data[econaSessionToken] = 1;
      data.econaUpload = upload;
      jQuery.ajax({
        method: 'post',
        dataType: 'json',
        url: 'index.php?option=com_ajax&plugin=econa&group=k2&format=json',
        data: data
      }).done(function(response, status, xhr) {
        if (response.success === true) {
          econaUpload.val('');
        } else {
          alert(response.message);
        }
      }).fail(function(xhr, status, error) {
        alert(xhr.status + ' ' + xhr.statusText + ' ' + xhr.responseText);
      });
    }
  });

});
