jQuery(document).ready(function($){
    // Media Uploader Logic
    var mediaUploader;
    
    $('#cnc_gallery_upload_btn').on('click', function(e) {
        e.preventDefault();
        
        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Images for Gallery',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true // Allow multiple files to be selected
        });
        
        // When a file is selected, grab the URL and set it as the text field's value
        mediaUploader.on('select', function() {
            var selection = mediaUploader.state().get('selection');
            var ids = $('#cnc_gallery_image_ids').val() ? $('#cnc_gallery_image_ids').val().split(',') : [];
            
            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                ids.push(attachment.id);
                
                // Append preview
                $('#cnc_gallery_preview').append(
                    '<div class="cnc-gallery-preview-item" data-id="' + attachment.id + '" style="display:inline-block; margin:5px; position:relative;">' +
                        '<img src="' + attachment.sizes.thumbnail.url + '" style="width:100px; height:100px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">' +
                        '<span class="cnc-remove-image" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:20px; height:20px; text-align:center; line-height:20px; cursor:pointer;">&times;</span>' +
                    '</div>'
                );
            });
            
            $('#cnc_gallery_image_ids').val(ids.join(','));
        });
        
        // Open the uploader dialog
        mediaUploader.open();
    });
    
    // Remove Image Logic
    $(document).on('click', '.cnc-remove-image', function() {
        var item = $(this).parent();
        var id = item.data('id');
        var ids = $('#cnc_gallery_image_ids').val().split(',');
        
        // Remove id from array
        var index = ids.indexOf(id.toString());
        if (index > -1) {
            ids.splice(index, 1);
        }
        
        $('#cnc_gallery_image_ids').val(ids.join(','));
        item.remove();
    });
    
    // Clear All
    $('#cnc_gallery_clear_btn').on('click', function(e) {
        e.preventDefault();
        $('#cnc_gallery_image_ids').val('');
        $('#cnc_gallery_preview').empty();
    });
});
