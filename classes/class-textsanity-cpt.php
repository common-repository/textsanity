<?php
defined('ABSPATH') || exit;

class TextSanity_CPT {
	public $key_ = 'txsy_';
	public $full_key = 'txsy_widget';
	public $cap = 'manage_options';
	public $plural = 'Widgets';
	public $singular = 'Widget';

    // The class constructor.
    public function __construct() {
        add_action('init', [$this, 'init']);
    }

    // Initialize hooks for the Custom Post Type.
	public function init() {
        $this->register();

		add_filter('manage_edit-' . $this->full_key . '_columns', array($this, 'wpColumns')) ;
		add_action('manage_' . $this->full_key . '_posts_custom_column', array($this, 'wpColumnContent'), 10, 2 );
		add_action('save_post_' . $this->full_key, [$this, 'wpSave']);

		// Remove hover edits
		// From: https://wordpress.stackexchange.com/a/14982
		add_filter('post_row_actions', array($this, 'wpPostRowActions'), 10, 2);
		add_filter('page_row_actions', array($this, 'wpPostRowActions'), 10, 2);

		// Remove date filters (and other filters) as needed
		add_action('admin_init', array($this, 'wpAdminInit'));

        add_filter('views_edit-' . $this->full_key, '__return_empty_array', 99);

		add_action('admin_enqueue_scripts', array($this, 'wpAdminEnqueueScripts'));
		add_action('admin_print_scripts', array($this, 'wpAdminPrintScripts'));

        add_filter('bulk_actions-edit-' . $this->full_key, array($this, 'wpBulkActions'));

        add_filter('handle_bulk_actions-edit-' . $this->full_key, array($this, 'wpBulkActionsHandle'), 10, 3);
    }

