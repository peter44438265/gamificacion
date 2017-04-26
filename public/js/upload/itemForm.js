
/******************************************************************************/
/**                                                                          **/
/**                           Item form plugin                               **/
/**                                                                          **/

/**
 * @author Alberto Moyano S�nchez, 2010
 * @version 2.0
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

(function($) {

//------------------------------------------------------------------------------
// constructor (must be before the public functions) ---------------------------
//------------------------------------------------------------------------------

jQuery.itemForm = {

    insertAjaxUrl: 'php/insert.php',
    updateAjaxUrl: 'php/update.php',
    insertOrUpdateAjaxUrl: '',
    savedUrl: 'php/saved.php',
    itemId: '',
    loadingTimeout: 2000, // milliseconds
    checkingIntervalTime: 500, // milliseconds
    progressBarInterval: 5,
    messages: {
        savingTitle: 'Guardando',
        savingMessage: '<p>La imagen se esta guardando.</p><div id="progressbar"></div>',
        savedTitle: 'Guardado',
        errorTitle: 'Error',
        noImagesTitle: 'No hay imagen',
        noImagesMessage: 'No se puede guardar.',
        timeoutTitle: 'Tiempo excedido',
        timeoutMessage: 'Tiempo excedido al subir la imagen.'
    }
};

//------------------------------------------------------------------------------
// public functions ------------------------------------------------------------
//------------------------------------------------------------------------------

jQuery.itemForm.insert = function() {

    $.itemForm.insertOrUpdateAjaxUrl = $.itemForm.insertAjaxUrl;
    return save();
};

//------------------------------------------------------------------------------

jQuery.itemForm.update = function(itemId) {

    $.itemForm.insertOrUpdateAjaxUrl = $.itemForm.updateAjaxUrl;
    this.itemId = itemId;
    return save();
};

//------------------------------------------------------------------------------

jQuery.itemForm.submitFormIfNotImageLoading = function(loadingTime) {

    if (jQuery.uploaderPreviewer.loadingImages()) {
      if(loadingTime > $.itemForm.loadingTimeout) {
        var settings = {
            title: $.itemForm.messages.timeoutTitle,
            message: $.itemForm.messages.timeoutMessage,
            buttons: { 'OK': function() { $(this).dialog("close"); } }
        };
        
        $.globalFunctions.openDialog(settings);
      }
      else {
        loadingTime += $.itemForm.checkingIntervalTime;
        var progressBarValue = $("#progressbar").progressbar('value')
                             + $.itemForm.progressBarInterval;
        $("#progressbar").progressbar('value', progressBarValue);
        var recursiveCall = "$.itemForm.submitFormIfNotImageLoading(" + loadingTime + ")";
        setTimeout(recursiveCall, $.itemForm.checkingIntervalTime);
      }
    }
    else {
      return submitForm();
    }
};

//------------------------------------------------------------------------------
// private functions -----------------------------------------------------------
//------------------------------------------------------------------------------

function save() {
var image=null;
    if (validateData()) {
        showImageLoadingMessage();
        image= $.itemForm.submitFormIfNotImageLoading(0);
    }
    else {
        settings = {
            title: $.itemForm.messages.noImagesTitle,
            message: $.itemForm.messages.noImagesMessage,
            buttons: { 'OK': function() { $(this).dialog("close"); } }
        };
        $.globalFunctions.openDialog(settings);
    }
    //alert(image)
    return image;
}

//------------------------------------------------------------------------------

function validateData() {

    var validationOk = true;

    if ( ! $.uploaderPreviewer.loadedImages().length) {
        validationOk = false;
    }
    
    return validationOk;
};

//------------------------------------------------------------------------------

function showImageLoadingMessage() {
//alert("entro")
    var options = {
        title: $.itemForm.messages.savingTitle,
        message: $.itemForm.messages.savingMessage
    };

    $.globalFunctions.openDialog(options);

    $("#progressbar").progressbar({
        value: 0
    });
    
    var progressBarInterval = $.itemForm.checkingIntervalTime * 100 / $.itemForm.loadingTimeout;
    if (progressBarInterval != Number.NaN) {
        $.itemForm.progressBarInterval = Math.floor(progressBarInterval);
    }
};

//------------------------------------------------------------------------------

function submitForm() {

    var parameters = {
        keywords: $('#keywords').val(),
        imagesCount: $.uploaderPreviewer.loadedImages().length
    };
    
    $.uploaderPreviewer.loadedImages().each(function(index) {
        parameters['image' + (index + 1)] = $(this).val();
    });

   $.post($.itemForm.insertOrUpdateAjaxUrl, parameters, saved);
   /*$.post($.itemForm.insertOrUpdateAjaxUrl, parameters, function(data){
       return $.trim(data);
   });*/
};

//------------------------------------------------------------------------------

function saved(result) {
    // the result html elements must be enclosed in a parent html element, to
    // be able to read all of them. The wrap function doesn't work in this case,
    // with several separated html elements
    var $result = $('<div>' + result + '</div>');
    var settings;
    
    var sportCenterUrl = $result.find('.sportCenterUrl').text();
    if (sportCenterUrl) {
        settings = {
            title: $.messages.dialog.titles.saved,
            message: $.messages.dialog.messages.saved,
            buttons: { 'OK': function() { $(this).dialog("close"); } },
            close: $.configuration.dialog.events.redirect
        };
        $.configuration.url.redirection = sportCenterUrl;
        
        if ($result.find('.warning').length) {
            settings.title = $.messages.dialog.titles.warning;
            settings.message = $result.find('.warning');
        }
    }
    else {
        settings = {
            title: $.itemForm.messages.savedTitle,
            message: $result,
            buttons: { 'OK': function() {
                //location.assign($.itemForm.savedUrl);
                //bootbox.alert("Imagen Guardada");
                //$(this).dialog("close");
            } }
        };
        if ($result.find('.error').length) {
            settings.message = $result.find('.error');
        }
    }
    
    $.globalFunctions.openDialog(settings);
};

//------------------------------------------------------------------------------

})(jQuery);

/**                                                                          **/
/**                           Item form plugin                               **/
/**                                                                          **/
/******************************************************************************/

