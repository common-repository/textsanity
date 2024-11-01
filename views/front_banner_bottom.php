<div class="<?php echo $this->key . $setting['style_class'] . ' ' . $this->key_ . 'banner_bottom'; ?> txsy_resize">
    <form class="txsy_form txsy_interior <?php echo esc_attr($this->key_ . 'ajax_form'); ?>" method="POST">
        <?php if($setting['description']): ?>
        <p><?php echo esc_html($setting['description']); ?></p>
        <?php endif; ?>
        <input type="hidden" name="action" value="<?php echo esc_attr($this->key_ . 'front'); ?>" />
        <input type="hidden" name="type" value="banner_bottom" />
        <input type="hidden" name="widget_id" value="<?php echo esc_attr($setting['widget_id']); ?>" />
        <div class="txsy_flex">
            <label>Phone Number:</label>
            <input type="text" name="phone" value="" />
            <p class="txsy_submit">
                <button><span class="dashicons dashicons-update"></span><span class="dashicons dashicons-yes"></span> Submit</button>
            </p>
        </div>
        <p class="txsy_src"><a href="<?php echo esc_url($this->textsanity_url); ?>">Powered By <img src="<?php echo esc_url($logo_url); ?>" alt="TextSanity Logo" /></a></p>
    </form>
</div>
