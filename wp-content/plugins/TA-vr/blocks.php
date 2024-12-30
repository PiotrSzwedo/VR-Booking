<?php 

if (!defined('ABSPATH')) {
    exit;
}

// Funkcja rejestrująca zasoby dla bloku "VR Reservation Form"
function ta_vr_reservation_block_assets() {
    // Rejestracja skryptu edytora dla bloku "VR Reservation Form"
    wp_register_script(
        'ta-vr-reservation-form-editor',
        plugins_url('src/blockRender.php?blok=form&name=VR-reservation-form&icon=/wp-content/plugins/TA-vr/_inc/img/vr.webp', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    wp_register_style(
        'ta-vr-reservation-form-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/form.css')
    );
    wp_register_style(
        'ta-vr-reservation-form-colors',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/colors.css')
    );

    register_block_type('ta-vr-reservation/form', array(
        'editor_script' => 'ta-vr-reservation-form-editor',
        'style' => ['ta-vr-reservation-form-style', 'ta-vr-reservation-form-colors'],
        'render_callback' => 'ta_vr_reservation_render_block',
    ));
}

// Funkcja renderująca blok "VR Reservation Form"
function ta_vr_reservation_render_block() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'view/form.php';
    return ob_get_clean();
}

function ta_vr_my_reservation_block_assets() {
    wp_register_script(
        'ta-vr-reservation-my-editor',
        plugins_url('_inc/js/my-reservation.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    wp_register_style(
        'ta-vr-reservation-my-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/form.css')
    );
    wp_register_style(
        'ta-vr-reservation-my-colors',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/colors.css')
    );

    // Correct script handle in wp_script_add_data
    wp_script_add_data('ta-vr-reservation-my-editor', 'type', 'module');

    // Register the "VR My Reservation" block
    register_block_type('ta-vr-reservation/my', array(
        'editor_script' => 'ta-vr-reservation-my-editor',
        'style' => ['ta-vr-reservation-my-style', 'ta-vr-reservation-my-colors'],
        'render_callback' => 'ta_vr_my_reservation_render_block',
    ));
}

// Function to render the "VR My Reservation" block
function ta_vr_my_reservation_render_block() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'view/user.dashboard.php';
    return ob_get_clean();
}


function ta_vr_all_reservation_block_assets() {
    wp_register_script(
        'ta-vr-all-reservation-editor',
        plugins_url('_inc/js/all-reservation.js', __FILE__), 
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    wp_register_style(
        'ta-vr-reservation-all-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/form.css')
    );
    wp_register_style(
        'ta-vr-reservation-all-colors',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/colors.css')
    );
    
    wp_script_add_data('ta-vr-all-reservation-editor', 'type', 'module'); 
    

    register_block_type('ta-vr-all-reservation/all', array(
        'editor_script' => 'ta-vr-all-reservation-editor',
        'style' => ['ta-vr-reservation-all-style', 'ta-vr-reservation-all-colors'],
        'render_callback' => 'ta_vr_all_reservation_render_block'
    ));
}

function ta_vr_all_reservation_render_block() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'view/admin.booked.php';
    return ob_get_clean();
}


function ta_vr_add_block(){
    wp_register_script(
        'ta-vr-add-reservation-editor',
        plugins_url('_inc/js/vr-add.js', __FILE__), 
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    wp_register_style(
        'ta-vr-reservation-add-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/form.css')
    );
    wp_register_style(
        'ta-vr-reservation-add-colors',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/colors.css')
    );

    wp_script_add_data('ta-vr-add-reservation-editor', 'type', 'module'); 
    

    register_block_type('ta-vr-all-reservation/add', array(
        'editor_script' => 'ta-vr-add-reservation-editor',
        'style' => ['ta-vr-reservation-add-style', 'ta-vr-reservation-add-colors'],
        'render_callback' => 'ta_vr_add_render_block',
    ));
}

function ta_vr_add_render_block() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'view/admin.addVr.php';
    return ob_get_clean();
}

function ta_vr_delete_block(){
    wp_register_script(
        'ta-vr-delete-reservation-editor',
        plugins_url('_inc/js/vr-delete.js', __FILE__), 
        array('wp-blocks', 'wp-element', 'wp-editor'),
        null,
        true
    );

    wp_register_style(
        'ta-vr-reservation-delete-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/form.css')
    );
    wp_register_style(
        'ta-vr-reservation-delete-colors',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/colors.css')
    );

    wp_script_add_data('ta-vr-delete-reservation-editor', 'type', 'module'); 
    

    register_block_type('ta-vr-all-reservation/delete', array(
        'editor_script' => 'ta-vr-delete-reservation-editor',
        'style' => ['ta-vr-reservation-delete-style', 'ta-vr-reservation-delete-colors'],
        'render_callback' => 'ta_vr_delete_render_block',
    ));
}


function ta_vr_delete_render_block() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'view/admin.deleteVr.php';
    return ob_get_clean();
}

function ta_register_vr_blocks() {
    ta_vr_my_reservation_block_assets();
    ta_vr_add_block();
    ta_vr_delete_block();
    ta_vr_all_reservation_block_assets();
    ta_vr_reservation_block_assets();
}

add_action('init', 'ta_register_vr_blocks');