(function($) {
    $(document).ready(function() {

        $('#mashfs-sortable-wrapper').sortable();

        // Start colorpicker
        $mashfs = jQuery.noConflict();

        $mashfs('.mashfs_backgroundcolor')
            .colpick({
                layout: 'hex',
                submit: 0,
                colorScheme: 'light',
                onChange: function(hsb, hex, rgb, el, bySetColor) {
                    $mashfs(el).css('border-color', '#' + hex);
                    // Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
                    if (!bySetColor)
                        $mashfs(el).val(hex);
                }
            })
            .keyup(function() {
                $mashfs(this).colpickSetColor(this.value);
            })
        ;

        $mashfs('.mashfs_sharecountcolor')
            .colpick({
                layout: 'hex',
                submit: 0,
                colorScheme: 'light',
                onChange: function(hsb, hex, rgb, el, bySetColor) {
                    $mashfs(el).css('border-color', '#' + hex);
                    // Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
                    if (!bySetColor)
                    {
                        $mashfs(el).val(hex);
                    }
                }
            })
            .keyup(function() {
                $mashfs(this).colpickSetColor(this.value);
            })
        ;

        var $emailTitleContainer    = $('#mashsb_settings\\[mashfs_mail_title\\]').parents('tr'),
            $emailBodyContainer     = $('#mashsb_settings\\[mashfs_mail_body\\]').parents('tr')
        ;

        toggleMailFields();

        $('#mashsb_settings\\[mashfs_networks\\]\\[mail\\]').on('change', function()
        {
            toggleMailFields((this.checked));
        });

        if ($('#mashsb_settings\\[mashfs_networks\\]\\[mail\\]').is(':checked'))
        {
            toggleMailFields(true);
        }

        function toggleMailFields(show)
        {
            if ('undefined' === typeof(show))
            {
                show = false;
            }

            if (show)
            {
                $emailTitleContainer.show();
                $emailBodyContainer.show();
            }
            else
            {
                $emailTitleContainer.hide();
                $emailBodyContainer.hide();
            }
        }
    });

})(jQuery);
