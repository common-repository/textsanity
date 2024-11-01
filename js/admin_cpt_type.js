jQuery(document).ready(function($) {
    // Handle delete click.
    function clickDelete() {
        if(!confirm('Are you sure you want to delete this item?')) {
            return false;
        }
        var action = $(this).attr('data-action');
        var id = $(this).attr('data-id');
        var data = {};
        var nonce = $(this).attr('data-nonce');

        var msg = '';

        // Show the spinner.
        var $spinner = $(this).next();
        $spinner.addClass('is-active');

        data._wpnonce = nonce;
        data.action = action;
        data.id = id;


        $.ajax({
            data: data,
            dataType: 'json',
            method: 'POST',
            url: ajaxurl,
            success: function(data) {
                if(data.status && data.status == 'success' && data.reload) {
                    window.location.href = window.location.href;
                } else {
                    if(data.messages) {
                        $('#ajax_message').addClass('notice');
                        $('#ajax_message').addClass('notice-error');
                        $('#ajax_message').removeClass('notice-success');
                        for(i = 0; i < data.messages.length; i++) {
                            if(i > 0) {
                                msg += '<br>';
                            }
                            msg += data.messages[i];
                        }
                        $('#ajax_message').html('<p>' + msg + '</p>');
                    } else {
                        $('#ajax_message').addClass('error');
                        $('#ajax_message').removeClass('success');
                        $('#ajax_message').html('<p>There was a problem with the submission.</p>');
                    }
                    $spinner.removeClass('is-active');

                    $('html, body').animate({
                        scrollTop: 0
                    }, 300);
                }
            },
            error: function(jqXHR, textStatus, error) {
                $spinner.removeClass('is-active');

                var data = jqXHR.responseJSON;
                if(data && data.message) {
                    $('#ajax_message').addClass('notice');
                    $('#ajax_message').addClass('notice-error');
                    $('#ajax_message').removeClass('notice-success');
                    $('#ajax_message').html('<p>' + data.message + '</p>');
                } else {
                    $('#ajax_message').addClass('notice');
                    $('#ajax_message').addClass('notice-error');
                    $('#ajax_message').removeClass('notice-success');
                    $('#ajax_message').html('<p>There was a problem with the submission.</p>');
                }
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }
        });
        return false;
    }

    // Initialize everything.
	function init() {
        $(document).on('click', '.delete', clickDelete);

		$(document).on('change', '[name=type]', updateFields);
		$(document).on('change', '[name=position]', updateFields);

        updateFields();

        $('.color_picker').wpColorPicker();
        $('#txsy_widget_details').css({'visibility': 'initial'});
	}

    // Make sure proper fields are shown.
    function updateFields() {
        var type = $('[name=type]').val();
        var position = $('[name=position]').val();

        $('[name=keywords]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=tags]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=message]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});

        $('[name=location]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=background_color]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=text_color]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=popup_delay]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=chat_popup_auto]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});
        $('[name=chat_popup_delay]').closest('tr').css({'visibility': 'hidden', 'position': 'absolute'});

        if(type == 'individual') {
            $('[name=tags]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
            $('[name=message]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        } else if(type == 'opt_in') {
            $('[name=tags]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        } else if(type == 'campaign') {
            $('[name=keywords]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        }

        if(position != 'inline') {
            $('[name=location]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        }

        if(position == 'chat') {
            $('[name=background_color]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
            $('[name=text_color]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
            $('[name=chat_popup_auto]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
            $('[name=chat_popup_delay]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        }

        if(position == 'popup') {
            $('[name=popup_delay]').closest('tr').css({'visibility': 'initial', 'position': 'static'});
        }

    }

    // Get everything started.
    init();
});
