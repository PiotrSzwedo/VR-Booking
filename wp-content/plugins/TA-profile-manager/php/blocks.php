<?php

function ta_pm_login_block() {
    wp_register_script(
        'ta-pm-login-editor',
        plugins_url('../_inc/js/initializeBlocks.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    // Register the styles
    wp_register_style(
        'ta-pm-login',
        plugins_url('../_inc/css/loginAndRegister.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '../_inc/css/loginAndRegister.css')
    );

    // Register the block
    register_block_type('ta-pm/login', array(
        'editor_script' => 'ta-pm-login-editor',
        'style' => 'ta-pm-login',
        'render_callback' => 'ta_pm_login_render',
    ));
}

function ta_pm_login_render() {
    ob_start();
    include plugin_dir_path(__FILE__) . '../view/login.php';
    return ob_get_clean();
}

function ta_pm_register_blocks() {
    ta_pm_login_block();
}

add_action('init', 'ta_pm_register_blocks');