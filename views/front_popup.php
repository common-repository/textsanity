<div class="<?php echo $this->key . $setting['style_class'] . ' ' . $this->key_ . 'popup_cover'; ?>">
    <div class="<?php echo $this->key . ' ' . $this->key_ . 'popup'; ?>">
        <div class="txsy_interior">
            <form class="<?php echo esc_attr($this->key_ . 'ajax_form'); ?>" method="POST">
                <?php if($setting['description']): ?>
                <p><?php echo esc_html($setting['description']); ?></p>
                <?php endif; ?>
                <input type="hidden" name="action" value="<?php echo esc_attr($this->key_ . 'front'); ?>" />
                <input type="hidden" name="type" value="popup" />
                <input type="hidden" name="widget_id" value="<?php echo esc_attr($setting['widget_id']); ?>" />
                <div class="txsy_flex">
                    <label>Phone Number:</label>
                    <input type="text" name="phone" value="" />
                    <p class="txsy_submit">
                        <button><span class="dashicons dashicons-update"></span><span class="dashicons dashicons-yes"></span> Submit</button>
                    </p>
                </div>
                <button class="<?php echo esc_attr($this->key_ . 'do_not_show'); ?>">I'm Not Interested</button>
            </form>
        </div>
        <button class="<?php echo esc_attr($this->key_ . 'popup_close'); ?>" title="Close"><span class="dashicons dashicons-no-alt"></span></button>
        <p class="txsy_src"><a href="<?php echo esc_url($this->textsanity_url); ?>">Powered By <img src="<?php echo esc_url($logo_url); ?>" alt="TextSanity Logo" /></a></p>
    </div>
</div>
