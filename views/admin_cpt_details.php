<table class="form-table editcomment" role="presentation">
    <tbody>
        <tr>
            <td class="first"><label for="enabled"><?php esc_html_e('Enabled', 'txsy'); ?></label></td>
            <td>
                <?php if($setting['enabled'] == 'yes'): ?>
                <input type="checkbox" name="enabled" value="yes" checked />
                <?php else: ?>
                <input type="checkbox" name="enabled" value="yes" />
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Position', 'txsy'); ?></label></td>
            <td>
                <select name="position">
                    <?php foreach($positions as $position): ?>
                        <?php if($setting['position'] == $position[1]): ?>
                            <option value="<?php echo esc_attr($position[1]); ?>" selected><?php echo esc_html($position[0]); ?></option>
                        <?php else: ?>
                            <option value="<?php echo esc_attr($position[1]); ?>"><?php echo esc_html($position[0]); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Page', 'txsy'); ?></label></td>
            <td><?php echo $select_locations; ?></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Type', 'txsy'); ?></label></td>
            <td>
                <select name="type">
                    <?php foreach($types as $type): ?>
                        <?php if($setting['type'] == $type[1]): ?>
                            <option value="<?php echo esc_attr($type[1]); ?>" selected><?php echo esc_html($type[0]); ?></option>
                        <?php else: ?>
                            <option value="<?php echo esc_attr($type[1]); ?>"><?php echo esc_html($type[0]); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Tags', 'txsy'); ?></label></td>
            <td><?php echo $select_tags; ?></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Keywords', 'txsy'); ?></label></td>
            <td><?php echo $select_keywords; ?></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Message', 'txsy'); ?></label></td>
            <td><textarea name="message" size="30"><?php echo esc_html($setting['message']); ?></textarea></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Background Color', 'txsy'); ?></label></td>
            <td><input class="color_picker" name="background_color" size="30" value="<?php echo esc_attr($setting['background_color']); ?>" /></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Text Color', 'txsy'); ?></label></td>
            <td><input class="color_picker" name="text_color" size="30" value="<?php echo esc_attr($setting['text_color']); ?>" /></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Popup Display Delay In Seconds', 'txsy'); ?></label></td>
            <td><input type="text" name="popup_delay" size="30" value="<?php echo esc_attr($setting['popup_delay']); ?>" /></td>
        </tr>

        <tr>
            <td class="first"><label for="chat_popup_auto"><?php esc_html_e('Show Chat Popup Automatically', 'txsy'); ?></label></td>
            <td>
                <?php if($setting['chat_popup_auto'] == 'yes'): ?>
                <input type="checkbox" name="chat_popup_auto" value="yes" checked />
                <?php else: ?>
                <input type="checkbox" name="chat_popup_auto" value="yes" />
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Chat Popup Delay', 'txsy'); ?></label></td>
            <td><input type="text" name="chat_popup_delay" size="30" value="<?php echo esc_attr($setting['chat_popup_delay']); ?>" /></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Description', 'txsy'); ?></label></td>
            <td><textarea name="description" size="30"><?php echo esc_html($setting['description']); ?></textarea></td>
        </tr>

        <tr>
            <td class="first"><label><?php _e('Thank You Message', 'txsy'); ?></label></td>
            <td><textarea name="thank_you" size="30"><?php echo esc_html($setting['thank_you']); ?></textarea></td>
        </tr>

        <tr>
            <td class="first"><label for="no_style"><?php esc_html_e('Advanced: Do Not Style', 'txsy'); ?></label></td>
            <td>
                <?php if($setting['no_style'] == 'yes'): ?>
                <input type="checkbox" name="no_style" value="yes" checked />
                <?php else: ?>
                <input type="checkbox" name="no_style" value="yes" />
                <?php endif; ?>
            </td>
        </tr>

    </tbody>
</table>
