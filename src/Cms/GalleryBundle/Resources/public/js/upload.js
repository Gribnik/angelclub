var saveImagesBinded = false;

/**
 * Binds different actions on elements when images upload has been finished
 *
 * @param data
 * @param savePath
 */
function imageUploadCallback(data, savePath)
{
    $('#uploaded-images-wrapper').append(data.result.imageForm);

    if (false === saveImagesBinded) {
        $('#save-upload').click(function() {
            saveUploadedImages(savePath, false)
        });
        $('#cancel-upload').click(function() {
            cancelUploadAction(savePath);
        })
        $('body').delegate('.uploaded-image-remove', 'click', function() {
            previewRemoveAction($(this))
        });
        saveImagesBinded = true;
    }
}

/**
 * Saves all entered images data
 *
 * @param savePath
 * @param removeAll - If true, removes all images which have been just uploaded
 * @returns {boolean}
 */
function saveUploadedImages(savePath, removeAll)
{
    var uploadedItemsAdditionalData = _serializeUploadedImagesData(removeAll);
    if (uploadedItemsAdditionalData.length > 0) {
        $.ajax({
            url: savePath,
            data: { imagesDetails: uploadedItemsAdditionalData },
            type: "POST"
        }).done(function() {
                location.reload();
            }).fail(function() {
                alert('There was error during processing your request');
            });
    } else {
        alert('There are no items to save');
    }
    return false;
}


/**
 * Removes uploaded images, marked for removal
 *
 * @param element
 */
function previewRemoveAction(element)
{
    var imageRow = element.parents('.uploaded-image-row');
    var removeMark = imageRow.find('input[name="is_removed"]');
    removeMark.val('1');
    // TODO: Make an checkbox instead of hide entire row
    imageRow.toggle('100');
}

/**
 * Removes all images which have been just uploaded
 *
 * @param savePath
 */
function cancelUploadAction(savePath)
{
    saveUploadedImages(savePath, true);
}


/**
 * Collects all entered data for recently uploaded images
 *
 * @param markAsRemoved - If true, mark each image for removal
 * @returns {Array}
 * @private
 */
function _serializeUploadedImagesData(markAsRemoved)
{
    var uploadedItems = $('.uploaded-image-row');
    var uploadedItemsAdditionalData = [];
    if (uploadedItems.length > 0) {
        uploadedItems.each(function() {
            if (true === markAsRemoved) {
                $(this).find('input[name="is_removed"]').val('1')
            }
            uploadedItemsAdditionalData.push($(this).serialize());
        });
    }

    return uploadedItemsAdditionalData;
}