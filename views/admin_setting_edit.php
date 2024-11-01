<div class="wrap">
    <h1>
        Edit Setting - 
        <?php if($key == 'banner_bottom'): ?>
        Banner On Bottom
        <?php elseif($key == 'banner_top'): ?>
        Banner On Top
        <?php elseif($key == 'chat'): ?>
        Chat
        <?php elseif($key == 'popup'): ?>
        Popup
        <?php endif; ?>
    </h1>

    <div id="ajax_message"></div>

    <div id="poststuff">
        <form id="post-body" class="metabox-holder columns-2 ajax_form" action="admin-ajax.php" method="POST">
            <?php wp_nonce_field($action); ?>
            <input name="action" type="hidden" value="<?php echo esc_attr($action); ?>" />
            <input name="key" type="hidden" value="<?php echo esc_attr($key); ?>" />

            <div id="post-body-content">
                <div class="stuffbox">
                    <div class="inside">
                        <h2>Fields</h2>
                        <fieldset>
                            <table class="form-table editcomment" role="presentation">
                                <tbody>
                                    <tr>
                                        <td class="first"><label for="name"><?php esc_html_e('Enabled', 'txsy'); ?></label></td>
                                        <td>
                                            <?php if($setting['enabled'] == 'yes'): ?>
                                            <input type="checkbox" name="enabled" value="yes" checked />
                                            <?php else: ?>
                                            <input type="checkbox" name="enabled" value="yes" />
                                            <?php endif; ?>
                                        </td>
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
                                        <td class="first"><label><?php _e('Color', 'txsy'); ?></label></td>
                                        <td><input class="color_picker" name="color" size="30" value="<?php echo esc_attr($setting['color']); ?>" /></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Popup Display Delay In Seconds', 'txsy'); ?></label></td>
                                        <td><input name="popup_delay" size="30" value="<?php echo esc_attr($setting['popup_delay']); ?>" /></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Description', 'txsy'); ?></label></td>
                                        <td><textarea name="description" size="30"><?php echo esc_html($setting['description']); ?></textarea></td>
                                    </tr>

                                    <tr>
                                        <td class="first"><label><?php _e('Thank You Message', 'txsy'); ?></label></td>
                                        <td><textarea name="thank_you" size="30"><?php echo esc_html($setting['thank_you']); ?></textarea></td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                    </div>
                </div>

            </div><!-- /post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <div id="submitdiv" class="stuffbox">
                    <h2>Save</h2>
                    <div class="inside">
                        <div class="submitbox" id="submitcomment">
                            <div id="major-publishing-actions">
                                <a href="<?php echo esc_url($url_back); ?>" class="button button-large button_cancel">Cancel</a>
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <input type="submit" name="save" id="save" class="button button-primary button-large" value="Save">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div><!-- /submitdiv -->
            </div>
        </form><!-- /post-body -->
    </div>
</div>
