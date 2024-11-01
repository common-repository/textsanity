<?php
defined('ABSPATH') || exit;

class TextSanity_Widget extends WP_Widget {
    public $key = 'txsy';
    public $key_ = 'txsy_';

    // The class constructor.
    public function __construct() {
        parent::__construct('textsanity_widget', $name = 'TextSanity Widget', array('description' => ''));
    }

    // Output the widget content on the front end.
    public function widget($args, $instance) {
        global $textSanity;
        wp_enqueue_style('dashicons');
        wp_enqueue_style('txsy_front', plugins_url('../css/front.css', __FILE__), [], $textSanity->version);
        wp_enqueue_script('txsy_front', plugins_url('../js/front.js', __FILE__), ['jquery'], $textSanity->version);

        $widget_id = $instance['widget_id'];
        $setting = [];
        $setting['description'] = get_post_meta($widget_id, 'description', true);
        $setting['enabled'] = get_post_meta($widget_id, 'enabled', true);

        $no_style = get_post_meta($widget_id, 'no_style', true);
        if($no_style == 'no') {
            $setting['style_class'] = ' ' . $textSanity->key_ . 'style';
        } else {
            $setting['style_class'] = '';
        }

        $logo_url = plugins_url('../images/textsanity_logo_150px.png', __FILE__);

        if($setting['enabled'] == 'yes') {
            $dir = plugin_dir_path(__FILE__);
            include $dir . '../views/front_widget.php';
        }
    }

    // Output the option form field on the admin widgets screen.
    public function form( $instance ) {
        $widget_id = !empty($instance['widget_id']) ? $instance['widget_id'] : ''; 

        $dropdown = wp_dropdown_pages([
            'show_option_none' => __( 'Please Select...' ),
            'post_type'=> $this->key_ . 'widget',
            'name' => $this->get_field_name('widget_id'),
            'echo' => 0,
            'selected' => $widget_id,
        ]);
?>
<p>
    <label for="<?php echo $this->get_field_id('widget_id'); ?>">Widget</label>
    <?php echo $dropdown; ?>
</p>
<?php
    }

    // Save the options for the widget.
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['widget_id'] = strip_tags($new_instance['widget_id']);
        return $instance;
    }
}