    // Register the CPT.
    public function register() {
		$capabilities =  array(
			'edit_post'          => $this->cap, 
			'read_post'          => $this->cap, 
			'delete_posts'        => $this->cap,
			'edit_posts'         => $this->cap,
			'edit_others_posts'  => $this->cap,
			'publish_posts'      => $this->cap,
			'read_private_posts' => $this->cap,
			'create_posts'       => $this->cap,
			'delete_post'        => $this->cap,
		);  
		$labels = array(
			'name'               => __( 'Texting ' . $this->plural ),
			'singular_name'      => __( $this->singular ),
			'add_new'            => __( 'Add New' ),
			'add_new_item'       => __( 'Add New ' . $this->singular ),
			'edit_item'          => __( 'Edit ' . $this->singular ),
			'new_item'           => __( 'New ' . $this->singular ),
			'all_items'          => __( 'Texting ' . $this->plural ),
			'view_item'          => __( 'View ' . $this->singular ),
			'search_items'       => __( 'Search ' . $this->plural ),
			'not_found'          => __( 'No ' . $this->plural . ' found' ),
			'not_found_in_trash' => __( 'No ' . $this->plural . ' found in the Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => 'Texting ' . $this->plural
		);

		$args = array(
			'labels'        => $labels,
			'description'   => 'A list of ' . $this->plural . '.',
			'supports'      => ['title'],
			'public' => false,
			'show_ui' => true,
			//'show_in_menu' => true,
			'show_in_menu' => 'txsy_settings',
			//'show_in_menu' => false,
			'capabilities' => $capabilities,
			'hierarchical' => true,
			'register_meta_box_cb' => array($this, 'wpMetaBoxes')
		);

		register_post_type($this->full_key, $args);
    }

    // Remove date filter
    public function wpAdminInit() {
        global $typenow;
        if($typenow == $this->full_key) {
            add_filter('months_dropdown_results', '__return_empty_array');
        }
    }

    // Set up the needed assets in the admin.
	public function wpAdminEnqueueScripts($hook_suffix) {
		global $post_type;

		if($post_type == $this->full_key) {
            wp_deregister_script('autosave');

            wp_enqueue_style('wp-color-picker');
			wp_enqueue_script($this->full_key . '_type', plugins_url('../js/admin_cpt_type.js', __FILE__), array('jquery', 'wp-color-picker'));

			wp_enqueue_style($this->full_key . '_type', plugins_url('../css/admin_cpt_type.css', __FILE__), []);
		}
	}

    // Hide the search box on the CPT list page.
	public function wpAdminPrintScripts() {
		global $post_type;

		if($post_type == $this->full_key) {
			$css = "";
			$css .= "<style>\n";
            $css .= "#posts-filter .search-box { display: none; } \n";
			$css .= "</style>\n";
			echo $css;
		}
	}

    public function wpBulkActions($bulk_actions) {
        $bulk_actions = [];
        $bulk_actions['txsy_delete'] = __('Delete', 'txsy');

        return $bulk_actions;
    }

    public function wpBulkActionsHandle($redirect_to, $doaction, $post_ids) {
        if($doaction !== 'txsy_delete') {
            return $redirect_to;
        }
        foreach($post_ids as $post_id) {
            wp_delete_post($post_id, true);
        }
        return $redirect_to;
    }

    // Set up the columns.
	public function wpColumns() {
		$columns = [];
		$columns['cb'] = 'cb';
		$columns['title'] = 'Title';
		$columns['enabled'] = 'Enabled';
		$columns['type'] = 'Type';
		$columns['widget_code'] = 'Widget Shortcode';
		$columns['actions'] = 'Actions';

		return $columns;
	}

    // Set up the column content.
	public function wpColumnContent($column, $post_id) {
        $output = '';
        if($column == 'enabled') {
			$output = get_post_meta($post_id, $column, true);
        } elseif($column == 'type') {
			$type = get_post_meta($post_id, 'position', true);
            $types = [];
            $types['inline'] = 'Inline';
            $types['banner_bottom'] = 'Banner Bottom';
            $types['banner_top'] = 'Banner Top';
            $types['chat'] = 'Chat';
            $types['popup'] = 'Popup';
            if(isset($types[$type])) {
                $output = $types[$type];
            }
        } elseif($column == 'widget_code') {
			$type = get_post_meta($post_id, 'position', true);
            if($type == 'inline') {
                $output = '[textsanity id="' . $post_id . '"]';
            }
        } elseif($column == 'actions') {
            $nonce = wp_create_nonce($this->key_ . 'delete');
            $output = '<a href="' . admin_url('post.php?action=edit&post=' . $post_id)  . '" class="button">Edit</a>';
            $output .= ' <a class="button delete" href="#" data-id="' . $post_id . '" data-action="' . $this->key_ . 'delete' . '" data-nonce="' . $nonce . '">Delete</a>';
        }
        echo $output;
	}

    // Set up the meta boxes for the edit page.
	public function wpMetaBoxes() {
        add_meta_box(
            $this->full_key . '_details',
            'Details',
            array($this, 'wpMetaDetails'),
            $this->full_key,
            'normal',
            'default'
        );

        remove_meta_box( 'submitdiv', $this->full_key, 'side' );
        add_meta_box(
            $this->full_key . '_save',
            'Save',
            array($this, 'wpMetaSave'),       
            $this->full_key,      
            'side',
            'high'
        );
	}

    // Output the details meta box.
    public function wpMetaDetails() {
        global $post, $textSanity;

        $setting = [];
        $setting['title'] = '';
        $setting['type'] = '';
        $setting['keywords'] = '';
        $setting['tags'] = '';
        $setting['message'] = '';
        $setting['description'] = '';
        $setting['thank_you'] = '';

        $setting['enabled'] = 'yes';
        $setting['position'] = '';
        $setting['location'] = '';
        $setting['no_style'] = 'no';
        $setting['background_color'] = '#007aff';
        $setting['text_color'] = '#ffffff';
        $setting['popup_delay'] = 0;

        $setting['chat_popup_auto'] = 'no';
        $setting['chat_popup_delay'] = 0;

        foreach($setting as $key => $item) {
            if($key == 'title') {
                $setting['title'] = get_the_title($post->ID);
            } else {
                $temp = get_post_meta($post->ID, $key, true);
                if($temp) {
                    $setting[$key] = $temp;
                }
            }
        }

        $positions = [['Inline', 'inline'], ['Banner Bottom', 'banner_bottom'], ['Banner Top', 'banner_top'], ['Chat', 'chat'], ['Popup', 'popup']];

        $select_locations = wp_dropdown_pages([
            'show_option_none' => __( 'SITEWIDE' ),
            'post_type'=> 'page',
            'name' => 'location',
            'echo' => 0,
            'selected' => $setting['location'],
        ]); 

        $types = [['Please Select...', ''], ['Individual Text Conversation', 'individual'], ['Opt In Response', 'opt_in'], ['Keyword Campaign', 'campaign']];

        $item = new stdClass();
        $item->key = 'tags';
        $select_tags = $textSanity->cptWidgetTags('', $item, $setting['tags'], false);

        $item = new stdClass();
        $item->key = 'keywords';
        $select_keywords = $textSanity->cptWidgetKeywords('', $item, $setting['keywords'], false);


        $dir = plugin_dir_path(__FILE__);
        include $dir . '../views/admin_cpt_details.php';
    }

    // Adjust the save box.
    public function wpMetaSave() {
        global $post;

        $data = array();
        // From: https://gist.github.com/NiloySarker/2d1954eef3b0003d718d#file-replace-wp_submit-php-L93
        if (!in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
            $initial_save = true;
        } else {
            $initial_save = false;
        }    

        $redirect_info = [];

        // Modified from: https://gist.github.com/NiloySarker/2d1954eef3b0003d718d
?>
<div class="submitbox" id="submitpost">
         <div id="major-publishing-actions" style="background: transparent; border: 0;">
                <?php do_action( 'post_submitbox_start' ); ?>
                <a href="edit.php?post_type=<?php echo $post->post_type; ?>" class="button button-large">Cancel</a>
                 <div id="publishing-action">    
                         <span class="spinner"></span>   
                        <input name="post_status" type="hidden" id="post_status" value="publish" />
                        <input name="original_publish" type="hidden" id="original_publish" value="Update" />
                        <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="Save" />
                        <?php if($initial_save): ?>
                        <input type="hidden" name="initial_save" value="yes" />
                        <?php endif; ?>                 

                        <?php foreach($redirect_info as  $key => $val): ?>
                        <input type="hidden" name="redirect_<?php echo $key; ?>" value="<?php echo $val; ?>" />
                        <?php endforeach; ?>            
                 </div>
                 <div class="clear"></div>       
         </div>
 </div>
<?php

    }

    // Remove hover edit.
    public function wpPostRowActions($actions, $post) {
        if($post->post_type == $this->full_key) {
            unset($actions['edit']);
            unset($actions['trash']);
            $actions['inline hide-if-no-js'] = '';
        }
        return $actions;
    }

    // Handle the save event.
    public function wpSave($post_id) {
        global $wpdb;
        if(!empty($_POST)) {
            if(isset($_POST['enabled']) && $_POST['enabled'] == 'yes') {
                update_post_meta($post_id, 'enabled', 'yes');
            } else {
                update_post_meta($post_id, 'enabled', 'no');
            }

            if(isset($_POST['position'])) {
                update_post_meta($post_id, 'position', sanitize_text_field( $_POST['position']));
            }

            if(isset($_POST['location'])) {
                update_post_meta($post_id, 'location', sanitize_text_field( $_POST['location']));
            }

            if(isset($_POST['type'])) {
                update_post_meta($post_id, 'type', sanitize_text_field( $_POST['type']));
            }

            if(isset($_POST['tags'])) {
                update_post_meta($post_id, 'tags', sanitize_text_field( $_POST['tags']));
            }

            if(isset($_POST['keywords'])) {
                update_post_meta($post_id, 'keywords', sanitize_text_field( $_POST['keywords']));
            }

            if(isset($_POST['message'])) {
                update_post_meta($post_id, 'message', sanitize_textarea_field( $_POST['message']));
            }

            if(isset($_POST['background_color'])) {
                update_post_meta($post_id, 'background_color', sanitize_text_field( $_POST['background_color']));
            }

            if(isset($_POST['text_color'])) {
                update_post_meta($post_id, 'text_color', sanitize_text_field( $_POST['text_color']));
            }

            if(isset($_POST['popup_delay'])) {
                update_post_meta($post_id, 'popup_delay', sanitize_text_field( $_POST['popup_delay']));
            }

            if(isset($_POST['chat_popup_auto']) && $_POST['chat_popup_auto'] == 'yes') {
                update_post_meta($post_id, 'chat_popup_auto', 'yes');
            } else {
                update_post_meta($post_id, 'chat_popup_auto', 'no');
            }

            if(isset($_POST['chat_popup_delay'])) {
                update_post_meta($post_id, 'chat_popup_delay', sanitize_text_field( $_POST['chat_popup_delay']));
            }

            if(isset($_POST['description'])) {
                update_post_meta($post_id, 'description', sanitize_textarea_field( $_POST['description']));
            }

            if(isset($_POST['thank_you'])) {
                update_post_meta($post_id, 'thank_you', sanitize_textarea_field( $_POST['thank_you']));
            }

            if(isset($_POST['no_style']) && $_POST['no_style'] == 'yes') {
                update_post_meta($post_id, 'no_style', 'yes');
            } else {
                update_post_meta($post_id, 'no_style', 'no');
            }
        }
    }
}
