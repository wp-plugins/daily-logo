jQuery(document).ready(function() {

    /**
     * Init uploader
     */
    jQuery('#image_button').click(function() {
        jQuery('#image_frame').val('image');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    jQuery('#image_alternative_button').click(function() {
        jQuery('#image_frame').val('image_alternative');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    window.send_to_editor = function(html) {
        var img_url = jQuery('img', html).attr('src');
        if (img_url == undefined || img_url == '') img_url = jQuery(html).attr('src');
        var img_frame = jQuery('#image_frame').val();

        jQuery('#' + img_frame).val(img_url);
        tb_remove();
    };

    /**
     * Init date picker
     */
    jQuery('.date').datepicker({
        dateFormat : 'yy-mm-dd'
    });

    /**
     * Save row
     */
    jQuery("#daily_logo_table").submit(function() {
        var ajax_url = "/wp-admin/admin-ajax.php";
        var form = jQuery('#daily_logo_table');

        // Validate form
        form.validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                date: {
                    required: true
                },
                link: {
                    url: true
                },
                image: {
                    url: true
                }
            },
            messages: {
                name: "Please enter a valid name",
                date: "Please enter a valid date (format: 'YYYY-MM-DD')",
                link: "Please enter a valid link",
                image: "Please enter a valid image"
            }
        });

        var is_form_valid = form.valid();
        if (is_form_valid) {
            var data = form.serializeArray();

            // Prepare post data
            data.push({
                name: 'action',
                value: 'daily_logo_save_row',
                type: 'post',
                dataType: 'json'
            });

            // Post data
            jQuery.post(ajax_url, data, function(response) {
                // Manage logo list
                jQuery('#logo_rows').hide().empty().append(response).fadeIn();

                // Clean form fields
                jQuery('#id').val('');
                jQuery('#name').val('');
                jQuery('#date').val('');
                jQuery('#link').val('');
                jQuery('#target').prop('checked', false);
                jQuery('#image').val('');
                jQuery('#image_alternative').val('');
                jQuery('#class').val('');
            });
        }

        return false;
    });

    /**
     * Save setting action
     */
    jQuery("#daily_logo_settings").submit(function() {
        var form = jQuery('#daily_logo_settings');

        // Validate form
        form.validate({
            rules: {
                template_without_logo: {
                    required: true
                },
                alternative_template_without_logo: {
                    required: true
                },
                template_with_logo: {
                    required: true
                },
                alternative_template_with_logo: {
                    required: true
                }
            },
            messages: {
                template_without_logo: "Please enter a valid template",
                alternative_template_without_logo: "Please enter a valid template",
                template_with_logo: "Please enter a valid template",
                alternative_template_with_logo: "Please enter a valid template"
            }
        });

        return form.valid();
    });

    /**
     * Reset action
     */
    jQuery('body').on('click', '.button-primary.reset', function(e) {
        jQuery('#action').val('restore');
    });
});

/**
 * Modify row
 */
function modify_row(id) {
    var ajax_url = '/wp-admin/admin-ajax.php';
    var nonce = jQuery("#daily_logo_table").find('input#nonce').val();

    // Prepare post data
    var data = {
        name: 'action',
        action: 'daily_logo_get_row',
        type: 'post',
        dataType: 'json',
        id: id,
        nonce: nonce
    };

    // Post data
    jQuery.post(ajax_url, data, function(response) {
        // Fill data
        jQuery('#id').val(response.id);
        jQuery('#name').val(response.name);
        jQuery('#date').val(response.year + '-' + format_digit_date(response.month) + '-' + format_digit_date(response.day));
        jQuery('#link').val(response.link);
        jQuery('#target').prop('checked',(response.target == 1));
        jQuery('#image').val(response.image);
        jQuery('#image_alternative').val(response.image_alternative);
        jQuery('#class').val(response.class);
    });
}

/**
 * Delete row
 */
 function delete_row(id) {
    var ajax_url = "/wp-admin/admin-ajax.php";
    var choice = confirm('Are you sure?');
    var nonce = jQuery("#daily_logo_table").find('input#nonce').val();

    if (choice == true) {
        // Prepare post data
        var data = {
            name: 'action',
            action: 'daily_logo_remove_row',
            type: 'post',
            dataType: 'json',
            id: id,
            nonce: nonce
        };

        // Post data
        jQuery.post(ajax_url, data, function(response) {
            //jQuery("#logo_rows").hide();
            if (response) jQuery("#logo-"+id).remove();
            jQuery("#logo_rows").fadeIn();
        });
    }

    return false;
}

/**
 * Format digit date appending the leading 0
 *
 * @param value
 * @returns {string}
 */
function format_digit_date(value) {
    return value < 10 ? "0" + value : value;
}
