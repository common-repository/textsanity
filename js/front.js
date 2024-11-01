jQuery(document).ready(function($) {
    // From http://www.quirksmode.org/js/cookies.html
    // Work with cookies.
    var cookie = {};
    cookie.create = function(name, value, days) {
        if ( days) {
            var date = new Date( );
            date.setTime( date.getTime( )+( days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString( );
        } else {
            var expires = "";
        }
        document.cookie = name+"="+value+expires+"; path=/";
    }

    cookie.erase = function(name) {
        cookie.create(name,"",-1);
    }

    cookie.read = function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split( ';');
        for( var i=0;i < ca.length;i++) {
            var c = ca[i];
            while ( c.charAt( 0)==' ') c = c.substring( 1,c.length);
            if ( c.indexOf( nameEQ) == 0) return c.substring( nameEQ.length,c.length);
        }
        return null;
    }

    // Close popup.
	function clickPopupClose() {
        $('.txsy_popup_cover').removeClass('txsy_show');
		return false;
	}

    // Close chat.
	function clickChatClose() {
        $('.txsy_chat').removeClass('txsy_show');
		return false;
	}

    // Open chat.
	function clickChatOpen() {
        $('.txsy_chat').addClass('txsy_show');
		return false;
	}

    // Do not show popup anymore.
	function clickDoNotShow() {
        var widget_id = $('.txsy_popup_cover [name=widget_id]').val();
        cookie.create('txsy_popup_hide_' + widget_id, 'yes', 365);
        $('.txsy_popup_cover .txsy_popup_close').click();
        return false;
    }

    // Initialize everything.
	function init() {
		$(document).on('click', '.txsy_popup_close', clickPopupClose);
		$(document).on('click', '.txsy_do_not_show', clickDoNotShow);

		$(document).on('click', '.txsy_chat_open', clickChatOpen);
		$(document).on('click', '.txsy_chat_close', clickChatClose);

		$(document).on('submit', '.txsy_ajax_form', submit);

        // Based on: https://heydonworks.com/article/the-flexbox-holy-albatross/
        // markBreak function is listed at the bottom of this file.
        $('.txsy_resize').each(function() {
            markBreak($(this)[0], 620);
        });
        //$('.txsy_resize').trigger('resize');

        setTimeout(showPopup, txsy_popup_delay * 1000);

        if(txsy_chat_popup_auto) {
            setTimeout(clickChatOpen, txsy_chat_popup_delay * 1000);
        }
	}

    /*
    function resize() {
        var small = $(this).width() <= 620;
        if(small) {
            $('.txsy_resize .txsy_flex label').css('text-align', 'left');
        } else {
            $('.txsy_resize .txsy_flex label').css('text-align', 'right');
        }
    }
    */

    // Handle form submission.
	function submit() {
		$form = $(this);
		var $button = $form.find('.txsy_submit button');
		var data = $(this).serialize();

        data += '&_wpnonce=' + txsy_nonce_front;

		$button.prop('disabled', true);

		$.ajax({
			data: data,
			dataType: 'json',
			method: $form.attr('method'),
			url: txsy_ajaxurl,
			success: responseSuccess.bind(null, $form),
			error: responseError.bind(null, $form)
		});
		return false;
	} 

    // Handle response error.
	function responseError($form, jqXHR, textStatus, error) {
		var $button = $form.find('.txsy_submit button');

		$button.prop('disabled', false);

		var data = jqXHR.responseJSON;  
		if(data && data.message) {      
            alert(data.message);
            /*
			$('#ajax_message').addClass('notice');
			$('#ajax_message').addClass('notice-error');
			$('#ajax_message').removeClass('notice-success');
			$('#ajax_message').html('<p>' + data.message + '</p>');
            */
		} else {
            alert('There was a problem with the submission.');
            /*
			$('#ajax_message').addClass('notice');
			$('#ajax_message').addClass('notice-error');
			$('#ajax_message').removeClass('notice-success');
			$('#ajax_message').html('<p>There was a problem with the submission.</p>');
            */
		}
	}

    // Handle success response.
	function responseSuccess($form, data) {
		var $button = $form.find('.txsy_submit button');
        var $txsy = $form.closest('.txsy');
        var $interior;
		var msg = '';

		$button.prop('disabled', false);

		if(data.status && data.status == 'success' && data.messages) {
			for(i = 0; i < data.messages.length; i++) {
				if(i > 0) {
					msg += '<br>';
				}
				msg += data.messages[i];
			}
            if($txsy.hasClass('txsy_interior')) {
                $txsy.html('<p>' + msg + '</p>');
                $form.remove();
            } else {
                $interior = $txsy.find('.txsy_interior');
                if($interior.length) {
                    $interior.html('<p>' + msg + '</p>');
                    if(!$interior.is('form')) {
                        $form.remove();
                    }
                }
            }

		} else if(data.status && data.status == 'success') {
			window.location.href = window.location.href;
		} else {
			if(data.messages) {
                /*
				$('#ajax_message').addClass('notice');
				$('#ajax_message').addClass('notice-error');
				$('#ajax_message').removeClass('notice-success');
                */
				for(i = 0; i < data.messages.length; i++) {
					if(i > 0) {
						msg += '<br>';
					}
					msg += data.messages[i];
				}
                alert(msg);
				//$('#ajax_message').html('<p>' + msg + '</p>');
			} else {
                alert('There was a problem with the submission.');
                /*
				$('#ajax_message').addClass('error');
				$('#ajax_message').removeClass('success');
				$('#ajax_message').html('<p>There was a problem with the submission.</p>');
                */
			}
		}
	}

    // Show popup.
	function showPopup() {
        var widget_id = $('.txsy_popup_cover [name=widget_id]').val();
        var hide = cookie.read('txsy_popup_hide_' + widget_id);
        if(hide != 'yes') {
            $('.txsy_popup_cover').addClass('txsy_show');
        }
    }

	init();

    // Based on: https://heydonworks.com/article/the-flexbox-holy-albatross/
    function markBreak(elem, width) {
        const ro = new ResizeObserver( entries => {
            for (let entry of entries) {
                const cr = entry.contentRect;
                //const q = cr.width <= br;
                const q = cr.width <= width;
                entry.target.classList.toggle('txsy_small', q);
            }
        });

        ro.observe(elem);
    }
});
