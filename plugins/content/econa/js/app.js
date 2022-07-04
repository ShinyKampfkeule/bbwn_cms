/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

jQuery(document).ready(function() {

  // Init some vars/cache dom
  var econaContainer = jQuery('#econaContainer');
  var econaImage = jQuery('#econaImage > img');
  var econaToolbar = jQuery('#econaToolbar');
  var econaCropperButtons = jQuery('#econaCropperButtons');
  var econaAspectRatio = econaCropperButtons.find('select[name="econaAspectRatio"]');
  var econaMoveButton = jQuery('#econaCropperButtons [data-option="move"]');
  var econaCropButton = jQuery('#econaCropperButtons [data-option="crop"]');
  var econaUpload = jQuery('input[name="jform[econa][upload]"]');
  var econaDeleteFlag = jQuery('input[name="jform[econa][delete]"]');
  var econaFile = jQuery('input[name="jform[econa][file]"]');
  var econaFilename = jQuery('input[name="jform[econa][filename]"]');
  var econaKey = jQuery('input[name="jform[econa][key]"]').val();
  var econaPath = jQuery('input[name="jform[econa][path]"]');
  var econaParams = JSON.parse(econaContainer.find('.econa-params').text());

  // Hide clear button of media field
  econaPath.parent().removeClass('input-prepend').removeClass('input-append');
  econaPath.next().next().remove();

  // File upload - First lines are a simple fix for Joomla! HTML5 fallback form validation bug
  econaFile.change(function(event) {
    event.stopPropagation();
  });
  econaFile.fileupload({
    dataType: 'json',
    url: 'index.php?option=com_ajax&plugin=econa&group=content&format=json',
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
        if(econaParams.auto_edit) {
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
  jQuery('input[name="jform[econa][path]"]').on('change', function(event) {
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
      url: 'index.php?option=com_ajax&plugin=econa&group=content&format=json',
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
        if(econaParams.auto_edit) {
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
  econaToolbar.find('[data-action="preview"]').on('click', function(event) {
    event.preventDefault();
      var url = econaImage.attr('src');
      jQuery.featherlight(url);
  });

  // Cropper Buttons
  econaCropperButtons.on('click', '[data-method]', function() {
    var button = jQuery(this);
    var method = button.data('method');
    var option = button.data('option');
    var secondOption = button.data('second-option');
    var direction = button.data('direction');
    var degrees = econaCropperButtons.find('input[name="econaRotateDegree"]').val();


    if (button.prop('disabled') || button.hasClass('disabled')) {
      return;
    }

    if (econaImage.data('cropper') && method) {

      var data = econaImage.cropper('getData');

      if (method === 'rotate') {

        if(data.scaleX === -1 || data.scaleY === -1) {
          direction = direction === 'left' ? 'right' : 'left';
        }

        if (direction === 'left') {
          option = '-' + degrees;
        } else {
          option = degrees;
        }

      }

      if(method === 'scaleX' && data.scaleX === -1) {
        option = 1;
      }
      if(method === 'scaleY' && data.scaleY === -1) {
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
  econaToolbar.find('[data-action="edit"]').on('click', function(event) {
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
  econaCropperButtons.find('[data-action="apply"]').on('click', function(event) {
    event.preventDefault();
    econaContainer.addClass('econaLoading');
    var data = econaImage.cropper('getData');
    data.task = 'process';
    data[econaSessionToken] = 1;
    data.econaUpload = econaUpload.val();
    data.id = jQuery('input[name="jform[id]"]').val();
    data.econaKey = econaKey;
    jQuery.ajax({
      method: 'post',
      dataType: 'json',
      url: 'index.php?option=com_ajax&plugin=econa&group=content&format=json',
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
  econaCropperButtons.find('[data-action="cancel"]').on('click', function(event) {
    econaImage.cropper('destroy');
    econaToolbar.show();
    econaCropperButtons.hide();
  });

  // Delete
  econaToolbar.find('[data-action="delete"]').on('click', function(event) {
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
        url: 'index.php?option=com_ajax&plugin=econa&group=content&format=json',
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
