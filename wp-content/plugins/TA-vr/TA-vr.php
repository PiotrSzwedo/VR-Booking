<?php
/**
 * Plugin Name: TA VR Reservation
 * Version: 1.3
 * Author: Technikum Akademickie w Legnicy
 * Requires PHP: 8.0
 * Requires at least: 6.6.2
 * Description: TA VR to wtyczka, która powstała do rezerwacji VR posiadanych przez Technikum Akademickie w Legnicy
*/


if (!defined('ABSPATH')) {
    exit;
}

include __DIR__."/blocks.php";
include __DIR__."/post.php";

add_action('admin_init', 'ta_vr_register_settings');

function ta_vr_register_settings() {
    if (get_option('ta_vr_day_in_past') === false) {
        add_option('ta_vr_day_in_past', 3);
    }

    if (get_option('ta_vr_day_in_future') === false) {
        add_option('ta_vr_day_in_future', 3);
    }
    
    register_setting('ta_vr_options_group', 'ta_vr_day_in_past');
    register_setting('ta_vr_options_group', 'ta_vr_day_in_future');
}
add_action('admin_init', 'ta_vr_register_settings');

function custom_admin_menu() {
    add_menu_page(
        'Booked Vr', 
        'Booked Vr',
        'manage_options', 
        'custom_menu_slug', 
        'my_custom_page', 
        'dashicons-admin-site',
        2 
    );
    
    add_submenu_page(
        'custom_menu_slug',                  
        'TA VR ustawienia',          
        'Ustawienia',                        
        'manage_options',                    
        'ta_vr_settings',                    
        'ta_vr_settings_page'         
    );
}
add_action('admin_menu', 'custom_admin_menu');

function load_custom_admin_style($hook) {
    if ($hook != 'toplevel_page_custom_menu_slug' && $hook != 'booked-vr_page_ta_vr_settings') {
        return;
    }

    wp_enqueue_style(
        'ta-vr-reservation-blok-style',
        plugins_url('_inc/css/form.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/admin.css')  
    );

    wp_enqueue_style(
        'ta-vr-reservation-color-style',
        plugins_url('_inc/css/colors.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . '_inc/css/admin.css')  
    );
}
add_action('admin_enqueue_scripts', 'load_custom_admin_style');

function my_custom_page() {
    ?>
    <div class="wrap">
    <?php
        include plugin_dir_path(__FILE__) . 'view/admin.booked.php';
    ?>
    </div>
    <?php
}

function ta_vr_settings_page() {
    include plugin_dir_path(__FILE__) . 'view/admin.settings.php';
}

register_activation_hook(__FILE__, 'ta_vr_plugin_activate'); 

function ta_vr_plugin_activate() {
    global $wpdb;

    add_role('technik', 'Technik');

    $table_name_vr = 'ta_vr';
    $table_name_vr_description ='ta_vr_description';
    $table_name_reservation = 'ta_vr_reservation';
    $table_name_reservation_vr = 'ta_vr_reservation_vr';

    // Tworzenie tabeli ta_vr
    $sql_vr = "CREATE TABLE IF NOT EXISTS $table_name_vr (
        id INT(11) NOT NULL AUTO_INCREMENT,
        number INT(11) NOT NULL,
        developer_mode TINYINT(1) NOT NULL DEFAULT 0,
        active TINYINT(1) NOT NULL DEFAULT 1
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    // Tworzenie tabeli ta_vr_description
    $sql_vr_description = "CREATE TABLE IF NOT EXISTS $table_name_vr_description (
        id INT(11) NOT NULL AUTO_INCREMENT,
        `key` LONGTEXT NOT NULL,
        element LONGTEXT NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    // Tworzenie tabeli ta_vr_reservation
    $sql_reservation = "CREATE TABLE IF NOT EXISTS $table_name_reservation (
        id INT(11) NOT NULL AUTO_INCREMENT,
        end DATE NOT NULL,
        user_id INT(11) NOT NULL,
        booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        description LONGTEXT DEFAULT NULL,
        active TINYINT(1) DEFAULT 1,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    // Tworzenie tabeli ta_vr_reservation_vr
    $sql_reservation_vr = "CREATE TABLE IF NOT EXISTS $table_name_reservation_vr (
        id INT(11) NOT NULL AUTO_INCREMENT,
        vr_id INT(11) NOT NULL,
        reservation_id INT(11) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); 
    dbDelta($sql_vr);
    dbDelta($sql_vr_description);
    dbDelta($sql_reservation);
    dbDelta($sql_reservation_vr);

    $descriptions = [
        [0, 'form-book', 'Zarezerwuj'],
        [1, 'form-description', 'Opis zamówienia:'],
        [2, 'form-vr', 'Gogle VR:'],
        [3, 'form-date', 'Data:'],
        [4, 'form-description-description', 'Programy do instalacji, drobne uwagi'],
        [5, 'booking-text', 'Rezerwacja nr.'],
        [6, 'booking-user-data', 'Dane użytkownika:'],
        [7, 'booking-user-data-email', 'Email: '],
        [8, 'booking-user-data-name', 'Nazwa: '],
        [9, 'booking-vr-numbers', 'Numery Vr: '],
        [10, 'booking-vr-delete', 'Usuń'],
        [11, 'booking-text-more-info', 'Więcej Informacji'],
        [12, 'booking-text-about-reservation', 'Opis zamówienia:'],
        [13, 'form--add-vr-number', 'Numer:'],
        [14, 'form--add-vr-developer-mode', 'Tryb developerski'],
        [15, 'form--add-vr-add-vr', 'Dodaj'],
        [16, 'vr-delete', 'Usuń'],
        [17, 'vr-save-changes', 'Zapisz zmiany'],
        [18, 'vr-form-devmode-info', 'VR posiada tryb developerski']
    ];

    foreach ($descriptions as $desc) {
        $wpdb->insert(
            $table_name_vr_description,
            [
                'id' => $desc[0],
                'key' => $desc[1],
                'element' => $desc[2],
            ]
        );
    }
}