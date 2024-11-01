<div class="wrap settings_wrap">
    <h1 class="wp-heading-inline">Settings</h1>

    <div id="ajax_message"></div>

    <hr class="wp-header-end">

    <form id="settings-filter" method="get">
        <?php wp_nonce_field($this->key_ . 'settings'); ?>
    <input type="hidden" name="page" value="<?php echo esc_attr($this->key_ . 'settings'); ?>" />
    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>
                <th>Title</th>
                <th>Enabled</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($settings['items'] as $key => $setting): ?>
            <tr>
                <td><strong><a href="<?php echo $this->adminLink('settings', $key); ?>"><?php echo esc_html($setting['title']); ?></a></strong></td>
                <td><?php echo esc_html($setting['enabled']); ?></td>
                <td>
                    <a class="button" href="<?php echo $this->adminLink('settings', $key); ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th>Title</th>
                <th>Enabled</th>
                <th>Actions</th>
            </tr>
        </tfoot>

    </table>


    <div class="tablenav bottom">
        <br class="clear">
    </div>

    </form>
</div>
