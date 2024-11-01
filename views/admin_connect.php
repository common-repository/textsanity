<div class="wrap admin_setting_basic_page" id="<?php echo $action; ?>_page">
    <h1 class="wp-heading-inline"><?php echo esc_html($title); ?></h1>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div id="ajax_message" class="notice notice-success"><p>All updated.</p></div>
        <?php else: ?>
        <div id="ajax_message"></div>
        <?php endif; ?>

        <?php if(!$connected): ?>
        <p>Please connect your TextSanity account by clicking the button below.</p>
        <p><a class="button button-primary" href="<?php echo esc_url($oauth_url); ?>">Connect TextSanity Account</a></p>
        <?php else: ?>

        <p>Your TextSanity account is currently connected. To disconnect your account, please click the button below.</p>
        <form action="admin-ajax.php" method="post" class="ajax_form">
            <?php wp_nonce_field( $action ); ?>
                <input name="action" type="hidden" value="<?php echo $action; ?>" />

                <p class="submit">
                    <input type="submit" class="button button-primary" value="Disconnect TextSanity Account">
                    <span class="spinner"></span>
                </p>
        </form> 
        <?php endif; ?>
</div> 
