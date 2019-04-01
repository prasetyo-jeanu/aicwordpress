(function($){
    $(document).ready(function(){
       
        var tmls_uploadlogo_button = $('#uploadlogo_button');
        var tmls_removelogo_button = $('#removelogo_button');
        var tmls_logo_input = $('#logo_input');
        var tmls_logo_img = $('#logo_img');
        
        /* ================================================
                Select Logo image
        ================================================ */

        tmls_uploadlogo_button.click(function(e) {

            var custom_uploader;

            e.preventDefault();

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                tmls_logo_input.val(attachment.url);
                tmls_logo_img.attr('src', attachment.url);
                tmls_logo_img.css('display', 'block');
                tmls_removelogo_button.css('display', 'inline-block');
            });

            //Open the uploader dialog
            custom_uploader.open();

        });

        tmls_removelogo_button.click(function() {
            tmls_logo_img.attr('src','');
            tmls_logo_img.css('display', 'none');
            tmls_logo_input.val('');
            $(this).css('display', 'none');
        });
        
    });
}) (jQuery);