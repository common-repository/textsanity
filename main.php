<?php
/*
 * Plugin Name: TextSanity
 * Description: Integrates TextSanity with WordPress.
 * Author: TextSanity
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

require_once(__DIR__ . '/classes/class-textsanity-api.php');
require_once(__DIR__ . '/classes/class-textsanity-cpt.php');
require_once(__DIR__ . '/classes/class-textsanity-widget.php');

class TextSanity {
    public $key = 'txsy';
    public $key_ = 'txsy_';
    public $version = '1.0.0';

    // The hooks for the admin pages.
    public $hook_settings;
    public $hook_widgets;
    public $hook_connection;
    public $hook_help;

    public $settings = [];
    public $show_banner_bottom = false;
    public $show_banner_top = false;
    public $show_chat = false;
    public $show_popup = false;

    public $textsanity_url = 'https://textsanity.com/';

    // Get everything started.
    public function __construct() {
        add_action('widgets_init', [$this, 'widgetsInit']);
        add_action('init', [$this, 'init']);
    }

    // Load admin assets as needed.
    public function adminEnqueueScripts($hook) {
        if($this->hook_settings == $hook || $this->hook_connection == $hook) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script($this->key_ . 'admin_ajax_form', plugins_url('js/admin_ajax_form.js', __FILE__), ['jquery', 'wp-color-picker'], $this->version);
            wp_enqueue_style($this->key_ . 'admin_ajax_form', plugins_url('css/admin_ajax_form.css', __FILE__), [], $this->version);
        }
    }

    // Output admin connect page.
    public function adminConnect() {
        global $textSanityAPI;
        $action = $this->key_ . 'disconnect';
        $title = 'Connect TextSanity Account';

        $connected = $textSanityAPI->isConnected();
        $oauth_url = $textSanityAPI->getOAuthURL();

        include 'views/admin_connect.php';
    }

    // Create a settings page link.
    public function adminLink($page = 'settings', $key = '', $subkey = '', $paged = 1) {
        if(is_numeric($key)) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&paged=' . $key);
        } elseif($subkey) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&key=' . $key . '&subkey=' . $subkey . '&paged=' . $paged);
        } elseif($key) {
            $url = admin_url('admin.php?page=' . $this->key_ . $page . '&key=' . $key);
        } else {
            $url = admin_url('admin.php?page=' . $this->key_ . $page);
        }
        $url = esc_url($url);
        return $url;
    }

    // Build the admin menu.
    public function adminMenu() {
        // Use a custom icon to build menu.
        $this->getIcon();
        //add_menu_page('TextSanity', 'TextSanity', 'manage_options', $this->key_ . 'settings', false);
        $this->hook_connection = add_submenu_page($this->key_ . 'settings', 'Account Connection', 'Account Connection', 'manage_options', $this->key_ . 'connection', [$this, 'adminConnect']);
        $this->hook_help = add_submenu_page($this->key_ . 'settings', 'Help', 'Help', 'manage_options', $this->key_ . 'help', [$this, 'adminHelp']);
        //$this->hook_settings = add_submenu_page($this->key_ . 'settings', 'TEMP - Settings', 'TEMP - Settings', 'manage_options', $this->key_ . 'settings', [$this, 'adminSettings']);
    }

    // Output admin help page.
    public function adminHelp() {
        $title = 'TextSanity Help';

        $connection_url = admin_url('admin.php?page=' . $this->key_ . 'connection');
        $texting_widgets_url = admin_url('edit.php?post_type=' . $this->key_ . 'widget');
        $widgets_url = admin_url('widgets.php');

        include 'views/admin_help.php';
    }

    // Output admin settings page (not currently used).
    public function adminSettings() {
        $action = $this->key_ . 'setting_basic';
        $title = 'Settings';

        if(isset($_GET['key'])) {
            $key = sanitize_text_field($_GET['key']);
        } else {
            $key = '';
        }    

        if(isset($_GET['subkey'])) {
            $subkey = sanitize_text_field($_GET['subkey']);
        } else {
            $subkey = '';
        }  

        if($key) {
            $action = $this->key_ . 'setting_update';
            $url_back = $this->adminLink('settings');

            $settings = get_option($this->key_ . 'settings');
            if(!is_array($settings)) {
                $settings = [];
            }
            if(!isset($settings[$key])) {
                $settings[$key] = [];
                $settings[$key]['enabled'] = 'no';
                $settings[$key]['type'] = '';
                $settings[$key]['keywords'] = '';
                $settings[$key]['tags'] = '';
                $settings[$key]['message'] = '';
                $settings[$key]['popup_delay'] = 0;
                $settings[$key]['color'] = '#007aff';
                $settings[$key]['description'] = '';
                $settings[$key]['thank_you'] = '';
            }
            $setting = $settings[$key];
            $types = [['Please Select...', ''], ['Individual Text Conversation', 'individual'], ['Opt In Response', 'opt_in'], ['Keyword Campaign', 'campaign']];

            $item = new stdClass();
            $item->key = 'tags';
            $select_tags = $this->cptWidgetTags('', $item, $setting['tags'], false);

            $item = new stdClass();
            $item->key = 'keywords';
            $select_keywords = $this->cptWidgetKeywords('', $item, $setting['keywords'], false);

            include 'views/admin_setting_edit.php';
        } else {
            $nonce_setting_action = wp_create_nonce($this->key_ . 'setting_action');

            if(isset($_GET['paged']) && is_numeric($_GET['paged'])) {
                $page = $_GET['paged'];
            } else {
                $page = 1;
            }
            $settings = [];

            $settings['total_pages'] = 1;
            $settings['total'] = 0;
            $settings['current_page'] = 1;

            $settings['items'] = get_option($this->key_ . 'settings');
            if(!isset($settings['items']['banner_bottom']) || !is_array($settings['items']['banner_bottom'])) {
                $settings['items']['banner_bottom'] = [
                    'title' => 'Banner On Bottom',
                    'enabled' => 'No'
                ];
            }
            if(!isset($settings['items']['banner_top']) || !is_array($settings['items']['banner_top'])) {
                $settings['items']['banner_top'] = [
                    'title' => 'Banner On Top',
                    'enabled' => 'No'
                ];
            }
            if(!isset($settings['items']['popup']) || !is_array($settings['items']['popup'])) {
                $settings['items']['popup'] = [
                    'title' => 'Popup',
                    'enabled' => 'No'
                ];
            }
            if(!isset($settings['items']['chat']) || !is_array($settings['items']['chat'])) {
                $settings['items']['chat'] = [
                    'title' => 'Chat',
                    'enabled' => 'No'
                ];
            }

            include 'views/admin_settings.php';
        }
    }

    // Delete a widget.
    public function ajaxDelete() {
        $output = [];
        $output['status'] = 'error';
        $output['messages'] = [];
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'delete')) {
            if(!isset($_POST['id']) || $_POST['id'] == '') {
                $pass = false;
                $output['messages'][] = 'There was a problem getting the ID.';
            }

            if($pass) {
                $id = sanitize_text_field($_POST['id']);

                // Delete
                wp_delete_post($id, true);

                $output['status'] = 'success';
                $output['messages'] = ['The items have been successfully deleted.'];
                $output['reload'] = true;
            }
        } else {
            $output['status'] = 'error';
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // Disconnect the TextSanity OAuth connection.
    public function ajaxDisconnect() {
        $output = []; 
        $output['status'] = 'error';
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'disconnect')) {
            update_option($this->key_ . 'refresh_token', '');

            $output['status'] = 'success';
        }   
        echo json_encode($output);
        exit;
    } 

    // Handle front end forms.
    public function ajaxFront() {
        global $textSanityAPI;

        $output = [];
        $output['status'] = 'error';
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'front')) {
            if(!isset($_POST['phone']) || $_POST['phone'] == '') {
                $output['messages'] = ['Please enter a valid phone number.'];
                $pass = false;
            }
            if(!isset($_POST['type']) || !in_array($_POST['type'], ['banner_top', 'banner_bottom', 'chat', 'popup', 'widget'])) {
                $output['messages'] = ['There was a problem processing the form.'];
                $pass = false;
            }
            if(!isset($_POST['widget_id']) || !is_numeric($_POST['widget_id'])) {
                $output['messages'] = ['There was a problem processing this form.'];
                $pass = false;
            }

            if($pass) {
                $widget_id = sanitize_text_field($_POST['widget_id']);
                $type = sanitize_text_field($_POST['type']);

                $setting = [];
                $setting['type'] = get_post_meta($widget_id, 'type', true);
                $setting['thank_you'] = get_post_meta($widget_id, 'thank_you', true);
                $setting['tags'] = get_post_meta($widget_id, 'tags', true);
                $setting['keywords'] = get_post_meta($widget_id, 'keywords', true);
                $setting['message'] = get_post_meta($widget_id, 'message', true);
            }

            if($pass) {
                $phone = sanitize_text_field($_POST['phone']);

                // Make API call.
                if($setting['type'] == 'individual') {
                    $pass = $textSanityAPI->sendMessage($phone, $setting['message'], $setting['tags']);
                } elseif($setting['type'] == 'opt_in') {
                    $pass = $textSanityAPI->optInMessage($phone, $setting['tags']);
                } elseif($setting['type'] == 'campaign') {
                    $pass = $textSanityAPI->initiateCampaign($phone, $setting['keywords']);
                }

                if($pass) {
                    $output['status'] = 'success';  
                    if(isset($setting['thank_you']) && $setting['thank_you']) {
                        $output['messages'] = [$setting['thank_you']];
                    } else {
                        $output['messages'] = ['Thank you.'];
                    }
                }
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // Save setting submissions (not currently used).
    public function ajaxSettingUpdate() {
        $output = [];
        $output['status'] = 'error';
        $pass = true;
        if(wp_verify_nonce($_POST['_wpnonce'], $this->key_ . 'setting_update')) {
            if(!isset($_POST['key']) || !in_array($_POST['key'], ['banner_top', 'banner_bottom', 'chat', 'popup', 'widget'])) {
                $output['messages'] = ['There was a problem processing the form type. Please reload the page and try again.'];
                $pass = false;
            }
            if(!isset($_POST['type']) || !in_array($_POST['type'], ['individual', 'opt_in', 'campaign'])) {
                $output['messages'] = ['Please select a Type.'];
                $pass = false;
            }

            if($pass) {
                $type = sanitize_text_field($_POST['type']);

                if($type == 'individual') {
                    if(!isset($_POST['message']) || $_POST['message'] == '' ) {
                        $output['messages'] = ['Please enter a Message to be sent with the initial submission.'];
                        $pass = false;
                    }
                } elseif($type == 'opt_in') {
                    // Tag not required
                    //if(!isset($_POST['tags']) || $_POST['tags'] == '' ) {
                    //$output['messages'] = ['Please enter a Tag to apply to the submission.'];
                    //$pass = false;
                    //}
                } elseif($type == 'campaign') {
                    if(!isset($_POST['keywords']) || $_POST['keywords'] == '' ) {
                        $output['messages'] = ['Please enter a Keyword to apply to the submission.'];
                        $pass = false;
                    }
                }
            }

            if($pass) {
                $_POST = stripslashes_deep($_POST);

                $titles = [];
                $titles['banner_bottom'] = 'Banner On Bottom';
                $titles['banner_top'] = 'Banner On Top';
                $titles['chat'] = 'Chat';
                $titles['popup'] = 'Popup';

                $key = sanitize_text_field($_POST['key']);
                if(isset($_POST['enabled']) && $_POST['enabled'] == 'yes') {
                    $enabled = 'yes';
                } else {
                    $enabled = 'no';
                }  
                $keywords = sanitize_text_field($_POST['keywords']);
                $tags = sanitize_text_field($_POST['tags']);
                $message = sanitize_text_field($_POST['message']);
                $popup_delay = sanitize_text_field($_POST['popup_delay']);
                $color = sanitize_text_field($_POST['color']);
                $description = sanitize_textarea_field($_POST['description']);
                $thank_you = sanitize_textarea_field($_POST['thank_you']);

                if(!is_numeric($popup_delay)) {
                    $popup_delay = 0;
                }

                $settings = get_option($this->key_ . 'settings');
                if(!is_array($settings)) {
                    $settings = [];
                }
                if(!isset($settings[$key])) {
                    $settings[$key] = [];
                    $settings[$key]['title'] = '';
                    $settings[$key]['enabled'] = 'no';
                    $settings[$key]['type'] = '';
                    $settings[$key]['keywords'] = '';
                    $settings[$key]['tags'] = '';
                    $settings[$key]['message'] = '';
                    $settings[$key]['popup_delay'] = 0;
                    $settings[$key]['color'] = '#007aff';
                    $settings[$key]['description'] = '';
                    $settings[$key]['thank_you'] = '';
                }

                $settings[$key]['title'] = $titles[$key];
                $settings[$key]['enabled'] = $enabled;
                $settings[$key]['type'] = $type;
                $settings[$key]['keywords'] = $keywords;
                $settings[$key]['tags'] = $tags;
                $settings[$key]['message'] = $message;
                $settings[$key]['popup_delay'] = $popup_delay;
                $settings[$key]['color'] = $color;
                $settings[$key]['description'] = $description;
                $settings[$key]['thank_you'] = $thank_you;

                update_option($this->key_ . 'settings', $settings);

                $output['status'] = 'success';  
                $output['messages'] = ['All items have been updated.'];
                $output['redirect'] = true;
            }  
        } else {
            $output['status'] = 'error';    
            $output['messages'] = ['There was a problem processing the request. Please reload the page and try again.'];
        }
        echo json_encode($output);
        exit;
    }

    // Ensures the API settings have been saved. Otherwise, redirects user to the connection page.
    // Also handles any bulk actions.
    public function adminTemplateRedirect() {
        global $pagenow, $textSanityAPI;

        if($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == $this->key_ . 'settings') {
            if(!$textSanityAPI->isConnected()) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'connection' . '&msg=access'));
                exit;
            }
        }

        if($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $this->key_ . 'widget') {
            if(!$textSanityAPI->isConnected()) {
                wp_redirect(admin_url('/admin.php?page=' . $this->key_ . 'connection' . '&msg=access'));
                exit;
            }
        }
    }

    // Output banner top widget if active.
    public function bodyOpen() {
        if($this->show_banner_top) {
            $setting = $this->settings['banner_top'];
            $logo_url = plugins_url('images/textsanity_logo_150px.png', __FILE__);
            include 'views' . DIRECTORY_SEPARATOR . 'front_banner_top' . '.php';
        }
    }

    // Build drop down of keywords from API.
    public function cptWidgetKeywords($html, $item, $value, $include_div = true) {
        global $textSanityAPI;

        $keywords = $textSanityAPI->getKeywords();
        $options = [];
        $options[] = ['Please Select...', ''];
        foreach($keywords as $keyword) {
            //$options[] = [$keyword['name'], $keyword['id']];
            //$options[] = [$keyword['name'], $keyword['name']];
            $options[] = [$keyword['name'], $keyword['keyword']];
        }

        $html = '';
        if($include_div) {
            $html .= '<div>';
            $html .= '<label>' . esc_html($item->title) . '</label>';
            $html .= '<br>';
        }
        $html .= '<select name="' . esc_attr($item->key) . '">';
        foreach($options as $option) {
            if($option[1] == $value) {
                $html .= '<option value="' . esc_attr($option[1]) . '" selected>' . esc_html($option[0]) . '</option>';
            } else {
                $html .= '<option value="' . esc_attr($option[1]) . '">' . esc_html($option[0]) . '</option>';
            }
        }
        $html .= '</select>';
        if($include_div) {
            $html .= '<br>';
            $html .= '<br>';
            $html .= '</div>';
        }

        return $html;
    }

    // Build a drop down of tags from API.
    public function cptWidgetTags($html, $item, $value, $include_div = true) {
        global $textSanityAPI;

        $tags = $textSanityAPI->getTags();
        $options = [];
        $options[] = ['Please Select...', ''];
        foreach($tags as $tag) {
            //$options[] = [$tag['name'], $tag['id']];
            $options[] = [$tag['name'], $tag['name']];
        }

        $html = '';
        if($include_div) {
            $html .= '<div>';
            $html .= '<label>' . esc_html($item->title) . '</label>';
            $html .= '<br>';
        }
        $html .= '<select name="' . esc_attr($item->key) . '">';
        foreach($options as $option) {
            if($option[1] == $value) {
                $html .= '<option value="' . esc_attr($option[1]) . '" selected>' . esc_html($option[0]) . '</option>';
            } else {
                $html .= '<option value="' . esc_attr($option[1]) . '">' . esc_html($option[0]) . '</option>';
            }
        }
        $html .= '</select>';
        if($include_div) {
            $html .= '<br>';
            $html .= '<br>';
            $html .= '</div>';
        }

        return $html;
    }

    // Load front end assets.
    public function enqueueScripts() {
        global $post;

        $this->settings = [];

        // Get top widgets.
        $banner_top_ids = get_posts([
            'post_type' => $this->key_ . 'widget',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'enabled', 'value' => 'yes'],
                ['key' => 'position', 'value' => 'banner_top']
            ],
            'meta_key' => 'location',
            'orderby' => 'location',
            'order' => 'DESC'
        ]);

        foreach($banner_top_ids as $widget_id) {
            $page_id = get_post_meta($widget_id, 'location', true);
            if($page_id && isset($post->ID) && $post->ID == $page_id) {
                $this->settings['banner_top'] = $this->getSetting($widget_id);
                break;
            } elseif(!$page_id) {
                $this->settings['banner_top'] = $this->getSetting($widget_id);
                break;
            }
        }


        // Get bottom widgets.
        $banner_bottom_ids = get_posts([
            'post_type' => $this->key_ . 'widget',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'enabled', 'value' => 'yes'],
                ['key' => 'position', 'value' => 'banner_bottom']
            ],
            'meta_key' => 'location',
            'orderby' => 'location',
            'order' => 'DESC'
        ]);

        foreach($banner_bottom_ids as $widget_id) {
            $page_id = get_post_meta($widget_id, 'location', true);
            if($page_id && isset($post->ID) && $post->ID == $page_id) {
                $this->settings['banner_bottom'] = $this->getSetting($widget_id);
                break;
            } elseif(!$page_id) {
                $this->settings['banner_bottom'] = $this->getSetting($widget_id);
                break;
            }
        }

        // Get chat widgets.
        $chat_ids = get_posts([
            'post_type' => $this->key_ . 'widget',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'enabled', 'value' => 'yes'],
                ['key' => 'position', 'value' => 'chat']
            ],
            'meta_key' => 'location',
            'orderby' => 'location',
            'order' => 'DESC'
        ]);

        foreach($chat_ids as $widget_id) {
            $page_id = get_post_meta($widget_id, 'location', true);
            if($page_id && isset($post->ID) && $post->ID == $page_id) {
                $this->settings['chat'] = $this->getSetting($widget_id);
                break;
            } elseif(!$page_id) {
                $this->settings['chat'] = $this->getSetting($widget_id);
                break;
            }
        }

        // Get popup widgets.
        $popup_ids = get_posts([
            'post_type' => $this->key_ . 'widget',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'enabled', 'value' => 'yes'],
                ['key' => 'position', 'value' => 'popup']
            ],
            'meta_key' => 'location',
            'orderby' => 'location',
            'order' => 'DESC'
        ]);

        foreach($popup_ids as $widget_id) {
            $page_id = get_post_meta($widget_id, 'location', true);
            if($page_id && isset($post->ID) && $post->ID == $page_id) {
                $this->settings['popup'] = $this->getSetting($widget_id);
                break;
            } elseif(!$page_id) {
                $this->settings['popup'] = $this->getSetting($widget_id);
                break;
            }
        }

        if(isset($this->settings['banner_top'])) {
            $this->show_banner_top = true;
        }
        if(isset($this->settings['banner_bottom'])) {
            $this->show_banner_bottom = true;
        }
        if(isset($this->settings['chat'])) {
            $this->show_chat = true;
        }
        if(isset($this->settings['popup'])) {
            $this->show_popup = true;
        }


        if(
            $this->show_banner_top
            || $this->show_banner_bottom
            || $this->show_chat
            || $this->show_popup
        ) {
            // Make sure dashicons are loaded.
            wp_enqueue_style('dashicons');
            wp_enqueue_style('txsy_front', plugins_url('css/front.css', __FILE__), [], $this->version);
            wp_enqueue_script('txsy_front', plugins_url('js/front.js', __FILE__), ['jquery'], $this->version);
        } elseif(isset($post->post_content) && has_shortcode($post->post_content, 'textsanity')) {
            // Make sure dashicons are loaded.
            wp_enqueue_style('dashicons');
            wp_enqueue_style('txsy_front', plugins_url('css/front.css', __FILE__), [], $this->version);
            wp_enqueue_script('txsy_front', plugins_url('js/front.js', __FILE__), ['jquery'], $this->version);
        }
    }

    // Output needed widget code in the footer.
    public function footer() {
        $logo_url = plugins_url('images/textsanity_logo_150px.png', __FILE__);
        if($this->show_banner_bottom) {
            $setting = $this->settings['banner_bottom'];
            include 'views' . DIRECTORY_SEPARATOR . 'front_banner_bottom' . '.php';
        }

        if($this->show_chat) {
            $setting = $this->settings['chat'];
            $temp = $setting['chat_popup_auto'];
            if($temp == 'yes') {
                $chat_popup_auto = 'true';
            } else {
                $chat_popup_auto = 'false';
            }
            $chat_popup_delay = $setting['chat_popup_delay'];
            include 'views' . DIRECTORY_SEPARATOR . 'front_chat' . '.php';
        } else {
            $chat_popup_auto = 'false';
            $chat_popup_delay = 0;
        }

        if($this->show_popup) {
            $setting = $this->settings['popup'];
            $popup_delay = $setting['popup_delay'];
            include 'views' . DIRECTORY_SEPARATOR . 'front_popup' . '.php';
        } else {
            $popup_delay = 0;
        }

        $nonce_front = wp_create_nonce($this->key_ . 'front');

        $htm = '';
        $htm .= "\n";
        $htm .= '<script>';
        $htm .= "\n";
        $htm .= 'var txsy_ajaxurl = ' . '"' . admin_url('admin-ajax.php') . '";';
        $htm .= "\n";
        $htm .= 'var txsy_nonce_front = ' . '"' . $nonce_front . '";';
        $htm .= "\n";
        $htm .= 'var txsy_popup_delay = ' . $popup_delay . ';';
        $htm .= "\n";
        $htm .= 'var txsy_chat_popup_auto = ' . $chat_popup_auto . ';';
        $htm .= "\n";
        $htm .= 'var txsy_chat_popup_delay = ' . $chat_popup_delay . ';';
        $htm .= "\n";
        $htm .= '</script>';
        $htm .= "\n";

        echo $htm;
    }

    // Output shortcode content.
    public function shortcodeTextsanity($atts) {
        if(!isset($atts['id'])) {
            $atts['id'] = 0;
        }

        $logo_url = plugins_url('images/textsanity_logo_150px.png', __FILE__);

        $widget_id = $atts['id'];
        $setting = [];
        $setting['description'] = get_post_meta($widget_id, 'description', true);
        $setting['enabled'] = get_post_meta($widget_id, 'enabled', true);

        $no_style = get_post_meta($widget_id, 'no_style', true);
        if($no_style == 'no') {
            $setting['style_class'] = ' ' . $this->key_ . 'style';
        } else {
            $setting['style_class'] = '';
        }

        ob_start();
        if($setting['enabled'] == 'yes') {
            $include_file = locate_template('plugin/textsanity' . '.php');
            if($include_file) {
                include $include_file;
            } else {
                include 'views' . DIRECTORY_SEPARATOR . 'front_widget' . '.php';
            }   
        }
        $output = ob_get_clean();

        return $output;
    }

    // Set up various hooks.
    public function init() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
        add_action('admin_menu', [$this, 'adminMenu']);

        add_action('wp_ajax_' . $this->key_ . 'delete', [$this, 'ajaxDelete']);
        add_action('wp_ajax_' . $this->key_ . 'disconnect', [$this, 'ajaxDisconnect']);
        add_action('wp_ajax_' . $this->key_ . 'setting_update', [$this, 'ajaxSettingUpdate']);

        add_action('wp_ajax_' . $this->key_ . 'front', [$this, 'ajaxFront']);
        add_action('wp_ajax_nopriv_' . $this->key_ . 'front', [$this, 'ajaxFront']);

        add_shortcode('textsanity', [$this, 'shortcodeTextsanity']);

        add_action('wp_body_open', [$this, 'bodyOpen']);
        add_action('wp_footer', [$this, 'footer'], 99);

        add_action('admin_init', [$this, 'adminTemplateRedirect']);

        add_filter($this->key_ . 'widget_tags', [$this, 'cptWidgetTags'], 10, 3);
        add_filter($this->key_ . 'widget_keywords', [$this, 'cptWidgetKeywords'], 10, 3);
    }

    // Initialize the WordPress widget code.
    public function widgetsInit() {         
        register_widget('TextSanity_Widget');
    }

    // Get the settings for a specific widget.
    public function getSetting($widget_id) {
        $setting = [];
        $setting['widget_id'] = $widget_id;
        $setting['description'] = get_post_meta($widget_id, 'description', true);
        $setting['popup_delay'] = get_post_meta($widget_id, 'popup_delay', true);
        $setting['background_color'] = get_post_meta($widget_id, 'background_color', true);
        $setting['text_color'] = get_post_meta($widget_id, 'text_color', true);
        $setting['chat_popup_auto'] = get_post_meta($widget_id, 'chat_popup_auto', true);
        $setting['chat_popup_delay'] = get_post_meta($widget_id, 'chat_popup_delay', true);

        $no_style = get_post_meta($widget_id, 'no_style', true);
        if($no_style == 'no') {
            $setting['style_class'] = ' ' . $this->key_ . 'style';
        } else {
            $setting['style_class'] = '';
        }

        return $setting;
    }

    // Create the main menu page with a custom icon.
    public function getIcon() {
        add_menu_page('TextSanity', 'TextSanity', 'manage_options', $this->key_ . 'settings', false, 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" id="svg2" xml:space="preserve" width="480" height="480" viewBox="0 0 480 480" sodipodi:docname="TextSanity_Brand.ai"><metadata id="metadata8"><rdf:RDF><cc:Work rdf:about=""><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" /></cc:Work></rdf:RDF></metadata><defs id="defs6"><clipPath clipPathUnits="userSpaceOnUse" id="clipPath22"><path d="M 0,360 H 360 V 0 H 0 Z" id="path20" /></clipPath></defs><sodipodi:namedview pagecolor="#ffffff" bordercolor="#666666" borderopacity="1" objecttolerance="10" gridtolerance="10" guidetolerance="10" inkscape:pageopacity="0" inkscape:pageshadow="2" inkscape:window-width="640" inkscape:window-height="480" id="namedview4" /><g id="g10" inkscape:groupmode="layer" inkscape:label="TextSanity_Brand" transform="matrix(1.3333333,0,0,-1.3333333,0,480)"><g id="g12" transform="translate(112.3684,256.2823)"><path d="m 0,0 h -20.85 l 2.607,12.286 H 37.231 L 34.625,0 H 14.706 L 3.351,-54.172 h -14.706 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path14" /></g><g id="g16"><g id="g18" clip-path="url(#clipPath22)"><g id="g24" transform="translate(169.8918,231.3369)"><path d="M 0,0 C 0,4.934 -1.21,10.238 -8.936,10.238 -16.382,10.238 -20.105,5.398 -22.06,0 Z m -23.456,-8.377 c -0.093,-1.21 -0.093,-2.048 -0.093,-2.792 0,-5.585 3.444,-9.401 10.611,-9.401 5.305,0 7.912,3.537 9.866,6.608 H 10.146 C 5.957,-24.759 -0.931,-30.529 -14.8,-30.529 c -12.845,0 -21.408,7.352 -21.408,21.035 0,15.358 10.146,29.692 26.527,29.692 13.404,0 22.433,-7.074 22.433,-21.036 0,-2.605 -0.279,-5.212 -0.745,-7.539 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path26" /></g><g id="g28" transform="translate(214.2903,227.1485)"><path d="M 0,0 12.193,-25.038 H -1.769 L -8.75,-9.215 -21.595,-25.038 H -36.58 l 22.339,25.783 -10.891,22.339 h 14.149 L -5.678,9.215 5.212,23.084 h 14.8 z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path30" /></g><g id="g32" transform="translate(259.6204,250.2325)"><path d="M 0,0 H 9.494 L 7.446,-8.843 h -9.401 l -4.933,-23.921 c -0.186,-1.024 -0.372,-1.862 -0.372,-2.234 0,-3.444 2.513,-3.537 4.467,-3.537 1.583,0 3.165,0.093 4.747,0.279 l -2.233,-10.238 c -2.7,-0.279 -5.492,-0.466 -8.284,-0.466 -6.144,0 -12.752,1.955 -12.566,9.773 0,1.21 0.279,2.886 0.651,4.561 l 5.399,25.783 h -8.656 L -21.687,0 h 8.47 l 2.979,14.613 H 2.979 Z" style="fill:#000000;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path34" /></g><g id="g36" transform="translate(121.303,180.0938)"><path d="m 0,0 c -0.08,10.048 -5.386,13.264 -15.192,13.264 -7.074,0 -14.629,-3.296 -14.629,-11.495 0,-6.11 4.822,-8.039 9.645,-9.486 l 6.11,-1.848 c 7.877,-2.412 15.433,-4.984 15.433,-14.63 0,-5.627 -3.537,-17.685 -22.186,-17.685 -12.862,0 -22.266,6.19 -21.623,20.096 h 5.466 c -0.562,-11.173 6.109,-15.434 16.559,-15.434 7.476,0 16.318,3.698 16.318,12.299 0,8.119 -7.878,9.565 -14.228,11.415 l -5.627,1.688 c -6.672,1.929 -11.334,5.948 -11.334,13.343 0,11.495 10.129,16.399 20.337,16.399 11.656,0 21.06,-4.662 20.418,-17.926 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path38" /></g><g id="g40" transform="translate(157.553,161.6055)"><path d="m 0,0 -0.161,0.161 c -1.608,-2.01 -6.591,-2.171 -9.163,-2.331 -6.19,-0.482 -16.399,-0.804 -16.399,-9.485 0,-5.306 4.421,-7.476 9.244,-7.476 8.119,0 13.263,4.903 14.872,12.138 z m -25.08,6.913 c 1.367,9.405 8.601,13.585 17.524,13.585 5.626,0 14.388,-1.93 14.388,-9.244 C 6.832,7.234 5.064,0.965 4.26,-2.732 2.411,-12.058 1.527,-14.228 1.527,-16.72 c 0,-1.446 1.447,-1.687 2.653,-1.687 0.723,0 1.286,0.08 2.009,0.16 l -0.723,-4.019 c -1.125,-0.241 -2.814,-0.402 -4.18,-0.402 -2.813,0 -4.341,1.769 -4.421,4.502 0,0.723 0.08,1.527 0.16,2.25 l -0.16,0.161 c -2.894,-4.823 -8.601,-7.637 -14.388,-7.637 -7.557,0 -13.264,3.376 -13.264,11.496 0,8.761 6.993,11.575 14.389,12.861 5.305,0.804 9.967,0.402 13.182,1.206 3.296,0.804 4.985,2.652 4.985,8.118 0,4.903 -4.904,5.948 -8.762,5.948 -6.431,0 -11.978,-2.411 -13.023,-9.324 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path42" /></g><g id="g44" transform="translate(176.1194,180.8975)"><path d="M 0,0 H 4.662 L 3.216,-7.556 h 0.16 c 3.136,4.904 8.602,8.762 14.791,8.762 7.234,0 12.54,-3.216 12.54,-11.173 0,-1.287 -0.241,-2.974 -0.644,-4.904 l -5.707,-26.607 h -5.064 l 5.788,26.768 c 0.321,1.286 0.563,2.814 0.563,4.18 0,5.467 -4.18,7.475 -8.682,7.475 -7.315,0 -13.986,-6.591 -16.237,-17.121 l -4.582,-21.302 h -5.064 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path46" /></g><g id="g48" transform="translate(221.6135,196.8135)"><path d="M 0,0 H 5.063 L 3.296,-8.118 h -5.065 z m -3.457,-15.916 h 5.064 l -8.922,-41.478 h -5.064 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path50" /></g><g id="g52" transform="translate(227.3196,180.8975)"><path d="m 0,0 h 7.477 l 2.652,12.54 h 5.064 L 12.54,0 h 8.279 L 20.016,-4.26 H 11.575 L 5.868,-30.867 c -0.401,-1.929 -0.562,-2.653 -0.562,-3.939 0,-1.447 0.723,-2.893 2.652,-2.893 2.01,0 3.939,0.16 5.948,0.482 l -0.884,-4.422 c -1.688,-0.16 -3.456,-0.321 -5.144,-0.321 -3.778,0 -7.637,0.965 -7.637,5.948 0,0.885 0.161,2.412 0.564,4.261 L 6.512,-4.26 h -7.476 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path54" /></g><g id="g56" transform="translate(243.0735,128.5684)"><path d="m 0,0 c 1.446,-0.161 2.894,-0.241 4.34,-0.241 3.055,0 5.225,2.331 6.512,4.501 l 3.777,6.592 -7.636,41.477 h 5.305 l 5.948,-35.208 h 0.161 l 19.132,35.208 h 5.466 L 13.504,1.045 c -2.09,-3.698 -5.707,-5.547 -10.048,-5.547 -1.447,0 -2.975,0.322 -4.421,0.402 z" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path58" /></g><g id="g60" transform="translate(280.0388,313.5235)"><path d="m 0,0 c 6.52,0.111 13.142,-1.809 18.618,-5.48 5.493,-3.645 9.875,-8.976 12.372,-15.155 1.257,-3.082 2.038,-6.371 2.313,-9.693 0.089,-0.819 0.093,-1.687 0.126,-2.47 l 0.035,-2.028 0.07,-4.053 0.28,-16.217 1.167,-64.866 1.292,-64.866 0.093,-4.054 0.044,-2.041 c -0.007,-0.853 -0.002,-1.708 -0.034,-2.562 -0.141,-3.419 -0.679,-6.838 -1.6,-10.151 -1.835,-6.635 -5.206,-12.805 -9.691,-17.944 -8.902,-10.347 -22.31,-16.649 -36.179,-16.871 l 9.568,15.515 c 1.149,-2.237 2.138,-4.363 3.161,-6.512 l 3.062,-6.444 6.124,-12.885 8.291,-17.445 -19.187,2.21 -52.029,5.993 -5.015,0.578 -2.745,4.235 -12.537,19.338 8.981,-4.883 -73.199,1.14 -36.599,0.539 -9.15,0.135 -4.575,0.067 c -1.558,0.029 -2.9,0.016 -4.935,0.105 -7.523,0.371 -14.972,2.652 -21.387,6.557 -6.431,3.89 -11.831,9.372 -15.649,15.8 -1.888,3.223 -3.407,6.668 -4.465,10.249 -1.046,3.584 -1.657,7.29 -1.817,10.998 l -0.024,1.39 c -0.006,0.457 -0.016,0.96 -0.007,1.306 l 0.028,2.287 0.055,4.575 0.111,9.151 0.222,18.3 0.852,73.199 0.357,36.6 0.09,9.15 0.046,4.591 c 0.022,1.876 0.205,3.755 0.536,5.608 1.313,7.429 5.272,14.311 10.931,19.192 5.627,4.923 12.999,7.821 20.435,8.069 l 1.391,0.024 1.16,-0.01 2.288,-0.022 4.575,-0.045 9.15,-0.089 36.6,-0.358 c 24.4,-0.235 48.8,-0.521 73.2,-0.581 24.4,0.02 48.8,0.431 73.2,0.824 m 0,-11.323 c -24.4,0.393 -48.8,0.804 -73.2,0.824 -24.4,-0.061 -48.8,-0.346 -73.2,-0.581 l -36.6,-0.358 -9.15,-0.089 -4.575,-0.045 -2.288,-0.022 -1.126,-0.013 -0.896,-0.035 c -4.76,-0.263 -9.35,-2.186 -12.85,-5.374 -3.517,-3.163 -5.887,-7.52 -6.602,-12.131 -0.18,-1.154 -0.272,-2.322 -0.263,-3.496 l 0.043,-4.56 0.09,-9.15 0.358,-36.6 0.851,-73.199 0.222,-18.3 0.111,-9.151 0.055,-4.575 0.028,-2.287 c 10e-4,-0.418 0.02,-0.676 0.032,-0.981 l 0.034,-0.897 c 0.168,-2.383 0.62,-4.726 1.336,-6.982 0.73,-2.252 1.751,-4.403 2.99,-6.412 2.52,-4 6.022,-7.373 10.099,-9.695 4.072,-2.333 8.679,-3.61 13.347,-3.724 1.014,-0.023 2.723,0.004 4.214,0.024 l 4.575,0.065 9.15,0.129 36.601,0.513 73.201,1.09 5.76,0.086 3.222,-4.969 12.537,-19.339 -7.76,4.813 52.029,-5.992 -10.896,-15.234 -6.068,12.769 -3.035,6.385 c -1.011,2.126 -2.021,4.292 -3.038,6.264 l -8.274,16.055 17.842,-0.538 c 3.902,-0.118 7.845,0.6 11.524,2.106 3.683,1.492 7.065,3.807 9.82,6.689 2.771,2.874 4.871,6.337 6.102,10.072 0.619,1.868 1.025,3.806 1.205,5.783 l 0.093,1.489 0.048,2.014 0.092,4.054 1.292,64.866 1.167,64.866 0.28,16.217 0.071,4.053 0.035,2.028 c -0.011,0.566 0.019,1.052 -0.025,1.579 -0.094,2.069 -0.502,4.12 -1.216,6.07 -1.412,3.904 -4.061,7.405 -7.493,9.866 -3.426,2.478 -7.606,3.872 -11.901,3.96" style="fill:#007aff;fill-opacity:1;fill-rule:nonzero;stroke:none" id="path62" /></g></g></g></g></svg>'));
    }
}

// Creates instances of the API class, CPT class, and the main plugin class.
$textSanityAPI = new TextSanity_API();
$textSanityCPT = new TextSanity_CPT();
$textSanity = new TextSanity();
